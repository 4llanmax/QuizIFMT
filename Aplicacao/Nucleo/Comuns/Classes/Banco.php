<?php

/**
 * Abre conexão PDO com MariaDB, UTF-8 e instruções preparadas nativas.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Comuns\Classes;

use PDO;

final class Banco
{
    public function __construct(private Parametros $parametros)
    {
    }

    /** A conexão é independente da área e não aplica esquemas automaticamente. */
    public function conectar(): PDO
    {
        return new PDO(
            'mysql:host=' . $this->parametros->obter('bancoHost') . ';port=' . $this->parametros->obter('bancoPorta') . ';dbname=' . $this->parametros->obter('bancoNome') . ';charset=utf8mb4',
            $this->parametros->obter('bancoUsuario'),
            $this->parametros->obter('bancoSenha'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_TIMEOUT => 5]
        );
    }
}
