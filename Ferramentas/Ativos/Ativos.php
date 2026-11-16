<?php

/**
 * Processa os ativos próprios e confere a correspondência com os arquivos entregues.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

$raiz = dirname(__DIR__, 2);
$argumentos = $_SERVER['argv'] ?? [];
$conferir = ($argumentos[1] ?? '') === '--conferir';
if (count($argumentos) > 2 || (isset($argumentos[1]) && !$conferir)) {
    fwrite(STDERR, "Use php Ferramentas/Ativos/Ativos.php [--conferir].\n");
    exit(2);
}
$css = file_get_contents($raiz . '/Publico/Ativos/CSS/Principal.css');
$css = preg_replace('~/\*.*?\*/~s', '', $css);
$css = preg_replace('/\s+/', ' ', $css);
$css = trim(preg_replace('/\s*([{}:;,])\s*/', '$1', $css)) . "\n";
$javascript = file_get_contents($raiz . '/Publico/Ativos/JavaScript/Principal.js');
$javascript = preg_replace('~/\*.*?\*/~s', '', $javascript);
$javascript = implode('', array_map('trim', explode("\n", trim($javascript))));
$arquivos = ['Publico/Ativos/CSS/Principal.min.css' => $css, 'Publico/Ativos/JavaScript/Principal.min.js' => $javascript];
foreach ($arquivos as $arquivo => $conteudo) {
    if ($conferir) {
        if (file_get_contents($raiz . '/' . $arquivo) !== $conteudo) {
            fwrite(STDERR, "Ativo desatualizado: " . $arquivo . "\n");
            exit(1);
        }
    } else {
        file_put_contents($raiz . '/' . $arquivo, $conteudo);
    }
}
echo $conferir ? "Ativos conferidos.\n" : "Ativos processados.\n";
