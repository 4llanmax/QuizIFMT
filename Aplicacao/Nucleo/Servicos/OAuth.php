<?php

/**
 * Autoriza clientes MCP com código, PKCE S256 e renovação rotativa vinculada ao recurso.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Servicos;

use Aplicacao\Nucleo\Manipuladores\Autorizacoes;
use Aplicacao\Nucleo\Manipuladores\Tentativas;
use Aplicacao\Nucleo\Servicos\Acesso;
use DomainException;
use PDO;
use PDOException;

final class OAuth
{
    private Autorizacoes $dados;
    private Acesso $acesso;

    public function __construct(PDO $banco, private string $urlBase, private string $senhaInicialHash = '')
    {
        $this->dados = new Autorizacoes($banco);
        $this->acesso = new Acesso(new Tentativas($banco));
    }

    public function registrarCliente(array $entrada): array
    {
        $redirecionamentos = $entrada['redirect_uris'] ?? null;
        $nome = $entrada['client_name'] ?? 'Cliente MCP';
        if (!\is_array($redirecionamentos) || !\array_is_list($redirecionamentos) || \count($redirecionamentos) < 1 || \count($redirecionamentos) > 10 || !\is_string($nome) || \mb_strlen($nome) < 1 || \mb_strlen($nome) > 80 || ($entrada['token_endpoint_auth_method'] ?? 'none') !== 'none') {
            throw new DomainException('invalid_client_metadata');
        }
        foreach ($redirecionamentos as $url) {
            $partes = \is_string($url) ? \parse_url($url) : false;
            if (!\is_string($url) || \strlen($url) > 2048 || !\filter_var($url, FILTER_VALIDATE_URL) || !\is_array($partes) || isset($partes['fragment']) || isset($partes['user']) || isset($partes['pass']) || !\in_array($partes['scheme'] ?? '', ['http', 'https'], true) || ($partes['scheme'] === 'http' && !\in_array($partes['host'] ?? '', ['localhost', '127.0.0.1', '[::1]'], true))) {
                throw new DomainException('invalid_redirect_uri');
            }
        }
        $identificador = \bin2hex(\random_bytes(32));
        $this->dados->criarCliente($identificador, $nome, $redirecionamentos);
        return ['client_id' => $identificador, 'client_name' => $nome, 'redirect_uris' => $redirecionamentos, 'grant_types' => ['authorization_code', 'refresh_token'], 'response_types' => ['code'], 'token_endpoint_auth_method' => 'none'];
    }

    public function limitar(string $origem, int $agora): void
    {
        $this->acesso->conferir('mcp:' . $origem, $agora);
    }

    public function validarPedido(array $entrada): array
    {
        foreach (['client_id', 'redirect_uri', 'code_challenge', 'code_challenge_method', 'response_type', 'resource'] as $chave) {
            if (!isset($entrada[$chave]) || !\is_string($entrada[$chave])) {
                throw new DomainException('invalid_request');
            }
        }
        $cliente = $this->dados->cliente($entrada['client_id']);
        if ($cliente === null || !\in_array($entrada['redirect_uri'], \json_decode($cliente['redirecionamentos'], true, 512, JSON_THROW_ON_ERROR), true)) {
            throw new DomainException('invalid_redirect_uri');
        }
        if ($entrada['response_type'] !== 'code' || $entrada['code_challenge_method'] !== 'S256' || !\preg_match('/^[A-Za-z0-9_-]{43}$/D', $entrada['code_challenge']) || $entrada['resource'] !== $this->urlBase . '/mcp') {
            throw new DomainException('invalid_request');
        }
        if (!\is_string($entrada['scope'] ?? '') || !\in_array(\trim($entrada['scope'] ?? ''), ['', 'mcp:gestao'], true) || !\is_string($entrada['state'] ?? '') || \strlen($entrada['state'] ?? '') > 2048) {
            throw new DomainException('invalid_scope');
        }
        return \array_intersect_key($entrada, \array_flip(['client_id', 'redirect_uri', 'code_challenge', 'code_challenge_method', 'response_type', 'resource', 'scope', 'state'])) + ['state' => '', 'scope' => 'mcp:gestao', 'clienteNome' => $cliente['nome']];
    }

    public function entrar(string $email, string $senha, string $origem, int $agora): array
    {
        $this->limitar('login:' . $origem, $agora);
        $usuario = $this->dados->usuarioEmail(\mb_strtolower(\trim($email)));
        if ($usuario === null || !(int) $usuario['ativo'] || !\password_verify($senha, $usuario['senha_hash'])) {
            throw new DomainException('E-mail ou senha incorretos.');
        }
        return $this->publicar($usuario);
    }

    public function autorizar(array $pedido, int $usuario, int $agora): string
    {
        $pedido = $this->validarPedido($pedido);
        $identidade = $this->dados->usuario($usuario);
        if ($identidade === null || !(int) $identidade['ativo'] || (int) $identidade['senha_inicial']) {
            throw new DomainException('Troque a senha inicial antes de conectar.');
        }
        $this->dados->limpar($agora);
        $codigo = \bin2hex(\random_bytes(32));
        $this->dados->criarCodigo(\hash('sha256', $codigo), $usuario, $pedido, $agora + 300000);
        return $codigo;
    }

    public function trocar(array $entrada, int $agora): array
    {
        foreach ($entrada as $valor) {
            if (!\is_string($valor)) {
                throw new DomainException('invalid_request');
            }
        }
        if (($entrada['resource'] ?? '') !== $this->urlBase . '/mcp' || !isset($entrada['client_id'])) {
            throw new DomainException('invalid_target');
        }
        $resultado = $this->dados->transacionar(function () use ($entrada, $agora): array {
            if (($entrada['grant_type'] ?? '') === 'authorization_code') {
                $resumo = \hash('sha256', $entrada['code'] ?? '');
                $registro = $this->dados->codigo($resumo);
                $verificador = $entrada['code_verifier'] ?? '';
                if ($registro === null || (int) $registro['expira'] <= $agora || $registro['cliente'] !== $entrada['client_id'] || $registro['recurso'] !== $entrada['resource'] || $registro['redirecionamento'] !== ($entrada['redirect_uri'] ?? '') || !\preg_match('/^[A-Za-z0-9._~-]{43,128}$/D', $verificador) || !\hash_equals($registro['desafio'], \rtrim(\strtr(\base64_encode(\hash('sha256', $verificador, true)), '+/', '-_'), '='))) {
                    throw new DomainException('invalid_grant');
                }
                $this->dados->excluirCodigo($resumo);
            } elseif (($entrada['grant_type'] ?? '') === 'refresh_token') {
                $registro = $this->dados->token(\hash('sha256', $entrada['refresh_token'] ?? ''), true);
                if ($registro === null || (int) $registro['renovacao_expira'] <= $agora || $registro['cliente'] !== $entrada['client_id'] || $registro['recurso'] !== $entrada['resource']) {
                    throw new DomainException('invalid_grant');
                }
                if ((int) $registro['revogado']) {
                    $this->dados->revogarFamilia((int) $registro['usuario'], $registro['cliente']);
                    return [];
                }
                $this->dados->marcarToken($registro['resumo']);
            } else {
                throw new DomainException('unsupported_grant_type');
            }
            $usuario = $this->dados->usuario((int) $registro['usuario']);
            if ($usuario === null || !(int) $usuario['ativo'] || (int) $usuario['senha_inicial']) {
                throw new DomainException('invalid_grant');
            }
            $acesso = \bin2hex(\random_bytes(32));
            $renovacao = \bin2hex(\random_bytes(32));
            $this->dados->criarToken(\hash('sha256', $acesso), \hash('sha256', $renovacao), (int) $usuario['identificador'], $entrada['client_id'], $entrada['resource'], $agora);
            return ['access_token' => $acesso, 'token_type' => 'Bearer', 'expires_in' => 3600, 'refresh_token' => $renovacao, 'scope' => 'mcp:gestao'];
        });
        if ($resultado === []) {
            throw new DomainException('invalid_grant');
        }
        return $resultado;
    }

    public function autenticarToken(string $token, int $agora): array
    {
        $registro = $this->dados->token(\hash('sha256', $token));
        $usuario = $registro === null ? null : $this->dados->usuario((int) $registro['usuario']);
        if ($registro === null || (int) $registro['revogado'] || (int) $registro['expira'] <= $agora || $registro['recurso'] !== $this->urlBase . '/mcp' || $usuario === null || !(int) $usuario['ativo'] || (int) $usuario['senha_inicial']) {
            throw new DomainException('invalid_token');
        }
        return $this->publicar($usuario);
    }

    public function revogar(string $token, string $cliente): void
    {
        $this->dados->revogar(\hash('sha256', $token), $cliente);
    }

    public function listarUsuarios(): array
    {
        return $this->dados->usuarios();
    }

    public function criarUsuario(string $email, int $criador): array
    {
        $this->exigirAdministrador($criador);
        $email = \mb_strtolower(\trim($email));
        if (!\filter_var($email, FILTER_VALIDATE_EMAIL) || \strlen($email) > 254 || (\password_get_info($this->senhaInicialHash)['algo'] ?? null) === null) {
            throw new DomainException('Informe um e-mail válido e configure o hash da senha inicial.');
        }
        try {
            $identificador = $this->dados->criarUsuario($email, $this->senhaInicialHash);
        } catch (PDOException $falha) {
            if (($falha->errorInfo[1] ?? null) !== 1062) {
                throw $falha;
            }
            throw new DomainException('Já existe um usuário com esse e-mail.');
        }
        return $this->publicar($this->dados->usuario($identificador));
    }

    public function trocarSenha(int $identificador, string $atual, string $nova): void
    {
        $usuario = $this->dados->usuario($identificador);
        if ($usuario === null || !(int) $usuario['ativo'] || !\password_verify($atual, $usuario['senha_hash']) || \strlen($nova) < 10 || \strlen($nova) > 72 || \str_contains($nova, "\0") || \hash_equals($atual, $nova) || ($this->senhaInicialHash !== '' && \password_verify($nova, $this->senhaInicialHash))) {
            throw new DomainException('Confira a senha atual e escolha uma nova senha de 10 a 72 bytes diferente da inicial.');
        }
        $this->dados->transacionar(fn () => $this->dados->senha($identificador, \password_hash($nova, PASSWORD_DEFAULT)));
    }

    public function desativarUsuario(int $identificador, int $operador): void
    {
        $this->exigirAdministrador($operador);
        $usuario = $this->dados->usuario($identificador);
        if ($usuario === null || $identificador === $operador || (int) $usuario['administrador']) {
            throw new DomainException('Não é permitido desativar este usuário.');
        }
        $this->dados->transacionar(fn () => $this->dados->desativar($identificador));
    }

    private function exigirAdministrador(int $identificador): void
    {
        $usuario = $this->dados->usuario($identificador);
        if ($usuario === null || !(int) $usuario['ativo'] || !(int) $usuario['administrador']) {
            throw new DomainException('Somente administradores gerenciam usuários MCP.');
        }
    }

    private function publicar(array $usuario): array
    {
        unset($usuario['senha_hash']);
        $usuario['identificador'] = (int) $usuario['identificador'];
        return $usuario;
    }
}
