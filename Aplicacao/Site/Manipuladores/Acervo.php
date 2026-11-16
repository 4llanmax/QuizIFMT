<?php

/**
 * Consulta e grava jogadores, perguntas e resultados no MariaDB.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Site\Manipuladores;

use Aplicacao\Site\Contratos\DadosJogo;
use PDO;
use Throwable;

final class Acervo implements DadosJogo
{
    public function __construct(private PDO $banco)
    {
    }

    /** Serializa a reserva de nomes entre convidados e contas, inclusive sob concorrência. */
    public function transacionar(callable $operacao): mixed
    {
        $this->banco->beginTransaction();
        try {
            $this->banco->query('SELECT identificador FROM configuracao WHERE identificador = 1 FOR UPDATE');
            $resultado = $operacao();
            $this->banco->commit();
            return $resultado;
        } catch (Throwable $falha) {
            $this->banco->rollBack();
            throw $falha;
        }
    }

    public function configuracao(): array
    {
        $configuracao = \json_decode($this->banco->query('SELECT conteudo FROM configuracao WHERE identificador = 1')->fetchColumn(), true, 512, JSON_THROW_ON_ERROR);
        $configuracao['nomesTemas'] = $this->banco->query('SELECT chave, nome FROM categorias ORDER BY chave')->fetchAll(PDO::FETCH_KEY_PAIR);
        $capacidades = $this->banco->query('SELECT tema, COUNT(*) FROM perguntas WHERE arquivada = 0 GROUP BY tema ORDER BY tema')->fetchAll(PDO::FETCH_KEY_PAIR);
        $configuracao['distribuicao'] = \Aplicacao\Nucleo\Servicos\Distribuicao::calcular($configuracao['total'], $capacidades);
        return $configuracao;
    }

    public function perguntas(): array
    {
        $perguntas = $this->banco->query('SELECT tema, enunciado, alternativas, correta FROM perguntas WHERE arquivada = 0 ORDER BY identificador')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($perguntas as &$pergunta) {
            $pergunta['alternativas'] = \json_decode($pergunta['alternativas'], true, 512, JSON_THROW_ON_ERROR);
            $pergunta['correta'] = (int) $pergunta['correta'];
        }
        return $perguntas;
    }

    public function jogador(int $identificador): ?array
    {
        $consulta = $this->banco->prepare('SELECT * FROM jogadores WHERE identificador = ?');
        $consulta->execute([$identificador]);
        return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function email(string $email): ?array
    {
        $consulta = $this->banco->prepare('SELECT * FROM jogadores WHERE email = ?');
        $consulta->execute([$email]);
        return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function nomeRegistrado(string $nome): bool
    {
        $consulta = $this->banco->prepare('SELECT identificador FROM jogadores WHERE nome_normalizado = ? AND email IS NOT NULL LIMIT 1');
        $consulta->execute([$nome]);
        return $consulta->fetchColumn() !== false;
    }

    public function nomeReservado(string $nome, ?int $proprio): bool
    {
        $consulta = $this->banco->prepare('SELECT identificador FROM jogadores WHERE nome_normalizado = ? AND email IS NOT NULL AND identificador <> ? LIMIT 1');
        $consulta->execute([$nome, $proprio ?? 0]);
        if ($consulta->fetchColumn() !== false) {
            return true;
        }
        foreach ($this->ranking() as $jogador) {
            if ($jogador['nome_normalizado'] === $nome && (int) $jogador['identificador'] !== $proprio) {
                return true;
            }
        }
        return false;
    }

    public function cadastrar(string $nome, string $normalizado, string $email, string $hash): array
    {
        $consulta = $this->banco->prepare('INSERT INTO jogadores (nome, nome_normalizado, email, senha_hash) VALUES (?, ?, ?, ?)');
        $consulta->execute([$nome, $normalizado, $email, $hash]);
        return $this->jogador((int) $this->banco->lastInsertId());
    }

    public function convidado(string $token, string $nome, string $normalizado): array
    {
        $consulta = $this->banco->prepare('SELECT * FROM jogadores WHERE token = ? AND nome_normalizado = ?');
        $consulta->execute([$token, $normalizado]);
        $jogador = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($jogador) {
            return $jogador;
        }
        $consulta = $this->banco->prepare('INSERT INTO jogadores (nome, nome_normalizado, token) VALUES (?, ?, ?)');
        $consulta->execute([$nome, $normalizado, $token]);
        return $this->jogador((int) $this->banco->lastInsertId());
    }

    public function renomear(int $identificador, string $nome, string $normalizado): void
    {
        $consulta = $this->banco->prepare('UPDATE jogadores SET nome = ?, nome_normalizado = ? WHERE identificador = ?');
        $consulta->execute([$nome, $normalizado, $identificador]);
    }

    /** Transfere somente o último resultado do convidado identificado pela sessão. */
    public function transferir(int $origem, int $destino): void
    {
        $consulta = $this->banco->prepare('UPDATE partidas SET jogador = ? WHERE jogador = ? ORDER BY criado_em DESC, identificador DESC LIMIT 1');
        $consulta->execute([$destino, $origem]);
    }

    /** A chave de partida torna a gravação idempotente, sem sobrescrever o proprietário. */
    public function gravar(int $jogador, array $partida): void
    {
        $consulta = $this->banco->prepare('INSERT INTO partidas (identificador, jogador, acertos, total, nivel, tempo, motivo, temas) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE identificador = identificador');
        $consulta->execute([$partida['identificador'], $jogador, $partida['acertos'], \count($partida['perguntas']), $partida['indice'] + 1, $partida['tempo'], $partida['situacao'], \json_encode($partida['temas'], JSON_THROW_ON_ERROR)]);
    }

    public function historico(int $jogador): array
    {
        $consulta = $this->banco->prepare('SELECT * FROM partidas WHERE jogador = ? ORDER BY criado_em DESC, identificador DESC LIMIT 300');
        $consulta->execute([$jogador]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /** A janela escolhe o melhor resultado de cada jogador antes de limitar o ranking. */
    public function ranking(): array
    {
        return $this->banco->query('SELECT j.identificador, j.nome, j.nome_normalizado, (j.email IS NOT NULL) AS registrado, p.acertos, p.tempo, p.total, p.criado_em FROM (SELECT partidas.*, ROW_NUMBER() OVER (PARTITION BY jogador ORDER BY acertos DESC, tempo ASC, criado_em ASC, identificador ASC) AS ordem FROM partidas) p JOIN jogadores j ON j.identificador = p.jogador WHERE p.ordem = 1 ORDER BY p.acertos DESC, p.tempo ASC, p.criado_em ASC, p.identificador ASC LIMIT 100')->fetchAll(PDO::FETCH_ASSOC);
    }

    /** A chave estrangeira remove os resultados associados à conta excluída. */
    public function excluir(int $identificador): void
    {
        $consulta = $this->banco->prepare('DELETE FROM jogadores WHERE identificador = ?');
        $consulta->execute([$identificador]);
    }

    public function recorde(int $jogador): ?array
    {
        $consulta = $this->banco->prepare('SELECT acertos, tempo, total FROM partidas WHERE jogador = ? ORDER BY acertos DESC, tempo ASC, criado_em ASC, identificador ASC LIMIT 1');
        $consulta->execute([$jogador]);
        return $consulta->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
