<?php

/**
 * Constrói dependências do Site e entrega suas rotas ao mecanismo comum.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Site\Provedores;

use Aplicacao\Nucleo\Comuns\Classes\Parametros;
use Aplicacao\Nucleo\Comuns\Classes\Renderizacao;
use Aplicacao\Nucleo\Comuns\Classes\Roteador;
use Aplicacao\Nucleo\Manipuladores\Tentativas;
use Aplicacao\Nucleo\Servicos\Acesso;
use Aplicacao\Site\Controles\Jogo;
use Aplicacao\Site\Manipuladores\Acervo;
use Aplicacao\Site\Servicos\Consulta;
use Aplicacao\Site\Servicos\Jogadores;
use Aplicacao\Site\Servicos\Rodadas;
use PDO;

final class Atendimento
{
    public function __construct(private PDO $banco, private Parametros $parametros)
    {
    }

    public function construir(): Roteador
    {
        $acervo = new Acervo($this->banco);
        $identidade = new \Aplicacao\Site\Filtros\Identidade(new Consulta($acervo));
        $controle = new Jogo(new Jogadores($acervo), new Rodadas($acervo), new Consulta($acervo), new Acesso(new Tentativas($this->banco)), new Renderizacao(), $this->parametros->obter('urlPrefixo'));
        return new Roteador(require \dirname(__DIR__) . '/Rotas/Mapa.php', static function (string $operacao, array $campos, array &$sessao, int $agora, string $origem) use ($controle, $identidade): \Aplicacao\Nucleo\Transportes\Resposta {
            $identidade->conferir($sessao);
            return $controle->atender($operacao, $campos, $sessao, $agora, $origem);
        });
    }
}
