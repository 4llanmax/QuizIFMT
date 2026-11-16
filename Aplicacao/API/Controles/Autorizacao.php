<?php

/**
 * Atende descoberta OAuth, registro de clientes, emissão e revogação de tokens MCP.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\API\Controles;

use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use Aplicacao\Nucleo\Servicos\OAuth;
use Aplicacao\Nucleo\Transportes\Resposta;
use DomainException;
use JsonException;

final class Autorizacao
{
    public function __construct(private OAuth $oauth, private string $urlBase)
    {
    }

    public function responder(array $servidor, string $corpo, array $campos, array &$sessao, int $agora): Resposta
    {
        $caminho = \parse_url($servidor['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $metodo = $servidor['REQUEST_METHOD'] ?? 'GET';
        $prefixo = \parse_url($this->urlBase, PHP_URL_PATH) ?: '';
        $caminho = \substr($caminho, \strlen($prefixo));
        try {
            if ($metodo === 'OPTIONS') {
                return $this->json([], 204);
            }
            if ($metodo === 'GET' && $caminho === '/.well-known/oauth-authorization-server') {
                return $this->json(['issuer' => $this->urlBase, 'authorization_endpoint' => $this->urlBase . '/admin/mcp/autorizar',
                    'token_endpoint' => $this->urlBase . '/oauth/token', 'registration_endpoint' => $this->urlBase . '/oauth/registrar', 'revocation_endpoint' => $this->urlBase . '/oauth/revogar',
                    'response_types_supported' => ['code'], 'grant_types_supported' => ['authorization_code', 'refresh_token'],
                    'token_endpoint_auth_methods_supported' => ['none'], 'code_challenge_methods_supported' => ['S256'], 'scopes_supported' => ['mcp:gestao']]);
            }
            if ($metodo === 'GET' && \in_array($caminho, ['/.well-known/oauth-protected-resource', '/.well-known/oauth-protected-resource/mcp'], true)) {
                return $this->json(['resource' => $this->urlBase . '/mcp', 'authorization_servers' => [$this->urlBase], 'scopes_supported' => ['mcp:gestao'], 'bearer_methods_supported' => ['header']]);
            }
            if ($metodo !== 'POST') {
                return $this->json(['error' => 'invalid_request', 'error_description' => 'Método não permitido.'], 405);
            }
            if ($caminho === '/oauth/registrar') {
                $this->oauth->limitar('registro:' . ($servidor['REMOTE_ADDR'] ?? ''), $agora);
                $entrada = \json_decode($corpo, true, 16, JSON_THROW_ON_ERROR);
                if (!\is_array($entrada) || \array_is_list($entrada)) {
                    throw new DomainException('invalid_client_metadata');
                }
                return $this->json($this->oauth->registrarCliente($entrada), 201);
            }
            if ($caminho === '/oauth/token') {
                return $this->json($this->oauth->trocar($campos, $agora));
            }
            if ($caminho === '/oauth/revogar') {
                if (!\is_string($campos['token'] ?? null) || !\is_string($campos['client_id'] ?? null)) {
                    throw new DomainException('invalid_request');
                }
                $this->oauth->revogar($campos['token'], $campos['client_id']);
                return $this->json([]);
            }
            return $this->json(['error' => 'invalid_request', 'error_description' => 'Caminho inexistente.'], 404);
        } catch (DomainException | JsonException $falha) {
            return $this->json(['error' => $falha instanceof JsonException ? 'invalid_request' : $falha->getMessage(), 'error_description' => 'A solicitação OAuth é inválida ou expirou. Refaça a conexão.'], 400);
        } catch (FalhaHTTP $falha) {
            return $this->json(['error' => 'temporarily_unavailable', 'error_description' => $falha->getMessage()], $falha->status);
        }
    }

    private function json(array $dados, int $status = 200): Resposta
    {
        return new Resposta($status, $status === 204 ? '' : \json_encode($dados === [] ? new \stdClass() : $dados, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE), [
            'Content-Type' => 'application/json; charset=UTF-8', 'Cache-Control' => 'no-store', 'Pragma' => 'no-cache',
            'Access-Control-Allow-Origin' => '*', 'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS', 'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
        ]);
    }
}
