<?php

/**
 * Representa status, corpo e cabeçalhos sem emitir saída durante a composição.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Transportes;

final readonly class Resposta
{
    public function __construct(public int $status, public string $corpo = '', public array $cabecalhos = [])
    {
    }

    /** Emite os cabeçalhos comuns de proteção e os definidos pela operação. */
    public function enviar(): void
    {
        \http_response_code($this->status);
        $cabecalhos = \array_replace([
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'same-origin',
            'X-Frame-Options' => 'DENY',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self'; font-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'",
        ], $this->cabecalhos);
        foreach ($cabecalhos as $nome => $valor) {
            \header($nome . ': ' . $valor);
        }
        echo $this->corpo;
    }
}
