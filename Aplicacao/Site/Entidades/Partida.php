<?php

/**
 * Mantém perguntas, gabarito e pontuação exclusivamente no servidor.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Site\Entidades;

use DomainException;

final class Partida
{
    private array $dados;

    /** Perguntas e configuração são fotografadas para toda a duração da partida; tempos em milissegundos. */
    public function __construct(string $identificador, array $perguntas, int $segundos, int $alerta, int $agora)
    {
        $this->dados = ['identificador' => $identificador, 'perguntas' => $perguntas, 'segundos' => $segundos,
            'alerta' => $alerta, 'indice' => 0, 'acertos' => 0, 'tempo' => 0, 'temas' => [],
            'situacao' => 'jogando', 'inicio' => $agora, 'alternativa' => null];
    }

    /** Restaura somente o estado interno da sessão; a entrada HTTP não fornece esse conteúdo. */
    public static function restaurar(array $dados): self
    {
        $partida = new self($dados['identificador'], $dados['perguntas'], $dados['segundos'], $dados['alerta'], $dados['inicio']);
        $partida->dados = $dados;
        return $partida;
    }

    public function estado(): array
    {
        return $this->dados;
    }

    public function terminou(): bool
    {
        return \in_array($this->dados['situacao'], ['vitoria', 'erro', 'tempo', 'abandono'], true);
    }

    /** Entrega somente a pergunta atual e libera o gabarito após a resposta. */
    public function publicar(int $agora): array
    {
        $dados = $this->dados;
        $pergunta = $dados['perguntas'][$dados['indice']];
        unset($pergunta['correta']);
        $publico = ['identificador' => $dados['identificador'], 'pergunta' => $pergunta,
            'indice' => $dados['indice'], 'total' => \count($dados['perguntas']), 'acertos' => $dados['acertos'],
            'tempo' => $dados['tempo'], 'situacao' => $dados['situacao'], 'alerta' => $dados['alerta'],
            'segundos' => $dados['segundos'], 'restante' => \max(0, $dados['segundos'] * 1000 - ($agora - $dados['inicio']))];
        if ($dados['situacao'] !== 'jogando') {
            $publico['respostaCorreta'] = $pergunta['alternativas'][$dados['perguntas'][$dados['indice']]['correta']];
        }
        return $publico;
    }

    /** Índice esperado e estado impedem respostas repetidas e avanço forjado. */
    public function responder(int $indice, int $alternativa, int $agora): void
    {
        if ($this->dados['situacao'] !== 'jogando' || $indice !== $this->dados['indice'] || $alternativa < 0 || $alternativa > 4) {
            throw new DomainException('Esta resposta não corresponde à pergunta atual.');
        }
        $this->expirar($agora);
        if ($this->terminou()) {
            return;
        }
        $pergunta = $this->dados['perguntas'][$indice];
        $correta = $alternativa === $pergunta['correta'];
        $tema = $pergunta['tema'];
        $this->dados['temas'][$tema] ??= [0, 0];
        $this->dados['temas'][$tema][1]++;
        $this->dados['alternativa'] = $alternativa;
        if (!$correta) {
            $this->dados['situacao'] = 'erro';
            return;
        }
        $this->dados['temas'][$tema][0]++;
        $this->dados['acertos']++;
        $this->dados['tempo'] += \max(0, $agora - $this->dados['inicio']);
        $this->dados['situacao'] = $this->dados['acertos'] === \count($this->dados['perguntas']) ? 'vitoria' : 'correta';
    }

    public function avancar(int $agora): void
    {
        if ($this->dados['situacao'] !== 'correta') {
            throw new DomainException('Responda corretamente antes de avançar.');
        }
        $this->dados['indice']++;
        $this->dados['situacao'] = 'jogando';
        $this->dados['inicio'] = $agora;
        $this->dados['alternativa'] = null;
    }

    public function expirar(int $agora): void
    {
        if ($this->dados['situacao'] === 'jogando' && $agora - $this->dados['inicio'] >= $this->dados['segundos'] * 1000) {
            $tema = $this->dados['perguntas'][$this->dados['indice']]['tema'];
            $this->dados['temas'][$tema] ??= [0, 0];
            $this->dados['temas'][$tema][1]++;
            $this->dados['situacao'] = 'tempo';
        }
    }

    public function abandonar(): void
    {
        if (!$this->terminou()) {
            $this->dados['situacao'] = 'abandono';
        }
    }
}
