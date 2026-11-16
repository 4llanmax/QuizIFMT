<?php

/**
 * Confere linhas e ramos das classes, funções, visões e entrada no relatório do PHPUnit.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

$caminho = $argv[1] ?? dirname(__DIR__, 2) . '/Dados/Arquivos/Cache/CoberturaPHP/Cobertura.php';
$conteudo = file_get_contents($caminho);
$inicio = strpos($conteudo, "<<<'END_OF_COVERAGE_SERIALIZATION'");
$fim = strrpos($conteudo, "\nEND_OF_COVERAGE_SERIALIZATION");
if ($inicio === false || $fim === false) {
    fwrite(STDERR, "Relatório de cobertura ausente ou incompatível.\n");
    exit(2);
}
$inicio = strpos($conteudo, "\n", $inicio) + 1;
$serializado = rtrim(substr($conteudo, $inicio, $fim - $inicio), "\r\n");
// Nenhuma classe do relatório é instanciada; o conferidor lê somente dados.
$relatorio = unserialize($serializado, ['allowed_classes' => false]);
$linhas = [];
$funcoes = [];
foreach ((array) $relatorio['codeCoverage'] as $chave => $valor) {
    if (str_ends_with($chave, 'lineCoverage')) {
        $linhas = $valor;
    }
    if (str_ends_with($chave, 'functionCoverage')) {
        $funcoes = $valor;
    }
}
$totalLinhas = 0;
$linhasCobertas = 0;
$totalRamos = 0;
$ramosCobertos = 0;
$faltas = [];
foreach ($linhas as $arquivo => $registros) {
    foreach ($registros as $numero => $execucoes) {
        if (!is_array($execucoes)) {
            continue;
        }
        $totalLinhas++;
        if ($execucoes !== []) {
            $linhasCobertas++;
        } else {
            $faltas[] = $arquivo . ':' . $numero . ' linha não exercitada';
        }
    }
}
foreach ($funcoes as $arquivo => $declaracoes) {
    foreach ($declaracoes as $nome => $funcao) {
        $ramos = ((array) $funcao)['branches'];
        // Um bloco principal único de declarações não contém decisão selecionável.
        if ($nome === '{main}' && count($ramos) === 1) {
            continue;
        }
        foreach ($ramos as $ramo) {
            $ramo = (array) $ramo;
            $totalRamos++;
            if ($ramo['hit'] !== []) {
                $ramosCobertos++;
            } else {
                $faltas[] = $arquivo . ':' . $ramo['line_start'] . ' ramo não exercitado';
            }
        }
    }
}
echo 'Linhas: ', $linhasCobertas, '/', $totalLinhas, "\n";
echo 'Ramos: ', $ramosCobertos, '/', $totalRamos, "\n";
foreach ($faltas as $falta) {
    echo $falta, "\n";
}
if ($totalLinhas === 0 || $totalRamos === 0) {
    fwrite(STDERR, "O relatório não contém todas as métricas exigidas.\n");
    exit(2);
}
exit($faltas === [] ? 0 : 1);
