<?php

/**
 * Constrói a administração sem depender dos componentes específicos do Site.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Painel\Provedores;

use Aplicacao\Nucleo\Comuns\Classes\Parametros;
use Aplicacao\Nucleo\Comuns\Classes\Renderizacao;
use Aplicacao\Nucleo\Comuns\Classes\Roteador;
use Aplicacao\Nucleo\Manipuladores\Tentativas;
use Aplicacao\Nucleo\Servicos\Acesso;
use Aplicacao\Painel\Controles\Administracao;
use Aplicacao\Painel\Manipuladores\Programacao;
use Aplicacao\Painel\Servicos\Autenticacao;
use PDO;

final class Atendimento
{
    public function __construct(private PDO $banco, private Parametros $parametros)
    {
    }

    /** Registra a correspondência do parâmetro pertencente exclusivamente ao Painel. */
    public static function parametros(): array
    {
        return ['ADMIN_SENHA_HASH' => 'adminSenhaHash'];
    }

    public function construir(): Roteador
    {
        $controle = new Administracao(new \Aplicacao\Painel\Servicos\Programacao(new Programacao($this->banco)), new Autenticacao($this->parametros->obter('adminSenhaHash'), new Acesso(new Tentativas($this->banco))), new Renderizacao(), $this->parametros->obter('urlPrefixo'));
        return new Roteador(require \dirname(__DIR__) . '/Rotas/Mapa.php', static function (string $operacao, array $campos, array &$sessao, int $agora, string $origem) use ($controle): \Aplicacao\Nucleo\Transportes\Resposta {
            return $controle->atender($operacao, $campos, $sessao, $agora, $origem);
        });
    }
}
