<?php

/**
 * Revoga a identidade da sessão quando a conta não existe mais.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Site\Filtros;

use Aplicacao\Site\Servicos\Consulta;

final class Identidade
{
    public function __construct(private Consulta $consulta)
    {
    }

    public function conferir(array &$sessao): void
    {
        if (($sessao['conta'] ?? false) && $this->consulta->jogador($sessao['jogador']) === null) {
            unset($sessao['jogador'], $sessao['conta'], $sessao['partida'], $sessao['jogadorPartida']);
            $sessao['renovar'] = true;
        }
    }
}
