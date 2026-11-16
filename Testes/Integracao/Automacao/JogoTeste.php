<?php

/**
 * Confere as etapas de validação acrescentadas pela implementação do jogo.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Integracao\Automacao;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JogoTeste extends TestCase
{
    #[Test]
    public function automacaoExecutaSomenteSuaSuiteEAplicacaoExigeCoberturaEAtivos(): void
    {
        $raiz = dirname(__DIR__, 3);
        require_once $raiz . '/.github/Verificacoes/Validacao.php';
        $comandos = [];
        $validacao = new \Validacao($raiz, static function (array $comando) use (&$comandos): int {
            $comandos[] = $comando;
            return 0;
        });
        self::assertSame(0, $validacao->executar('testes'));
        self::assertContains('--testsuite', $comandos[0]);
        self::assertContains('Automacao', $comandos[0]);
        self::assertContains('--no-coverage', $comandos[0]);
        foreach (['cobertura', 'ativos', 'javascript'] as $etapa) {
            self::assertSame(0, $validacao->executar($etapa));
        }
        self::assertContains('Ferramentas/PHPUnit/ConferenciaCobertura.php', $comandos[1]);
        self::assertContains('Ferramentas/Ativos/Ativos.php', $comandos[2]);
        self::assertContains('Testes/Apoio/ConferenciaJavaScript.mjs', $comandos[3]);
    }

    #[Test]
    public function fluxoPreparaMariaDbNaVersaoExigidaSemAlterarGatilho(): void
    {
        $raiz = dirname(__DIR__, 3);
        $fluxo = file_get_contents($raiz . '/.github/workflows/Validacao.yml');
        self::assertStringContainsString('image: mariadb:10.6.28', $fluxo);
        self::assertStringContainsString('TESTE_BANCO_HOST: mariadb', $fluxo);
        self::assertStringContainsString("->executar('cobertura')", $fluxo);
        self::assertStringContainsString("->executar('javascript')", $fluxo);
        self::assertStringContainsString('branches: [Validacao]', $fluxo);
        self::assertStringNotContainsString('branches: [Principal]', $fluxo);
    }
}
