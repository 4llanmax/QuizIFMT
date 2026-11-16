<?php

/**
 * Coordena contas e convidados, reserva de nomes e transferência do último resultado.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Site\Servicos;

use Aplicacao\Site\Contratos\DadosJogo;
use Aplicacao\Site\Validacoes\Entrada;
use DomainException;

final class Jogadores
{
    public function __construct(private DadosJogo $dados)
    {
    }

    public function convidado(string $token, mixed $nome): array
    {
        $nome = Entrada::nome($nome, false);
        $normalizado = Entrada::normalizar($nome);
        return $this->dados->transacionar(function () use ($token, $nome, $normalizado): array {
            if ($this->dados->nomeRegistrado($normalizado)) {
                throw new DomainException('Esse nome pertence a uma conta. Entre ou escolha outro nome.');
            }
            return $this->dados->convidado($token, $nome, $normalizado);
        });
    }

    public function cadastrar(mixed $nome, mixed $email, mixed $senha, ?int $convidado): array
    {
        $nome = Entrada::nome($nome, true);
        $email = Entrada::email($email);
        $senha = Entrada::senha($senha);
        return $this->dados->transacionar(function () use ($nome, $email, $senha, $convidado): array {
            $normalizado = Entrada::normalizar($nome);
            if ($this->dados->nomeReservado($normalizado, $convidado) || $this->dados->email($email) !== null) {
                throw new DomainException('Nome reservado ou cadastro indisponível. Escolha outro apelido ou entre na sua conta.');
            }
            $conta = $this->dados->cadastrar($nome, $normalizado, $email, \password_hash($senha, PASSWORD_DEFAULT));
            if ($convidado !== null) {
                $this->dados->transferir($convidado, (int) $conta['identificador']);
            }
            return $conta;
        });
    }

    public function entrar(mixed $email, mixed $senha): array
    {
        $email = Entrada::email($email);
        $senha = Entrada::senha($senha);
        $conta = $this->dados->email($email);
        // O hash fixo fictício mantém o custo da verificação quando a conta não existe.
        $hash = $conta['senha_hash'] ?? '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        if (!\password_verify($senha, $hash) || $conta === null) {
            throw new DomainException('E-mail ou senha incorretos.');
        }
        return $conta;
    }

    public function renomear(int $identificador, mixed $nome): void
    {
        $nome = Entrada::nome($nome, true);
        $this->dados->transacionar(function () use ($identificador, $nome): void {
            $normalizado = Entrada::normalizar($nome);
            if ($this->dados->nomeReservado($normalizado, $identificador)) {
                throw new DomainException('O apelido está reservado por outro jogador.');
            }
            $this->dados->renomear($identificador, $nome, $normalizado);
        });
    }

    public function excluir(int $identificador, mixed $senha): void
    {
        $senha = Entrada::senha($senha);
        $this->dados->transacionar(function () use ($identificador, $senha): void {
            $conta = $this->dados->jogador($identificador);
            if ($conta === null || !\is_string($conta['senha_hash']) || !\password_verify($senha, $conta['senha_hash'])) {
                throw new DomainException('Confirme a senha da conta antes de excluí-la.');
            }
            $this->dados->excluir($identificador);
        });
    }
}
