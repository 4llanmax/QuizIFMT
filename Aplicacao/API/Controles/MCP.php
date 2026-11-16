<?php

/**
 * Atende o transporte HTTP autenticado do MCP e recusa origens e formatos inválidos.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\API\Controles;

use Aplicacao\API\Servicos\Protocolo;
use Aplicacao\Nucleo\Transportes\Resposta;
use Closure;
use DomainException;
use JsonException;
use Throwable;

final class MCP
{
    private Protocolo $protocolo;

    public function __construct(private Closure $autenticar, Closure $listar, Closure $executar, private string $urlBase)
    {
        $this->urlBase = \rtrim($urlBase, '/');
        $this->protocolo = new Protocolo($listar, $executar);
    }

    public function responder(array $servidor, string $corpo, int $agora): Resposta
    {
        $url = \parse_url($this->urlBase);
        $origem = $url['scheme'] . '://' . $url['host'] . (isset($url['port']) ? ':' . $url['port'] : '');
        if (isset($servidor['HTTP_ORIGIN']) && $servidor['HTTP_ORIGIN'] !== $origem) {
            return $this->falhar(403, -32000, 'Origem não autorizada.');
        }
        $autorizacao = $servidor['HTTP_AUTHORIZATION'] ?? $servidor['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        try {
            if (!\preg_match('/^Bearer ([A-Za-z0-9._~+\/-]+=*)$/iD', $autorizacao, $partes)) {
                throw new DomainException('Token ausente.');
            }
            $usuario = ($this->autenticar)($partes[1], $agora);
        } catch (DomainException) {
            return $this->falhar(401, -32000, 'Entre na conta para conectar o MCP.', ['WWW-Authenticate' => 'Bearer resource_metadata="' . $this->urlBase . '/.well-known/oauth-protected-resource"']);
        } catch (Throwable) {
            return $this->falhar(500, -32603, 'Não foi possível verificar a conexão MCP.');
        }
        if (($servidor['REQUEST_METHOD'] ?? '') !== 'POST') {
            return $this->falhar(405, -32000, 'O MCP recebe mensagens por POST.', ['Allow' => 'POST']);
        }
        if (\strtolower(\trim(\explode(';', $servidor['CONTENT_TYPE'] ?? '')[0])) !== 'application/json') {
            return $this->falhar(415, -32000, 'Envie a mensagem como application/json.');
        }
        $aceitos = [];
        foreach (\explode(',', $servidor['HTTP_ACCEPT'] ?? '') as $tipo) {
            $partesTipo = \explode(';', \strtolower(\trim($tipo)));
            $qualidade = 1.0;
            foreach (\array_slice($partesTipo, 1) as $parametro) {
                if (\preg_match('/^\s*q\s*=\s*([0-9.]+)\s*$/D', $parametro, $peso)) {
                    $qualidade = (float) $peso[1];
                }
            }
            if ($qualidade > 0) {
                $aceitos[] = \trim($partesTipo[0]);
            }
        }
        if (!\in_array('application/json', $aceitos, true) || !\in_array('text/event-stream', $aceitos, true)) {
            return $this->falhar(406, -32000, 'O cabeçalho Accept deve incluir application/json e text/event-stream.');
        }
        if (\strlen($corpo) > 1048576) {
            return $this->falhar(413, -32000, 'A mensagem MCP excede 1 MiB.');
        }
        if (!\in_array($servidor['HTTP_MCP_PROTOCOL_VERSION'] ?? '2025-03-26', Protocolo::VERSOES, true)) {
            return $this->falhar(400, -32000, 'Versão MCP não suportada.');
        }
        try {
            $mensagem = \json_decode($corpo, false, 64, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->falhar(400, -32700, 'O corpo não contém JSON válido.');
        }
        try {
            $resultado = $this->protocolo->responder($mensagem, $usuario);
            if ($resultado === null) {
                return new Resposta(202);
            }
            $codigo = $resultado['error']['code'] ?? 0;
            return new Resposta($codigo === -32600 ? 400 : 200, \json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE), ['Content-Type' => 'application/json; charset=UTF-8']);
        } catch (Throwable) {
            return $this->falhar(500, -32603, 'Não foi possível concluir a operação MCP.', [], $mensagem->id);
        }
    }

    private function falhar(int $status, int $codigo, string $mensagem, array $cabecalhos = [], int|string|null $identificador = null): Resposta
    {
        return new Resposta($status, \json_encode(Protocolo::erro($identificador, $codigo, $mensagem), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), \array_replace(['Content-Type' => 'application/json; charset=UTF-8'], $cabecalhos));
    }
}
