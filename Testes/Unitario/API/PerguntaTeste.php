<?php

/**
 * Confere a validação das perguntas recebidas pelo MCP.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\API;

use Aplicacao\API\Validacoes\Pergunta;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PerguntaTeste extends TestCase
{
    #[Test]
    public function aceitaPerguntaCompletaNormalizaEspacosEPreservaAcentos(): void
    {
        $dados = ['tema' => 'geografia', 'enunciado' => '  Qual é a capital?  ', 'alternativas' => [' Á ', 'B', 'C', 'D', 'E'], 'correta' => 0];
        $resultado = Pergunta::validar($dados);
        self::assertSame('Qual é a capital?', $resultado['enunciado']);
        self::assertSame('Á', $resultado['alternativas'][0]);
        self::assertSame(0, $resultado['correta']);
    }

    #[Test]
    public function recusaDadosInvalidosAntesDaPersistencia(): void
    {
        $dados = ['tema' => 'musica', 'enunciado' => 'Pergunta?', 'alternativas' => ['A', 'B', 'C', 'D', 'E'], 'correta' => 4];
        foreach ([['tema' => 'tema invalido'], ['tema' => []], ['enunciado' => ' '], ['enunciado' => str_repeat('á', 513)], ['enunciado' => "\xFF"], ['alternativas' => []], ['alternativas' => ['A', 'B', 'C', 'D', '']], ['alternativas' => ['A', 'A', 'C', 'D', 'E']], ['alternativas' => ['A', 'B', 'C', 'D', 4]], ['alternativas' => ['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E']], ['correta' => -1], ['correta' => 5], ['correta' => '1'], ['desconhecida' => true]] as $alteracao) {
            try {
                Pergunta::validar(array_replace($dados, $alteracao));
                self::fail('A pergunta inválida precisa ser recusada.');
            } catch (DomainException) {
                self::assertTrue(true);
            }
        }
        $this->expectException(DomainException::class);
        Pergunta::validar([]);
    }
}
