<?php

/**
 * Registra o autocarregamento exclusivo das declarações da suíte de testes.
 * Redator: Allan Max
 * Data de criação: 11/11/2026
 * Status: Revisado
 * Data do Status: 11/11/2026
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
