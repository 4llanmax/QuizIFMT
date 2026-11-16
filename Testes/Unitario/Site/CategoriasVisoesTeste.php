<?php

/**
 * Confere apresentação segura de categorias cadastradas e distribuição automática.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\Site;

use Aplicacao\Nucleo\Comuns\Classes\Renderizacao;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CategoriasVisoesTeste extends TestCase
{
    #[Test]
    public function apresentaCategoriaNovaComEscapeEInformaEquilibrioAutomatico(): void
    {
        $configuracao = ['total' => 5, 'segundos' => 45, 'alerta' => 10,
            'distribuicao' => ['tecnologia' => 5], 'nomesTemas' => ['tecnologia' => 'Tecnologia <nova>']];
        $base = ['prefixo' => '/', 'csrf' => 'ficticio', 'configuracao' => $configuracao,
            'jogador' => null, 'conta' => false, 'ranking' => [], 'historico' => [],
            'temas' => [], 'posicao' => null, 'melhor' => null, 'partida' => null];
        $renderizacao = new Renderizacao();
        $estrutura = dirname(__DIR__, 3) . '/Aplicacao/Site/Visoes/Estruturas/Principal.php';
        $html = $renderizacao->renderizar($estrutura, $base + ['pagina' => 'rodada']);
        self::assertStringContainsString('Tecnologia &lt;nova&gt;', $html);
        self::assertStringNotContainsString('Tecnologia <nova>', $html);
        self::assertStringContainsString('equilibrada', $html);
        $html = $renderizacao->renderizar($estrutura, $base + ['pagina' => 'regras']);
        self::assertStringNotContainsString('seis temas', $html);
        $html = $renderizacao->renderizar(
            dirname(__DIR__, 3) . '/Aplicacao/Painel/Visoes/Paginas/Programacao.php',
            $base + ['autenticado' => true, 'capacidades' => ['tecnologia' => 8]]
        );
        self::assertStringContainsString('Tecnologia &lt;nova&gt;', $html);
        self::assertStringNotContainsString('name="distribuicao[', $html);
        self::assertStringContainsString('8 perguntas no acervo', $html);
    }
}
