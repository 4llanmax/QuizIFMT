<?php

/**
 * Verifica nomes, credenciais e programação das rodadas.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\Site;

use Aplicacao\Site\Validacoes\Entrada;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EntradaTeste extends TestCase
{
    #[Test]
    public function normalizaNomesEValidaCadastro(): void
    {
        self::assertSame('joao', Entrada::normalizar(' JOÃO '));
        self::assertSame('João Silva', Entrada::nome(' João   Silva ', false));
        self::assertSame('João_1', Entrada::nome('João_1', true));
        self::assertSame('teste@example.invalid', Entrada::email(' TESTE@example.invalid '));
        self::assertSame('12345678', Entrada::senha('12345678'));
        self::assertSame(0, Entrada::inteiro('0', 0, 4));
    }

    #[Test]
    public function recusaCamposInvalidos(): void
    {
        foreach (
            [fn () => Entrada::nome([], false), fn () => Entrada::nome('a', false),
            fn () => Entrada::nome(str_repeat('a', 17), true), fn () => Entrada::nome('tem espaço', true),
            fn () => Entrada::nome('<script>', false), fn () => Entrada::email('invalido'),
            fn () => Entrada::senha('curta'), fn () => Entrada::senha(str_repeat('a', 73)),
            fn () => Entrada::inteiro([], 0, 4), fn () => Entrada::inteiro('1x', 0, 4),
            fn () => Entrada::inteiro('5', 0, 4), fn () => Entrada::inteiro('-1', 0, 4)] as $operacao
        ) {
            try {
                $operacao();
                self::fail('O campo inválido precisa ser recusado.');
            } catch (DomainException) {
                self::assertTrue(true);
            }
        }
    }
}
