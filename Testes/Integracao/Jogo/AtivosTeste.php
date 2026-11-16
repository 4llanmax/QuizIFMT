<?php

/**
 * Confere manifesto, fontes locais, minificação e limites de entrega.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Integracao\Jogo;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AtivosTeste extends TestCase
{
    #[Test]
    public function confereManifestoAtivosProcessadosEDeclaracaoDeTipo(): void
    {
        $raiz = dirname(__DIR__, 3);
        $manifesto = json_decode(file_get_contents($raiz . '/Publico/Manifesto.webmanifest'), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('pt-BR', $manifesto['lang']);
        self::assertSame('./', $manifesto['scope']);
        self::assertFileExists($raiz . '/Publico/' . $manifesto['icons'][0]['src']);
        self::assertStringContainsString('AddType application/manifest+json .webmanifest', file_get_contents($raiz . '/Publico/.htaccess'));
        $configuracao = file_get_contents($raiz . '/Publico/.htaccess');
        self::assertSame(1, preg_match('~^RewriteRule \^\(\?:([^\r\n]+)\)\$ - \[END\]$~m', $configuracao, $regra));
        $padrao = '~^(?:' . $regra[1] . ')$~D';
        foreach (['Ativos/CSS/Principal.min.css', 'Ativos/JavaScript/Principal.min.js', 'Ativos/Imagens/Bomba.svg', 'Manifesto.webmanifest'] as $recurso) {
            self::assertSame(1, preg_match($padrao, $recurso));
        }
        foreach (['Ativos/CSS/Principal.css', 'Ativos/JavaScript/Principal.js', 'Ativos/Fontes/Manrope/OFL.txt', 'Ativos/../index.php', 'Configuracoes/Aplicacao/.env'] as $recurso) {
            self::assertSame(0, preg_match($padrao, $recurso));
        }
        $css = file_get_contents($raiz . '/Publico/Ativos/CSS/Principal.min.css');
        self::assertStringNotContainsString('/*', $css);
        self::assertStringNotContainsString('https://', $css);
        self::assertStringNotContainsString('<metadata', file_get_contents($raiz . '/Publico/Ativos/Imagens/Bomba.svg'));
        preg_match_all('~url\(([^)]+)\)~', $css, $fontes);
        self::assertCount(3, $fontes[1]);
        foreach ($fontes[1] as $fonte) {
            self::assertFileExists($raiz . '/Publico/Ativos/CSS/' . $fonte);
        }
    }

    #[Test]
    public function confereQueVersoesEntreguesCorrespondemAosOriginais(): void
    {
        $raiz = dirname(__DIR__, 3);
        $processo = proc_open([PHP_BINARY, $raiz . '/Ferramentas/Ativos/Ativos.php', '--conferir'], [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $canais, $raiz);
        self::assertIsResource($processo);
        fclose($canais[0]);
        $saida = stream_get_contents($canais[1]);
        $erro = stream_get_contents($canais[2]);
        fclose($canais[1]);
        fclose($canais[2]);
        self::assertSame(0, proc_close($processo), $saida . $erro);
    }
}
