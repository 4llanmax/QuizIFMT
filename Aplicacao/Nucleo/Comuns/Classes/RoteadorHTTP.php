<?php

/**
 * Encaminha corpo e contexto HTTP aos transportes descritos pelas áreas.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Comuns\Classes;

use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use Aplicacao\Nucleo\Transportes\Resposta;

final class RoteadorHTTP
{
    public function __construct(private array $rotas)
    {
    }

    public function atender(string $caminho, array $servidor, string $corpo, array $campos, array &$sessao, int $agora): Resposta
    {
        if (!isset($this->rotas[$caminho])) {
            throw new FalhaHTTP(404, 'Página não encontrada.');
        }
        return ($this->rotas[$caminho])($servidor, $corpo, $campos, $sessao, $agora);
    }
}
