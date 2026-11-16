<?php

/**
 * Declara a contagem técnica de tentativas independente do mecanismo persistente.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Contratos;

interface ContagemAcessos
{
    public function registrar(string $chave, int $agora): int;
}
