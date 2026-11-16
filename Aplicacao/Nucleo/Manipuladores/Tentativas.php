<?php

/**
 * Conta tentativas técnicas por chave opaca em janela limitada no MariaDB.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Manipuladores;

use PDO;
use Aplicacao\Nucleo\Contratos\ContagemAcessos;

final class Tentativas implements ContagemAcessos
{
    public function __construct(private PDO $banco)
    {
    }

    /** A atualização atômica mantém a contagem mesmo com sessões e requisições concorrentes. */
    public function registrar(string $chave, int $agora): int
    {
        $limpeza = $this->banco->prepare('DELETE FROM tentativas WHERE inicio < ?');
        $limpeza->execute([$agora - 86400000]);
        $consulta = $this->banco->prepare('INSERT INTO tentativas (chave, quantidade, inicio) VALUES (?, 1, ?) ON DUPLICATE KEY UPDATE quantidade = IF(inicio <= ?, 1, quantidade + 1), inicio = IF(inicio <= ?, VALUES(inicio), inicio)');
        $consulta->execute([$chave, $agora, $agora - 900000, $agora - 900000]);
        $leitura = $this->banco->prepare('SELECT quantidade FROM tentativas WHERE chave = ?');
        $leitura->execute([$chave]);
        return (int) $leitura->fetchColumn();
    }
}
