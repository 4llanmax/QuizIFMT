<?php

/**
 * Divide um total entre categorias com perguntas ativas e confere a capacidade.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Servicos;

use DomainException;

final class Distribuicao
{
    public static function calcular(int $total, array $capacidades, bool $embaralhar = false): array
    {
        $capacidades = \array_filter($capacidades, static fn (int|string $quantidade): bool => (int) $quantidade > 0);
        if ($total < 1 || $capacidades === []) {
            throw new DomainException('Cadastre perguntas ativas antes de programar a rodada.');
        }
        $temas = \array_keys($capacidades);
        $base = \intdiv($total, \count($temas));
        $resto = $total % \count($temas);
        $distribuicao = [];
        $extras = [];
        foreach ($temas as $tema) {
            if ($base > (int) $capacidades[$tema]) {
                throw new DomainException('O acervo ativo não atende à divisão equilibrada da rodada. Adicione perguntas ao tema ' . $tema . ' ou reduza o total.');
            }
            $distribuicao[$tema] = $base;
            if ((int) $capacidades[$tema] > $base) {
                $extras[] = $tema;
            }
        }
        if (\count($extras) < $resto) {
            throw new DomainException('O acervo ativo não atende ao total de perguntas da rodada.');
        }
        if ($embaralhar) {
            \shuffle($extras);
        }
        foreach (\array_slice($extras, 0, $resto) as $tema) {
            $distribuicao[$tema]++;
        }
        return $distribuicao;
    }
}
