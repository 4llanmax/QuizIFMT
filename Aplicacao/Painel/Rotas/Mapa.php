<?php

/**
 * Declara as associações HTTP exclusivas da administração.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

return [
    '/admin' => ['GET' => 'inicio'],
    '/admin/entrar' => ['POST' => 'entrar'],
    '/admin/salvar' => ['POST' => 'salvar'],
    '/admin/restaurar' => ['POST' => 'restaurar'],
    '/admin/sair' => ['POST' => 'sair'],
];
