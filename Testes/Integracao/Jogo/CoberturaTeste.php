<?php

/**
 * Verifica que o conferidor recusa lacunas sem depender de relatórios de outra execução.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Integracao\Jogo;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CoberturaTeste extends TestCase
{
    #[Test]
    public function aprovaCoberturaCompletaERecusaLinhaOuRamoAusente(): void
    {
        $arquivo = tempnam(sys_get_temp_dir(), 'quiz-cobertura-');
        try {
            foreach ([true, false] as $completa) {
                $dados = ['metadados' => str_repeat('x', 2000000), 'codeCoverage' => (object) [
                    'lineCoverage' => ['Ficticio.php' => [1 => [0 => 1], 2 => $completa ? [0 => 1] : []]],
                    'functionCoverage' => ['Ficticio.php' => ['funcao' => (object) ['branches' => [(object) ['line_start' => 1, 'hit' => [0 => 1]], (object) ['line_start' => 2, 'hit' => $completa ? [0 => 1] : []]]]]],
                ]];
                file_put_contents($arquivo, "<?php\nreturn \\unserialize(<<<'END_OF_COVERAGE_SERIALIZATION'\n" . serialize($dados) . "\nEND_OF_COVERAGE_SERIALIZATION\n);\n");
                file_put_contents($arquivo, str_replace("\n", "\r\n", file_get_contents($arquivo)));
                $processo = proc_open([PHP_BINARY, '-d', 'xdebug.log=', dirname(__DIR__, 3) . '/Ferramentas/PHPUnit/ConferenciaCobertura.php', $arquivo], [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $canais);
                self::assertIsResource($processo);
                fclose($canais[0]);
                $saida = stream_get_contents($canais[1]);
                $erro = stream_get_contents($canais[2]);
                fclose($canais[1]);
                fclose($canais[2]);
                self::assertSame($completa ? 0 : 1, proc_close($processo), $saida . $erro);
                self::assertSame('', $erro);
                self::assertStringContainsString('Linhas:', $saida);
                self::assertStringContainsString('Ramos:', $saida);
            }
        } finally {
            unlink($arquivo);
        }
    }
}
