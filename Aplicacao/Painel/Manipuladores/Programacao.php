<?php

/**
 * Lê e grava a programação no MariaDB sem alterar resultados anteriores.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Painel\Manipuladores;

use Aplicacao\Painel\Contratos\DadosProgramacao;
use PDO;

final class Programacao implements DadosProgramacao
{
    public function __construct(private PDO $banco)
    {
    }

    public function obter(): array
    {
        return \json_decode($this->banco->query('SELECT conteudo FROM configuracao WHERE identificador = 1')->fetchColumn(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function padrao(): array
    {
        return \json_decode($this->banco->query('SELECT padrao FROM configuracao WHERE identificador = 1')->fetchColumn(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function capacidades(): array
    {
        return $this->banco->query('SELECT tema, COUNT(*) FROM perguntas GROUP BY tema')->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function salvar(array $configuracao): void
    {
        $consulta = $this->banco->prepare('UPDATE configuracao SET conteudo = ? WHERE identificador = 1');
        $consulta->execute([\json_encode($configuracao, JSON_THROW_ON_ERROR)]);
    }
}
