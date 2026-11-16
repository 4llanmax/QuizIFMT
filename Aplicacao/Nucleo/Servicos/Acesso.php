<?php

/**
 * Limita tentativas de autenticação e cadastro com armazenamento compartilhado entre sessões.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Servicos;

use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use Aplicacao\Nucleo\Contratos\ContagemAcessos;

final class Acesso
{
    public function __construct(private ContagemAcessos $tentativas)
    {
    }

    public function conferir(string $origem, int $agora): void
    {
        if ($this->tentativas->registrar(\hash('sha256', $origem), $agora) > 10) {
            throw new FalhaHTTP(429, 'Muitas tentativas. Aguarde 15 minutos antes de tentar novamente.');
        }
    }
}
