<?php

/**
 * Confere apresentação, escape e variantes de todas as visões do jogo.
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

final class VisoesTeste extends TestCase
{
    #[Test]
    public function apresentaVariantesDeContaRankingPartidaEHistoricoComEscape(): void
    {
        $renderizacao = new Renderizacao();
        $raiz = dirname(__DIR__, 3);
        $configuracao = ['total' => 20, 'segundos' => 45, 'alerta' => 10, 'distribuicao' => ['geografia' => 4, 'historia' => 4, 'ciencias' => 3, 'esportes' => 3, 'cinema' => 3, 'musica' => 3]];
        $jogador = ['identificador' => 1, 'nome' => '<Teste>', 'criado_em' => '2026-11-16 00:00:00'];
        $registro = ['identificador' => 1, 'nome' => '<Teste>', 'registrado' => true, 'acertos' => 20, 'tempo' => 1000, 'total' => 20];
        $historico = [['acertos' => 20, 'tempo' => 1000, 'total' => 20, 'nivel' => 20, 'motivo' => 'vitoria', 'criado_em' => '2026-11-16 00:00:00'], ['acertos' => 1, 'tempo' => 2000, 'total' => 20, 'nivel' => 2, 'motivo' => 'erro', 'criado_em' => '2026-11-16 00:01:00']];
        $base = ['prefixo' => '/quiz/', 'csrf' => 'ficticio', 'configuracao' => $configuracao, 'jogador' => null, 'conta' => false, 'ranking' => [], 'historico' => [], 'temas' => [], 'posicao' => null, 'melhor' => null, 'partida' => null, 'capacidades' => array_fill_keys(array_keys($configuracao['distribuicao']), 8)];
        foreach (['inicio', 'entrar', 'cadastro', 'regras', 'privacidade', 'ranking', 'rodada'] as $pagina) {
            foreach ([[], ['jogador' => $jogador], ['jogador' => $jogador, 'conta' => true, 'ranking' => [$registro, array_replace($registro, ['identificador' => 2, 'registrado' => false]), array_replace($registro, ['identificador' => 3])], 'posicao' => 1]] as $alteracao) {
                $html = $renderizacao->renderizar($raiz . '/Aplicacao/Site/Visoes/Estruturas/Principal.php', array_replace($base, $alteracao, ['pagina' => $pagina]));
                self::assertStringContainsString('BombQUIZ', $html);
                self::assertStringNotContainsString('<Teste>', $html);
                self::assertDoesNotMatchRegularExpression('/>\s+</', $html);
            }
        }
        foreach ([[], ['historico' => $historico, 'temas' => ['geografia' => [1, 2]], 'posicao' => 1, 'melhor' => $registro], ['historico' => $historico, 'melhor' => $registro]] as $alteracao) {
            $html = $renderizacao->renderizar($raiz . '/Aplicacao/Site/Visoes/Estruturas/Principal.php', array_replace($base, ['pagina' => 'conta', 'jogador' => $jogador, 'conta' => true], $alteracao));
            self::assertStringContainsString('&lt;Teste&gt;', $html);
            self::assertStringContainsString('Desempenho por tema', $html);
        }
        $partida = ['identificador' => 'ficticio', 'indice' => 1, 'total' => 20, 'acertos' => 1, 'tempo' => 1200, 'segundos' => 45, 'alerta' => 10, 'restante' => 44000, 'pergunta' => ['tema' => 'geografia', 'enunciado' => '<Pergunta>', 'alternativas' => ['A', 'B', 'C', 'D', 'E']], 'respostaCorreta' => '<Resposta>'];
        foreach (['jogando', 'correta', 'vitoria', 'erro', 'tempo', 'abandono'] as $situacao) {
            foreach ([false, true] as $conta) {
                $html = $renderizacao->renderizar($raiz . '/Aplicacao/Site/Visoes/Estruturas/Principal.php', array_replace($base, ['pagina' => in_array($situacao, ['jogando', 'correta'], true) ? 'jogo' : 'resultado', 'jogador' => $jogador, 'conta' => $conta, 'partida' => $partida + ['situacao' => $situacao]]));
                self::assertStringNotContainsString('<Pergunta>', $html);
                self::assertStringNotContainsString('<Resposta>', $html);
            }
        }
        $base['capacidades']['geografia'] = 12;
        foreach ([false, true] as $autenticado) {
            $html = $renderizacao->renderizar($raiz . '/Aplicacao/Painel/Visoes/Paginas/Programacao.php', $base + ['autenticado' => $autenticado]);
            self::assertStringContainsString($autenticado ? 'SALVAR PROGRAMAÇÃO' : 'Senha administrativa', $html);
            if ($autenticado) {
                self::assertStringContainsString('12 perguntas no acervo', $html);
            }
        }
    }
}
