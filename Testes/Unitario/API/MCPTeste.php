<?php

/**
 * Confere transporte MCP, autenticação obrigatória e ausência de efeitos de mensagens inválidas.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\API;

use Aplicacao\API\Controles\MCP;
use Aplicacao\API\Servicos\Protocolo;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MCPTeste extends TestCase
{
    private array $execucoes = [];

    private function controle(?\Closure $autenticar = null, ?\Closure $listar = null, ?\Closure $executar = null): MCP
    {
        return new MCP(
            $autenticar ?? static function (string $token, int $agora): array {
                if ($token !== 'token-valido' || $agora !== 1000) {
                    throw new DomainException('Token inválido.');
                }
                return ['identificador' => 7];
            },
            $listar ?? static fn (): array => [['name' => 'consultar', 'inputSchema' => ['type' => 'object']]],
            $executar ?? function (string $nome, array $argumentos, array $usuario): array {
                $this->execucoes[] = [$nome, $argumentos, $usuario];
                return ['perguntas' => [['enunciado' => 'Qual é a pergunta?']]];
            },
            'https://jogo.example.invalid/quiz'
        );
    }

    private function servidor(array $alteracoes = []): array
    {
        return array_replace(['REQUEST_METHOD' => 'POST', 'HTTP_AUTHORIZATION' => 'Bearer token-valido', 'CONTENT_TYPE' => 'application/json; charset=utf-8', 'HTTP_ACCEPT' => 'application/json, text/event-stream'], $alteracoes);
    }

    private function mensagem(string $metodo, mixed $parametros = null): string
    {
        $dados = ['jsonrpc' => '2.0', 'id' => 1, 'method' => $metodo];
        if ($parametros !== null) {
            $dados['params'] = $parametros;
        }
        return json_encode($dados, JSON_THROW_ON_ERROR);
    }

    #[Test]
    public function iniciaNegociaVersoesEPreservaIdentificador(): void
    {
        foreach (['2025-11-25', '2025-06-18', '2025-03-26', 'desconhecida'] as $versao) {
            $resposta = $this->controle()->responder($this->servidor(), $this->mensagem('initialize', ['protocolVersion' => $versao, 'capabilities' => (object) [], 'clientInfo' => ['name' => 'Cliente de teste', 'version' => '1']]), 1000);
            self::assertSame(200, $resposta->status);
            $dados = json_decode($resposta->corpo, true, 512, JSON_THROW_ON_ERROR);
            self::assertSame($versao === 'desconhecida' ? '2025-11-25' : $versao, $dados['result']['protocolVersion']);
            self::assertSame(false, $dados['result']['capabilities']['tools']['listChanged']);
            self::assertSame(1, $dados['id']);
            self::assertSame('application/json; charset=UTF-8', $resposta->cabecalhos['Content-Type']);
        }
        $resposta = $this->controle()->responder($this->servidor(), '{"jsonrpc":"2.0","id":"abc","method":"ping"}', 1000);
        self::assertSame('{"jsonrpc":"2.0","id":"abc","result":{}}', $resposta->corpo);
    }

    #[Test]
    public function listaFerramentasEExecutaComIdentidadeSemSessao(): void
    {
        $controle = $this->controle();
        $lista = $controle->responder($this->servidor(['HTTP_ORIGIN' => 'https://jogo.example.invalid', 'HTTP_MCP_PROTOCOL_VERSION' => '2025-06-18']), $this->mensagem('tools/list'), 1000);
        self::assertSame('consultar', json_decode($lista->corpo, true)['result']['tools'][0]['name']);
        $resultado = $controle->responder($this->servidor(), $this->mensagem('tools/call', ['name' => 'consultar', 'arguments' => ['pagina' => 2]]), 1000);
        $dados = json_decode($resultado->corpo, true);
        self::assertSame(false, $dados['result']['isError']);
        self::assertSame('Qual é a pergunta?', $dados['result']['structuredContent']['perguntas'][0]['enunciado']);
        self::assertSame(['consultar', ['pagina' => 2], ['identificador' => 7]], $this->execucoes[0]);
        $controle->responder($this->servidor(), $this->mensagem('tools/call', ['name' => 'consultar']), 1000);
        self::assertSame([], $this->execucoes[1][1]);
    }

    #[Test]
    public function aceitaNotificacoesERespostasSemExecutarFerramentas(): void
    {
        foreach (['{"jsonrpc":"2.0","method":"notifications/initialized"}', '{"jsonrpc":"2.0","method":"notifications/cancelled","params":{"requestId":1}}', '{"jsonrpc":"2.0","method":"tools/call","params":{"name":"consultar"}}', '{"jsonrpc":"2.0","id":1,"result":{}}', '{"jsonrpc":"2.0","id":1,"error":{"code":-1,"message":"Falha"}}'] as $mensagem) {
            $resposta = $this->controle()->responder($this->servidor(), $mensagem, 1000);
            self::assertSame(202, $resposta->status);
            self::assertSame('', $resposta->corpo);
        }
        self::assertSame([], $this->execucoes);
    }

    #[Test]
    public function exigeBearerEmTodasOperacoesEIndicaDescoberta(): void
    {
        foreach (['', 'Basic segredo', 'Bearer', 'Bearer token-invalido', 'Bearer token-valido extra'] as $autorizacao) {
            $resposta = $this->controle()->responder($this->servidor(['HTTP_AUTHORIZATION' => $autorizacao]), $this->mensagem('ping'), 1000);
            self::assertSame(401, $resposta->status);
            self::assertStringContainsString('resource_metadata="https://jogo.example.invalid/quiz/.well-known/oauth-protected-resource"', $resposta->cabecalhos['WWW-Authenticate']);
        }
        $resposta = $this->controle()->responder(['REQUEST_METHOD' => 'GET'], '', 1000);
        self::assertSame(401, $resposta->status);
        self::assertSame([], $this->execucoes);
    }

    #[Test]
    public function recusaOrigemExternaSemConsultarToken(): void
    {
        $controle = $this->controle(static function (): array {
            self::fail('A origem inválida não pode consultar a identidade.');
        });
        foreach (['null', 'https://externo.example.invalid', 'https://jogo.example.invalid:444', 'https://jogo.example.invalid/', 'https://jogo.example.invalid.evil.test'] as $origem) {
            self::assertSame(403, $controle->responder($this->servidor(['HTTP_ORIGIN' => $origem]), $this->mensagem('ping'), 1000)->status);
        }
    }

    #[Test]
    public function reconhecePortaExplicitaEAutorizacaoEncaminhadaPeloApache(): void
    {
        $controle = new MCP(static fn (): array => ['identificador' => 1], static fn (): array => [], static fn (): array => [], 'https://jogo.example.invalid:8443/quiz/');
        $servidor = $this->servidor(['HTTP_ORIGIN' => 'https://jogo.example.invalid:8443', 'REDIRECT_HTTP_AUTHORIZATION' => 'Bearer token-valido']);
        unset($servidor['HTTP_AUTHORIZATION']);
        self::assertSame(200, $controle->responder($servidor, $this->mensagem('ping'), 1000)->status);
        self::assertSame(403, $controle->responder($this->servidor(['HTTP_ORIGIN' => 'https://jogo.example.invalid']), $this->mensagem('ping'), 1000)->status);
    }

    #[Test]
    public function recusaMetodosFormatosTamanhosVersoesENegociacao(): void
    {
        foreach (['GET', 'DELETE', 'PUT', 'OPTIONS'] as $metodo) {
            $resposta = $this->controle()->responder($this->servidor(['REQUEST_METHOD' => $metodo]), '', 1000);
            self::assertSame(405, $resposta->status);
            self::assertSame('POST', $resposta->cabecalhos['Allow']);
        }
        foreach (['text/plain', '', 'application/json-patch+json'] as $tipo) {
            self::assertSame(415, $this->controle()->responder($this->servidor(['CONTENT_TYPE' => $tipo]), '{}', 1000)->status);
        }
        foreach (['', '*/*', 'application/json', 'text/event-stream', 'application/json;q=0,text/event-stream', 'application/json,text/event-stream;q=0'] as $aceite) {
            self::assertSame(406, $this->controle()->responder($this->servidor(['HTTP_ACCEPT' => $aceite]), '{}', 1000)->status);
        }
        self::assertSame(200, $this->controle()->responder($this->servidor(['HTTP_ACCEPT' => 'text/event-stream;q=0.5, application/json;q=1']), $this->mensagem('ping'), 1000)->status);
        self::assertSame(413, $this->controle()->responder($this->servidor(), str_repeat('a', 1048577), 1000)->status);
        self::assertSame(400, $this->controle()->responder($this->servidor(['HTTP_MCP_PROTOCOL_VERSION' => '2024-11-05']), $this->mensagem('ping'), 1000)->status);
        self::assertSame(400, $this->controle()->responder($this->servidor(), '{', 1000)->status);
    }

    #[Test]
    public function validaEnvelopeSemExecutarMensagensInvalidas(): void
    {
        foreach (['null', '[]', '[{"jsonrpc":"2.0","id":1,"method":"ping"}]', '{}', '{"jsonrpc":"1.0","id":1,"method":"ping"}', '{"jsonrpc":"2.0","id":null,"method":"ping"}', '{"jsonrpc":"2.0","id":true,"method":"ping"}', '{"jsonrpc":"2.0","id":1.5,"method":"ping"}', '{"jsonrpc":"2.0","id":1,"method":1}', '{"jsonrpc":"2.0","id":1,"method":""}', '{"jsonrpc":"2.0","id":1,"method":"ping","params":[]}', '{"jsonrpc":"2.0","id":1,"method":"ping","params":null}', '{"jsonrpc":"2.0","id":1}', '{"jsonrpc":"2.0","id":1,"result":{},"error":{}}', '{"jsonrpc":"2.0","id":1,"error":{}}', '{"jsonrpc":"2.0","id":1,"error":{"code":"1","message":"x"}}', '{"jsonrpc":"2.0","id":1,"error":{"code":1,"message":5}}'] as $mensagem) {
            $resposta = $this->controle()->responder($this->servidor(), $mensagem, 1000);
            self::assertSame(400, $resposta->status, $mensagem);
            self::assertSame(-32600, json_decode($resposta->corpo, true)['error']['code']);
        }
        self::assertSame([], $this->execucoes);
    }

    #[Test]
    public function diferenciaMetodoAusenteParametrosInvalidosEFalhasDeFerramenta(): void
    {
        $resposta = $this->controle()->responder($this->servidor(), $this->mensagem('inexistente'), 1000);
        self::assertSame(-32601, json_decode($resposta->corpo, true)['error']['code']);
        foreach ([['initialize', null], ['initialize', ['protocolVersion' => 1]], ['initialize', ['protocolVersion' => '2025-11-25', 'capabilities' => [], 'clientInfo' => ['name' => 'Cliente', 'version' => '1']]], ['initialize', ['protocolVersion' => '2025-11-25', 'capabilities' => (object) [], 'clientInfo' => []]], ['initialize', ['protocolVersion' => '2025-11-25', 'capabilities' => (object) [], 'clientInfo' => ['name' => 1, 'version' => '1']]], ['initialize', ['protocolVersion' => '2025-11-25', 'capabilities' => (object) [], 'clientInfo' => ['name' => 'Cliente', 'version' => 1]]], ['tools/list', ['cursor' => 'pagina-inexistente']], ['tools/call', null], ['tools/call', ['name' => 4]], ['tools/call', ['name' => '']], ['tools/call', ['name' => 'consultar', 'arguments' => []]], ['tools/call', ['name' => 'inexistente']]] as [$metodo, $parametros]) {
            $resposta = $this->controle()->responder($this->servidor(), $this->mensagem($metodo, $parametros), 1000);
            self::assertSame(-32602, json_decode($resposta->corpo, true)['error']['code'], $resposta->corpo);
        }
        $controle = $this->controle(executar: static function (): array {
            throw new DomainException('A pergunta não existe.');
        });
        $resposta = $controle->responder($this->servidor(), $this->mensagem('tools/call', ['name' => 'consultar']), 1000);
        $dados = json_decode($resposta->corpo, true)['result'];
        self::assertSame(true, $dados['isError']);
        self::assertSame('A pergunta não existe.', $dados['content'][0]['text']);
    }

    #[Test]
    public function protegeDetalhesDeFalhasInternasEAceitaListaNoResultado(): void
    {
        foreach (['listar', 'executar', 'autenticar'] as $origem) {
            $falhar = static function (): array {
                throw new RuntimeException('Detalhe privado que não pode sair.');
            };
            $controle = $this->controle(...[$origem => $falhar]);
            $metodo = $origem === 'listar' ? 'tools/list' : 'tools/call';
            $resposta = $controle->responder($this->servidor(), $this->mensagem($metodo, ['name' => 'consultar']), 1000);
            self::assertSame(500, $resposta->status);
            self::assertStringNotContainsString('Detalhe privado', $resposta->corpo);
            self::assertSame($origem === 'autenticar' ? null : 1, json_decode($resposta->corpo, true)['id']);
        }
        $controle = $this->controle(executar: static fn (): array => [1, 2]);
        $resposta = $controle->responder($this->servidor(), $this->mensagem('tools/call', ['name' => 'consultar']), 1000);
        self::assertSame(['dados' => [1, 2]], json_decode($resposta->corpo, true)['result']['structuredContent']);
        $protocolo = new Protocolo(static fn (): array => [], static fn (): array => []);
        self::assertSame(['tools' => []], $protocolo->responder(json_decode($this->mensagem('tools/list')), [])['result']);
    }
}
