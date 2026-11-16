<?php

/**
 * Valida perguntas completas antes da inclusão ou alteração pelo MCP.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\API\Validacoes;

use DomainException;

final class Pergunta
{
    public static function validar(array $dados): array
    {
        if (\array_diff(\array_keys($dados), ['tema', 'enunciado', 'alternativas', 'correta']) !== [] || !\is_string($dados['tema'] ?? null) || !\preg_match('/^[a-z][a-z0-9_-]{0,23}$/D', $dados['tema'])) {
            throw new DomainException('Informe somente os campos reconhecidos e a chave de uma categoria cadastrada.');
        }
        $enunciado = self::texto($dados['enunciado'] ?? null);
        $alternativas = $dados['alternativas'] ?? null;
        if (!\is_array($alternativas) || !\array_is_list($alternativas) || \count($alternativas) !== 5) {
            throw new DomainException('Informe uma lista com exatamente cinco alternativas.');
        }
        $alternativas = \array_map(self::texto(...), $alternativas);
        if (\count(\array_unique($alternativas)) !== 5) {
            throw new DomainException('As cinco alternativas precisam ter textos distintos.');
        }
        $correta = $dados['correta'] ?? null;
        if (!\is_int($correta) || $correta < 0 || $correta > 4) {
            throw new DomainException('O índice da alternativa correta deve ser um inteiro de 0 a 4.');
        }
        return ['tema' => $dados['tema'], 'enunciado' => $enunciado, 'alternativas' => $alternativas, 'correta' => $correta];
    }

    private static function texto(mixed $texto): string
    {
        if (!\is_string($texto) || !\mb_check_encoding($texto, 'UTF-8') || \trim($texto) === '' || \mb_strlen(\trim($texto), 'UTF-8') > 512 || \str_contains($texto, "\0")) {
            throw new DomainException('O enunciado e cada alternativa devem conter de 1 a 512 caracteres UTF-8, sem byte nulo.');
        }
        return \trim($texto);
    }
}
