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

$construir = static function (PDO $banco, Parametros $parametros): Web {
    $areas = [];
    if ($parametros->obter('mcpURLBase') !== '') {
        $api = new Aplicacao\API\Provedores\Atendimento($banco, $parametros);
        $integracao = $api->construir();
        $areas = [
            '/.well-known' => $integracao,
            '/oauth' => $integracao,
            '/mcp' => $integracao,
            '/admin/mcp' => (new Aplicacao\Painel\Provedores\ConexaoMCP($api->oauth(), $parametros->obter('mcpURLBase')))->construir(),
        ];
    }
    $areas += [
        '/admin' => (new Aplicacao\Painel\Provedores\Atendimento($banco, $parametros))->construir(),
        '/' => (new Aplicacao\Site\Provedores\Atendimento($banco, $parametros))->construir(),
    ];
    return new Web($areas, $parametros->obter('urlPrefixo'));
};
$adicionais = Aplicacao\Painel\Provedores\Atendimento::parametros() + Aplicacao\API\Provedores\Atendimento::parametros();
(new Aplicacao\Nucleo\Comuns\Classes\EntradaHTTP(dirname(__DIR__), $construir, $adicionais))->executar();
