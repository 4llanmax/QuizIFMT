<?php

/**
 * Confere instala??o isolada, persistência, contas, ranking e administração no MariaDB.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Testes\Integracao\Jogo;

use Aplicacao\Nucleo\Comuns\Classes\Banco;
use Aplicacao\Nucleo\Comuns\Classes\Parametros;
use Aplicacao\Site\Manipuladores\Acervo;
use Aplicacao\Site\Servicos\Jogadores;
use Aplicacao\Site\Servicos\Rodadas;
use Aplicacao\Painel\Servicos\Programacao;
use DomainException;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BancoJogoTeste extends TestCase
{
    private static PDO $banco;
    private static \Testes\Apoio\BancoIsolado $apoio;
    private static string $nome;
    private Acervo $acervo;
    private ?string $pastaSessao = null;
    private array $servidorOriginal;
    private array $ambienteOriginal;
    private string $sessaoCaminhoOriginal;

    public static function setUpBeforeClass(): void
    {
        require_once dirname(__DIR__, 3) . '/Aplicacao/Nucleo/Comuns/Funcoes/AutoCarregamento.php';
        self::$apoio = new \Testes\Apoio\BancoIsolado(getenv());
        self::$banco = self::$apoio->criar();
        self::$nome = self::$apoio->nome();
    }

    public static function tearDownAfterClass(): void
    {
        if (isset(self::$banco)) {
            self::$apoio->limpar();
        }
    }

    protected function setUp(): void
    {
        $this->servidorOriginal = $_SERVER;
        $this->sessaoCaminhoOriginal = ini_get('session.save_path');
        $this->ambienteOriginal = [];
        foreach (array_keys(self::$apoio->parametros()) as $chave) {
            $this->ambienteOriginal[$chave] = getenv($chave);
        }
        $this->acervo = new Acervo(self::$banco);
        self::$banco->exec('DELETE FROM partidas');
        self::$banco->exec('DELETE FROM jogadores');
        self::$banco->exec('DELETE FROM tentativas');
    }

    #[Test]
    public function instalaPerguntasConectaEConfereTransacao(): void
    {
        $parametros = new Parametros(__DIR__ . '/Ausente.env', self::$apoio->parametros());
        self::assertStringStartsWith('10.6.28-MariaDB', (new Banco($parametros))->conectar()->query('SELECT VERSION()')->fetchColumn());
        self::assertFalse(self::$banco->getAttribute(PDO::ATTR_EMULATE_PREPARES));
        self::assertCount(48, $this->acervo->perguntas());
        self::assertCount(6, $this->acervo->configuracao()['distribuicao']);
        try {
            $this->acervo->transacionar(function (): void {
                $this->acervo->cadastrar('Teste', 'teste', 'teste@example.invalid', 'hash');
                throw new DomainException('Reversão esperada.');
            });
            self::fail('A falha precisa interromper a transação.');
        } catch (DomainException) {
            self::assertNull($this->acervo->email('teste@example.invalid'));
        }
    }

    #[Test]
    public function cadastraAutenticaRenomeiaEProtegeNomes(): void
    {
        $jogadores = new Jogadores($this->acervo);
        $convidado = $jogadores->convidado(str_repeat('a', 64), 'João');
        self::assertSame($convidado['identificador'], $jogadores->convidado(str_repeat('a', 64), 'João')['identificador']);
        $conta = $jogadores->cadastrar('Jogador_1', 'teste@example.invalid', 'senha12345', null);
        self::assertTrue(password_verify('senha12345', $conta['senha_hash']));
        self::assertSame($conta['identificador'], $jogadores->entrar('teste@example.invalid', 'senha12345')['identificador']);
        $jogadores->renomear((int) $conta['identificador'], 'Jogador_2');
        self::assertSame('Jogador_2', $this->acervo->jogador((int) $conta['identificador'])['nome']);
        foreach (
            [fn () => $jogadores->entrar('ausente@example.invalid', 'senha12345'),
            fn () => $jogadores->entrar('teste@example.invalid', 'incorreta'),
            fn () => $jogadores->cadastrar('Outra', 'teste@example.invalid', 'senha12345', null),
            fn () => $jogadores->convidado(str_repeat('b', 64), 'Jogador_2'),
            fn () => $jogadores->cadastrar('Jogador_2', 'outra@example.invalid', 'senha12345', null)] as $operacao
        ) {
            try {
                $operacao();
                self::fail('O acesso ou nome precisa ser recusado.');
            } catch (DomainException) {
                self::assertTrue(true);
            }
        }
    }

    #[Test]
    public function sorteiaSemRepeticaoGravaResultadoUmaVezEReservaNomeTop(): void
    {
        $jogadores = new Jogadores($this->acervo);
        $convidado = $jogadores->convidado(str_repeat('a', 64), 'Visitante');
        $rodadas = new Rodadas($this->acervo);
        $partida = $rodadas->iniciar(1000);
        self::assertCount(20, $partida->estado()['perguntas']);
        self::assertCount(20, array_unique(array_column($partida->estado()['perguntas'], 'enunciado')));
        $partida->abandonar();
        $rodadas->guardar((int) $convidado['identificador'], $partida);
        $rodadas->guardar((int) $convidado['identificador'], $partida);
        self::assertCount(1, $this->acervo->historico((int) $convidado['identificador']));
        self::assertCount(1, $this->acervo->ranking());
        try {
            $jogadores->cadastrar('Visitante', 'outra@example.invalid', 'senha12345', null);
            self::fail('O nome no Top 100 fica reservado.');
        } catch (DomainException) {
            self::assertTrue(true);
        }
        $conta = $jogadores->cadastrar('Visitante', 'teste@example.invalid', 'senha12345', (int) $convidado['identificador']);
        self::assertCount(1, $this->acervo->historico((int) $conta['identificador']));
        self::assertCount(1, $this->acervo->ranking());
    }

    #[Test]
    public function validaProgramacaoERestauraPadrao(): void
    {
        $programacao = new Programacao(new \Aplicacao\Painel\Manipuladores\Programacao(self::$banco));
        $padrao = $this->acervo->configuracao();
        $nova = $padrao;
        $nova['segundos'] = 30;
        $programacao->salvar($nova);
        self::assertSame(30, $this->acervo->configuracao()['segundos']);
        foreach (
            [array_replace($padrao, ['total' => 21]), array_replace($padrao, ['segundos' => 1]),
            array_replace($padrao, ['alerta' => 45]), array_replace($padrao, ['distribuicao' => ['geografia' => 20]])] as $invalida
        ) {
            try {
                $programacao->salvar($invalida);
                self::fail('A programação inválida precisa ser recusada.');
            } catch (DomainException) {
                self::assertSame(30, $this->acervo->configuracao()['segundos']);
            }
        }
        $programacao->restaurar();
        self::assertSame($padrao, $this->acervo->configuracao());
    }

    private function web(): \Aplicacao\Nucleo\Comuns\Classes\Web
    {
        $parametros = new Parametros(__DIR__ . '/Ausente.env', self::$apoio->parametros() + ['ADMIN_SENHA_HASH' => password_hash('senha-admin-ficticia', PASSWORD_DEFAULT)], \Aplicacao\Painel\Provedores\Atendimento::parametros());
        return new \Aplicacao\Nucleo\Comuns\Classes\Web([
            '/admin' => (new \Aplicacao\Painel\Provedores\Atendimento(self::$banco, $parametros))->construir(),
            '/' => (new \Aplicacao\Site\Provedores\Atendimento(self::$banco, $parametros))->construir(),
        ]);
    }

    #[Test]
    public function atendeInterfaceProtegeCsrfOcultaGabaritoEGravaResultado(): void
    {
        $web = $this->web();
        $sessao = [];
        $resposta = $web->atender('GET', '/', [], $sessao, 1000, 'teste');
        self::assertSame(200, $resposta->status);
        self::assertStringContainsString('BOMB', $resposta->corpo);
        self::assertStringNotContainsString('Admin (demo)', $resposta->corpo);
        self::assertSame(403, $web->atender('POST', '/identificar', ['nome' => 'Visitante'], $sessao, 1000, 'teste')->status);
        $campos = ['csrf' => $sessao['csrf'], 'nome' => 'Visitante'];
        self::assertSame(303, $web->atender('POST', '/identificar', $campos, $sessao, 1000, 'teste')->status);
        self::assertSame(200, $web->atender('GET', '/rodada', [], $sessao, 1000, 'teste')->status);
        self::assertSame(303, $web->atender('POST', '/jogar', $campos, $sessao, 1000, 'teste')->status);
        $identificador = $sessao['partida']['identificador'];
        $jogo = $web->atender('GET', '/jogo', [], $sessao, 2000, 'teste');
        self::assertSame(200, $jogo->status);
        self::assertStringContainsString('data-restante="44000"', $jogo->corpo);
        self::assertStringNotContainsString('"correta"', $jogo->corpo);
        $resposta = $web->atender('POST', '/responder', ['csrf' => $sessao['csrf'], 'partida' => $identificador, 'indice' => '0', 'alternativa' => (string) $sessao['partida']['perguntas'][0]['correta']], $sessao, 3000, 'teste');
        self::assertSame(303, $resposta->status);
        self::assertSame(1, $sessao['partida']['acertos']);
        self::assertSame(200, $web->atender('GET', '/jogo', [], $sessao, 3500, 'teste')->status);
        self::assertSame(303, $web->atender('POST', '/avancar', ['csrf' => $sessao['csrf'], 'partida' => $identificador], $sessao, 4000, 'teste')->status);
        self::assertSame(200, $web->atender('GET', '/jogo', [], $sessao, 49000, 'teste')->status);
        self::assertSame('tempo', $sessao['partida']['situacao']);
        self::assertCount(1, $this->acervo->ranking());
        self::assertSame(200, $web->atender('GET', '/ranking', [], $sessao, 50000, 'teste')->status);
        self::assertSame(404, $web->atender('GET', '/inexistente', [], $sessao, 50000, 'teste')->status);
        self::assertSame(405, $web->atender('DELETE', '/', [], $sessao, 50000, 'teste')->status);
    }

    #[Test]
    public function protegeAdministracaoExigeLoginEValidaProgramacao(): void
    {
        $web = $this->web();
        $sessao = [];
        self::assertSame(200, $web->atender('GET', '/admin', [], $sessao, 1000, 'teste')->status);
        self::assertSame(403, $web->atender('POST', '/admin/salvar', ['csrf' => $sessao['csrf']], $sessao, 1000, 'teste')->status);
        self::assertSame(422, $web->atender('POST', '/admin/entrar', ['csrf' => $sessao['csrf'], 'senha' => 'incorreta'], $sessao, 1000, 'teste')->status);
        self::assertSame(303, $web->atender('POST', '/admin/entrar', ['csrf' => $sessao['csrf'], 'senha' => 'senha-admin-ficticia'], $sessao, 2000, 'teste')->status);
        self::assertTrue($sessao['administrador']);
        self::assertSame(200, $web->atender('GET', '/admin', [], $sessao, 3000, 'teste')->status);
        self::assertSame(303, $web->atender('POST', '/admin/restaurar', ['csrf' => $sessao['csrf']], $sessao, 4000, 'teste')->status);
        self::assertSame(303, $web->atender('POST', '/admin/sair', ['csrf' => $sessao['csrf']], $sessao, 5000, 'teste')->status);
        self::assertArrayNotHasKey('administrador', $sessao);
    }

    #[Test]
    public function excluiSomenteContaAutenticadaAposConferirSenha(): void
    {
        $servico = new Jogadores($this->acervo);
        $conta = $servico->cadastrar('Jogador', 'teste@example.invalid', 'senha12345', null);
        try {
            $servico->excluir((int) $conta['identificador'], 'senhaErrada');
            self::fail('A senha precisa ser conferida.');
        } catch (DomainException) {
            self::assertNotNull($this->acervo->jogador((int) $conta['identificador']));
        }
        $servico->excluir((int) $conta['identificador'], 'senha12345');
        self::assertNull($this->acervo->jogador((int) $conta['identificador']));
    }

    #[Test]
    public function inicializaEntradaERecusaAmbienteInseguroOuIncompleto(): void
    {
        $inicializacao = new \Aplicacao\Nucleo\Comuns\Classes\Inicializacao(dirname(__DIR__, 3), \Aplicacao\Painel\Provedores\Atendimento::parametros());
        $ambiente = self::$apoio->parametros();
        $sessao = [];
        $construir = fn (PDO $banco, Parametros $parametros) => $this->web();
        self::assertSame(200, $inicializacao->responder(['REQUEST_URI' => '/', 'REQUEST_METHOD' => 'GET', 'REMOTE_ADDR' => '127.0.0.1'], [], $ambiente, $sessao, $construir, 1000)->status);
        self::assertSame(400, $inicializacao->responder(['REQUEST_URI' => '/', 'REQUEST_METHOD' => 'GET', 'REMOTE_ADDR' => '203.0.113.1'], [], $ambiente, $sessao, $construir, 1000)->status);
        self::assertSame(503, $inicializacao->responder(['REMOTE_ADDR' => '127.0.0.1'], [], ['BANCO_NOME' => 'nome;invalido'], $sessao, $construir, 1000)->status);
    }

    #[Test]
    public function percorreCadastroHistoricoVitoriaLoginEdicaoEExclusao(): void
    {
        $web = $this->web();
        $sessao = [];
        foreach (['/', '/entrar', '/cadastro', '/ranking', '/como-funciona', '/privacidade'] as $pagina) {
            self::assertSame(200, $web->atender('GET', $pagina, [], $sessao, 1000, 'fluxo')->status);
        }
        foreach (['/jogo', '/rodada'] as $pagina) {
            self::assertSame(303, $web->atender('GET', $pagina, [], $sessao, 1000, 'fluxo')->status);
        }
        self::assertSame(403, $web->atender('GET', '/conta', [], $sessao, 1000, 'fluxo')->status);
        self::assertSame(303, $web->atender('POST', '/jogar', ['csrf' => $sessao['csrf']], $sessao, 1000, 'fluxo')->status);
        self::assertSame(409, $web->atender('POST', '/responder', ['csrf' => $sessao['csrf']], $sessao, 1000, 'fluxo')->status);
        $enviar = function (string $rota, array $campos = [], int $agora = 1000) use ($web, &$sessao) {
            return $web->atender('POST', $rota, ['csrf' => $sessao['csrf']] + $campos, $sessao, $agora, 'fluxo');
        };
        self::assertSame(303, $enviar('/cadastro', ['nome' => 'Jogador', 'email' => 'teste@example.invalid', 'senha' => 'senha12345'])->status);
        self::assertSame(200, $web->atender('GET', '/conta', [], $sessao, 1000, 'fluxo')->status);
        self::assertStringContainsString('Jogando como', $web->atender('GET', '/', [], $sessao, 1000, 'fluxo')->corpo);
        self::assertSame(303, $enviar('/jogar')->status);
        $identificador = $sessao['partida']['identificador'];
        self::assertSame(303, $enviar('/jogar', [], 1100)->status);
        self::assertSame($identificador, $sessao['partida']['identificador']);
        self::assertSame(422, $enviar('/responder', ['partida' => 'antiga', 'indice' => '0', 'alternativa' => '0'])->status);
        $agora = 1000;
        for ($indice = 0; $indice < 20; $indice++) {
            $agora += 1000;
            self::assertSame(303, $enviar('/responder', ['partida' => $identificador, 'indice' => (string) $indice, 'alternativa' => (string) $sessao['partida']['perguntas'][$indice]['correta']], $agora)->status);
            if ($indice < 19) {
                self::assertSame(303, $enviar('/avancar', ['partida' => $identificador], $agora)->status);
            }
        }
        self::assertSame('vitoria', $sessao['partida']['situacao']);
        self::assertStringContainsString('BOMBA DESARMADA!', $web->atender('GET', '/jogo', [], $sessao, $agora, 'fluxo')->corpo);
        self::assertStringContainsString('Desempenho por tema', $web->atender('GET', '/conta', [], $sessao, $agora, 'fluxo')->corpo);
        self::assertSame(303, $enviar('/apelido', ['nome' => 'Jogador_2'])->status);
        self::assertStringContainsString('Jogador_2', $web->atender('GET', '/ranking', [], $sessao, $agora, 'fluxo')->corpo);
        self::assertSame(303, $enviar('/sair')->status);
        self::assertSame(303, $enviar('/entrar', ['email' => 'teste@example.invalid', 'senha' => 'senha12345'])->status);
        self::assertSame(303, $enviar('/excluir-conta', ['senha' => 'senha12345'])->status);
        self::assertCount(0, $this->acervo->ranking());
    }

    #[Test]
    public function confereErroAbandonoExpiracaoEProprietarioDaPartida(): void
    {
        $web = $this->web();
        $sessao = [];
        $web->atender('GET', '/', [], $sessao, 1000, 'partidas');
        $web->atender('POST', '/identificar', ['csrf' => $sessao['csrf'], 'nome' => 'Visitante'], $sessao, 1000, 'partidas');
        $enviar = function (string $rota, array $campos = [], int $agora = 1000) use ($web, &$sessao) {
            return $web->atender('POST', $rota, ['csrf' => $sessao['csrf']] + $campos, $sessao, $agora, 'partidas');
        };
        $enviar('/jogar');
        $identificador = $sessao['partida']['identificador'];
        $enviar('/expirar', ['partida' => $identificador], 2000);
        self::assertSame('jogando', $sessao['partida']['situacao']);
        $enviar('/responder', ['partida' => $identificador, 'indice' => '0', 'alternativa' => (string) (($sessao['partida']['perguntas'][0]['correta'] + 1) % 5)], 3000);
        self::assertStringContainsString('RESPOSTA ERRADA', $web->atender('GET', '/jogo', [], $sessao, 3000, 'partidas')->corpo);
        $enviar('/jogar', [], 4000);
        self::assertNotSame($identificador, $sessao['partida']['identificador']);
        $enviar('/abandonar', ['partida' => $sessao['partida']['identificador']], 4500);
        self::assertStringContainsString('PARTIDA ENCERRADA', $web->atender('GET', '/jogo', [], $sessao, 4500, 'partidas')->corpo);
        $enviar('/jogar', [], 5000);
        $enviar('/identificar', ['nome' => 'Outro']);
        $enviar('/jogar', [], 6000);
        self::assertSame($sessao['jogador'], $sessao['jogadorPartida']);
        self::assertCount(3, $this->acervo->historico((int) $this->acervo->ranking()[0]['identificador']));
        self::assertSame(303, $enviar('/cadastro', ['nome' => 'Outro', 'email' => 'outro@example.invalid', 'senha' => 'senha12345'])->status);
    }

    #[Test]
    public function limitaTentativasERenovaJanelaCompartilhada(): void
    {
        $limite = new \Aplicacao\Nucleo\Servicos\Acesso(new \Aplicacao\Nucleo\Manipuladores\Tentativas(self::$banco));
        for ($indice = 0; $indice < 10; $indice++) {
            $limite->conferir('origem-ficticia', 100000000);
        }
        try {
            $limite->conferir('origem-ficticia', 100000001);
            self::fail('A décima primeira tentativa precisa ser bloqueada.');
        } catch (\Aplicacao\Nucleo\Excecoes\FalhaHTTP $falha) {
            self::assertSame(429, $falha->status);
        }
        $limite->conferir('origem-ficticia', 100900000);
        $limite->conferir('origem-diferente', 200000000);
        self::assertSame(1, (int) self::$banco->query('SELECT COUNT(*) FROM tentativas')->fetchColumn());
    }

    #[Test]
    public function gravaProgramacaoPelaInterfaceERecusaFormatoInvalido(): void
    {
        $web = $this->web();
        $sessao = ['administrador' => true];
        $web->atender('GET', '/admin', [], $sessao, 1000, 'admin');
        $configuracao = $this->acervo->configuracao();
        $campos = $configuracao;
        $campos['total'] = '20';
        $campos['segundos'] = '30';
        $campos['alerta'] = '10';
        $campos['distribuicao'] = array_map('strval', $campos['distribuicao']);
        $campos['csrf'] = $sessao['csrf'];
        self::assertSame(303, $web->atender('POST', '/admin/salvar', $campos, $sessao, 1000, 'admin')->status);
        self::assertSame(30, $this->acervo->configuracao()['segundos']);
        $campos['distribuicao'] = 'invalida';
        self::assertSame(422, $web->atender('POST', '/admin/salvar', $campos, $sessao, 1000, 'admin')->status);
        $campos['total'] = [];
        self::assertSame(422, $web->atender('POST', '/admin/salvar', $campos, $sessao, 1000, 'admin')->status);
        $web->atender('POST', '/admin/restaurar', ['csrf' => $sessao['csrf']], $sessao, 1000, 'admin');
    }

    #[Test]
    public function emiteEntradaPublicaComCookieECabecalhos(): void
    {
        $this->pastaSessao = sys_get_temp_dir() . '/quiz-sessao-' . bin2hex(random_bytes(6));
        mkdir($this->pastaSessao);
        ini_set('session.save_path', $this->pastaSessao);
        foreach (self::$apoio->parametros() as $chave => $valor) {
            putenv($chave . '=' . $valor);
        }
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        ob_start();
        require dirname(__DIR__, 3) . '/Publico/index.php';
        $corpo = ob_get_clean();
        self::assertSame(200, http_response_code());
        self::assertStringContainsString('BOMB', $corpo);
        self::assertSame('QUIZIFMT', session_name());
    }

    #[Test]
    public function renovaIdentificadorSessaoNaEntrada(): void
    {
        $this->pastaSessao = sys_get_temp_dir() . '/quiz-sessao-' . bin2hex(random_bytes(6));
        mkdir($this->pastaSessao);
        ini_set('session.save_path', $this->pastaSessao);
        foreach (self::$apoio->parametros() as $chave => $valor) {
            putenv($chave . '=' . $valor);
        }
        ini_set('session.use_strict_mode', '1');
        session_name('QUIZIFMT');
        session_start();
        $anterior = session_id();
        $_SESSION['renovar'] = true;
        session_write_close();
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        ob_start();
        require dirname(__DIR__, 3) . '/Publico/index.php';
        ob_end_clean();
        self::assertNotSame($anterior, session_id());
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->servidorOriginal;
        foreach ($this->ambienteOriginal as $chave => $valor) {
            putenv($valor === false ? $chave : $chave . '=' . $valor);
        }
        ini_set('session.save_path', $this->sessaoCaminhoOriginal);
        if ($this->pastaSessao !== null) {
            foreach (glob($this->pastaSessao . '/sess_*') as $arquivo) {
                unlink($arquivo);
            }
            rmdir($this->pastaSessao);
        }
    }

    #[Test]
    public function recusaDistribuicaoExcedenteSemAlterarProgramacao(): void
    {
        $servico = new Programacao(new \Aplicacao\Painel\Manipuladores\Programacao(self::$banco));
        $invalida = $this->acervo->configuracao();
        $invalida['distribuicao']['geografia'] = 9;
        $this->expectException(DomainException::class);
        $servico->salvar($invalida);
    }

    #[Test]
    public function encerraPartidaAoSairENaoReutilizaContaExcluida(): void
    {
        $web = $this->web();
        $sessao = [];
        $web->atender('GET', '/', [], $sessao, 1000, 'sessao');
        $web->atender('POST', '/cadastro', ['csrf' => $sessao['csrf'], 'nome' => 'Jogador', 'email' => 'teste@example.invalid', 'senha' => 'senha12345'], $sessao, 1000, 'sessao');
        $web->atender('POST', '/jogar', ['csrf' => $sessao['csrf']], $sessao, 2000, 'sessao');
        $web->atender('POST', '/sair', ['csrf' => $sessao['csrf']], $sessao, 3000, 'sessao');
        self::assertArrayNotHasKey('partida', $sessao);
        self::assertSame(303, $web->atender('GET', '/jogo', [], $sessao, 4000, 'sessao')->status);
        self::assertCount(1, $this->acervo->ranking());
        $web->atender('POST', '/entrar', ['csrf' => $sessao['csrf'], 'email' => 'teste@example.invalid', 'senha' => 'senha12345'], $sessao, 5000, 'sessao');
        $outraSessao = $sessao;
        $web->atender('POST', '/excluir-conta', ['csrf' => $sessao['csrf'], 'senha' => 'senha12345'], $sessao, 6000, 'sessao');
        self::assertSame(403, $web->atender('GET', '/conta', [], $outraSessao, 7000, 'sessao')->status);
        self::assertArrayNotHasKey('conta', $outraSessao);
    }

    #[Test]
    public function cadastroEmOutraContaNaoTransfereHistoricoERecusaIdentificadorInvalido(): void
    {
        $web = $this->web();
        $sessao = [];
        $web->atender('GET', '/', [], $sessao, 1000, 'titularidade');
        $enviar = function (string $rota, array $campos = [], int $agora = 1000) use ($web, &$sessao) {
            return $web->atender('POST', $rota, ['csrf' => $sessao['csrf']] + $campos, $sessao, $agora, 'titularidade');
        };
        self::assertSame(303, $enviar('/cadastro', ['nome' => 'Primeiro', 'email' => 'primeiro@example.invalid', 'senha' => 'senha12345'])->status);
        $primeiro = $sessao['jogador'];
        $enviar('/jogar');
        $enviar('/abandonar', ['partida' => $sessao['partida']['identificador']]);
        self::assertSame(303, $enviar('/cadastro', ['nome' => 'Segundo', 'email' => 'segundo@example.invalid', 'senha' => 'senha12345'])->status);
        self::assertCount(1, $this->acervo->historico($primeiro));
        self::assertCount(0, $this->acervo->historico($sessao['jogador']));
        $enviar('/jogar', [], 2000);
        self::assertSame(422, $enviar('/abandonar', ['partida' => []], 2500)->status);
        self::assertSame('jogando', $sessao['partida']['situacao']);
        $enviar('/sair', [], 3000);
        self::assertSame(303, $enviar('/sair', [], 4000)->status);
    }

    #[Test]
    public function recusaAtendimentoSeSessaoNaoPodeSerPersistida(): void
    {
        $arquivo = tempnam(sys_get_temp_dir(), 'quiz-sessao-invalida-');
        $avisos = [];
        $pastaAnterior = ini_get('session.save_path');
        try {
            ini_set('session.save_path', $arquivo);
            foreach (self::$apoio->parametros() as $chave => $valor) {
                putenv($chave . '=' . $valor);
            }
            $_SERVER['REQUEST_URI'] = '/';
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            set_error_handler(static function (int $nivel, string $mensagem) use (&$avisos): bool {
                if ($nivel === E_WARNING && str_starts_with($mensagem, 'session_start()')) {
                    $avisos[] = $mensagem;
                    return true;
                }
                return false;
            });
            ob_start();
            require dirname(__DIR__, 3) . '/Publico/index.php';
            ob_end_clean();
            self::assertSame(503, http_response_code());
            self::assertNotEmpty($avisos);
        } finally {
            restore_error_handler();
            ini_set('session.save_path', $pastaAnterior);
            unlink($arquivo);
        }
    }
}
