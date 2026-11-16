<?php

/**
 * Registra o autocarregamento exclusivo das declarações da suíte de testes.
 * Redator: Allan Max
 * Data de criação: 11/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

// O prefixo Testes corresponde somente a esta árvore, sem carregar a aplicação.
spl_autoload_register(static function (string $classe): void {
    $prefixo = 'Testes\\';
    if (!str_starts_with($classe, $prefixo)) {
        return;
    }
    $relativo = str_replace('\\', '/', substr($classe, strlen($prefixo)));
    $arquivo = dirname(__DIR__) . '/' . $relativo . '.php';
    if (is_file($arquivo)) {
        require_once $arquivo;
    }
});

// A aplicação registra seu próprio prefixo sem misturar as duas árvores.
require_once dirname(__DIR__, 2) . '/Aplicacao/Nucleo/Comuns/Funcoes/AutoCarregamento.php';
