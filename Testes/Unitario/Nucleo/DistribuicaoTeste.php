<?php

/**
 * Confere a divisão equilibrada das perguntas entre categorias com acervo ativo.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\Nucleo;

use Aplicacao\Nucleo\Servicos\Distribuicao;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DistribuicaoTeste extends TestCase
{
    #[Test]
    public function divideTotalIgualmenteComRestoEIgnoraCategoriasVazias(): void
    {
        self::assertSame(['a' => 4, 'b' => 3, 'c' => 3], Distribuicao::calcular(10, ['a' => 10, 'b' => 10, 'c' => 10, 'vazia' => 0]));
        $resultado = Distribuicao::calcular(10, ['a' => 10, 'b' => 10, 'c' => 10], true);
        self::assertSame(10, array_sum($resultado));
        self::assertSame(1, max($resultado) - min($resultado));
        self::assertSame(['a' => 2, 'b' => 2], Distribuicao::calcular(4, ['a' => 2, 'b' => 2]));
    }

    #[Test]
    public function recusaAcervoVazioOuInsuficienteParaCotaIgual(): void
    {
        foreach ([[10, []], [0, ['a' => 10]], [10, ['a' => 1, 'b' => 20]], [5, ['a' => 2, 'b' => 2]]] as [$total, $capacidades]) {
            try {
                Distribuicao::calcular($total, $capacidades);
                self::fail('A distribuição inviável precisa ser recusada.');
            } catch (DomainException) {
                self::assertTrue(true);
            }
        }
    }
}
