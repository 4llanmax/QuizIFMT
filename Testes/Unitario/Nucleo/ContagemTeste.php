<?php

/**
 * Confere o limite de acesso usando somente o contrato de persistência.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\Nucleo;

use Aplicacao\Nucleo\Contratos\ContagemAcessos;
use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use Aplicacao\Nucleo\Servicos\Acesso;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ContagemTeste extends TestCase
{
    #[Test]
    public function recusaExcessoSemDependerDeManipuladorConcreto(): void
    {
        $dados = $this->createMock(ContagemAcessos::class);
        $dados->expects(self::once())->method('registrar')->with(hash('sha256', 'origem'), 1000)->willReturn(11);
        try {
            (new Acesso($dados))->conferir('origem', 1000);
            self::fail('O excesso precisa ser recusado.');
        } catch (FalhaHTTP $falha) {
            self::assertSame(429, $falha->status);
        }
    }
}
