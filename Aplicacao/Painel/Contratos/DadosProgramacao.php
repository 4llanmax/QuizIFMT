<?php

/**
 * Declara a persistência consumida pela administração das rodadas.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Painel\Contratos;

interface DadosProgramacao
{
    public function obter(): array;
    public function padrao(): array;
    public function capacidades(): array;
    public function salvar(array $configuracao): void;
}
