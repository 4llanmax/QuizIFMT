<?php

/**
 * Confere conexão OAuth, páginas do Painel e ferramentas MCP em banco local descartável.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Integracao\Jogo;

use Aplicacao\API\Provedores\Atendimento;
use Aplicacao\Nucleo\Comuns\Classes\Parametros;
use Aplicacao\Nucleo\Comuns\Classes\RoteadorHTTP;
use Aplicacao\Nucleo\Transportes\Resposta;
use Aplicacao\Painel\Provedores\ConexaoMCP;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Testes\Apoio\BancoIsolado;

final class IntegracaoMCPTeste extends TestCase
{
    private static PDO $banco;
    private static BancoIsolado $apoio;
    private static string $hashInicial;
    private Atendimento $provedor;
    private RoteadorHTTP $api;
    private RoteadorHTTP $painel;
    private array $sessao = [];
    private string $base = 'https://jogo.example.invalid';
    private const int AGORA = 1000000;
    private const string SENHA_INICIAL = 'InicialFicticia123';

    public static function setUpBeforeClass(): void
    {
        self::$apoio = new BancoIsolado(getenv());
        self::$banco = self::$apoio->criar();
        self::$hashInicial = password_hash(self::SENHA_INICIAL, PASSWORD_DEFAULT);
    }

    public static function tearDownAfterClass(): void
    {
        if (isset(self::$banco)) {
            self::$apoio->limpar();
        }
    }

    protected function setUp(): void
    {
        self::$banco->exec('DELETE FROM usuarios_mcp');
        self::$banco->exec('DELETE FROM clientes_mcp');
        self::$banco->exec('DELETE FROM tentativas');
        $consulta = self::$banco->prepare('INSERT INTO usuarios_mcp (email, senha_hash, administrador) VALUES (?, ?, 1)');
        $consulta->execute(['operador@example.invalid', self::$hashInicial]);
        $this->provedor = new Atendimento(self::$banco, $this->parametros());
        $this->api = $this->provedor->construir();
        $this->painel = (new ConexaoMCP($this->provedor->oauth(), $this->base))->construir();
        $this->sessao = [];
    }

    private function parametros(array $alteracoes = []): Parametros
    {
        return new Parametros(__DIR__ . '/Ausente.env', array_replace(self::$apoio->parametros(), ['MCP_URL_BASE' => $this->base, 'MCP_SENHA_INICIAL_HASH' => self::$hashInicial], $alteracoes), Atendimento::parametros());
    }

    private function atender(string $caminho, string $metodo = 'GET', array $campos = [], string $corpo = '', array $cabecalhos = []): Resposta
    {
        $servidor = array_replace(['REQUEST_URI' => $this->base . $caminho, 'REQUEST_METHOD' => $metodo, 'REMOTE_ADDR' => '127.0.0.1'], $cabecalhos);
        $rota = str_starts_with($caminho, '/admin/') ? $this->painel : $this->api;
        return $rota->atender(parse_url($caminho, PHP_URL_PATH), $servidor, $corpo, $campos, $this->sessao, self::AGORA);
    }

    private function json(Resposta $resposta): array
    {
        return json_decode($resposta->corpo, true, 512, JSON_THROW_ON_ERROR);
    }

    private function conectar(): array
    {
        $registro = $this->atender('/oauth/registrar', 'POST', [], json_encode(['client_name' => 'Cliente fictício', 'redirect_uris' => ['https://cliente.example.invalid/retorno'], 'token_endpoint_auth_method' => 'none'], JSON_THROW_ON_ERROR));
        self::assertSame(201, $registro->status);
        $cliente = $this->json($registro)['client_id'];
        $verificador = str_repeat('a', 43);
        $pedido = ['client_id' => $cliente, 'redirect_uri' => 'https://cliente.example.invalid/retorno', 'resource' => $this->base . '/mcp', 'response_type' => 'code', 'code_challenge_method' => 'S256', 'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $verificador, true)), '+/', '-_'), '='), 'state' => 'estado-ficticio'];
        $pagina = $this->atender('/admin/mcp/autorizar?' . http_build_query($pedido));
        self::assertSame(200, $pagina->status);
        self::assertStringContainsString('Cliente fictício', $pagina->corpo);
        self::assertStringNotContainsString(self::SENHA_INICIAL, $pagina->corpo);
        self::assertStringNotContainsString('senhaNova', $pagina->corpo);
        $autorizacao = $this->atender('/admin/mcp/autorizar', 'POST', ['csrf' => $this->sessao['mcpCSRF'], 'acao' => 'autorizar', 'email' => 'operador@example.invalid', 'senha' => self::SENHA_INICIAL], '', ['HTTP_ORIGIN' => $this->base]);
        self::assertSame(303, $autorizacao->status, $autorizacao->corpo);
        $operador = self::$banco->query("SELECT senha_hash, senha_inicial FROM usuarios_mcp WHERE email = 'operador@example.invalid'")->fetch();
        self::assertSame(self::$hashInicial, $operador['senha_hash']);
        self::assertSame(1, (int) $operador['senha_inicial']);
        parse_str(parse_url($autorizacao->cabecalhos['Location'], PHP_URL_QUERY), $retorno);
        self::assertSame('estado-ficticio', $retorno['state']);
        $tokens = $this->atender('/oauth/token', 'POST', ['grant_type' => 'authorization_code', 'client_id' => $cliente, 'redirect_uri' => $pedido['redirect_uri'], 'resource' => $pedido['resource'], 'code' => $retorno['code'], 'code_verifier' => $verificador]);
        self::assertSame(200, $tokens->status, $tokens->corpo);
        return $this->json($tokens) + ['cliente' => $cliente, 'pedido' => $pedido, 'verificador' => $verificador];
    }

    private function chamar(string $token, string $nome, array $argumentos = []): array
    {
        $resposta = $this->atender('/mcp', 'POST', [], json_encode(['jsonrpc' => '2.0', 'id' => 1, 'method' => 'tools/call', 'params' => ['name' => $nome, 'arguments' => (object) $argumentos]], JSON_THROW_ON_ERROR), ['HTTP_AUTHORIZATION' => 'Bearer ' . $token, 'CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json, text/event-stream']);
        self::assertSame(200, $resposta->status, $resposta->corpo);
        return $this->json($resposta)['result'];
    }

    #[Test]
    public function validaEnderecoPublicoECompartilhaOAuth(): void
    {
        self::assertSame($this->provedor->oauth(), $this->provedor->oauth());
        self::assertSame(['MCP_URL_BASE' => 'mcpURLBase', 'MCP_SENHA_INICIAL_HASH' => 'mcpSenhaInicialHash'], Atendimento::parametros());
        foreach (['', 'invalida', 'http://jogo.example.invalid', 'https://usuario@jogo.example.invalid', 'https://usuario:segredo@jogo.example.invalid', 'https://jogo.example.invalid?', 'https://jogo.example.invalid#', 'https://jogo.example.invalid/', 'https://jogo.example.invalid/quiz', 'https://jogo.example.invalid:0'] as $base) {
            try {
                new Atendimento(self::$banco, $this->parametros(['MCP_URL_BASE' => $base]));
                self::fail('A URL base precisa ser recusada: ' . $base);
            } catch (RuntimeException $falha) {
                self::assertStringContainsString('MCP_URL_BASE', $falha->getMessage());
            }
        }
        $provedor = new Atendimento(self::$banco, $this->parametros(['MCP_URL_BASE' => 'https://jogo.example.invalid:8443/quiz', 'URL_PREFIXO' => '/quiz/']));
        $sessao = [];
        $resposta = $provedor->construir()->atender('/.well-known/oauth-protected-resource', ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/quiz/.well-known/oauth-protected-resource'], '', [], $sessao, self::AGORA);
        self::assertSame('https://jogo.example.invalid:8443/quiz/mcp', $this->json($resposta)['resource']);
    }

    #[Test]
    public function descobreRotasSemTokenEExigeAutenticacaoNoMCP(): void
    {
        $descoberta = $this->json($this->atender('/.well-known/oauth-authorization-server'));
        self::assertSame($this->base . '/admin/mcp/autorizar', $descoberta['authorization_endpoint']);
        foreach (['/.well-known/oauth-protected-resource', '/.well-known/oauth-protected-resource/mcp'] as $caminho) {
            self::assertSame($this->base . '/mcp', $this->json($this->atender($caminho))['resource']);
        }
        self::assertSame(401, $this->atender('/mcp', 'POST', [], '{"jsonrpc":"2.0","id":1,"method":"ping"}')->status);
        self::assertSame(400, $this->atender('/oauth/token', 'POST')->status);
        self::assertSame(200, $this->atender('/oauth/revogar', 'POST', ['token' => 'desconhecido', 'client_id' => 'desconhecido'])->status);
    }

    #[Test]
    public function conectaPelaPaginaEConsultaFerramentasReais(): void
    {
        $conexao = $this->conectar();
        $servidor = ['HTTP_AUTHORIZATION' => 'Bearer ' . $conexao['access_token'], 'CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json, text/event-stream'];
        $inicializacao = $this->atender('/mcp', 'POST', [], '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2025-11-25","capabilities":{},"clientInfo":{"name":"Teste","version":"1"}}}', $servidor);
        self::assertSame(200, $inicializacao->status);
        self::assertSame('BombQUIZ', $this->json($inicializacao)['result']['serverInfo']['name']);
        $lista = $this->atender('/mcp', 'POST', [], '{"jsonrpc":"2.0","id":2,"method":"tools/list"}', $servidor);
        $nomes = array_column($this->json($lista)['result']['tools'], 'name');
        self::assertContains('criar_perguntas', $nomes);
        self::assertContains('usuarios_criar', $nomes);
        self::assertCount(19, $nomes);
        self::assertNotContains('senha_trocar', $nomes);
        $consulta = $this->chamar($conexao['access_token'], 'listar_perguntas');
        self::assertFalse($consulta['isError']);
        self::assertStringNotContainsString('senha_hash', json_encode($consulta));
        self::assertFalse($this->chamar($conexao['access_token'], 'consultar_estatisticas')['isError']);
        $usuarios = $this->chamar($conexao['access_token'], 'usuarios_listar');
        self::assertCount(1, $usuarios['structuredContent']['usuarios']);
        $novo = $this->chamar($conexao['access_token'], 'usuarios_criar', ['email' => 'novo@example.invalid']);
        self::assertSame(1, (int) $novo['structuredContent']['senha_inicial']);
        self::assertFalse($this->chamar($conexao['access_token'], 'usuarios_desativar', ['identificador' => (int) $novo['structuredContent']['identificador'], 'confirmar' => true])['isError']);
        $senha = $this->atender('/mcp', 'POST', [], '{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"senha_trocar","arguments":{"atual":"InicialFicticia123","nova":"OutraSenhaFicticia123"}}}', $servidor);
        self::assertSame(-32602, $this->json($senha)['error']['code']);
        self::assertSame(self::$hashInicial, self::$banco->query("SELECT senha_hash FROM usuarios_mcp WHERE email = 'operador@example.invalid'")->fetchColumn());
        self::assertSame(200, $this->atender('/mcp', 'POST', [], '{"jsonrpc":"2.0","id":1,"method":"ping"}', $servidor)->status);
    }

    #[Test]
    public function restringeGestaoUsuariosParaContaSemAdministracao(): void
    {
        $conexao = $this->conectar();
        $novo = $this->chamar($conexao['access_token'], 'usuarios_criar', ['email' => 'consulta@example.invalid']);
        $usuario = (int) $novo['structuredContent']['identificador'];
        self::assertSame($usuario, $this->provedor->oauth()->entrar('consulta@example.invalid', self::SENHA_INICIAL, '127.0.0.1', self::AGORA)['identificador']);
        $codigo = $this->provedor->oauth()->autorizar($conexao['pedido'], $usuario, self::AGORA);
        $token = $this->provedor->oauth()->trocar(['grant_type' => 'authorization_code', 'client_id' => $conexao['cliente'], 'redirect_uri' => $conexao['pedido']['redirect_uri'], 'resource' => $this->base . '/mcp', 'code' => $codigo, 'code_verifier' => $conexao['verificador']], self::AGORA)['access_token'];
        self::assertTrue($this->chamar($token, 'usuarios_listar')['isError']);
        self::assertFalse($this->chamar($token, 'listar_categorias')['isError']);
    }
}
