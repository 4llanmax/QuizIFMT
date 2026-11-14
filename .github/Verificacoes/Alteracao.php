<?php

/**
 * Classifica a entrega pelos caminhos alterados e confere o Markdown próprio afetado.
 * Redator: Allan Max
 * Data de criação: 11/11/2026
 * Status: Revisado
 * Data do Status: 12/11/2026
 */

declare(strict_types=1);

final class Alteracao
{
    /** @var string Raiz explícita usada nas leituras e na comparação do Git. */
    private string $raiz;

    /** @var Closure Processo de Git, substituível nos testes sem executar comandos externos. */
    private Closure $executar;

    /** Recebe a raiz e, opcionalmente, o executor que retorna código, saída e erro do Git. */
    public function __construct(string $raiz, ?callable $executar = null)
    {
        $this->raiz = rtrim(realpath($raiz) ?: $raiz, '/\\');
        $this->executar = Closure::fromCallable($executar ?? $this->executarGit(...));
    }

    /** Identifica Markdown próprio; distribuições e caminhos ambíguos exigem a aplicação. */
    public function documentoProprio(string $caminho): bool
    {
        $partes = explode('/', $caminho);
        if ($caminho === '' || in_array('..', $partes, true) || str_contains($caminho, '\\')
            || str_starts_with($caminho, '/') || preg_match('/[\x00-\x1f]/', $caminho)
            || preg_match('//u', $caminho) !== 1) {
            return false;
        }
        if (in_array($caminho, ['README.md', 'AGENTS.md'], true)) {
            return true;
        }
        if ($partes[0] === 'Documentacao' && str_ends_with($caminho, '.md')) {
            return true;
        }
        return in_array($partes[0], ['Aplicacao', 'Configuracoes', 'Dados', 'Testes'], true)
            && count($partes) > 1 && end($partes) === 'README.md';
    }

    /** Dispensa a aplicação somente quando todos os caminhos conhecidos são documentais. */
    public function documental(array $caminhos): bool
    {
        if ($caminhos === []) {
            return false;
        }
        foreach ($caminhos as $caminho) {
            if (!$this->documentoProprio($caminho)) {
                return false;
            }
        }
        return true;
    }

    /** Exige a aplicação para produção e caminhos desconhecidos; apoios próprios têm suíte específica. */
    private function exigeAplicacao(array $caminhos): bool
    {
        if ($caminhos === []) {
            return true;
        }
        $automacao = ['.github/workflows/Validacao.yml', '.github/workflows/Reprovacao.yml',
            '.github/Verificacoes/Alteracao.php', '.github/Verificacoes/Reprovacao.php',
            '.github/Verificacoes/Validacao.php', '.githooks/pre-commit',
            '.githooks/ConferenciaDocumentosNumerados.php',
            'Ferramentas/PHPUnit/PHPUnit.xml', 'Testes/Apoio/Preparacao.php',
            'Testes/Integracao/Automacao/ValidacaoTeste.php', 'Testes/Integracao/Automacao/ReprovacaoTeste.php'];
        foreach ($caminhos as $caminho) {
            if (!$this->documentoProprio($caminho) && !in_array($caminho, $automacao, true)) {
                return true;
            }
        }
        return false;
    }

    /** Compara todo o envio ou o ancestral comum do pull request; incerteza não concede dispensa. */
    public function classificar(string $evento, array $dados): array
    {
        $referencia = $evento === 'pull_request'
            ? ($dados['pull_request']['base']['ref'] ?? '')
            : ($dados['ref'] ?? getenv('GITHUB_REF') ?: '');
        $destino = is_string($referencia) ? preg_replace('~^refs/heads/~', '', $referencia) : 'Outra';
        $desenvolvimento = preg_match('/\ADesenvolvimento_[A-Z][A-Za-z0-9]*\z/', $destino) === 1;
        if (!$desenvolvimento && !in_array($destino, ['Validacao', 'Principal'], true)) {
            $destino = 'Outra';
        }
        $resultado = ['documental' => false, 'aplicacao' => true, 'rigoroso' => !$desenvolvimento,
            'destino' => $destino, 'arquivos' => null, 'motivo' => 'Comparação indisponível.'];
        try {
            if ($evento === 'pull_request') {
                $origem = $dados['pull_request']['base']['sha'] ?? '';
                $fim = $dados['pull_request']['head']['sha'] ?? '';
                $intervalo = [is_string($origem) && is_string($fim) ? "$origem...$fim" : ''];
            } elseif ($evento === 'push') {
                $origem = $dados['before'] ?? '';
                $fim = $dados['after'] ?? '';
                $intervalo = [$origem, $fim];
            } else {
                $resultado['motivo'] = 'Execução sem intervalo conhecido exige a aplicação.';
                return $resultado;
            }
            foreach ([$origem, $fim] as $identificador) {
                if (!is_string($identificador)
                    || !preg_match('/\A(?:[0-9a-f]{40}|[0-9a-f]{64})\z/', $identificador)
                    || trim($identificador, '0') === '') {
                    return $resultado;
                }
            }
            $processo = ($this->executar)(['git', 'diff', '--name-only', '--no-renames', '-z', ...$intervalo, '--']);
            if ($processo['codigo'] !== 0) {
                throw new RuntimeException('O Git não concluiu a comparação.');
            }
            $caminhos = array_values(array_filter(explode("\0", $processo['saida']),
                static fn (string $caminho): bool => $caminho !== ''));
            $resultado['arquivos'] = $caminhos;
            $resultado['documental'] = $this->documental($caminhos);
            $resultado['aplicacao'] = $this->exigeAplicacao($caminhos);
            $resultado['motivo'] = 'Intervalo comparado sem detecção de renomeação.';
        } catch (Throwable) {
            $resultado['motivo'] = 'Falha na comparação; a aplicação permanece exigida.';
        }
        return $resultado;
    }

    /** Executa argumentos separados, sem interpolá-los em shell, preservando falhas do Git. */
    private function executarGit(array $comando): array
    {
        $processo = proc_open($comando, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $canais, $this->raiz, null, ['bypass_shell' => true]);
        if (!is_resource($processo)) {
            throw new RuntimeException('Não foi possível iniciar o Git.');
        }
        fclose($canais[0]);
        $saida = stream_get_contents($canais[1]);
        $erro = stream_get_contents($canais[2]);
        fclose($canais[1]);
        fclose($canais[2]);
        return ['codigo' => proc_close($processo), 'saida' => $saida, 'erro' => $erro];
    }

    /** Retira modelos em blocos de código, sem tratá-los como títulos ou referências reais. */
    private function semBlocos(string $texto): string
    {
        return preg_replace('/^(`{3,}|~{3,})[^\n]*\n.*?^\1[^\n]*$/ms', '', $texto);
    }

    /** Extrai âncoras de títulos, conservando acentos e distinguindo títulos repetidos. */
    private function ancoras(string $texto): array
    {
        preg_match_all('/^#{1,6} (.+)$/m', $this->semBlocos($texto), $titulos);
        $repeticoes = [];
        $resultado = [];
        foreach ($titulos[1] as $titulo) {
            $base = str_replace(' ', '-', preg_replace('/[^\p{L}\p{N}_\s-]/u', '', mb_strtolower(trim($titulo))));
            $numero = $repeticoes[$base] ?? 0;
            $repeticoes[$base] = $numero + 1;
            $resultado[] = $numero ? "$base-$numero" : $base;
        }
        return $resultado;
    }

    /** Lista Markdown próprio, sem percorrer as distribuições de terceiros. */
    private function documentos(): array
    {
        $resultado = ['README.md', 'AGENTS.md'];
        foreach (['Documentacao', 'Aplicacao', 'Configuracoes', 'Dados', 'Testes'] as $pasta) {
            $diretorio = $this->raiz . '/' . $pasta;
            if (!is_dir($diretorio)) {
                continue;
            }
            $arquivos = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($diretorio,
                FilesystemIterator::SKIP_DOTS));
            foreach ($arquivos as $arquivo) {
                $relativo = str_replace('\\', '/', substr($arquivo->getPathname(), strlen($this->raiz) + 1));
                if ($arquivo->isFile() && $this->documentoProprio($relativo)) {
                    $resultado[] = $relativo;
                }
            }
        }
        return $resultado;
    }

    /** Confere cabeçalhos, revisão exigida e links locais; remoções também verificam referências de entrada. */
    public function conferirDocumentos(?array $caminhos, bool $exigirRevisao): array
    {
        $documentos = $this->documentos();
        $caminhos ??= $documentos;
        $referencias = $caminhos;
        foreach ($caminhos as $nome) {
            if ($this->documentoProprio($nome) && !file_exists($this->raiz . '/' . $nome)) {
                $referencias = array_merge($referencias, $documentos);
                break;
            }
        }
        $erros = [];
        foreach (array_unique($referencias) as $nome) {
            $arquivo = $this->raiz . '/' . $nome;
            if (!$this->documentoProprio($nome) || !is_file($arquivo)) {
                continue;
            }
            $real = str_replace('\\', '/', realpath($arquivo));
            if (!str_starts_with($real, str_replace('\\', '/', $this->raiz) . '/')) {
                $erros[] = "$nome: documento fora da raiz do projeto.";
                continue;
            }
            $texto = str_replace("\r\n", "\n", file_get_contents($arquivo));
            if (preg_match('//u', $texto) !== 1) {
                $erros[] = "$nome: codificação UTF-8 inválida.";
                continue;
            }
            $conteudo = $this->semBlocos($texto);
            $status = [];
            $cabecalho = (bool) preg_match('/^\|[^\n]*\| (Sem revisão|Em revisão|Revisado)\s*\|/m', $conteudo, $status);
            foreach (['Redator', 'Data de criação', 'Status', 'Data do Status'] as $campo) {
                $cabecalho = $cabecalho && str_contains($conteudo, $campo);
            }
            if (!$cabecalho) {
                $erros[] = "$nome: cabeçalho documental incompleto.";
            } elseif ($exigirRevisao && in_array($nome, $caminhos, true) && $status[1] !== 'Revisado') {
                $erros[] = "$nome: revisão documental ainda não concluída.";
            }
            $reais = preg_replace('/(`+).*?\1/s', '', $conteudo);
            preg_match_all('/\[[^\]\n]+\]\(([^)]+)\)/', $reais, $links);
            foreach ($links[1] as $destino) {
                $partes = parse_url($destino);
                if ($partes === false || isset($partes['scheme']) || isset($partes['host'])) {
                    continue;
                }
                $alvo = ($partes['path'] ?? '') !== ''
                    ? dirname($arquivo) . '/' . rawurldecode($partes['path']) : $arquivo;
                if (!file_exists($alvo)) {
                    $erros[] = "$nome: destino inexistente: $destino.";
                } elseif (isset($partes['fragment']) && (!is_file($alvo)
                    || !in_array(rawurldecode($partes['fragment']),
                        $this->ancoras(str_replace("\r\n", "\n", file_get_contents($alvo))), true))) {
                    $erros[] = "$nome: âncora inexistente: $destino.";
                }
            }
        }
        return $erros;
    }
}

// A inclusão pelos testes declara somente a classe; a execução direta lê o evento como dados.
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    $apoio = new Alteracao(dirname(__DIR__, 2));
    $resultado = ['documental' => false, 'aplicacao' => true, 'rigoroso' => true, 'destino' => 'Outra',
        'arquivos' => null, 'motivo' => 'Evento ilegível; aplicação exigida.'];
    $evento = getenv('GITHUB_EVENT_PATH');
    if ($evento && is_file($evento)) {
        try {
            $dados = json_decode(file_get_contents($evento), true, flags: JSON_THROW_ON_ERROR);
            if (is_array($dados)) {
                $resultado = $apoio->classificar(getenv('GITHUB_EVENT_NAME') ?: '', $dados);
            }
        } catch (JsonException) {
            // Evento inválido mantém os indicadores conservadores e não concede dispensa.
        }
    }
    if (in_array('--conferir-documentos', $argv, true)) {
        $erros = $apoio->conferirDocumentos($resultado['arquivos'],
            in_array($resultado['destino'], ['Validacao', 'Principal'], true));
        foreach ($erros as $erro) {
            echo "::error::$erro\n";
        }
        echo 'Conferência documental: ' . count($erros) . " erro(s). Links externos e conteúdo normativo exigem revisão.\n";
        exit($erros === [] ? 0 : 1);
    }
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) . "\n";
    $saida = getenv('GITHUB_OUTPUT');
    if ($saida) {
        $indicadores = '';
        foreach (['documental', 'aplicacao', 'rigoroso', 'destino'] as $chave) {
            $valor = $resultado[$chave];
            $indicadores .= $chave . '=' . (is_bool($valor) ? ($valor ? 'true' : 'false') : $valor) . "\n";
        }
        file_put_contents($saida, $indicadores, FILE_APPEND);
    }
}
