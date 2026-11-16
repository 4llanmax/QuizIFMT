<?php

/**
 * Confere normalização e isolamento dos prefixos públicos.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\Nucleo;

use Aplicacao\Nucleo\Comuns\Classes\Entrega;
use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EntregaTeste extends TestCase
{
    #[Test]
    public function reconheceSomenteCaminhosAplicacao(): void
    {
        self::assertSame('/ranking', (new Entrega('/quiz/'))->caminho('/quiz/ranking?pagina=1'));
        self::assertSame('/', (new Entrega('/'))->caminho('/index.php'));
        self::assertSame('/', (new Entrega('/'))->caminho('/'));
        $this->expectException(FalhaHTTP::class);
        (new Entrega('/quiz/'))->caminho('/outra/ranking');
    }
}
