<?php

/**
 * Registra a reprovação de uma execução e encerra seu pull request para Validacao.
 * Redator: Allan Max
 * Data de criação: 12/11/2026
 * Status: Revisado
 * Data do Status: 12/11/2026
 */

declare(strict_types=1);

final class Reprovacao
{
    /** @var Closure Transporte HTTP substituível: método, caminho e dados produzem status e dados da resposta. */
    private Closure $transporte;

    /** Repositório e token pertencem à execução; o token não integra URLs, registros ou diagnósticos. */
    public function __construct(private string $repositorio, private string $token, ?callable $transporte = null)
    {
        if (!preg_match('~\A[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+\z~', $repositorio)
            || $token === '' || strpbrk($token, "\r\n") !== false) {
            throw new InvalidArgumentException('Repositório ou autenticação da automação inválidos.');
        }
        $this->transporte = Closure::fromCallable($transporte ?? $this->http(...));
    }

    /** Envia somente à API fixa do GitHub, sem redirecionar a credencial nem registrar o corpo de erros. */
    private function http(string $metodo, string $caminho, ?array $dados): array
    {
        $cabecalhos = ['Accept: application/vnd.github+json', 'User-Agent: EstruturaPHP',
            'Authorization: Bearer ' . $this->token, 'X-GitHub-Api-Version: 2022-11-28'];
        $opcoes = ['method' => $metodo, 'header' => implode("\r\n", $cabecalhos),
            'ignore_errors' => true, 'follow_location' => 0, 'timeout' => 20];
        if ($dados !== null) {
            $opcoes['header'] .= "\r\nContent-Type: application/json";
            $opcoes['content'] = json_encode($dados, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        }
        $resposta = @file_get_contents('https://api.github.com' . $caminho, false,
            stream_context_create(['http' => $opcoes]));
        if ($resposta === false) {
            throw new RuntimeException('Não foi possível acessar a API do GitHub.');
        }
        $status = 0;
        foreach (http_get_last_response_headers() ?? [] as $linha) {
            if (preg_match('~^HTTP/\S+ (\d{3})~', $linha, $codigo)) { $status = (int) $codigo[1]; }
        }
        return ['status' => $status, 'dados' => $status >= 200 && $status < 300 && $resposta !== ''
            ? json_decode($resposta, true, flags: JSON_THROW_ON_ERROR) : []];
    }

    /** Preserva falhas HTTP sem propagar respostas que possam conter informações sensíveis. */
    private function api(string $metodo, string $caminho, ?array $dados = null): array
    {
        $resposta = ($this->transporte)($metodo, '/repos/' . $this->repositorio . $caminho, $dados);
        $status = $resposta['status'] ?? 0;
        if (!is_int($status) || $status < 200 || $status >= 300) {
            throw new RuntimeException('A API do GitHub recusou a operação: HTTP ' . (int) $status . '.', (int) $status);
        }
        if (!is_array($resposta['dados'] ?? null)) {
            throw new RuntimeException('A API do GitHub retornou dados inválidos.');
        }
        return $resposta['dados'];
    }

    /** Percorre todas as páginas; um limite excedido é falha, não uma listagem silenciosamente incompleta. */
    private function listar(string $caminho, ?string $campo = null): array
    {
        $itens = [];
        for ($pagina = 1; $pagina <= 50; ++$pagina) {
            $dados = $this->api('GET', $caminho . (str_contains($caminho, '?') ? '&' : '?')
                . 'per_page=100&page=' . $pagina);
            $lote = $campo !== null ? ($dados[$campo] ?? null) : $dados;
            if (!is_array($lote) || !array_is_list($lote)) {
                throw new RuntimeException('A API do GitHub retornou uma listagem inválida.');
            }
            $itens = array_merge($itens, $lote);
            if (count($lote) < 100) { return $itens; }
        }
        throw new RuntimeException('A listagem excedeu o limite de páginas da automação.');
    }

    /** Exibe metadados como texto, sem aceitar HTML, comandos de Markdown ou menções da entrega. */
    private function texto(mixed $valor): string
    {
        $texto = is_scalar($valor) ? (string) $valor : 'Pendente';
        return str_replace(['@', '`', '<', '>', '|', "\r", "\n"],
            ["@\u{200B}", "'", '&lt;', '&gt;', '/', ' ', ' '], $texto);
    }

    /** Reúne referências, commits, responsável e resultados; logs ficam no acesso próprio da execução. */
    private function corpo(array $execucao, array $pull, array $jobs, string $marcador): string
    {
        $origem = 'https://github.com/' . $this->repositorio;
        $numero = (int) $pull['number'];
        $id = (int) $execucao['id'];
        $linhas = [$marcador, '', 'O conjunto foi reprovado antes do merge em `Validacao`.', '',
            '- Pull request #' . $numero . ': ' . $origem . '/pull/' . $numero,
            '- Responsável pela correção: ' . $this->texto($pull['user']['login'] ?? 'Pendente'),
            '- Branch de origem: ' . $this->texto($pull['head']['ref'] ?? 'Pendente'),
            '- Commit reprovado: ' . $execucao['head_sha'],
            '- Commit atual do pull request: ' . $this->texto($pull['head']['sha'] ?? 'Pendente'),
            '- Commit de partida do destino: ' . $this->texto($pull['base']['sha'] ?? 'Pendente'),
            '- Resultado da execução: ' . $this->texto($execucao['conclusion']),
            '- Data e hora UTC: ' . $this->texto($execucao['updated_at'] ?? $execucao['created_at'] ?? 'Pendente'),
            '- Execução e logs: ' . $origem . '/actions/runs/' . $id,
            '- Configuração executada: ' . $origem . '/blob/' . $execucao['head_sha'] . '/.github/workflows/Validacao.yml',
            '', '| Tarefa ou etapa | Resultado | Executor ou referência |', '| --- | --- | --- |'];
        foreach ($jobs as $job) {
            $referencia = isset($job['id']) ? $origem . '/actions/runs/' . $id . '/job/' . (int) $job['id'] : 'Pendente';
            $executor = ($job['runner_name'] ?? 'Pendente') . ' ' . implode(', ', $job['labels'] ?? []);
            $linhas[] = '| ' . $this->texto($job['name'] ?? 'Pendente') . ' | '
                . $this->texto($job['conclusion'] ?? 'Pendente') . ' | ' . $this->texto($executor) . ' — ' . $referencia . ' |';
            foreach ($job['steps'] ?? [] as $etapa) {
                $linhas[] = '| ' . $this->texto($etapa['name'] ?? 'Pendente') . ' | '
                    . $this->texto($etapa['conclusion'] ?? 'Pendente') . ' | Etapa ' . (int) ($etapa['number'] ?? 0) . ' |';
            }
        }
        $corpo = implode("\n", $linhas);
        if (strlen($corpo) > 48000) {
            $corpo = mb_strcut($corpo, 0, 48000, 'UTF-8')
                . "\n\nListagem abreviada; os dados completos estão na execução indicada.";
        }
        return $corpo . "\n\nResultado esperado: todas as verificações obrigatórias aprovadas. "
            . 'A causa, os passos de reprodução, as versões efetivas e os códigos de saída não expostos pela API '
            . 'permanecem pendentes de investigação nos logs vinculados. Logs e dados sensíveis não são copiados automaticamente.'
            . "\n\nPreparar a correção na branch do responsável e abrir outro pull request para `Validacao`, "
            . 'com referência a este pull request, ao commit reprovado e a esta issue.';
    }

    /** Registra primeiro a issue; se a atribuição não for aceita, mantém o responsável no texto. */
    private function registrar(array $execucao, array $pull, array $jobs): int
    {
        $marcador = '<!-- reprovacao:' . (int) $pull['number'] . ':' . $execucao['head_sha'] . ' -->';
        foreach ($this->listar('/issues?state=all') as $issue) {
            if (!isset($issue['pull_request']) && str_contains($issue['body'] ?? '', $marcador)) {
                return (int) $issue['number'];
            }
        }
        $dados = ['title' => 'Reprovação do PR #' . (int) $pull['number'] . ' em Validacao',
            'body' => $this->corpo($execucao, $pull, $jobs, $marcador)];
        if (($pull['user']['type'] ?? '') === 'User') { $dados['assignees'] = [$pull['user']['login']]; }
        try { $issue = $this->api('POST', '/issues', $dados); }
        catch (RuntimeException $erro) {
            if ($erro->getCode() !== 422 || !isset($dados['assignees'])) { throw $erro; }
            unset($dados['assignees']);
            $issue = $this->api('POST', '/issues', $dados);
        }
        if (!is_int($issue['number'] ?? null) || $issue['number'] < 1) {
            throw new RuntimeException('A API não confirmou o registro da issue.');
        }
        return $issue['number'];
    }

    /** Confere o evento na API; outro commit no PR reprovado não substitui a obrigação de nova submissão. */
    public function processar(array $evento): array
    {
        $id = $evento['workflow_run']['id'] ?? null;
        if (($evento['repository']['full_name'] ?? '') !== $this->repositorio || !is_int($id) || $id < 1) {
            throw new InvalidArgumentException('Evento de reprovação inválido ou de outro repositório.');
        }
        $resultado = ['issues' => [], 'encerrados' => []];
        $execucao = $this->api('GET', '/actions/runs/' . $id);
        if (($execucao['event'] ?? '') !== 'pull_request' || ($execucao['status'] ?? '') !== 'completed'
            || ($execucao['path'] ?? '') !== '.github/workflows/Validacao.yml'
            || !in_array($execucao['conclusion'] ?? '',
                ['failure', 'cancelled', 'timed_out', 'action_required', 'skipped', 'neutral', 'stale'], true)) {
            return $resultado;
        }
        if (($execucao['repository']['full_name'] ?? '') !== $this->repositorio
            || ($execucao['id'] ?? null) !== $id
            || !preg_match('/\A(?:[0-9a-f]{40}|[0-9a-f]{64})\z/', $execucao['head_sha'] ?? '')) {
            throw new RuntimeException('A execução não possui identificação válida no repositório.');
        }
        $jobs = $this->listar('/actions/runs/' . $id . '/jobs', 'jobs');
        $associados = $execucao['pull_requests'] ?? [];
        if ($associados === []) { $associados = $this->listar('/commits/' . $execucao['head_sha'] . '/pulls'); }
        foreach ($associados as $associado) {
            $numero = $associado['number'] ?? null;
            if (!is_int($numero) || $numero < 1) { throw new RuntimeException('Pull request associado inválido.'); }
            $pull = $this->api('GET', '/pulls/' . $numero);
            if (($pull['state'] ?? '') !== 'open' || ($pull['merged'] ?? false)
                || ($pull['base']['ref'] ?? '') !== 'Validacao'
                || ($pull['base']['repo']['full_name'] ?? '') !== $this->repositorio) { continue; }
            $issue = $this->registrar($execucao, $pull, $jobs);
            $atual = $this->api('GET', '/pulls/' . $numero);
            if (($atual['state'] ?? '') !== 'open' || ($atual['merged'] ?? false)
                || ($atual['base']['ref'] ?? '') !== 'Validacao'
                || ($atual['base']['repo']['full_name'] ?? '') !== $this->repositorio) { continue; }
            $this->api('PATCH', '/pulls/' . $numero, ['state' => 'closed']);
            $resultado['issues'][] = $issue;
            $resultado['encerrados'][] = $numero;
        }
        return $resultado;
    }

    /** Lê apenas os dados e as credenciais da execução do GitHub Actions; falhas mantêm o workflow reprovado. */
    public static function executar(): int
    {
        try {
            $arquivo = getenv('GITHUB_EVENT_PATH');
            if (!$arquivo || !is_file($arquivo)) { throw new RuntimeException('Evento da execução ausente.'); }
            $evento = json_decode(file_get_contents($arquivo), true, flags: JSON_THROW_ON_ERROR);
            if (!is_array($evento)) { throw new RuntimeException('Evento da execução inválido.'); }
            $apoio = new self(getenv('GITHUB_REPOSITORY') ?: '', getenv('GITHUB_TOKEN') ?: '');
            echo json_encode($apoio->processar($evento), JSON_THROW_ON_ERROR), "\n";
            return 0;
        } catch (Throwable $erro) {
            fwrite(STDERR, 'Falha no registro da reprovação: ' . $erro->getMessage() . "\n");
            return 1;
        }
    }
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) { exit(Reprovacao::executar()); }
