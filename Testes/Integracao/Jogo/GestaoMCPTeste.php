<?php

/**
 * Confere gestão de perguntas e consultas do MCP em banco descartável.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Integracao\Jogo;

use Aplicacao\API\Servicos\Ferramentas;
use Aplicacao\Site\Manipuladores\Acervo;
use DomainException;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Testes\Apoio\BancoIsolado;

final class GestaoMCPTeste extends TestCase
{
    private BancoIsolado $apoio;
    private PDO $banco;
    private Ferramentas $ferramentas;

    protected function setUp(): void
    {
        $this->apoio = new BancoIsolado(getenv());
        $this->banco = $this->apoio->criar();
        $this->ferramentas = new Ferramentas($this->banco);
    }

    protected function tearDown(): void
    {
        if (isset($this->apoio)) {
            $this->apoio->limpar();
        }
    }

    private function pergunta(): array
    {
        return ['tema' => 'geografia', 'enunciado' => 'Pergunta fictícia?', 'alternativas' => ['A', 'B', 'C', 'D', 'E'], 'correta' => 0];
    }

    #[Test]
    public function cadastraCategoriaDinamicaInsereLoteEquilibradoENaoRemoveCategoriaEmUso(): void
    {
        self::assertCount(6, $this->ferramentas->executar('listar_categorias', [])['categorias']);
        self::assertSame('tecnologia', $this->ferramentas->executar('criar_categoria', ['chave' => 'tecnologia', 'nome' => 'Tecnologia'])['chave']);
        self::assertCount(6, (new Acervo($this->banco))->configuracao()['distribuicao']);
        $pergunta = array_replace($this->pergunta(), ['tema' => 'tecnologia']);
        $this->ferramentas->executar('criar_perguntas', ['perguntas' => [$pergunta, $pergunta, $pergunta]]);
        self::assertCount(7, (new Acervo($this->banco))->configuracao()['distribuicao']);
        self::assertSame('Tecnologia geral', $this->ferramentas->executar('editar_categoria', ['chave' => 'tecnologia', 'nome' => 'Tecnologia geral'])['nome']);
        try {
            $this->ferramentas->executar('excluir_categoria', ['chave' => 'tecnologia', 'confirmar' => true]);
            self::fail('A categoria com perguntas não pode ser removida.');
        } catch (DomainException) {
            self::assertCount(7, $this->ferramentas->executar('listar_categorias', [])['categorias']);
        }
        $this->ferramentas->executar('criar_categoria', ['chave' => 'vazia', 'nome' => 'Categoria vazia']);
        self::assertTrue($this->ferramentas->executar('excluir_categoria', ['chave' => 'vazia', 'confirmar' => true])['excluida']);
        $partida = (new \Aplicacao\Site\Servicos\Rodadas(new Acervo($this->banco)))->iniciar(1000)->estado();
        $quantidades = array_count_values(array_column($partida['perguntas'], 'tema'));
        self::assertSame(20, array_sum($quantidades));
        self::assertSame(1, max($quantidades) - min($quantidades));
    }

    #[Test]
    public function criaEditaArquivaRestauraEExcluiPergunta(): void
    {
        $criadas = $this->ferramentas->executar('criar_perguntas', ['perguntas' => [$this->pergunta()]]);
        $identificador = $criadas['perguntas'][0]['identificador'];
        self::assertIsInt($identificador);
        self::assertSame(49, $this->ferramentas->executar('listar_perguntas', [])['total']);
        $editada = $this->ferramentas->executar('editar_pergunta', ['identificador' => $identificador, 'enunciado' => 'Enunciado corrigido?', 'alternativas' => ['E', 'D', 'C', 'B', 'A'], 'correta' => 4]);
        self::assertSame('Enunciado corrigido?', $editada['enunciado']);
        self::assertSame(4, $editada['correta']);
        self::assertTrue($this->ferramentas->executar('arquivar_pergunta', ['identificador' => $identificador, 'arquivada' => true])['arquivada']);
        self::assertCount(48, (new Acervo($this->banco))->perguntas());
        self::assertSame(1, $this->ferramentas->executar('listar_perguntas', ['arquivadas' => 'arquivadas'])['total']);
        self::assertSame(49, $this->ferramentas->executar('listar_perguntas', ['arquivadas' => 'todas'])['total']);
        self::assertFalse($this->ferramentas->executar('arquivar_pergunta', ['identificador' => $identificador, 'arquivada' => false])['arquivada']);
        self::assertCount(49, (new Acervo($this->banco))->perguntas());
        self::assertTrue($this->ferramentas->executar('excluir_pergunta', ['identificador' => $identificador, 'confirmar' => true])['excluida']);
        self::assertSame(48, $this->ferramentas->executar('listar_perguntas', [])['total']);
    }

    #[Test]
    public function protegeCapacidadeDoJogoConfirmaExclusaoEReverteLoteInvalido(): void
    {
        $programacao = json_decode($this->banco->query('SELECT conteudo FROM configuracao')->fetchColumn(), true);
        $programacao['total'] = 48;
        $consulta = $this->banco->prepare('UPDATE configuracao SET conteudo = ?');
        $consulta->execute([json_encode($programacao)]);
        foreach ([['arquivar_pergunta', ['identificador' => 1, 'arquivada' => true]], ['excluir_pergunta', ['identificador' => 1, 'confirmar' => true]], ['editar_pergunta', ['identificador' => 1, 'tema' => 'musica']], ['excluir_pergunta', ['identificador' => 1]], ['criar_perguntas', ['perguntas' => [$this->pergunta(), []]]]] as [$nome, $argumentos]) {
            try {
                $this->ferramentas->executar($nome, $argumentos);
                self::fail('A alteração inválida precisa ser recusada.');
            } catch (DomainException) {
                self::assertSame(48, $this->ferramentas->executar('listar_perguntas', [])['total']);
                self::assertFalse($this->ferramentas->executar('obter_pergunta', ['identificador' => 1])['arquivada']);
            }
        }
        self::assertSame(8, (new \Aplicacao\Painel\Manipuladores\Programacao($this->banco))->capacidades()['geografia']);
    }

    #[Test]
    public function expoeConsultasPaginadasSemSegredos(): void
    {
        $this->banco->exec("INSERT INTO jogadores (nome, nome_normalizado, email, senha_hash, token) VALUES ('Ficticio', 'ficticio', 'teste@example.invalid', 'segredo', 'tokenprivado')");
        $jogador = (int) $this->banco->lastInsertId();
        $consulta = $this->banco->prepare('INSERT INTO partidas (identificador, jogador, acertos, total, nivel, tempo, motivo, temas) VALUES (?, ?, 3, 20, 4, 1000, ?, ?)');
        $consulta->execute([str_repeat('a', 32), $jogador, 'erro', '{}']);
        $jogadores = $this->ferramentas->executar('listar_jogadores', ['pagina' => 1, 'limite' => 1]);
        self::assertSame(1, $jogadores['total']);
        self::assertArrayNotHasKey('senha_hash', $jogadores['jogadores'][0]);
        self::assertArrayNotHasKey('token', $jogadores['jogadores'][0]);
        self::assertSame(1, $this->ferramentas->executar('listar_partidas', ['jogador' => $jogador])['total']);
        self::assertSame(1, $this->ferramentas->executar('listar_partidas', [])['total']);
        self::assertSame(0, $this->ferramentas->executar('listar_partidas', ['jogador' => $jogador + 1])['total']);
        self::assertSame(str_repeat('a', 32), $this->ferramentas->executar('obter_partida', ['identificador' => str_repeat('a', 32)])['identificador']);
        self::assertSame(1, $this->ferramentas->executar('consultar_estatisticas', [])['partidas']);
        self::assertCount(1, $this->ferramentas->executar('consultar_ranking', [])['ranking']);
        self::assertSame(20, $this->ferramentas->executar('consultar_programacao', [])['programacao']['total']);
        self::assertCount(8, $this->ferramentas->executar('listar_perguntas', ['tema' => 'historia', 'limite' => 100])['perguntas']);
        self::assertSame([], $this->ferramentas->executar('listar_perguntas', ['pagina' => 100])['perguntas']);
        foreach ($this->ferramentas->listar() as $ferramenta) {
            self::assertSame('object', $ferramenta['inputSchema']['type']);
            self::assertFalse($ferramenta['inputSchema']['additionalProperties']);
        }
    }

    #[Test]
    public function recusaOperacoesArgumentosERegistrosInvalidos(): void
    {
        foreach ([['inexistente', []], ['listar_perguntas', ['pagina' => 0]], ['listar_perguntas', ['pagina' => 1000001]], ['listar_perguntas', ['limite' => 101]], ['listar_perguntas', ['tema' => 'outro']], ['listar_perguntas', ['arquivadas' => false]], ['listar_partidas', ['jogador' => '1']], ['obter_pergunta', ['identificador' => 99999]], ['obter_partida', ['identificador' => str_repeat('f', 32)]], ['obter_partida', ['identificador' => 'x']], ['criar_perguntas', ['perguntas' => []]], ['criar_perguntas', ['perguntas' => 'x']], ['editar_pergunta', ['identificador' => 1]], ['editar_pergunta', ['identificador' => 1, 'inventada' => true]], ['arquivar_pergunta', ['identificador' => 1, 'arquivada' => 'sim']], ['excluir_pergunta', ['identificador' => 1, 'confirmar' => false]], ['consultar_estatisticas', ['inventada' => true]]] as [$nome, $argumentos]) {
            try {
                $this->ferramentas->executar($nome, $argumentos);
                self::fail('Os argumentos ou o registro inválidos precisam ser recusados.');
            } catch (DomainException) {
                self::assertTrue(true);
            }
        }
    }

    #[Test]
    public function recusaCategoriasDuplicadasCamposNulosELotesMalformados(): void
    {
        $this->ferramentas->executar('criar_categoria', ['chave' => 'tecnologia', 'nome' => 'Tecnologia']);
        self::assertSame('Tecnologia', $this->ferramentas->executar('editar_categoria', ['chave' => 'tecnologia', 'nome' => 'Tecnologia'])['nome']);
        $operacoes = [
            ['criar_categoria', ['chave' => 'geografia', 'nome' => 'Outra']],
            ['criar_categoria', ['chave' => 'outra', 'nome' => 'Geografia']],
            ['editar_categoria', ['chave' => 'tecnologia', 'nome' => 'Geografia']],
            ['editar_categoria', ['chave' => 'inexistente', 'nome' => 'Nome']],
            ['criar_categoria', ['chave' => 'Chave Inválida', 'nome' => 'Nome']],
            ['criar_categoria', ['chave' => null, 'nome' => 'Nome']],
            ['criar_categoria', ['chave' => 'outra', 'nome' => ' ']],
            ['criar_categoria', ['chave' => 'outra', 'nome' => []]],
            ['criar_categoria', ['chave' => 'outra', 'nome' => "\xFF"]],
            ['criar_categoria', ['chave' => 'outra', 'nome' => str_repeat('á', 81)]],
            ['criar_categoria', ['chave' => 'outra', 'nome' => "Nome\0inválido"]],
            ['excluir_categoria', ['chave' => 'inexistente', 'confirmar' => true]],
            ['listar_perguntas', ['tema' => []]],
            ['listar_perguntas', ['tema' => 'Tema Inválido']],
            ['listar_perguntas', ['arquivadas' => null]],
            ['listar_perguntas', ['pagina' => null]],
            ['listar_perguntas', ['limite' => null]],
            ['listar_partidas', ['jogador' => null]],
            ['obter_pergunta', ['identificador' => null]],
            ['criar_perguntas', ['perguntas' => ['inválida']]],
            ['criar_perguntas', ['perguntas' => ['chave' => $this->pergunta()]]],
            ['criar_perguntas', ['perguntas' => array_fill(0, 101, $this->pergunta())]],
            ['criar_perguntas', ['perguntas' => [array_replace($this->pergunta(), ['tema' => 'ausente'])]]],
        ];
        foreach ($operacoes as [$nome, $argumentos]) {
            try {
                $this->ferramentas->executar($nome, $argumentos);
                self::fail('A operação inválida precisa ser recusada: ' . $nome . ' ' . json_encode($argumentos));
            } catch (DomainException) {
                self::assertTrue(true);
            }
        }
    }
}
