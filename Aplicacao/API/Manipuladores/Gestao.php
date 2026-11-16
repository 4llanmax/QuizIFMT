<?php

/**
 * Mantém perguntas e consulta dados administrativos do jogo sem expor segredos.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\API\Manipuladores;

use DomainException;
use PDO;
use Throwable;

final class Gestao
{
    public function __construct(private PDO $banco)
    {
    }

    public function listarPerguntas(array $argumentos, int $pagina, int $limite): array
    {
        $condicoes = [];
        $valores = [];
        if (isset($argumentos['tema'])) {
            $condicoes[] = 'tema = ?';
            $valores[] = $argumentos['tema'];
        }
        $arquivadas = $argumentos['arquivadas'] ?? 'ativas';
        if ($arquivadas !== 'todas') {
            $condicoes[] = 'arquivada = ?';
            $valores[] = (int) ($arquivadas === 'arquivadas');
        }
        $filtro = $condicoes === [] ? '' : ' WHERE ' . \implode(' AND ', $condicoes);
        $resultado = $this->paginar('perguntas', 'identificador, tema, enunciado, alternativas, correta, arquivada', $filtro, $valores, 'identificador', $pagina, $limite);
        $resultado['perguntas'] = \array_map($this->converterPergunta(...), $resultado['perguntas']);
        return $resultado;
    }

    public function obterPergunta(int $identificador): array
    {
        $consulta = $this->banco->prepare('SELECT identificador, tema, enunciado, alternativas, correta, arquivada FROM perguntas WHERE identificador = ?');
        $consulta->execute([$identificador]);
        $pergunta = $consulta->fetch();
        if ($pergunta === false) {
            throw new DomainException('Pergunta não encontrada.');
        }
        return $this->converterPergunta($pergunta);
    }

    private function converterPergunta(array $pergunta): array
    {
        $pergunta['identificador'] = (int) $pergunta['identificador'];
        $pergunta['correta'] = (int) $pergunta['correta'];
        $pergunta['arquivada'] = (bool) $pergunta['arquivada'];
        $pergunta['alternativas'] = \json_decode($pergunta['alternativas'], true, 512, JSON_THROW_ON_ERROR);
        return $pergunta;
    }

    public function criarPerguntas(array $perguntas): array
    {
        $consulta = $this->banco->prepare('INSERT INTO perguntas (tema, enunciado, alternativas, correta) VALUES (?, ?, ?, ?)');
        $criadas = [];
        foreach ($perguntas as $pergunta) {
            $consulta->execute([$pergunta['tema'], $pergunta['enunciado'], \json_encode($pergunta['alternativas'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE), $pergunta['correta']]);
            $criadas[] = $this->obterPergunta((int) $this->banco->lastInsertId());
        }
        return ['perguntas' => $criadas];
    }

    public function editarPergunta(int $identificador, array $pergunta): array
    {
        $consulta = $this->banco->prepare('UPDATE perguntas SET tema = ?, enunciado = ?, alternativas = ?, correta = ? WHERE identificador = ?');
        $consulta->execute([$pergunta['tema'], $pergunta['enunciado'], \json_encode($pergunta['alternativas'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE), $pergunta['correta'], $identificador]);
        return $this->obterPergunta($identificador);
    }

    public function arquivarPergunta(int $identificador, bool $arquivada): array
    {
        $consulta = $this->banco->prepare('UPDATE perguntas SET arquivada = ? WHERE identificador = ?');
        $consulta->execute([(int) $arquivada, $identificador]);
        return $this->obterPergunta($identificador);
    }

    public function excluirPergunta(int $identificador): array
    {
        $consulta = $this->banco->prepare('DELETE FROM perguntas WHERE identificador = ?');
        $consulta->execute([$identificador]);
        return ['identificador' => $identificador, 'excluida' => true];
    }

    /** O bloqueio da programação serializa as operações fornecidas pelo serviço. */
    public function transacionar(callable $operacao): array
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

    public function listarPartidas(?int $jogador, int $pagina, int $limite): array
    {
        return $this->paginar('partidas', 'identificador, jogador, acertos, total, nivel, tempo, motivo, temas, criado_em', $jogador === null ? '' : ' WHERE jogador = ?', $jogador === null ? [] : [$jogador], 'criado_em DESC, identificador DESC', $pagina, $limite);
    }

    public function obterPartida(string $identificador): array
    {
        $consulta = $this->banco->prepare('SELECT identificador, jogador, acertos, total, nivel, tempo, motivo, temas, criado_em FROM partidas WHERE identificador = ?');
        $consulta->execute([$identificador]);
        $partida = $consulta->fetch();
        if ($partida === false) {
            throw new DomainException('Partida não encontrada.');
        }
        $partida['temas'] = \json_decode($partida['temas'], true, 512, JSON_THROW_ON_ERROR);
        return $partida;
    }

    public function listarJogadores(int $pagina, int $limite): array
    {
        return $this->paginar('jogadores', 'identificador, nome, email, criado_em', '', [], 'identificador', $pagina, $limite);
    }

    /** Os fragmentos SQL são escolhidos exclusivamente pelos métodos internos, nunca pelo cliente. */
    private function paginar(string $tabela, string $colunas, string $filtro, array $valores, string $ordem, int $pagina, int $limite): array
    {
        $contagem = $this->banco->prepare('SELECT COUNT(*) FROM ' . $tabela . $filtro);
        $contagem->execute($valores);
        $consulta = $this->banco->prepare('SELECT ' . $colunas . ' FROM ' . $tabela . $filtro . ' ORDER BY ' . $ordem . ' LIMIT ' . $limite . ' OFFSET ' . (($pagina - 1) * $limite));
        $consulta->execute($valores);
        return [$tabela => $consulta->fetchAll(), 'total' => (int) $contagem->fetchColumn(), 'pagina' => $pagina, 'limite' => $limite];
    }

    public function estatisticas(): array
    {
        $resultado = $this->banco->query('SELECT COUNT(*) AS partidas, COALESCE(AVG(acertos), 0) AS media_acertos, COALESCE(AVG(tempo), 0) AS media_tempo FROM partidas')->fetch();
        $resultado['partidas'] = (int) $resultado['partidas'];
        $resultado['jogadores'] = (int) $this->banco->query('SELECT COUNT(*) FROM jogadores')->fetchColumn();
        $resultado['perguntas'] = $this->banco->query('SELECT tema, arquivada, COUNT(*) AS quantidade FROM perguntas GROUP BY tema, arquivada ORDER BY tema, arquivada')->fetchAll();
        $resultado['motivos'] = $this->banco->query('SELECT motivo, COUNT(*) AS quantidade FROM partidas GROUP BY motivo ORDER BY motivo')->fetchAll();
        return $resultado;
    }

    public function ranking(): array
    {
        return ['ranking' => $this->banco->query('SELECT j.identificador, j.nome, p.acertos, p.tempo, p.total, p.criado_em FROM (SELECT partidas.*, ROW_NUMBER() OVER (PARTITION BY jogador ORDER BY acertos DESC, tempo ASC, criado_em ASC, identificador ASC) AS ordem FROM partidas) p JOIN jogadores j ON j.identificador = p.jogador WHERE p.ordem = 1 ORDER BY p.acertos DESC, p.tempo ASC, p.criado_em ASC, p.identificador ASC LIMIT 100')->fetchAll()];
    }

    public function programacao(): array
    {
        $programacao = \json_decode($this->banco->query('SELECT conteudo FROM configuracao WHERE identificador = 1')->fetchColumn(), true, 512, JSON_THROW_ON_ERROR);
        $capacidades = $this->banco->query('SELECT tema, COUNT(*) FROM perguntas WHERE arquivada = 0 GROUP BY tema ORDER BY tema')->fetchAll(PDO::FETCH_KEY_PAIR);
        return ['programacao' => $programacao, 'capacidades' => $capacidades];
    }

    public function categorias(): array
    {
        return ['categorias' => $this->banco->query('SELECT c.chave, c.nome, COALESCE(SUM(p.arquivada = 0), 0) AS ativas, COALESCE(SUM(p.arquivada = 1), 0) AS arquivadas FROM categorias c LEFT JOIN perguntas p ON p.tema = c.chave GROUP BY c.chave, c.nome ORDER BY c.chave')->fetchAll()];
    }

    public function categoria(string $chave): ?array
    {
        $consulta = $this->banco->prepare('SELECT chave, nome, (SELECT COUNT(*) FROM perguntas WHERE tema = categorias.chave) AS perguntas FROM categorias WHERE chave = ?');
        $consulta->execute([$chave]);
        return $consulta->fetch() ?: null;
    }

    public function categoriasCoincidentes(string $chave, string $nome): array
    {
        $consulta = $this->banco->prepare('SELECT chave FROM categorias WHERE chave = ? OR nome = ?');
        $consulta->execute([$chave, $nome]);
        return $consulta->fetchAll(PDO::FETCH_COLUMN);
    }

    public function salvarCategoria(string $chave, string $nome, bool $criar): array
    {
        $consulta = $this->banco->prepare($criar ? 'INSERT INTO categorias (nome, chave) VALUES (?, ?)' : 'UPDATE categorias SET nome = ? WHERE chave = ?');
        $consulta->execute([$nome, $chave]);
        return ['chave' => $chave, 'nome' => $nome];
    }

    public function excluirCategoria(string $chave): array
    {
        $consulta = $this->banco->prepare('DELETE FROM categorias WHERE chave = ?');
        $consulta->execute([$chave]);
        return ['chave' => $chave, 'excluida' => true];
    }
}
