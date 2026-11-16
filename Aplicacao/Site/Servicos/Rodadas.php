<?php

/**
 * Sorteia rodadas sem repetição e grava somente resultados encerrados.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Site\Servicos;

use Aplicacao\Site\Contratos\DadosJogo;
use Aplicacao\Site\Entidades\Partida;
use DomainException;

final class Rodadas
{
    public function __construct(private DadosJogo $dados)
    {
    }

    public function iniciar(int $agora): Partida
    {
        $configuracao = $this->dados->configuracao();
        $banco = $this->dados->perguntas();
        $sorteadas = [];
        foreach ($configuracao['distribuicao'] as $tema => $quantidade) {
            $grupo = \array_values(\array_filter($banco, fn (array $pergunta): bool => $pergunta['tema'] === $tema));
            if (\count($grupo) < $quantidade) {
                throw new DomainException('O acervo não atende à programação da rodada.');
            }
            \shuffle($grupo);
            foreach (\array_slice($grupo, 0, $quantidade) as $pergunta) {
                $ordem = \range(0, 4);
                \shuffle($ordem);
                $pergunta['alternativas'] = \array_map(fn (int $indice): string => $pergunta['alternativas'][$indice], $ordem);
                $pergunta['correta'] = \array_search($pergunta['correta'], $ordem, true);
                $sorteadas[] = $pergunta;
            }
        }
        \shuffle($sorteadas);
        return new Partida(\bin2hex(\random_bytes(16)), $sorteadas, $configuracao['segundos'], $configuracao['alerta'], $agora);
    }

    public function guardar(int $jogador, Partida $partida): void
    {
        if (!$partida->terminou()) {
            throw new DomainException('O resultado só é gravado após o encerramento.');
        }
        $this->dados->gravar($jogador, $partida->estado());
    }

    /** Uma segunda aba retoma a partida ativa, sem trocar as perguntas nem reiniciar o prazo. */
    public function preparar(?array $estado, int $proprietario, int $jogador, int $agora): Partida
    {
        if ($estado !== null) {
            $anterior = Partida::restaurar($estado);
            if (!$anterior->terminou()) {
                if ($proprietario === $jogador) {
                    return $this->consultar($estado, $proprietario, $agora);
                }
                $anterior->abandonar();
                $this->guardar($proprietario, $anterior);
            }
        }
        return $this->iniciar($agora);
    }

    public function consultar(array $estado, int $jogador, int $agora): Partida
    {
        $partida = Partida::restaurar($estado);
        $partida->expirar($agora);
        if ($partida->terminou()) {
            $this->guardar($jogador, $partida);
        }
        return $partida;
    }

    /** O identificador recebido impede que formulários antigos alterem uma nova partida. */
    public function alterar(array $estado, int $jogador, string $identificador, string $operacao, array $entrada, int $agora): Partida
    {
        if ($estado['identificador'] !== $identificador) {
            throw new DomainException('Esta partida já foi substituída. Abra a partida atual.');
        }
        $partida = Partida::restaurar($estado);
        if ($operacao === 'responder') {
            $partida->responder($entrada['indice'], $entrada['alternativa'], $agora);
        } elseif ($operacao === 'avancar') {
            $partida->avancar($agora);
        } elseif ($operacao === 'abandonar') {
            $partida->expirar($agora);
            $partida->abandonar();
        } else {
            $partida->expirar($agora);
        }
        if ($partida->terminou()) {
            $this->guardar($jogador, $partida);
        }
        return $partida;
    }
}
