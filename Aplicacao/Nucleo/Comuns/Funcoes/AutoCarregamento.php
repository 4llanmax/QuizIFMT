<?php

/**
 * Registra a correspondência PSR-4 exclusiva da aplicação.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Comuns\Funcoes;

\spl_autoload_register(static function (string $classe): void {
    $prefixo = 'Aplicacao\\';
    if (!\str_starts_with($classe, $prefixo)) {
        return;
    }
    $arquivo = \dirname(__DIR__, 3) . '/' . \str_replace('\\', '/', \substr($classe, \strlen($prefixo))) . '.php';
    if (\is_file($arquivo)) {
        require_once $arquivo;
    }
});
