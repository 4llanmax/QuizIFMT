<?php

/**
 * Confere o registro e o encerramento de reprovações com a API do GitHub substituída.
 * Redator: Allan Max
 * Data de criação: 12/11/2026
 * Status: Revisado
 * Data do Status: 12/11/2026
 */

declare(strict_types=1);

namespace Testes\Integracao\Automacao;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ReprovacaoTeste extends TestCase
{
    /** Repositório, pessoas, datas e identificadores são fictícios e não acessam a rede. */
    private function cenario(array $ajustes = []): array
    {
        $sha = str_repeat('a', 40);
        return array_replace([
            'execucao' => ['id' => 10, 'name' => 'Validação', 'path' => '.github/workflows/Validacao.yml',
                'event' => 'pull_request', 'status' => 'completed', 'conclusion' => 'failure', 'head_sha' => $sha,
                'repository' => ['full_name' => 'Projeto/Exemplo'], 'created_at' => '2000-01-01T00:00:00Z'],
            'pull' => ['number' => 14, 'state' => 'open', 'merged' => false,
                'user' => ['login' => 'PessoaFicticia', 'type' => 'User'],
                'base' => ['ref' => 'Validacao', 'sha' => str_repeat('b', 40),
                    'repo' => ['full_name' => 'Projeto/Exemplo']],
                'head' => ['ref' => 'Desenvolvimento_PessoaFicticia', 'sha' => $sha]],
            'jobs' => [['id' => 20, 'name' => 'conferirAutomacao', 'conclusion' => 'failure',
                'runner_name' => 'Executor fictício', 'labels' => ['ubuntu-latest'],
                'steps' => [['number' => 4, 'name' => 'Executar os testes da automação', 'conclusion' => 'failure']]]],
            'issues' => [], 'associados' => [['number' => 14]], 'statusIssue' => 201,
        ], $ajustes);
    }

    /** Usa a implementação real; somente o transporte HTTP é substituído pelo cenário. */
    private function apoio(array $cenario, array &$pedidos): \Reprovacao
    {
        $arquivo = dirname(__DIR__, 3) . '/.github/Verificacoes/Reprovacao.php';
        self::assertFileExists($arquivo, 'O registro automático da reprovação ainda não foi implementado.');
        require_once $arquivo;
        $transporte = static function (string $metodo, string $caminho, ?array $dados = null) use ($cenario, &$pedidos): array {
            $pedidos[] = ['metodo' => $metodo, 'caminho' => $caminho, 'dados' => $dados];
            $base = '/repos/Projeto/Exemplo';
            if ($metodo === 'POST' && $caminho === "$base/issues") {
                return ['status' => $cenario['statusIssue'], 'dados' => ['number' => 99,
                    'message' => 'Resposta fictícia com token-ficticio que não deve aparecer no diagnóstico']];
            }
            if ($metodo === 'PATCH' && $caminho === "$base/pulls/14") {
                return ['status' => 200, 'dados' => ['state' => 'closed']];
            }
            if ($caminho === "$base/actions/runs/10") {
                return ['status' => 200, 'dados' => $cenario['execucao']];
            }
            if (str_starts_with($caminho, "$base/commits/")) {
                return ['status' => 200, 'dados' => $cenario['associados']];
            }
            if ($caminho === "$base/pulls/14") {
                return ['status' => 200, 'dados' => $cenario['pull']];
            }
            if (str_contains($caminho, '/jobs?')) {
                $jobs = str_ends_with($caminho, 'page=2') ? ($cenario['jobsSegundaPagina'] ?? []) : $cenario['jobs'];
                return ['status' => 200, 'dados' => ['jobs' => $jobs]];
            }
            if (str_starts_with($caminho, "$base/issues?")) {
                return ['status' => 200, 'dados' => $cenario['issues']];
            }
            throw new \RuntimeException('Requisição não prevista pelo cenário: ' . $metodo . ' ' . $caminho);
        };
        return new \Reprovacao('Projeto/Exemplo', 'token-ficticio', $transporte);
    }

    /** O evento apenas identifica a execução; os demais dados são conferidos na API. */
    private function evento(): array
    {
        return ['repository' => ['full_name' => 'Projeto/Exemplo'], 'workflow_run' => ['id' => 10]];
    }

    #[Test]
    public function registraIssueAntesDeEncerrarComEvidenciasEResponsavel(): void
    {
        $pedidos = [];
        $resultado = $this->apoio($this->cenario(), $pedidos)->processar($this->evento());
        self::assertSame([14], $resultado['encerrados']);
        self::assertSame([99], $resultado['issues']);
        $escritas = array_values(array_filter($pedidos, static fn (array $pedido): bool => $pedido['metodo'] !== 'GET'));
        self::assertCount(2, $escritas);
        self::assertSame('POST', $escritas[0]['metodo']);
        self::assertSame('/repos/Projeto/Exemplo/issues', $escritas[0]['caminho']);
        self::assertSame(['PessoaFicticia'], $escritas[0]['dados']['assignees']);
        $corpo = $escritas[0]['dados']['body'];
        foreach (['#14', str_repeat('a', 40), str_repeat('b', 40), 'Desenvolvimento_PessoaFicticia',
            'PessoaFicticia', 'conferirAutomacao', 'Executar os testes da automação', 'failure',
            'ubuntu-latest', '2000-01-01T00:00:00Z', '/actions/runs/10', '/pull/14'] as $evidencia) {
            self::assertStringContainsString($evidencia, $corpo);
        }
        self::assertSame(['state' => 'closed'], $escritas[1]['dados']);
    }

    #[Test]
    public function cancelamentosETempoEsgotadoTambemExigemNovaSubmissao(): void
    {
        foreach (['cancelled', 'timed_out', 'action_required', 'skipped', 'neutral', 'stale'] as $conclusao) {
            $cenario = $this->cenario();
            $cenario['execucao']['conclusion'] = $conclusao;
            $pedidos = [];
            $resultado = $this->apoio($cenario, $pedidos)->processar($this->evento());
            self::assertSame([14], $resultado['encerrados'], $conclusao);
        }
    }

    #[Test]
    public function naoEscreveParaSucessoEventoIncorretoOuExecucaoIncompleta(): void
    {
        foreach (['conclusion' => 'success', 'event' => 'push', 'status' => 'in_progress',
            'path' => '.github/workflows/Outra.yml'] as $campo => $valor) {
            $cenario = $this->cenario();
            $cenario['execucao'][$campo] = $valor;
            $pedidos = [];
            $resultado = $this->apoio($cenario, $pedidos)->processar($this->evento());
            self::assertSame([], $resultado['encerrados']);
            self::assertSame([], array_filter($pedidos, static fn (array $pedido): bool => $pedido['metodo'] !== 'GET'));
        }
    }

    #[Test]
    public function naoEncerraPullRequestJaIntegradoDeOutroDestinoOuFechado(): void
    {
        foreach (['merged', 'destino', 'fechado'] as $caso) {
            $cenario = $this->cenario();
            if ($caso === 'merged') { $cenario['pull']['merged'] = true; }
            if ($caso === 'destino') { $cenario['pull']['base']['ref'] = 'Principal'; }
            if ($caso === 'fechado') { $cenario['pull']['state'] = 'closed'; }
            $pedidos = [];
            self::assertSame([], $this->apoio($cenario, $pedidos)->processar($this->evento())['encerrados']);
            self::assertSame([], array_filter($pedidos, static fn (array $pedido): bool => $pedido['metodo'] !== 'GET'));
        }
    }

    #[Test]
    public function outroCommitNoMesmoPullRequestNaoEvitaEncerramentoDaReprovacao(): void
    {
        $cenario = $this->cenario();
        $cenario['pull']['head']['sha'] = str_repeat('c', 40);
        $pedidos = [];
        self::assertSame([14], $this->apoio($cenario, $pedidos)->processar($this->evento())['encerrados']);
        $post = array_values(array_filter($pedidos, static fn (array $pedido): bool => $pedido['metodo'] === 'POST'))[0];
        self::assertStringContainsString('Commit reprovado: ' . str_repeat('a', 40), $post['dados']['body']);
        self::assertStringContainsString('Commit atual do pull request: ' . str_repeat('c', 40), $post['dados']['body']);
    }

    #[Test]
    public function retomaEncerramentoSemDuplicarIssueJaRegistrada(): void
    {
        $cenario = $this->cenario(['issues' => [['number' => 99,
            'body' => '<!-- reprovacao:14:' . str_repeat('a', 40) . ' -->']]]);
        $pedidos = [];
        self::assertSame([99], $this->apoio($cenario, $pedidos)->processar($this->evento())['issues']);
        self::assertSame([], array_filter($pedidos, static fn (array $pedido): bool => $pedido['metodo'] === 'POST'));
    }

    #[Test]
    public function falhaNoRegistroImpedeEncerramentoENaoExibeRespostaOuToken(): void
    {
        $pedidos = [];
        try {
            $this->apoio($this->cenario(['statusIssue' => 403]), $pedidos)->processar($this->evento());
            self::fail('A falha de autorização precisa permanecer visível.');
        } catch (\RuntimeException $erro) {
            self::assertStringContainsString('403', $erro->getMessage());
            self::assertStringNotContainsString('token-ficticio', $erro->getMessage());
        }
        self::assertSame([], array_filter($pedidos, static fn (array $pedido): bool => $pedido['metodo'] === 'PATCH'));
    }

    #[Test]
    public function registroNaoDependeDePermissaoParaAtribuirResponsavel(): void
    {
        $cenario = $this->cenario(['statusIssue' => 422]);
        $pedidos = [];
        try {
            $this->apoio($cenario, $pedidos)->processar($this->evento());
        } catch (\RuntimeException) {
            // Duas recusas continuam sendo uma falha; a segunda tentativa deve retirar só a atribuição.
        }
        $tentativas = array_values(array_filter($pedidos, static fn (array $pedido): bool => $pedido['metodo'] === 'POST'));
        self::assertCount(2, $tentativas);
        self::assertArrayNotHasKey('assignees', $tentativas[1]['dados']);
        self::assertStringContainsString('PessoaFicticia', $tentativas[1]['dados']['body']);
    }

    #[Test]
    public function incluiEtapasDeTodasAsPaginas(): void
    {
        $cenario = $this->cenario();
        $cenario['jobs'] = array_fill(0, 100, $cenario['jobs'][0]);
        $cenario['jobsSegundaPagina'] = [['id' => 21, 'name' => 'Falha da segunda página', 'conclusion' => 'failure']];
        $pedidos = [];
        $this->apoio($cenario, $pedidos)->processar($this->evento());
        $post = array_values(array_filter($pedidos, static fn (array $pedido): bool => $pedido['metodo'] === 'POST'))[0];
        self::assertStringContainsString('Falha da segunda página', $post['dados']['body']);
    }

    #[Test]
    public function eventoDeOutroRepositorioNaoAcessaApi(): void
    {
        $pedidos = [];
        $apoio = $this->apoio($this->cenario(), $pedidos);
        $evento = $this->evento();
        $evento['repository']['full_name'] = 'Outro/Exemplo';
        $this->expectException(\InvalidArgumentException::class);
        try { $apoio->processar($evento); } finally { self::assertSame([], $pedidos); }
    }

    #[Test]
    public function fluxoDeReprovacaoUsaCodigoDaBranchPadraoSemExecutarTestes(): void
    {
        $arquivo = dirname(__DIR__, 3) . '/.github/workflows/Reprovacao.yml';
        self::assertFileExists($arquivo);
        $texto = file_get_contents($arquivo);
        self::assertStringContainsString('workflow_run:', $texto);
        self::assertStringContainsString('types: [completed]', $texto);
        self::assertStringContainsString('ref: ${{ github.event.repository.default_branch }}', $texto);
        self::assertStringNotContainsString('ref: ${{ github.event.workflow_run.head_sha }}', $texto);
        self::assertStringNotContainsString('->executar(', $texto);
        self::assertStringContainsString('shell: php {0}', $texto);
        self::assertStringContainsString('issues: write', $texto);
        self::assertStringContainsString('pull-requests: write', $texto);
    }
}
