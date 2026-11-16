<?php

/**
 * Inclui uma visão selecionada internamente com dados explícitos e minifica o HTML.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Comuns\Classes;

final class Renderizacao
{
    private array $visoes = [];
    /** O caminho é definido pelo controle, nunca por um campo recebido do navegador. */
    public function renderizar(string $arquivo, array $dados): string
    {
        $escapar = static fn (mixed $valor): string => \htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $incluir = function (string $caminho, array $contexto) use ($escapar, &$incluir): void {
            $this->visoes[$caminho] ??= require $caminho;
            ($this->visoes[$caminho])($contexto, $escapar, $incluir);
        };
        \ob_start();
        try {
            $incluir($arquivo, $dados);
            return \preg_replace('/>\s+</u', '><', \trim(\ob_get_contents()));
        } finally {
            \ob_end_clean();
        }
    }
}
