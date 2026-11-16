<?php

/**
 * Interpreta mensagens MCP JSON-RPC sem conservar sessão de transporte.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\API\Servicos;

use Closure;
use DomainException;
use stdClass;

final class Protocolo
{
    public const array VERSOES = ['2025-11-25', '2025-06-18', '2025-03-26'];

    public function __construct(private Closure $listar, private Closure $executar)
    {
    }

    public static function erro(int|string|null $identificador, int $codigo, string $mensagem): array
    {
        return ['jsonrpc' => '2.0', 'id' => $identificador, 'error' => ['code' => $codigo, 'message' => $mensagem]];
    }

    public function responder(mixed $mensagem, array $usuario): ?array
    {
        if (!$mensagem instanceof stdClass || ($mensagem->jsonrpc ?? null) !== '2.0') {
            return self::erro(null, -32600, 'Mensagem JSON-RPC inválida.');
        }
        $possuiIdentificador = \property_exists($mensagem, 'id');
        $identificador = $mensagem->id ?? null;
        if ($possuiIdentificador && !\is_int($identificador) && !\is_string($identificador)) {
            return self::erro(null, -32600, 'Identificador JSON-RPC inválido.');
        }
        if (!\property_exists($mensagem, 'method')) {
            return $this->receberResposta($mensagem, $possuiIdentificador);
        }
        if (!\is_string($mensagem->method) || $mensagem->method === '' || (\property_exists($mensagem, 'params') && !$mensagem->params instanceof stdClass)) {
            return self::erro($identificador, -32600, 'Método ou parâmetros JSON-RPC inválidos.');
        }
        // Notificações nunca acionam ferramentas, inclusive quando nomeiam uma operação de escrita.
        if (!$possuiIdentificador) {
            return null;
        }
        $parametros = $mensagem->params ?? new stdClass();
        $resultado = match ($mensagem->method) {
            'initialize' => $this->inicializar($parametros),
            'ping' => new stdClass(),
            'tools/list' => \property_exists($parametros, 'cursor') ? null : ['tools' => ($this->listar)()],
            'tools/call' => $this->chamar($parametros, $usuario),
            default => false,
        };
        if ($resultado === false) {
            return self::erro($identificador, -32601, 'Método MCP desconhecido.');
        }
        if ($resultado === null) {
            return self::erro($identificador, -32602, 'Parâmetros MCP inválidos ou ferramenta desconhecida.');
        }
        return ['jsonrpc' => '2.0', 'id' => $identificador, 'result' => $resultado];
    }

    private function receberResposta(stdClass $mensagem, bool $possuiIdentificador): ?array
    {
        $resultado = \property_exists($mensagem, 'result');
        $erro = \property_exists($mensagem, 'error');
        if (!$possuiIdentificador || $resultado === $erro) {
            return self::erro(null, -32600, 'Resposta JSON-RPC inválida.');
        }
        if ($erro && (!$mensagem->error instanceof stdClass || !\is_int($mensagem->error->code ?? null) || !\is_string($mensagem->error->message ?? null))) {
            return self::erro(null, -32600, 'Erro JSON-RPC inválido.');
        }
        return null;
    }

    private function inicializar(stdClass $parametros): ?array
    {
        if (!\is_string($parametros->protocolVersion ?? null) || !($parametros->capabilities ?? null) instanceof stdClass || !($parametros->clientInfo ?? null) instanceof stdClass || !\is_string($parametros->clientInfo->name ?? null) || !\is_string($parametros->clientInfo->version ?? null)) {
            return null;
        }
        return [
            'protocolVersion' => \in_array($parametros->protocolVersion, self::VERSOES, true) ? $parametros->protocolVersion : self::VERSOES[0],
            'capabilities' => ['tools' => ['listChanged' => false]],
            'serverInfo' => ['name' => 'BombQUIZ', 'version' => '1.0.0'],
            'instructions' => 'Gerencie o acervo e consulte as partidas. Confirme com o operador antes de excluir dados definitivamente.',
        ];
    }

    private function chamar(stdClass $parametros, array $usuario): ?array
    {
        if (!\is_string($parametros->name ?? null) || $parametros->name === '' || (\property_exists($parametros, 'arguments') && !$parametros->arguments instanceof stdClass)) {
            return null;
        }
        if (!\in_array($parametros->name, \array_column(($this->listar)(), 'name'), true)) {
            return null;
        }
        $argumentos = \json_decode(\json_encode($parametros->arguments ?? new stdClass(), JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        try {
            $dados = ($this->executar)($parametros->name, $argumentos, $usuario);
            return [
                'content' => [['type' => 'text', 'text' => \json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE)]],
                'structuredContent' => \array_is_list($dados) ? ['dados' => $dados] : $dados,
                'isError' => false,
            ];
        } catch (DomainException $falha) {
            return ['content' => [['type' => 'text', 'text' => $falha->getMessage()]], 'isError' => true];
        }
    }
}
