<?php

/**
 * Declara as operações persistentes consumidas pelos serviços do Site.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Site\Contratos;

interface DadosJogo
{
    public function transacionar(callable $operacao): mixed;
    public function configuracao(): array;
    public function perguntas(): array;
    public function jogador(int $identificador): ?array;
    public function email(string $email): ?array;
    public function nomeReservado(string $nome, ?int $proprio): bool;
    public function nomeRegistrado(string $nome): bool;
    public function cadastrar(string $nome, string $normalizado, string $email, string $hash): array;
    public function convidado(string $token, string $nome, string $normalizado): array;
    public function renomear(int $identificador, string $nome, string $normalizado): void;
    public function transferir(int $origem, int $destino): void;
    public function gravar(int $jogador, array $partida): void;
    public function historico(int $jogador): array;
    public function ranking(): array;
    public function recorde(int $jogador): ?array;
    public function excluir(int $identificador): void;
}
