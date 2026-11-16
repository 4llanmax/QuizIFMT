<?php

/**
 * Normaliza o caminho público segundo o prefixo configurado.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Comuns\Classes;

use Aplicacao\Nucleo\Excecoes\FalhaHTTP;

final class Entrega
{
    public function __construct(private string $prefixo)
    {
    }

    public function caminho(string $uri): string
    {
        $caminho = \rawurldecode(\explode('?', $uri, 2)[0]);
        if (!\str_starts_with($caminho, $this->prefixo) || \str_contains($caminho, "\0") || \str_contains($caminho, '\\')) {
            throw new FalhaHTTP(404, 'Página não encontrada.');
        }
        $relativo = '/' . \substr($caminho, \strlen($this->prefixo));
        return $relativo === '/index.php' ? '/' : $relativo;
    }
}
