<?php

/**
 * Inicializa a sessão HTTP e compõe as áreas sem executar regras de negócio.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

use Aplicacao\Nucleo\Comuns\Classes\Parametros;
use Aplicacao\Nucleo\Comuns\Classes\Web;

require_once dirname(__DIR__) . '/Aplicacao/Nucleo/Comuns/Funcoes/AutoCarregamento.php';

$construir = static fn (PDO $banco, Parametros $parametros): Web => new Web([
    '/admin' => (new Aplicacao\Painel\Provedores\Atendimento($banco, $parametros))->construir(),
    '/' => (new Aplicacao\Site\Provedores\Atendimento($banco, $parametros))->construir(),
], $parametros->obter('urlPrefixo'));
(new Aplicacao\Nucleo\Comuns\Classes\EntradaHTTP(dirname(__DIR__), $construir, Aplicacao\Painel\Provedores\Atendimento::parametros()))->executar();
