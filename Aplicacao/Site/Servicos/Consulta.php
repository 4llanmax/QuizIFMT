<?php

/**
 * Prepara dados de apresentação sem entregar credenciais ou gabaritos futuros.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Site\Servicos;

use Aplicacao\Site\Contratos\DadosJogo;

final class Consulta
{
    public function __construct(private DadosJogo $dados)
    {
    }

    public function jogador(?int $identificador): ?array
    {
        if ($identificador === null) {
            return null;
        }
        $jogador = $this->dados->jogador($identificador);
        if ($jogador === null) {
            return null;
        }
        unset($jogador['senha_hash'], $jogador['email'], $jogador['token'], $jogador['nome_normalizado']);
        return $jogador;
    }

    public function apresentar(?int $identificador, bool $conta): array
    {
        $jogador = $this->jogador($identificador);
        $historico = $conta && $jogador !== null ? $this->dados->historico($identificador) : [];
        $temas = [];
        foreach ($historico as $partida) {
            foreach (\json_decode($partida['temas'], true, 512, JSON_THROW_ON_ERROR) as $tema => [$acertos, $total]) {
                $temas[$tema] ??= [0, 0];
                $temas[$tema][0] += $acertos;
                $temas[$tema][1] += $total;
            }
        }
        $ranking = $this->dados->ranking();
        $posicao = null;
        $melhor = $jogador === null ? null : $this->dados->recorde($identificador);
        foreach ($ranking as $indice => $registro) {
            if ((int) $registro['identificador'] === $identificador) {
                $posicao = $indice + 1;
            }
        }
        return ['configuracao' => $this->dados->configuracao(), 'jogador' => $jogador, 'ranking' => $ranking,
            'historico' => $historico, 'temas' => $temas, 'posicao' => $posicao, 'melhor' => $melhor];
    }
}
