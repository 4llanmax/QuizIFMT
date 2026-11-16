<?php

/**
 * Valida a estrutura dos campos recebidos pelo Site.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Site\Validacoes;

use DomainException;
use Normalizer;

final class Entrada
{
    /** Produz a chave de comparação sem diferenças de acento ou caixa. */
    public static function normalizar(string $valor): string
    {
        return \mb_strtolower(\preg_replace('/\p{M}/u', '', Normalizer::normalize(\trim($valor), Normalizer::FORM_D)), 'UTF-8');
    }

    /** Convidados admitem espaços; contas usam letras, números, ponto e sublinhado. */
    public static function nome(mixed $valor, bool $conta): string
    {
        if (!\is_string($valor)) {
            throw new DomainException('Informe um nome válido.');
        }
        $nome = \preg_replace('/\s+/u', ' ', \trim($valor));
        $minimo = $conta ? 3 : 2;
        $padrao = $conta ? '/^[\p{L}\p{N}._]+$/uD' : '/^[\p{L}\p{N}._ -]+$/uD';
        if (\mb_strlen($nome) < $minimo || \mb_strlen($nome) > 16 || !\preg_match($padrao, $nome)) {
            throw new DomainException($conta ? 'Use de 3 a 16 letras, números, ponto ou _ no apelido.' : 'Use de 2 a 16 caracteres no nome, sem símbolos especiais.');
        }
        return $nome;
    }

    /** O e-mail é interno à conta e nunca integra o ranking público. */
    public static function email(mixed $valor): string
    {
        if (!\is_string($valor) || \strlen($valor) > 254 || !\filter_var(\trim($valor), FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Informe um e-mail válido.');
        }
        return \strtolower(\trim($valor));
    }

    /** O limite de 72 bytes impede truncamento de senhas pelo bcrypt. */
    public static function senha(mixed $valor): string
    {
        if (!\is_string($valor) || \strlen($valor) < 8 || \strlen($valor) > 72 || \str_contains($valor, "\0")) {
            throw new DomainException('A senha precisa ter de 8 a 72 bytes.');
        }
        return $valor;
    }

    /** Campos numéricos admitem somente inteiros na faixa explícita. */
    public static function inteiro(mixed $valor, int $minimo, int $maximo): int
    {
        if (!\is_int($valor) && !\is_string($valor)) {
            throw new DomainException('Informe um número inteiro.');
        }
        $numero = \filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => $minimo, 'max_range' => $maximo]]);
        if ($numero === false) {
            throw new DomainException('O número está fora do intervalo permitido.');
        }
        return $numero;
    }
}
