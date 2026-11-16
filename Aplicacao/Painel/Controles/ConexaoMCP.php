<?php

/**
 * Autentica o operador e recolhe consentimento explícito antes de autorizar o cliente MCP.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Painel\Controles;

use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use Aplicacao\Nucleo\Servicos\OAuth;
use Aplicacao\Nucleo\Transportes\Resposta;
use DomainException;

final class ConexaoMCP
{
    public function __construct(private OAuth $oauth, private string $urlBase)
    {
    }

    public function responder(array $servidor, string $corpo, array $campos, array &$sessao, int $agora): Resposta
    {
        $sessao['mcpCSRF'] ??= \bin2hex(\random_bytes(32));
        try {
            if (($servidor['REQUEST_METHOD'] ?? 'GET') === 'GET') {
                unset($sessao['mcpPedido'], $sessao['mcpPrazo']);
                \parse_str(\parse_url($servidor['REQUEST_URI'] ?? '', PHP_URL_QUERY) ?: '', $consulta);
                $sessao['mcpPedido'] = $this->oauth->validarPedido($consulta);
                $sessao['mcpPrazo'] = $agora + 600000;
                return $this->pagina($sessao);
            }
            if (($servidor['REQUEST_METHOD'] ?? '') !== 'POST') {
                return $this->pagina($sessao, 'Método não permitido.', 405);
            }
            $partes = \parse_url($this->urlBase);
            $origem = $partes['scheme'] . '://' . $partes['host'] . (isset($partes['port']) ? ':' . $partes['port'] : '');
            if (!\is_string($campos['csrf'] ?? null) || !\hash_equals($sessao['mcpCSRF'], $campos['csrf']) || (isset($servidor['HTTP_ORIGIN']) && $servidor['HTTP_ORIGIN'] !== $origem)) {
                return $this->pagina($sessao, 'A sessão do formulário expirou. Refaça a conexão.', 403);
            }
            if (!isset($sessao['mcpPedido'], $sessao['mcpPrazo']) || $sessao['mcpPrazo'] <= $agora) {
                return $this->pagina($sessao, 'A solicitação de conexão expirou. Refaça a conexão.', 400);
            }
            $pedido = $this->oauth->validarPedido($sessao['mcpPedido']);
            if (($campos['acao'] ?? '') === 'cancelar') {
                unset($sessao['mcpPedido'], $sessao['mcpPrazo']);
                return $this->redirecionar($pedido, ['error' => 'access_denied']);
            }
            if (($campos['acao'] ?? '') !== 'autorizar' || !\is_string($campos['email'] ?? null) || !\is_string($campos['senha'] ?? null) || \strlen($campos['email']) > 254 || \strlen($campos['senha']) > 72) {
                throw new DomainException('Preencha o e-mail e a senha para autorizar a conexão.');
            }
            $usuario = $this->oauth->entrar($campos['email'], $campos['senha'], $servidor['REMOTE_ADDR'] ?? '', $agora);
            $codigo = $this->oauth->autorizar($pedido, $usuario['identificador'], $agora);
            unset($sessao['mcpPedido'], $sessao['mcpPrazo']);
            $sessao['mcpCSRF'] = \bin2hex(\random_bytes(32));
            $sessao['renovar'] = true;
            return $this->redirecionar($pedido, ['code' => $codigo]);
        } catch (DomainException $falha) {
            return $this->pagina($sessao, \str_starts_with($falha->getMessage(), 'invalid_') ? 'Solicitação de conexão inválida. Refaça a conexão pelo cliente MCP.' : $falha->getMessage(), 422);
        } catch (FalhaHTTP $falha) {
            return $this->pagina($sessao, $falha->getMessage(), $falha->status);
        }
    }

    private function redirecionar(array $pedido, array $valores): Resposta
    {
        $valores['state'] = $pedido['state'];
        return new Resposta(303, '', ['Location' => $pedido['redirect_uri'] . (\str_contains($pedido['redirect_uri'], '?') ? '&' : '?') . \http_build_query($valores, '', '&', PHP_QUERY_RFC3986)]);
    }

    private function pagina(array $sessao, string $erro = '', int $status = 200): Resposta
    {
        $visao = require \dirname(__DIR__) . '/Visoes/Paginas/ConexaoMCP.php';
        return new Resposta($status, $visao(['csrf' => $sessao['mcpCSRF'], 'cliente' => $sessao['mcpPedido']['clienteNome'] ?? '',
            'redirecionamento' => $sessao['mcpPedido']['redirect_uri'] ?? '', 'erro' => $erro, 'urlBase' => $this->urlBase, 'pedido' => isset($sessao['mcpPedido'])]));
    }
}
