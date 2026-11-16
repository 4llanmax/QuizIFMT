<?php

/**
 * Confere pontuação, prazo, repetição e encerramento da partida.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\Site;

use Aplicacao\Site\Entidades\Partida;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PartidaTeste extends TestCase
{
    private function perguntas(): array
    {
        return [
            ['tema' => 'geografia', 'enunciado' => 'Capital?', 'alternativas' => ['A', 'B', 'C', 'D', 'E'], 'correta' => 1],
            ['tema' => 'historia', 'enunciado' => 'Ano?', 'alternativas' => ['F', 'G', 'H', 'I', 'J'], 'correta' => 0],
        ];
    }

    #[Test]
    public function ocultaGabaritoMantemPrazoAvancaSomenteAposConfirmacao(): void
    {
        $partida = new Partida('identificador', $this->perguntas(), 45, 10, 1000);
        self::assertArrayNotHasKey('correta', $partida->publicar(2000)['pergunta']);
        self::assertSame(44000, $partida->publicar(2000)['restante']);
        $partida->responder(0, 1, 4000);
        self::assertSame(1, $partida->estado()['acertos']);
        self::assertSame(3000, $partida->estado()['tempo']);
        self::assertSame('correta', $partida->estado()['situacao']);
        self::assertSame('A', $partida->publicar(5000)['pergunta']['alternativas'][0]);
        $partida->avancar(9000);
        self::assertSame(45000, $partida->publicar(9000)['restante']);
        self::assertSame(1, $partida->estado()['indice']);
        $partida->responder(1, 0, 10000);
        self::assertSame('vitoria', $partida->estado()['situacao']);
        self::assertSame(4000, $partida->estado()['tempo']);
        self::assertSame(['geografia' => [1, 1], 'historia' => [1, 1]], $partida->estado()['temas']);
        self::assertSame($partida->estado(), Partida::restaurar($partida->estado())->estado());
    }

    #[Test]
    public function erroNaoSomaTempoNemAcertos(): void
    {
        $partida = new Partida('id', $this->perguntas(), 45, 10, 1000);
        $partida->responder(0, 2, 2500);
        self::assertSame('erro', $partida->estado()['situacao']);
        self::assertSame(0, $partida->estado()['tempo']);
        self::assertSame('B', $partida->publicar(2500)['respostaCorreta']);
        self::assertSame(['geografia' => [0, 1]], $partida->estado()['temas']);
    }

    #[Test]
    public function prazoExatoExpiraMesmoComAlternativaCorreta(): void
    {
        $partida = new Partida('id', $this->perguntas(), 45, 10, 1000);
        $partida->responder(0, 1, 46000);
        self::assertSame('tempo', $partida->estado()['situacao']);
        self::assertTrue($partida->terminou());
    }

    #[Test]
    public function abandonoEPrazoSaoConferidosNoServidor(): void
    {
        $partida = new Partida('id', $this->perguntas(), 45, 10, 1000);
        $partida->expirar(45999);
        self::assertFalse($partida->terminou());
        $partida->expirar(46000);
        self::assertSame('tempo', $partida->estado()['situacao']);
        $partida->expirar(47000);
        $outra = new Partida('id', $this->perguntas(), 45, 10, 1000);
        $outra->abandonar();
        self::assertSame('abandono', $outra->estado()['situacao']);
        $outra->abandonar();
    }

    #[Test]
    public function recusaReenvioIndiceAntigoEAvancoIndevido(): void
    {
        $partida = new Partida('id', $this->perguntas(), 45, 10, 1000);
        foreach ([[-1, 1], [0, -1], [0, 5], [1, 1]] as [$indice, $alternativa]) {
            try {
                $partida->responder($indice, $alternativa, 2000);
                self::fail('A entrada inválida precisa ser recusada.');
            } catch (DomainException) {
                self::assertSame(0, $partida->estado()['acertos']);
            }
        }
        try {
            $partida->avancar(2000);
            self::fail('O avanço precisa exigir resposta correta.');
        } catch (DomainException) {
            self::assertSame(0, $partida->estado()['indice']);
        }
        $partida->responder(0, 1, 2000);
        $this->expectException(DomainException::class);
        $partida->responder(0, 1, 2200);
    }
}
