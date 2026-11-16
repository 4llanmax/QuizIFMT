<?php

/**
 * Compõe a página de conexão MCP com a autorização compartilhada entre Painel e API.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Painel\Provedores;

use Aplicacao\Nucleo\Comuns\Classes\RoteadorHTTP;
use Aplicacao\Nucleo\Servicos\OAuth;
use Aplicacao\Nucleo\Transportes\Resposta;

final class ConexaoMCP
{
    public function __construct(private OAuth $oauth, private string $urlBase)
    {
    }

    public function construir(): RoteadorHTTP
    {
        $controle = new \Aplicacao\Painel\Controles\ConexaoMCP($this->oauth, $this->urlBase);
        $rotas = [];
        foreach (require \dirname(__DIR__) . '/Rotas/MCP.php' as $caminho) {
            $rotas[$caminho] = static function (array $servidor, string $corpo, array $campos, array &$sessao, int $agora) use ($controle): Resposta {
                return $controle->responder($servidor, $corpo, $campos, $sessao, $agora);
            };
        }
        return new RoteadorHTTP($rotas);
    }
}
