<?php

/**
 * Cria e remove exclusivamente um banco temporário próprio de cada execução dos testes.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Apoio;

use PDO;
use Throwable;

final class BancoIsolado
{
    private PDO $servidor;
    private string $nome;
    private array $configuracao;
    private bool $criado = false;

    public function __construct(array $ambiente)
    {
        $this->nome = 'quiz_teste_' . bin2hex(random_bytes(6));
        $this->configuracao = [
            'BANCO_HOST' => $ambiente['TESTE_BANCO_HOST'] ?? '127.0.0.1',
            'BANCO_PORTA' => $ambiente['TESTE_BANCO_PORTA'] ?? '3306',
            'BANCO_USUARIO' => $ambiente['TESTE_BANCO_USUARIO'] ?? 'root',
            'BANCO_SENHA' => $ambiente['TESTE_BANCO_SENHA'] ?? '',
            'BANCO_NOME' => $this->nome,
        ];
        $this->servidor = new PDO($this->dsn(), $this->configuracao['BANCO_USUARIO'], $this->configuracao['BANCO_SENHA'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    }

    private function dsn(): string
    {
        return 'mysql:host=' . $this->configuracao['BANCO_HOST'] . ';port=' . $this->configuracao['BANCO_PORTA'] . ';charset=utf8mb4';
    }

    public function criar(): PDO
    {
        $this->servidor->exec('CREATE DATABASE ' . $this->nome . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $this->criado = true;
        try {
            $banco = new PDO($this->dsn() . ';dbname=' . $this->nome, $this->configuracao['BANCO_USUARIO'], $this->configuracao['BANCO_SENHA'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
            $raiz = dirname(__DIR__, 2);
            $banco->exec(file_get_contents($raiz . '/Dados/Esquemas/MariaDB/I.2026.11.15.0001.sql'));
            $banco->exec(file_get_contents($raiz . '/Dados/Cargas/MariaDB/I.2026.11.15.0001.sql'));
            return $banco;
        } catch (Throwable $falha) {
            $this->limpar();
            throw $falha;
        }
    }

    public function parametros(): array
    {
        return $this->configuracao;
    }

    public function nome(): string
    {
        return $this->nome;
    }

    public function limpar(): void
    {
        if ($this->criado) {
            $this->servidor->exec('DROP DATABASE ' . $this->nome);
            $this->criado = false;
        }
    }
}
