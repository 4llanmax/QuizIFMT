<?php

/**
 * Prepara somente o banco e os parâmetros fictícios do servidor descartável de validação.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

require __DIR__ . '/Preparacao.php';

$apoio = new \Testes\Apoio\BancoIsolado(getenv());
$apoio->criar();
$linhas = [];
foreach ($apoio->parametros() as $chave => $valor) {
    $linhas[] = $chave . '=' . $valor;
}
$linhas[] = 'URL_PREFIXO=/';
$linhas[] = 'ADMIN_SENHA_HASH=';
$arquivo = dirname(__DIR__, 2) . '/Configuracoes/Aplicacao/.env';
if (file_put_contents($arquivo, implode("\n", $linhas) . "\n") === false) {
    $apoio->limpar();
    throw new RuntimeException('Não foi possível preparar o servidor descartável.');
}
echo "Servidor descartável preparado; o banco permanece até o término do serviço de integração.\n";
