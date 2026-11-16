<?php

/**
 * Persiste identidades MCP e credenciais opacas com consumo transacional.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Manipuladores;

use PDO;
use Throwable;

final class Autorizacoes
{
    public function __construct(private PDO $banco)
    {
    }

    public function transacionar(callable $operacao): mixed
    {
        $this->banco->beginTransaction();
        try {
            $resultado = $operacao();
            $this->banco->commit();
            return $resultado;
        } catch (Throwable $falha) {
            $this->banco->rollBack();
            throw $falha;
        }
    }

    public function usuarioEmail(string $email): ?array
    {
        return $this->linha('SELECT * FROM usuarios_mcp WHERE email = ?', [$email]);
    }

    public function usuario(int $identificador): ?array
    {
        return $this->linha('SELECT * FROM usuarios_mcp WHERE identificador = ?', [$identificador]);
    }

    public function usuarios(): array
    {
        return $this->banco->query('SELECT identificador, email, administrador, ativo, senha_inicial, criado_em FROM usuarios_mcp ORDER BY identificador')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criarUsuario(string $email, string $hash): int
    {
        $this->executar('INSERT INTO usuarios_mcp (email, senha_hash) VALUES (?, ?)', [$email, $hash]);
        return (int) $this->banco->lastInsertId();
    }

    public function senha(int $identificador, string $hash): void
    {
        $this->executar('UPDATE usuarios_mcp SET senha_hash = ?, senha_inicial = 0 WHERE identificador = ?', [$hash, $identificador]);
        $this->revogarUsuario($identificador);
    }

    public function desativar(int $identificador): void
    {
        $this->executar('UPDATE usuarios_mcp SET ativo = 0 WHERE identificador = ?', [$identificador]);
        $this->revogarUsuario($identificador);
    }

    private function revogarUsuario(int $identificador): void
    {
        $this->executar('DELETE FROM tokens_mcp WHERE usuario = ?', [$identificador]);
        $this->executar('DELETE FROM codigos_mcp WHERE usuario = ?', [$identificador]);
    }

    public function criarCliente(string $identificador, string $nome, array $redirecionamentos): void
    {
        $this->executar('INSERT INTO clientes_mcp (identificador, nome, redirecionamentos) VALUES (?, ?, ?)', [$identificador, $nome, \json_encode($redirecionamentos, JSON_THROW_ON_ERROR)]);
    }

    public function cliente(string $identificador): ?array
    {
        return $this->linha('SELECT * FROM clientes_mcp WHERE identificador = ?', [$identificador]);
    }

    public function criarCodigo(string $resumo, int $usuario, array $pedido, int $expira): void
    {
        $this->executar('INSERT INTO codigos_mcp (resumo, usuario, cliente, redirecionamento, desafio, recurso, expira) VALUES (?, ?, ?, ?, ?, ?, ?)', [$resumo, $usuario, $pedido['client_id'], $pedido['redirect_uri'], $pedido['code_challenge'], $pedido['resource'], $expira]);
    }

    public function codigo(string $resumo): ?array
    {
        return $this->linha('SELECT * FROM codigos_mcp WHERE resumo = ? FOR UPDATE', [$resumo]);
    }

    public function excluirCodigo(string $resumo): void
    {
        $this->executar('DELETE FROM codigos_mcp WHERE resumo = ?', [$resumo]);
    }

    public function criarToken(string $resumo, string $renovacao, int $usuario, string $cliente, string $recurso, int $agora): void
    {
        $this->executar('INSERT INTO tokens_mcp (resumo, renovacao, usuario, cliente, recurso, expira, renovacao_expira) VALUES (?, ?, ?, ?, ?, ?, ?)', [$resumo, $renovacao, $usuario, $cliente, $recurso, $agora + 3600000, $agora + 2592000000]);
    }

    public function token(string $resumo, bool $renovacao = false): ?array
    {
        return $this->linha('SELECT * FROM tokens_mcp WHERE ' . ($renovacao ? 'renovacao' : 'resumo') . ' = ?' . ($renovacao ? ' FOR UPDATE' : ''), [$resumo]);
    }

    public function marcarToken(string $resumo): void
    {
        $this->executar('UPDATE tokens_mcp SET revogado = 1 WHERE resumo = ?', [$resumo]);
    }

    public function revogarFamilia(int $usuario, string $cliente): void
    {
        $this->executar('DELETE FROM tokens_mcp WHERE usuario = ? AND cliente = ?', [$usuario, $cliente]);
    }

    public function revogar(string $resumo, string $cliente): void
    {
        $registro = $this->linha('SELECT usuario FROM tokens_mcp WHERE cliente = ? AND (resumo = ? OR renovacao = ?)', [$cliente, $resumo, $resumo]);
        if ($registro !== null) {
            $this->revogarFamilia((int) $registro['usuario'], $cliente);
        }
    }

    public function limpar(int $agora): void
    {
        $this->executar('DELETE FROM codigos_mcp WHERE expira <= ?', [$agora]);
        $this->executar('DELETE FROM tokens_mcp WHERE renovacao_expira <= ?', [$agora]);
    }

    private function linha(string $sql, array $valores): ?array
    {
        $consulta = $this->banco->prepare($sql);
        $consulta->execute($valores);
        return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function executar(string $sql, array $valores): void
    {
        $consulta = $this->banco->prepare($sql);
        $consulta->execute($valores);
    }
}
