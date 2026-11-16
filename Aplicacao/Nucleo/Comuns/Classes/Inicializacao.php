<?php

/**
 * Prepara parâmetros e conexão, exigindo HTTPS fora do acesso local.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Comuns\Classes;

use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use Aplicacao\Nucleo\Transportes\Resposta;
use Closure;
use Throwable;

final class Inicializacao
{
    public function __construct(private string $raiz, private array $adicionais = [])
    {
    }

    /** A composição das áreas é fornecida pela entrada; o Núcleo não conhece suas implementações. */
    public function responder(array $servidor, array $campos, array $processo, array &$sessao, Closure $construir, int $agora): Resposta
    {
        try {
            if (($servidor['HTTPS'] ?? '') !== 'on' && !\in_array($servidor['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
                throw new FalhaHTTP(400, 'Use uma conexão HTTPS para acessar o jogo.');
            }
            $parametros = new Parametros($this->raiz . '/Configuracoes/Aplicacao/.env', $processo, $this->adicionais);
            $caminho = (new Entrega($parametros->obter('urlPrefixo')))->caminho($servidor['REQUEST_URI'] ?? '/');
            $web = $construir((new Banco($parametros))->conectar(), $parametros);
            return $web->atender($servidor['REQUEST_METHOD'] ?? 'GET', $caminho, $campos, $sessao, $agora, $servidor['REMOTE_ADDR'] ?? '');
        } catch (FalhaHTTP $falha) {
            return new Resposta($falha->status, \htmlspecialchars($falha->getMessage(), ENT_QUOTES, 'UTF-8'));
        } catch (Throwable $falha) {
            // A mensagem técnica exclui entrada, credenciais e detalhes de conexão.
            \error_log('BombQUIZ: falha de inicialização ou persistência (' . \get_class($falha) . ').');
            return new Resposta(503, 'O jogo está temporariamente indisponível. Tente novamente mais tarde.');
        }
    }
}
