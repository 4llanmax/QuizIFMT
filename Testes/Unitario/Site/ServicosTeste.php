<?php

/**
 * Confere operações sem dependência de banco, incluindo falhas sem efeitos persistentes.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\Site;

use Aplicacao\Site\Contratos\DadosJogo;
use Aplicacao\Site\Entidades\Partida;
use Aplicacao\Site\Servicos\Jogadores;
use Aplicacao\Site\Servicos\Rodadas;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ServicosTeste extends TestCase
{
    #[Test]
    public function recusaGravacaoPartidaEmAndamento(): void
    {
        $dados = $this->createMock(DadosJogo::class);
        $dados->expects(self::never())->method('gravar');
        $partida = new Partida('id', [['tema' => 'geografia', 'enunciado' => 'Pergunta', 'alternativas' => ['A', 'B', 'C', 'D', 'E'], 'correta' => 0]], 45, 10, 1000);
        $this->expectException(DomainException::class);
        (new Rodadas($dados))->guardar(1, $partida);
    }

    #[Test]
    public function recusaAcervoInsuficiente(): void
    {
        $dados = $this->createMock(DadosJogo::class);
        $dados->method('configuracao')->willReturn(['total' => 5, 'segundos' => 45, 'alerta' => 10, 'distribuicao' => ['geografia' => 5]]);
        $dados->expects(self::once())->method('perguntas')->willReturn([]);
        $this->expectException(DomainException::class);
        (new Rodadas($dados))->iniciar(1000);
    }

    #[Test]
    public function naoGravaCadastroComNomeReservado(): void
    {
        $dados = $this->createMock(DadosJogo::class);
        $dados->method('transacionar')->willReturnCallback(fn (callable $operacao) => $operacao());
        $dados->method('nomeReservado')->willReturn(true);
        $dados->expects(self::never())->method('cadastrar');
        $this->expectException(DomainException::class);
        (new Jogadores($dados))->cadastrar('Jogador', 'teste@example.invalid', 'senha12345', null);
    }

    #[Test]
    public function recusaRenomeacaoReservadaESessaoSemJogadorExistente(): void
    {
        $dados = $this->createMock(DadosJogo::class);
        $dados->method('transacionar')->willReturnCallback(fn (callable $operacao) => $operacao());
        $dados->method('nomeReservado')->willReturn(true);
        $dados->expects(self::never())->method('renomear');
        self::assertNull((new \Aplicacao\Site\Servicos\Consulta($dados))->jogador(1));
        $this->expectException(DomainException::class);
        (new Jogadores($dados))->renomear(1, 'Jogador');
    }

    #[Test]
    public function mostraRecordeMesmoForaDoTop100(): void
    {
        $dados = $this->createMock(DadosJogo::class);
        $dados->method('jogador')->willReturn(['identificador' => 1, 'nome' => 'Jogador']);
        $dados->method('ranking')->willReturn([]);
        $dados->expects(self::once())->method('recorde')->with(1)->willReturn(['acertos' => 10, 'tempo' => 12345]);
        $resultado = (new \Aplicacao\Site\Servicos\Consulta($dados))->apresentar(1, true);
        self::assertNull($resultado['posicao']);
        self::assertSame(10, $resultado['melhor']['acertos']);
    }
}
