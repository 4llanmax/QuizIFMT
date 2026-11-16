<?php

/**
 * Confere ausência de áreas e carregamento de nomes sem correspondência.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\Nucleo;

use Aplicacao\Nucleo\Comuns\Classes\Web;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EncaminhamentoTeste extends TestCase
{
    #[Test]
    public function recusaAreaSemDescricaoENaoCarregaDeclaracaoDesconhecida(): void
    {
        $sessao = [];
        self::assertSame(404, (new Web([]))->atender('GET', '/', [], $sessao, 1000, 'teste')->status);
        self::assertFalse(class_exists('FornecedorFicticio\\Ausente'));
        self::assertFalse(class_exists('Aplicacao\\Ausente'));
    }
}
