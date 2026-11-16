<?php

/**
 * Valida limites e distribuição de perguntas antes de alterar novas rodadas.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Painel\Servicos;

use Aplicacao\Painel\Contratos\DadosProgramacao;
use DomainException;

final class Programacao
{
    public function __construct(private DadosProgramacao $dados)
    {
    }

    public function consultar(): array
    {
        return $this->dados->obter();
    }

    public function capacidades(): array
    {
        return $this->dados->capacidades();
    }

    public function salvar(array $configuracao): void
    {
        foreach (['total' => [5, 48], 'segundos' => [10, 120], 'alerta' => [3, 30]] as $chave => [$minimo, $maximo]) {
            if (!isset($configuracao[$chave]) || !\is_int($configuracao[$chave]) || $configuracao[$chave] < $minimo || $configuracao[$chave] > $maximo) {
                throw new DomainException('Informe total entre 5 e 48, tempo entre 10 e 120 e alerta entre 3 e 30 segundos.');
            }
        }
        $capacidades = $this->dados->capacidades();
        $distribuicao = $configuracao['distribuicao'] ?? [];
        if (!\is_array($distribuicao) || \count($distribuicao) !== \count($capacidades) || $configuracao['alerta'] >= $configuracao['segundos']) {
            throw new DomainException('Informe todos os temas e um alerta menor que o tempo da pergunta.');
        }
        foreach ($capacidades as $tema => $quantidade) {
            if (!isset($distribuicao[$tema]) || !\is_int($distribuicao[$tema]) || $distribuicao[$tema] < 0 || $distribuicao[$tema] > (int) $quantidade) {
                throw new DomainException('A distribuição ultrapassa o acervo de perguntas disponível.');
            }
        }
        if (\array_sum($distribuicao) !== $configuracao['total']) {
            throw new DomainException('A soma dos temas precisa corresponder ao total de perguntas.');
        }
        $this->dados->salvar($configuracao);
    }

    public function restaurar(): void
    {
        $this->salvar($this->dados->padrao());
    }
}
