<?php

/**
 * Compõe autorização e ferramentas MCP com configuração pública validada e conexão compartilhada.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\API\Provedores;

use Aplicacao\API\Controles\Autorizacao;
use Aplicacao\API\Controles\MCP;
use Aplicacao\API\Servicos\Ferramentas;
use Aplicacao\API\Servicos\FerramentasUsuarios;
use Aplicacao\Nucleo\Comuns\Classes\Parametros;
use Aplicacao\Nucleo\Comuns\Classes\RoteadorHTTP;
use Aplicacao\Nucleo\Servicos\OAuth;
use Aplicacao\Nucleo\Transportes\Resposta;
use PDO;
use RuntimeException;

final class Atendimento
{
    private string $urlBase;
    private ?OAuth $autorizacao = null;

    public function __construct(private PDO $banco, private Parametros $parametros)
    {
        $this->urlBase = (string) $parametros->obter('mcpURLBase');
        $partes = \parse_url($this->urlBase);
        if (!\filter_var($this->urlBase, FILTER_VALIDATE_URL) || !\is_array($partes) || ($partes['scheme'] ?? '') !== 'https' || \array_intersect_key($partes, \array_flip(['user', 'pass', 'query', 'fragment'])) !== [] || \str_ends_with($this->urlBase, '/') || ($partes['path'] ?? '') !== \rtrim((string) $parametros->obter('urlPrefixo'), '/') || (isset($partes['port']) && $partes['port'] < 1)) {
            throw new RuntimeException('Configure MCP_URL_BASE como URL HTTPS pública, sem credenciais, consulta, fragmento ou barra final, com o caminho de URL_PREFIXO.');
        }
    }

    public static function parametros(): array
    {
        return ['MCP_URL_BASE' => 'mcpURLBase', 'MCP_SENHA_INICIAL_HASH' => 'mcpSenhaInicialHash'];
    }

    public function oauth(): OAuth
    {
        return $this->autorizacao ??= new OAuth($this->banco, $this->urlBase, (string) $this->parametros->obter('mcpSenhaInicialHash'));
    }

    public function construir(): RoteadorHTTP
    {
        $oauth = $this->oauth();
        $ferramentas = new Ferramentas($this->banco);
        $usuarios = new FerramentasUsuarios(
            static fn (): array => $oauth->listarUsuarios(),
            static fn (string $email, int $operador): array => $oauth->criarUsuario($email, $operador),
            static function (int $identificador, int $operador) use ($oauth): void {
                $oauth->desativarUsuario($identificador, $operador);
            },
            static function (int $identificador, string $atual, string $nova) use ($oauth): void {
                $oauth->trocarSenha($identificador, $atual, $nova);
            }
        );
        $nomesUsuarios = \array_column($usuarios->listar(), 'name');
        $mcp = new MCP(static fn (string $token, int $agora): array => $oauth->autenticarToken($token, $agora), static fn (): array => \array_merge($ferramentas->listar(), $usuarios->listar()), static fn (string $nome, array $argumentos, array $usuario): array => \in_array($nome, $nomesUsuarios, true) ? $usuarios->executar($nome, $argumentos, $usuario) : $ferramentas->executar($nome, $argumentos), $this->urlBase);
        $controle = new Autorizacao($oauth, $this->urlBase);
        $atendimentos = [
            'mcp' => static function (array $servidor, string $corpo, array $campos, array &$sessao, int $agora) use ($mcp): Resposta {
                return $mcp->responder($servidor, $corpo, $agora);
            },
            'oauth' => static function (array $servidor, string $corpo, array $campos, array &$sessao, int $agora) use ($controle): Resposta {
                return $controle->responder($servidor, $corpo, $campos, $sessao, $agora);
            },
        ];
        $rotas = [];
        foreach (require \dirname(__DIR__) . '/Rotas/Mapa.php' as $caminho => $atendimento) {
            $rotas[$caminho] = $atendimentos[$atendimento];
        }
        return new RoteadorHTTP($rotas);
    }
}
