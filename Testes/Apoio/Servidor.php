<?php

/**
 * Encaminha a prévia PHP local sem liberar originais nem metadados.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/Aplicacao/Nucleo/Comuns/Funcoes/AutoCarregamento.php';
$configuracao = file_get_contents(dirname(__DIR__, 2) . '/Publico/.htaccess');
if (!preg_match('~^RewriteRule \^\(\?:([^\r\n]+)\)\$ - \[END\]$~m', $configuracao, $regra)) {
    throw new RuntimeException('A configuração de entrega não contém a lista estática esperada.');
}
$padrao = '~^(?:' . $regra[1] . ')$~D';
$entrega = new \Aplicacao\Nucleo\Comuns\Classes\Entrega('/');
$caminho = $entrega->caminho($_SERVER['REQUEST_URI']);
if (preg_match($padrao, ltrim($caminho, '/')) === 1) {
    return false;
}
require dirname(__DIR__, 2) . '/Publico/index.php';
