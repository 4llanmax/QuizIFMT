<?php

/**
 * Confere autorização MCP, identidade própria e consumo único de credenciais.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Integracao\Jogo;

use Aplicacao\Nucleo\Servicos\OAuth;
use Aplicacao\Nucleo\Transportes\Resposta;
use DomainException;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OAuthTeste extends TestCase
{
    private static \Testes\Apoio\BancoIsolado $apoio;
    private static PDO $banco;
    private OAuth $oauth;
    private int $usuario;
    private string $senha = 'SenhaFicticia!234';
    private string $verificador;
    private array $pedido;

    public static function setUpBeforeClass(): void
    {
        self::$apoio = new \Testes\Apoio\BancoIsolado(getenv());
        self::$banco = self::$apoio->criar();
    }

    public static function tearDownAfterClass(): void
    {
        self::$apoio->limpar();
    }

    protected function setUp(): void
    {
        foreach (['tokens_mcp', 'codigos_mcp', 'clientes_mcp', 'usuarios_mcp', 'tentativas'] as $tabela) {
            self::$banco->exec('DELETE FROM ' . $tabela);
        }
        $consulta = self::$banco->prepare('INSERT INTO usuarios_mcp (email, senha_hash, administrador, senha_inicial) VALUES (?, ?, 1, 0)');
        $consulta->execute(['operador@example.invalid', password_hash($this->senha, PASSWORD_DEFAULT)]);
        $this->usuario = (int) self::$banco->lastInsertId();
        $this->oauth = new OAuth(self::$banco, 'https://quiz.example.invalid', password_hash('InicialFicticia!234', PASSWORD_DEFAULT));
        $cliente = $this->oauth->registrarCliente(['client_name' => 'Cliente fictício', 'redirect_uris' => ['https://cliente.example.invalid/callback']]);
        $this->verificador = str_repeat('v', 64);
        $this->pedido = ['response_type' => 'code', 'client_id' => $cliente['client_id'], 'redirect_uri' => $cliente['redirect_uris'][0],
            'state' => 'estado-preservado', 'code_challenge_method' => 'S256', 'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $this->verificador, true)), '+/', '-_'), '='),
            'resource' => 'https://quiz.example.invalid/mcp', 'scope' => 'mcp:gestao'];
    }

    #[Test]
    public function autorizaTrocaCodigoRenovaRevogaEImpedeRepeticao(): void
    {
        $pedido = $this->oauth->validarPedido($this->pedido);
        $usuario = $this->oauth->entrar('operador@example.invalid', $this->senha, '127.0.0.1', 1000);
        $codigo = $this->oauth->autorizar($pedido, $usuario['identificador'], 1000);
        $campos = ['grant_type' => 'authorization_code', 'code' => $codigo, 'code_verifier' => $this->verificador,
            'client_id' => $this->pedido['client_id'], 'redirect_uri' => $this->pedido['redirect_uri'], 'resource' => $this->pedido['resource']];
        $tokens = $this->oauth->trocar($campos, 1001);
        self::assertSame('Bearer', $tokens['token_type']);
        self::assertSame($this->usuario, $this->oauth->autenticarToken($tokens['access_token'], 1002)['identificador']);
        self::assertArrayNotHasKey('senha_hash', $this->oauth->autenticarToken($tokens['access_token'], 1002));
        $this->recusar(fn () => $this->oauth->trocar($campos, 1002));
        $renovados = $this->oauth->trocar(['grant_type' => 'refresh_token', 'refresh_token' => $tokens['refresh_token'], 'client_id' => $this->pedido['client_id'], 'resource' => $this->pedido['resource']], 1003);
        $this->recusar(fn () => $this->oauth->autenticarToken($tokens['access_token'], 1004));
        $this->oauth->revogar($renovados['refresh_token'], $this->pedido['client_id']);
        $this->recusar(fn () => $this->oauth->autenticarToken($renovados['access_token'], 1005));
    }

    #[Test]
    public function rejeitaRedirecionamentoPkceRecursoESenhaInvalidos(): void
    {
        foreach ([['redirect_uri' => 'https://intruso.example.invalid'], ['code_challenge_method' => 'plain'], ['resource' => 'https://outro.example.invalid/mcp'], ['response_type' => 'token'], ['scope' => 'tudo']] as $alteracao) {
            $this->recusar(fn () => $this->oauth->validarPedido(array_replace($this->pedido, $alteracao)));
        }
        $this->recusar(fn () => $this->oauth->registrarCliente(['redirect_uris' => ['http://externo.example.invalid/callback']]));
        $this->recusar(fn () => $this->oauth->entrar('ausente@example.invalid', $this->senha, '127.0.0.1', 1000));
        $this->recusar(fn () => $this->oauth->entrar('operador@example.invalid', 'incorreta', '127.0.0.1', 1000));
    }

    #[Test]
    public function administraUsuariosSemExporHashEPermiteTrocaInterna(): void
    {
        $novo = $this->oauth->criarUsuario('novo@example.invalid', $this->usuario);
        self::assertSame(1, (int) $novo['senha_inicial']);
        self::assertArrayNotHasKey('senha_hash', $novo);
        self::assertCount(2, $this->oauth->listarUsuarios());
        $this->recusar(fn () => $this->oauth->criarUsuario('terceiro@example.invalid', $novo['identificador']));
        $this->recusar(fn () => $this->oauth->criarUsuario('novo@example.invalid', $this->usuario));
        $this->recusar(fn () => $this->oauth->trocarSenha($novo['identificador'], 'incorreta', 'NovaFicticia!234'));
        $this->oauth->trocarSenha($novo['identificador'], 'InicialFicticia!234', 'NovaFicticia!234');
        $autenticado = $this->oauth->entrar('novo@example.invalid', 'NovaFicticia!234', '127.0.0.1', 1000);
        self::assertSame(0, (int) $autenticado['senha_inicial']);
        $this->oauth->desativarUsuario($novo['identificador'], $this->usuario);
        $this->recusar(fn () => $this->oauth->entrar('novo@example.invalid', 'NovaFicticia!234', '127.0.0.1', 1000));
        $this->recusar(fn () => $this->oauth->desativarUsuario($this->usuario, $this->usuario));
    }

    #[Test]
    public function descobreRegistraERecusaJsonInvalido(): void
    {
        $controle = new \Aplicacao\API\Controles\Autorizacao($this->oauth, 'https://quiz.example.invalid');
        $sessao = [];
        $servidor = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/.well-known/oauth-authorization-server', 'REMOTE_ADDR' => '127.0.0.1'];
        $resposta = $controle->responder($servidor, '', [], $sessao, 1000);
        self::assertSame(200, $resposta->status);
        self::assertSame(['S256'], json_decode($resposta->corpo, true)['code_challenge_methods_supported']);
        $servidor['REQUEST_URI'] = '/.well-known/oauth-protected-resource';
        self::assertSame('https://quiz.example.invalid/mcp', json_decode($controle->responder($servidor, '', [], $sessao, 1000)->corpo, true)['resource']);
        $servidor['REQUEST_METHOD'] = 'POST';
        $servidor['REQUEST_URI'] = '/oauth/registrar';
        self::assertSame(400, $controle->responder($servidor, '{', [], $sessao, 1000)->status);
        self::assertSame(201, $controle->responder($servidor, json_encode(['redirect_uris' => ['http://127.0.0.1:3456/callback']]), [], $sessao, 1000)->status);
    }

    #[Test]
    public function paginaExigeCsrfConsentimentoEEntregaCodigo(): void
    {
        $controle = new \Aplicacao\Painel\Controles\ConexaoMCP($this->oauth, 'https://quiz.example.invalid');
        $sessao = [];
        $servidor = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/admin/mcp/autorizar?' . http_build_query($this->pedido), 'REMOTE_ADDR' => '127.0.0.1'];
        self::assertSame(200, $controle->responder($servidor, '', [], $sessao, 1000)->status);
        $servidor['REQUEST_METHOD'] = 'POST';
        $campos = ['csrf' => 'incorreto', 'email' => 'operador@example.invalid', 'senha' => $this->senha, 'acao' => 'autorizar'];
        self::assertSame(403, $controle->responder($servidor, '', $campos, $sessao, 1001)->status);
        $campos['csrf'] = $sessao['mcpCSRF'];
        $resposta = $controle->responder($servidor, '', $campos, $sessao, 1002);
        self::assertSame(303, $resposta->status);
        self::assertStringStartsWith($this->pedido['redirect_uri'] . '?code=', $resposta->cabecalhos['Location']);
        self::assertStringContainsString('state=estado-preservado', $resposta->cabecalhos['Location']);
        self::assertTrue($sessao['renovar']);
    }

    #[Test]
    public function validaMetadadosLimitesETiposSemAceitarRetornosInseguros(): void
    {
        foreach (
            [[], ['redirect_uris' => []], ['redirect_uris' => 'texto'], ['redirect_uris' => ['chave' => 'https://cliente.example.invalid']],
            ['redirect_uris' => array_fill(0, 11, 'https://cliente.example.invalid')], ['client_name' => ''], ['client_name' => str_repeat('n', 81)], ['client_name' => 1], ['token_endpoint_auth_method' => 'client_secret_post']] as $alteracao
        ) {
            $this->recusar(fn () => $this->oauth->registrarCliente(array_replace(['redirect_uris' => ['https://cliente.example.invalid']], $alteracao === [] ? ['redirect_uris' => null] : $alteracao)));
        }
        foreach (['ftp://cliente.example.invalid', 'https://cliente.example.invalid/callback#fragmento', 'https://nome:senha@cliente.example.invalid/callback', 'texto', str_repeat('x', 2049), 1] as $retorno) {
            $this->recusar(fn () => $this->oauth->registrarCliente(['redirect_uris' => [$retorno]]));
        }
        foreach ([['client_id' => null], ['client_id' => 'ausente'], ['code_challenge' => 'curto'], ['scope' => []], ['state' => []], ['state' => str_repeat('e', 2049)]] as $alteracao) {
            $this->recusar(fn () => $this->oauth->validarPedido(array_replace($this->pedido, $alteracao)));
        }
        $pedido = $this->pedido;
        unset($pedido['scope'], $pedido['state']);
        self::assertSame('mcp:gestao', $this->oauth->validarPedido($pedido)['scope']);
        self::assertSame('', $this->oauth->validarPedido($pedido)['state']);
    }

    #[Test]
    public function vinculaCodigoClienteRecursoPkcePrazoEUsuario(): void
    {
        $codigo = $this->oauth->autorizar($this->pedido, $this->usuario, 1000);
        $campos = ['grant_type' => 'authorization_code', 'code' => $codigo, 'client_id' => $this->pedido['client_id'], 'redirect_uri' => $this->pedido['redirect_uri'], 'resource' => $this->pedido['resource'], 'code_verifier' => $this->verificador];
        foreach ([['code' => 'ausente'], ['code_verifier' => 'curto'], ['code_verifier' => str_repeat('x', 64)], ['client_id' => 'outro'], ['redirect_uri' => 'https://outro.example.invalid'], ['resource' => 'https://outro.example.invalid'], ['client_id' => null], ['grant_type' => 'password']] as $alteracao) {
            $this->recusar(fn () => $this->oauth->trocar(array_replace($campos, $alteracao), 1001));
        }
        $this->recusar(fn () => $this->oauth->trocar($campos, 301000));
        $campos['code'] = $this->oauth->autorizar($this->pedido, $this->usuario, 1000);
        $this->recusar(fn () => $this->oauth->autorizar($this->pedido, 999999, 1001));
        self::$banco->exec('UPDATE usuarios_mcp SET ativo = 0, senha_inicial = 0');
        $this->recusar(fn () => $this->oauth->autorizar($this->pedido, $this->usuario, 1001));
        $this->recusar(fn () => $this->oauth->trocar($campos, 1001));
    }

    #[Test]
    public function vinculaRenovacaoERecusaExpiradosInativosERecursosDiferentes(): void
    {
        $codigo = $this->oauth->autorizar($this->pedido, $this->usuario, 1000);
        $tokens = $this->oauth->trocar(['grant_type' => 'authorization_code', 'code' => $codigo, 'client_id' => $this->pedido['client_id'], 'redirect_uri' => $this->pedido['redirect_uri'], 'resource' => $this->pedido['resource'], 'code_verifier' => $this->verificador], 1001);
        $campos = ['grant_type' => 'refresh_token', 'refresh_token' => $tokens['refresh_token'], 'client_id' => $this->pedido['client_id'], 'resource' => $this->pedido['resource']];
        $this->recusar(fn () => $this->oauth->trocar(array_replace($campos, ['refresh_token' => 'ausente']), 1002));
        $this->recusar(fn () => $this->oauth->trocar(array_replace($campos, ['client_id' => 'outro']), 1002));
        $this->recusar(fn () => $this->oauth->trocar($campos, 2592001001));
        $this->recusar(fn () => $this->oauth->autenticarToken('ausente', 1002));
        $this->recusar(fn () => $this->oauth->autenticarToken($tokens['access_token'], 3601001));
        self::$banco->exec("UPDATE tokens_mcp SET recurso = 'https://outro.example.invalid/mcp'");
        $this->recusar(fn () => $this->oauth->trocar($campos, 1002));
        $this->recusar(fn () => $this->oauth->autenticarToken($tokens['access_token'], 1002));
        self::$banco->exec("UPDATE tokens_mcp SET recurso = 'https://quiz.example.invalid/mcp'");
        self::$banco->exec('UPDATE usuarios_mcp SET senha_inicial = 1');
        self::assertSame($this->usuario, $this->oauth->autenticarToken($tokens['access_token'], 1002)['identificador']);
        self::$banco->exec('UPDATE usuarios_mcp SET ativo = 0, senha_inicial = 0');
        $this->recusar(fn () => $this->oauth->autenticarToken($tokens['access_token'], 1002));
        $this->oauth->revogar($tokens['access_token'], 'outro');
        self::assertSame(1, (int) self::$banco->query('SELECT COUNT(*) FROM tokens_mcp')->fetchColumn());
        $this->oauth->revogar($tokens['access_token'], $this->pedido['client_id']);
        self::assertSame(0, (int) self::$banco->query('SELECT COUNT(*) FROM tokens_mcp')->fetchColumn());
    }

    #[Test]
    public function protegeGestaoIdentidadesESenhas(): void
    {
        foreach (['invalido', str_repeat('a', 250) . '@example.invalid'] as $email) {
            $this->recusar(fn () => $this->oauth->criarUsuario($email, $this->usuario));
        }
        $this->recusar(fn () => (new OAuth(self::$banco, 'https://quiz.example.invalid'))->criarUsuario('novo@example.invalid', $this->usuario));
        $this->recusar(fn () => $this->oauth->criarUsuario('novo@example.invalid', 999999));
        foreach (['curta', str_repeat('x', 73), "senha\0invalida", $this->senha, 'InicialFicticia!234'] as $nova) {
            $this->recusar(fn () => $this->oauth->trocarSenha($this->usuario, $this->senha, $nova));
        }
        $this->recusar(fn () => $this->oauth->trocarSenha(999999, $this->senha, 'NovaFicticia!234'));
        $this->recusar(fn () => $this->oauth->desativarUsuario(999999, $this->usuario));
        self::$banco->exec('UPDATE usuarios_mcp SET ativo = 0');
        $this->recusar(fn () => $this->oauth->criarUsuario('novo@example.invalid', $this->usuario));
        $this->recusar(fn () => $this->oauth->trocarSenha($this->usuario, $this->senha, 'NovaFicticia!234'));
    }

    #[Test]
    public function atendeRecusasMetodosRevogacaoELimiteRegistro(): void
    {
        $controle = new \Aplicacao\API\Controles\Autorizacao($this->oauth, 'https://quiz.example.invalid');
        $sessao = [];
        $servidor = ['REQUEST_METHOD' => 'OPTIONS', 'REQUEST_URI' => '/oauth/token', 'REMOTE_ADDR' => '127.0.0.1'];
        self::assertSame(204, $controle->responder($servidor, '', [], $sessao, 1000)->status);
        $servidor['REQUEST_METHOD'] = 'GET';
        self::assertSame(405, $controle->responder($servidor, '', [], $sessao, 1000)->status);
        $servidor['REQUEST_METHOD'] = 'POST';
        self::assertSame(400, $controle->responder($servidor, '', [], $sessao, 1000)->status);
        $servidor['REQUEST_URI'] = '/oauth/revogar';
        self::assertSame(400, $controle->responder($servidor, '', [], $sessao, 1000)->status);
        self::assertSame(200, $controle->responder($servidor, '', ['token' => 'ausente', 'client_id' => 'ausente'], $sessao, 1000)->status);
        $servidor['REQUEST_URI'] = '/oauth/ausente';
        self::assertSame(404, $controle->responder($servidor, '', [], $sessao, 1000)->status);
        $servidor['REQUEST_URI'] = '/oauth/registrar';
        self::assertSame(400, $controle->responder($servidor, '[]', [], $sessao, 1000)->status);
        for ($tentativa = 0; $tentativa < 10; $tentativa++) {
            $resposta = $controle->responder($servidor, '{}', [], $sessao, 1000);
        }
        self::assertSame(429, $resposta->status);
    }

    #[Test]
    public function paginaTrataCancelamentoOrigemExpiracaoESenhaDefinida(): void
    {
        $controle = new \Aplicacao\Painel\Controles\ConexaoMCP($this->oauth, 'https://quiz.example.invalid');
        $sessao = [];
        $servidor = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/admin/mcp/autorizar', 'REMOTE_ADDR' => '127.0.0.1'];
        self::assertSame(422, $controle->responder($servidor, '', [], $sessao, 1000)->status);
        $servidor['REQUEST_METHOD'] = 'PUT';
        self::assertSame(405, $controle->responder($servidor, '', [], $sessao, 1000)->status);
        $servidor['REQUEST_METHOD'] = 'POST';
        $campos = ['csrf' => $sessao['mcpCSRF'], 'acao' => 'autorizar', 'email' => 'operador@example.invalid', 'senha' => $this->senha];
        self::assertSame(400, $controle->responder($servidor, '', $campos, $sessao, 1000)->status);
        $servidor['REQUEST_METHOD'] = 'GET';
        $pedido = array_replace($this->pedido, ['redirect_uri' => $this->pedido['redirect_uri']]);
        $servidor['REQUEST_URI'] .= '?' . http_build_query($pedido);
        $controle->responder($servidor, '', [], $sessao, 1000);
        $servidor['REQUEST_METHOD'] = 'POST';
        $servidor['HTTP_ORIGIN'] = 'https://intruso.example.invalid';
        self::assertSame(403, $controle->responder($servidor, '', $campos, $sessao, 1001)->status);
        unset($servidor['HTTP_ORIGIN']);
        self::assertSame(400, $controle->responder($servidor, '', $campos, $sessao, 601000)->status);
        self::assertSame(422, $controle->responder($servidor, '', array_replace($campos, ['acao' => 'invalida']), $sessao, 1001)->status);
        self::$banco->exec('UPDATE usuarios_mcp SET senha_inicial = 1');
        $resposta = $controle->responder($servidor, '', $campos, $sessao, 1001);
        self::assertSame(303, $resposta->status);
        $servidor['REQUEST_METHOD'] = 'GET';
        $controle->responder($servidor, '', [], $sessao, 2000);
        $servidor['REQUEST_METHOD'] = 'POST';
        $resposta = $controle->responder($servidor, '', ['csrf' => $sessao['mcpCSRF'], 'acao' => 'cancelar'], $sessao, 2001);
        self::assertSame(303, $resposta->status);
        self::assertStringContainsString('error=access_denied', $resposta->cabecalhos['Location']);
    }

    #[Test]
    public function paginaDescartaPedidoAnteriorQuandoNovaSolicitacaoInvalida(): void
    {
        $controle = new \Aplicacao\Painel\Controles\ConexaoMCP($this->oauth, 'https://quiz.example.invalid');
        $sessao = [];
        $servidor = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/admin/mcp/autorizar?' . http_build_query($this->pedido)];
        self::assertSame(200, $controle->responder($servidor, '', [], $sessao, 1000)->status);
        $servidor['REQUEST_URI'] = '/admin/mcp/autorizar?client_id=invalido';
        self::assertSame(422, $controle->responder($servidor, '', [], $sessao, 1001)->status);
        self::assertArrayNotHasKey('mcpPedido', $sessao);
    }

    #[Test]
    public function reutilizarRenovacaoRevogaTambemCredenciaisDescendentes(): void
    {
        $codigo = $this->oauth->autorizar($this->pedido, $this->usuario, 1000);
        $tokens = $this->oauth->trocar(['grant_type' => 'authorization_code', 'code' => $codigo, 'client_id' => $this->pedido['client_id'], 'redirect_uri' => $this->pedido['redirect_uri'], 'resource' => $this->pedido['resource'], 'code_verifier' => $this->verificador], 1001);
        $campos = ['grant_type' => 'refresh_token', 'refresh_token' => $tokens['refresh_token'], 'client_id' => $this->pedido['client_id'], 'resource' => $this->pedido['resource']];
        $renovados = $this->oauth->trocar($campos, 1002);
        $this->recusar(fn () => $this->oauth->trocar($campos, 1003));
        $this->recusar(fn () => $this->oauth->autenticarToken($renovados['access_token'], 1004));
    }

    #[Test]
    public function preservaFalhaDePersistenciaDiferenteDeDuplicidade(): void
    {
        self::$banco->exec("CREATE TRIGGER usuarios_mcp_recusar BEFORE INSERT ON usuarios_mcp FOR EACH ROW SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Falha controlada'");
        try {
            $this->oauth->criarUsuario('novo@example.invalid', $this->usuario);
            self::fail('A indisponibilidade do banco deve permanecer visível.');
        } catch (\PDOException $falha) {
            self::assertSame('45000', $falha->getCode());
        } finally {
            self::$banco->exec('DROP TRIGGER usuarios_mcp_recusar');
        }
    }

    #[Test]
    public function paginaConfereOrigemTiposELimiteDeTentativas(): void
    {
        $controle = new \Aplicacao\Painel\Controles\ConexaoMCP($this->oauth, 'https://quiz.example.invalid:443');
        $sessao = [];
        $servidor = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/admin/mcp/autorizar?' . http_build_query($this->pedido), 'REMOTE_ADDR' => '127.0.0.1'];
        $controle->responder($servidor, '', [], $sessao, 1000);
        $servidor['REQUEST_METHOD'] = 'POST';
        $servidor['HTTP_ORIGIN'] = 'https://quiz.example.invalid:443';
        self::assertSame(403, $controle->responder($servidor, '', ['csrf' => []], $sessao, 1001)->status);
        $campos = ['csrf' => $sessao['mcpCSRF'], 'acao' => 'autorizar', 'email' => [], 'senha' => 'incorreta'];
        self::assertSame(422, $controle->responder($servidor, '', $campos, $sessao, 1001)->status);
        $campos['email'] = 'ausente@example.invalid';
        for ($tentativa = 0; $tentativa < 11; $tentativa++) {
            $resposta = $controle->responder($servidor, '', $campos, $sessao, 1001);
        }
        self::assertSame(429, $resposta->status);
    }

    #[Test]
    public function preservaConsultaPreexistenteNoRetornoAutorizado(): void
    {
        $cliente = $this->oauth->registrarCliente(['redirect_uris' => ['https://cliente.example.invalid/callback?origem=teste']]);
        $pedido = array_replace($this->pedido, ['client_id' => $cliente['client_id'], 'redirect_uri' => $cliente['redirect_uris'][0]]);
        $controle = new \Aplicacao\Painel\Controles\ConexaoMCP($this->oauth, 'https://quiz.example.invalid');
        $sessao = [];
        $servidor = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/admin/mcp/autorizar?' . http_build_query($pedido)];
        $controle->responder($servidor, '', [], $sessao, 1000);
        $servidor['REQUEST_METHOD'] = 'POST';
        $resposta = $controle->responder($servidor, '', ['csrf' => $sessao['mcpCSRF'], 'acao' => 'cancelar'], $sessao, 1001);
        self::assertStringStartsWith($cliente['redirect_uris'][0] . '&error=access_denied', $resposta->cabecalhos['Location']);
    }

    #[Test]
    public function emiteTokensPeloControleHttp(): void
    {
        $codigo = $this->oauth->autorizar($this->pedido, $this->usuario, 1000);
        $controle = new \Aplicacao\API\Controles\Autorizacao($this->oauth, 'https://quiz.example.invalid');
        $sessao = [];
        $servidor = ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/oauth/token'];
        $resposta = $controle->responder($servidor, '', ['grant_type' => 'authorization_code', 'code' => $codigo, 'client_id' => $this->pedido['client_id'], 'redirect_uri' => $this->pedido['redirect_uri'], 'resource' => $this->pedido['resource'], 'code_verifier' => $this->verificador], $sessao, 1001);
        self::assertSame(200, $resposta->status);
        self::assertSame('mcp:gestao', json_decode($resposta->corpo, true)['scope']);
        $servidor['REQUEST_METHOD'] = 'GET';
        $servidor['REQUEST_URI'] = '/.well-known/oauth-protected-resource/mcp';
        self::assertSame(200, $controle->responder($servidor, '', [], $sessao, 1001)->status);
    }

    #[Test]
    public function senhaInicialAutorizaEmiteRenovaEAceitaBearerSemAlterarHash(): void
    {
        self::$banco->exec('UPDATE usuarios_mcp SET senha_inicial = 1');
        $antes = self::$banco->query('SELECT senha_hash, senha_inicial FROM usuarios_mcp')->fetch(PDO::FETCH_ASSOC);
        $usuario = $this->oauth->entrar('operador@example.invalid', $this->senha, '127.0.0.1', 1000);
        self::assertSame(1, (int) $usuario['senha_inicial']);
        $codigo = $this->oauth->autorizar($this->pedido, $usuario['identificador'], 1000);
        $tokens = $this->oauth->trocar(['grant_type' => 'authorization_code', 'code' => $codigo, 'client_id' => $this->pedido['client_id'], 'redirect_uri' => $this->pedido['redirect_uri'], 'resource' => $this->pedido['resource'], 'code_verifier' => $this->verificador], 1001);
        self::assertSame($this->usuario, $this->oauth->autenticarToken($tokens['access_token'], 1002)['identificador']);
        $renovados = $this->oauth->trocar(['grant_type' => 'refresh_token', 'refresh_token' => $tokens['refresh_token'], 'client_id' => $this->pedido['client_id'], 'resource' => $this->pedido['resource']], 1003);
        self::assertSame($this->usuario, $this->oauth->autenticarToken($renovados['access_token'], 1004)['identificador']);
        self::assertSame($antes, self::$banco->query('SELECT senha_hash, senha_inicial FROM usuarios_mcp')->fetch(PDO::FETCH_ASSOC));
        self::assertSame($this->usuario, $this->oauth->entrar('operador@example.invalid', $this->senha, '127.0.0.1', 1005)['identificador']);
    }

    #[Test]
    public function formularioApresentaSomenteCredenciaisEConsentimento(): void
    {
        $controle = new \Aplicacao\Painel\Controles\ConexaoMCP($this->oauth, 'https://quiz.example.invalid');
        $sessao = [];
        $resposta = $controle->responder(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/admin/mcp/autorizar?' . http_build_query($this->pedido)], '', [], $sessao, 1000);
        self::assertSame(200, $resposta->status);
        self::assertStringContainsString('name="email"', $resposta->corpo);
        self::assertStringContainsString('name="senha"', $resposta->corpo);
        self::assertStringContainsString('Entrar e autorizar', $resposta->corpo);
        self::assertStringNotContainsString('senhaNova', $resposta->corpo);
        self::assertStringNotContainsString('Nova senha', $resposta->corpo);
        self::assertStringNotContainsString('primeiro acesso', $resposta->corpo);
    }

    #[Test]
    public function loginMantemSenhaDefinidaMesmoRecebendoCampoAntigoDeTroca(): void
    {
        self::$banco->exec('UPDATE usuarios_mcp SET senha_inicial = 1');
        $antes = self::$banco->query('SELECT senha_hash, senha_inicial FROM usuarios_mcp')->fetch(PDO::FETCH_ASSOC);
        $controle = new \Aplicacao\Painel\Controles\ConexaoMCP($this->oauth, 'https://quiz.example.invalid');
        $sessao = [];
        foreach ([[], ['senhaNova' => 'MudancaNaoSolicitada!234'], ['senhaNova' => ['valor inválido']]] as $extras) {
            $servidor = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/admin/mcp/autorizar?' . http_build_query($this->pedido), 'REMOTE_ADDR' => '127.0.0.1'];
            self::assertSame(200, $controle->responder($servidor, '', [], $sessao, 1000)->status);
            $servidor['REQUEST_METHOD'] = 'POST';
            $campos = ['csrf' => $sessao['mcpCSRF'], 'acao' => 'autorizar', 'email' => 'operador@example.invalid', 'senha' => $this->senha] + $extras;
            self::assertSame(303, $controle->responder($servidor, '', $campos, $sessao, 1001)->status);
            self::assertSame($antes, self::$banco->query('SELECT senha_hash, senha_inicial FROM usuarios_mcp')->fetch(PDO::FETCH_ASSOC));
            self::assertSame($this->usuario, $this->oauth->entrar('operador@example.invalid', $this->senha, '127.0.0.1', 1002)['identificador']);
        }
    }

    #[Test]
    public function paginaAutorizaSomenteOrigemDoRetornoValidadoNaPoliticaConteudo(): void
    {
        $controle = new \Aplicacao\Painel\Controles\ConexaoMCP($this->oauth, 'https://quiz.example.invalid');
        foreach (['https://chatgpt.com/connector_platform_oauth_redirect?origem=teste' => 'https://chatgpt.com', 'http://127.0.0.1:48765/callback' => 'http://127.0.0.1:48765'] as $retorno => $origem) {
            $cliente = $this->oauth->registrarCliente(['redirect_uris' => [$retorno]]);
            $pedido = array_replace($this->pedido, ['client_id' => $cliente['client_id'], 'redirect_uri' => $retorno]);
            $sessao = [];
            $servidor = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/admin/mcp/autorizar?' . http_build_query($pedido), 'REMOTE_ADDR' => '127.0.0.1'];
            $resposta = $controle->responder($servidor, '', [], $sessao, 1000);
            self::assertSame(200, $resposta->status);
            self::assertArrayHasKey('Content-Security-Policy', $resposta->cabecalhos);
            $politica = str_replace("form-action 'self'", "form-action 'self' " . $origem, Resposta::POLITICA_CONTEUDO);
            self::assertSame($politica, $resposta->cabecalhos['Content-Security-Policy']);
            self::assertStringNotContainsString('callback', $politica);
            self::assertStringNotContainsString('origem=teste', $politica);
            self::assertStringNotContainsString('*', $politica);
            $servidor['REQUEST_METHOD'] = 'POST';
            $campos = ['csrf' => 'invalido', 'acao' => 'autorizar', 'email' => 'operador@example.invalid', 'senha' => 'incorreta', 'redirect_uri' => 'https://intruso.example.invalid/callback'];
            $recusa = $controle->responder($servidor, '', $campos, $sessao, 1001);
            self::assertSame(403, $recusa->status);
            self::assertSame($politica, $recusa->cabecalhos['Content-Security-Policy']);
            $campos['csrf'] = $sessao['mcpCSRF'];
            $recusa = $controle->responder($servidor, '', $campos, $sessao, 1002);
            self::assertSame(422, $recusa->status);
            self::assertSame($politica, $recusa->cabecalhos['Content-Security-Policy']);
            self::assertStringNotContainsString('intruso.example.invalid', $politica);
        }
    }

    #[Test]
    public function pedidosAusentesOuInvalidosPreservamPoliticaRestritaDoJogo(): void
    {
        $padrao = "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self'; font-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'";
        self::assertSame($padrao, Resposta::POLITICA_CONTEUDO);
        $controle = new \Aplicacao\Painel\Controles\ConexaoMCP($this->oauth, 'https://quiz.example.invalid');
        $sessao = [];
        foreach (['/admin/mcp/autorizar', '/admin/mcp/autorizar?' . http_build_query(array_replace($this->pedido, ['redirect_uri' => 'https://intruso.example.invalid/callback']))] as $uri) {
            $resposta = $controle->responder(['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => $uri], '', [], $sessao, 1000);
            self::assertSame(422, $resposta->status);
            self::assertSame($padrao, $resposta->cabecalhos['Content-Security-Policy']);
            self::assertArrayNotHasKey('mcpPedido', $sessao);
        }
    }

    private function recusar(callable $operacao): void
    {
        try {
            $operacao();
            self::fail('A operação inválida deve ser recusada.');
        } catch (DomainException) {
            self::assertTrue(true);
        }
    }
}
