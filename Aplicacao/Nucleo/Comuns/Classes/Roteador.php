<?php

/**
 * Relaciona descrições fornecidas pelas áreas às respectivas operações.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Comuns\Classes;

use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use Aplicacao\Nucleo\Transportes\Resposta;
use Closure;

final class Roteador
{
    public function __construct(private array $rotas, private Closure $controle)
    {
    }

    public function atender(string $metodo, string $caminho, array $campos, array &$sessao, int $agora, string $origem): Resposta
    {
        if (!isset($this->rotas[$caminho])) {
            throw new FalhaHTTP(404, 'Página não encontrada.');
        }
        if (!isset($this->rotas[$caminho][$metodo])) {
            throw new FalhaHTTP(405, 'Método não permitido para esta página.');
        }
        return ($this->controle)($this->rotas[$caminho][$metodo], $campos, $sessao, $agora, $origem);
    }
}
