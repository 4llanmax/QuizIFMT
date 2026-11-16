<?php

/**
 * Publica e executa ferramentas MCP de gestão de conteúdo e consulta do jogo.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\API\Servicos;

use Aplicacao\API\Manipuladores\Gestao;
use Aplicacao\API\Validacoes\Pergunta;
use DomainException;
use PDO;

final class Ferramentas
{
    private Gestao $gestao;

    public function __construct(PDO $banco)
    {
        $this->gestao = new Gestao($banco);
    }

    /** Os identificadores snake_case e as chaves do protocolo preservam o contrato MCP. */
    public function listar(): array
    {
        $texto = ['type' => 'string'];
        $identificador = ['type' => 'integer', 'minimum' => 1];
        $pagina = ['pagina' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 1000000], 'limite' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100]];
        $pergunta = ['tema' => $texto, 'enunciado' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 512], 'alternativas' => ['type' => 'array', 'items' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 512], 'minItems' => 5, 'maxItems' => 5], 'correta' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 4]];
        $definicoes = [
            ['listar_perguntas', 'Lista perguntas paginadas, com filtro opcional por categoria e situação de arquivamento.', $pagina + ['tema' => $texto, 'arquivadas' => ['type' => 'string', 'enum' => ['ativas', 'arquivadas', 'todas']]], [], true, false],
            ['obter_pergunta', 'Obtém o enunciado, as alternativas, o gabarito e a situação de uma pergunta.', ['identificador' => $identificador], ['identificador'], true, false],
            ['criar_perguntas', 'Insere de uma a cem perguntas numa transação. Consulte as categorias antes; cada pergunta tem cinco alternativas e índice correto de 0 a 4.', ['perguntas' => ['type' => 'array', 'minItems' => 1, 'maxItems' => 100, 'items' => ['type' => 'object', 'properties' => $pergunta, 'required' => \array_keys($pergunta), 'additionalProperties' => false]]], ['perguntas'], false, false],
            ['editar_pergunta', 'Altera somente os campos informados de uma pergunta, inclusive categoria, enunciado, alternativas e gabarito.', ['identificador' => $identificador] + $pergunta, ['identificador'], false, true],
            ['arquivar_pergunta', 'Arquiva para impedir sorteio em novas rodadas; arquivada=false restaura. Preserva partidas já iniciadas.', ['identificador' => $identificador, 'arquivada' => ['type' => 'boolean']], ['identificador', 'arquivada'], false, true],
            ['excluir_pergunta', 'Exclui definitivamente uma pergunta. Exige confirmação explícita do usuário em confirmar=true.', ['identificador' => $identificador, 'confirmar' => ['type' => 'boolean', 'const' => true]], ['identificador', 'confirmar'], false, true],
            ['listar_categorias', 'Lista as categorias cadastradas e a quantidade de perguntas ativas e arquivadas em cada uma.', [], [], true, false],
            ['criar_categoria', 'Cria uma categoria com chave estável de até 24 letras minúsculas, números, hífen ou sublinhado e nome de até 80 caracteres. Categorias vazias não entram no sorteio.', ['chave' => $texto, 'nome' => $texto], ['chave', 'nome'], false, false],
            ['editar_categoria', 'Altera o nome de apresentação de uma categoria, preservando sua chave e seu histórico.', ['chave' => $texto, 'nome' => $texto], ['chave', 'nome'], false, true],
            ['excluir_categoria', 'Exclui definitivamente uma categoria vazia com confirmar=true. Mova ou exclua suas perguntas antes.', ['chave' => $texto, 'confirmar' => ['type' => 'boolean', 'const' => true]], ['chave', 'confirmar'], false, true],
            ['listar_partidas', 'Consulta resultados encerrados, com filtro opcional por jogador. Tempos são milissegundos.', $pagina + ['jogador' => $identificador], [], true, false],
            ['obter_partida', 'Consulta resultado e desempenho por categoria de uma partida encerrada.', ['identificador' => ['type' => 'string', 'pattern' => '^[a-f0-9]{32}$']], ['identificador'], true, false],
            ['listar_jogadores', 'Consulta jogadores paginados, sem senhas nem tokens de sessão.', $pagina, [], true, false],
            ['consultar_estatisticas', 'Consulta totais de jogadores, partidas, médias e capacidade do acervo por categoria.', [], [], true, false],
            ['consultar_ranking', 'Consulta os cem melhores resultados, com um resultado por jogador.', [], [], true, false],
            ['consultar_programacao', 'Consulta limites da rodada, capacidade de categorias e divisão equilibrada entre categorias com perguntas ativas.', [], [], true, false],
        ];
        return \array_map(static fn (array $definicao): array => ['name' => $definicao[0], 'description' => $definicao[1], 'inputSchema' => ['type' => 'object', 'properties' => (object) $definicao[2], 'required' => $definicao[3], 'additionalProperties' => false], 'annotations' => ['readOnlyHint' => $definicao[4], 'destructiveHint' => $definicao[5], 'idempotentHint' => $definicao[4], 'openWorldHint' => false]], $definicoes);
    }

    public function executar(string $nome, array $argumentos): array
    {
        $definicoes = \array_column($this->listar(), null, 'name');
        $esquema = $definicoes[$nome]['inputSchema'] ?? ['properties' => (object) [], 'required' => []];
        if (\array_diff(\array_keys($argumentos), \array_keys((array) $esquema['properties'])) !== [] || \array_diff($esquema['required'], \array_keys($argumentos)) !== []) {
            throw new DomainException('Informe somente os argumentos reconhecidos e todos os obrigatórios.');
        }
        $pagina = $this->inteiro(\array_key_exists('pagina', $argumentos) ? $argumentos['pagina'] : 1, 1, 1000000);
        $limite = $this->inteiro(\array_key_exists('limite', $argumentos) ? $argumentos['limite'] : 50, 1, 100);
        if (\array_key_exists('identificador', $argumentos) && $nome !== 'obter_partida') {
            $this->inteiro($argumentos['identificador'], 1, PHP_INT_MAX);
        }
        if (\array_key_exists('tema', $argumentos) && (!\is_string($argumentos['tema']) || !\preg_match('/^[a-z][a-z0-9_-]{0,23}$/D', $argumentos['tema']))) {
            throw new DomainException('Informe uma chave de categoria válida.');
        }
        if (\array_key_exists('arquivadas', $argumentos) && !\in_array($argumentos['arquivadas'], ['ativas', 'arquivadas', 'todas'], true)) {
            throw new DomainException('Use ativas, arquivadas ou todas no filtro de arquivamento.');
        }
        if (isset($argumentos['tema'])) {
            $this->categoria($argumentos['tema']);
        }
        return match ($nome) {
            'listar_perguntas' => $this->gestao->listarPerguntas($argumentos, $pagina, $limite),
            'obter_pergunta' => $this->gestao->obterPergunta($argumentos['identificador']),
            'listar_categorias' => $this->gestao->categorias(),
            'listar_partidas' => $this->gestao->listarPartidas(\array_key_exists('jogador', $argumentos) ? $this->inteiro($argumentos['jogador'], 1, PHP_INT_MAX) : null, $pagina, $limite),
            'obter_partida' => $this->gestao->obterPartida($this->partida($argumentos['identificador'])),
            'listar_jogadores' => $this->gestao->listarJogadores($pagina, $limite),
            'consultar_estatisticas' => $this->gestao->estatisticas(),
            'consultar_ranking' => $this->gestao->ranking(),
            'consultar_programacao' => $this->programacao(),
            default => $this->alterar($nome, $argumentos),
        };
    }

    private function alterar(string $nome, array $argumentos): array
    {
        return $this->gestao->transacionar(function () use ($nome, $argumentos): array {
            if (isset($argumentos['identificador'])) {
                $this->gestao->obterPergunta($argumentos['identificador']);
            }
            $resultado = match ($nome) {
                'criar_perguntas' => $this->gestao->criarPerguntas($this->lote($argumentos['perguntas'])),
                'editar_pergunta' => $this->editar($argumentos),
                'arquivar_pergunta' => $this->gestao->arquivarPergunta($argumentos['identificador'], $this->booleano($argumentos['arquivada'])),
                'excluir_pergunta' => $this->gestao->excluirPergunta($this->confirmar($argumentos, $argumentos['identificador'])),
                'criar_categoria', 'editar_categoria' => $this->salvarCategoria($argumentos['chave'], $argumentos['nome'], $nome === 'criar_categoria'),
                'excluir_categoria' => $this->excluirCategoria($this->confirmar($argumentos, $argumentos['chave'])),
                default => throw new DomainException('Ferramenta não encontrada.'),
            };
            $this->programacao();
            return $resultado;
        });
    }

    private function programacao(): array
    {
        $resultado = $this->gestao->programacao();
        $resultado['programacao']['distribuicao'] = \Aplicacao\Nucleo\Servicos\Distribuicao::calcular($resultado['programacao']['total'], $resultado['capacidades']);
        return $resultado;
    }

    private function categoria(string $chave): array
    {
        $categoria = $this->gestao->categoria($chave);
        if ($categoria === null) {
            throw new DomainException('Categoria não encontrada. Cadastre a categoria antes de enviar perguntas.');
        }
        return $categoria;
    }

    private function chaveCategoria(mixed $chave): string
    {
        if (!\is_string($chave) || !\preg_match('/^[a-z][a-z0-9_-]{0,23}$/D', $chave)) {
            throw new DomainException('A chave da categoria deve ter de 1 a 24 letras minúsculas, números, hífen ou sublinhado, iniciando por letra.');
        }
        return $chave;
    }

    private function salvarCategoria(mixed $chave, mixed $nome, bool $criar): array
    {
        $chave = $this->chaveCategoria($chave);
        if (!\is_string($nome) || !\mb_check_encoding($nome, 'UTF-8') || \trim($nome) === '' || \mb_strlen(\trim($nome), 'UTF-8') > 80 || \str_contains($nome, "\0")) {
            throw new DomainException('O nome da categoria deve conter de 1 a 80 caracteres UTF-8, sem byte nulo.');
        }
        $nome = \trim($nome);
        $existentes = $this->gestao->categoriasCoincidentes($chave, $nome);
        if ($criar && $existentes !== []) {
            throw new DomainException('A chave ou o nome da categoria já está cadastrado.');
        }
        if (!$criar) {
            $this->categoria($chave);
            if (\array_diff($existentes, [$chave]) !== []) {
                throw new DomainException('O nome já pertence a outra categoria.');
            }
        }
        return $this->gestao->salvarCategoria($chave, $nome, $criar);
    }

    private function excluirCategoria(mixed $chave): array
    {
        $chave = $this->chaveCategoria($chave);
        if ((int) $this->categoria($chave)['perguntas'] !== 0) {
            throw new DomainException('A categoria ainda possui perguntas. Mova ou exclua suas perguntas antes.');
        }
        return $this->gestao->excluirCategoria($chave);
    }

    private function inteiro(mixed $valor, int $minimo, int $maximo): int
    {
        if (!\is_int($valor) || $valor < $minimo || $valor > $maximo) {
            throw new DomainException('Informe identificadores e paginação com números inteiros dentro dos limites.');
        }
        return $valor;
    }

    private function booleano(mixed $valor): bool
    {
        if (!\is_bool($valor)) {
            throw new DomainException('Informe verdadeiro ou falso para arquivada.');
        }
        return $valor;
    }

    private function confirmar(array $argumentos, mixed $identificador): mixed
    {
        if (($argumentos['confirmar'] ?? false) !== true) {
            throw new DomainException('A exclusão definitiva exige confirmar=true após autorização do usuário.');
        }
        return $identificador;
    }

    private function lote(mixed $perguntas): array
    {
        if (!\is_array($perguntas) || !\array_is_list($perguntas) || \count($perguntas) < 1 || \count($perguntas) > 100) {
            throw new DomainException('Envie uma lista de uma a cem perguntas.');
        }
        foreach ($perguntas as &$pergunta) {
            if (!\is_array($pergunta)) {
                throw new DomainException('Cada pergunta do lote deve ser um objeto.');
            }
            $pergunta = Pergunta::validar($pergunta);
            $this->categoria($pergunta['tema']);
        }
        return $perguntas;
    }

    private function editar(array $argumentos): array
    {
        $identificador = $argumentos['identificador'];
        unset($argumentos['identificador']);
        if ($argumentos === []) {
            throw new DomainException('Informe pelo menos um campo da pergunta para alterar.');
        }
        $atual = $this->gestao->obterPergunta($identificador);
        unset($atual['identificador'], $atual['arquivada']);
        $pergunta = Pergunta::validar(\array_replace($atual, $argumentos));
        $this->categoria($pergunta['tema']);
        return $this->gestao->editarPergunta($identificador, $pergunta);
    }

    private function partida(mixed $identificador): string
    {
        if (!\is_string($identificador) || !\preg_match('/^[a-f0-9]{32}$/D', $identificador)) {
            throw new DomainException('Informe o identificador hexadecimal da partida com 32 caracteres.');
        }
        return $identificador;
    }
}
