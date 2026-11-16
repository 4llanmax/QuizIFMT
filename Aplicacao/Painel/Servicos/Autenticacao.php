<?php

/**
 * Confere o segredo administrativo configurado fora da pasta pública.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Painel\Servicos;

use Aplicacao\Nucleo\Servicos\Acesso;
use DomainException;

final class Autenticacao
{
    public function __construct(private string $hash, private Acesso $acesso)
    {
    }

    public function entrar(mixed $senha, string $origem, int $agora): void
    {
        $this->acesso->conferir('administracao:' . $origem, $agora);
        if ($this->hash === '' || !\is_string($senha) || \strlen($senha) > 72 || !\password_verify($senha, $this->hash)) {
            throw new DomainException('Acesso administrativo indisponível ou senha incorreta.');
        }
    }
}
