<?php

/**
 * Verifica classificação, documentação e etapas reais da automação em cópias isoladas.
 * Redator: Allan Max
 * Data de criação: 11/11/2026
 * Status: Revisado
 * Data do Status: 12/11/2026
 */

declare(strict_types=1);

namespace Testes\Integracao\Automacao;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ValidacaoTeste extends TestCase
{
    /** @var list<string> Pastas criadas por este cenário, removidas no encerramento. */
    private array $temporarios = [];

    /** Retorna a raiz independentemente do diretório usado para invocar o executor. */
    private function raiz(): string
    {
        return dirname(__DIR__, 3);
    }

    /** Cria somente uma pasta temporária identificável e pertencente a este cenário. */
    private function temporario(): string
    {
        $pasta = sys_get_temp_dir() . '/EstruturaPHP_Automacao_' . bin2hex(random_bytes(8));
        mkdir($pasta, 0700, true);
        $this->temporarios[] = $pasta;
        return $pasta;
    }

    /** Remove somente as pastas próprias verificadas dentro do temporário do sistema. */
    protected function tearDown(): void
    {
        foreach ($this->temporarios as $pasta) {
            $real = realpath($pasta);
            $prefixo = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . 'EstruturaPHP_Automacao_';
            self::assertIsString($real);
            self::assertStringStartsWith($prefixo, $real);
            $arquivos = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($real, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST,
            );
            foreach ($arquivos as $arquivo) {
                if ($arquivo->isDir() && !$arquivo->isLink()) {
                    rmdir($arquivo->getPathname());
                } else {
                    chmod($arquivo->getPathname(), 0600);
                    unlink($arquivo->getPathname());
                }
            }
            rmdir($real);
        }
        parent::tearDown();
    }

    /** Grava um arquivo fictício UTF-8, criando somente os diretórios que ele utiliza. */
    private function gravar(string $raiz, string $caminho, string $conteudo): void
    {
        $arquivo = $raiz . '/' . $caminho;
        if (!is_dir(dirname($arquivo))) {
            mkdir(dirname($arquivo), 0700, true);
        }
        file_put_contents($arquivo, $conteudo);
    }

    /** Carrega o apoio explicitamente; os cenários não dependem do autocarregamento da aplicação. */
    private function apoio(?string $raiz = null, ?callable $executar = null): \Alteracao
    {
        require_once $this->raiz() . '/.github/Verificacoes/Alteracao.php';
        return new \Alteracao($raiz ?? $this->raiz(), $executar);
    }

    /** Executa um processo sem shell intermediário e preserva saída, erro e código de término. */
    private function executar(array $comando, ?string $diretorio = null, ?array $ambiente = null): array
    {
        $processo = proc_open($comando, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $canais, $diretorio, $ambiente, ['bypass_shell' => true]);
        self::assertIsResource($processo);
        fclose($canais[0]);
        $saida = stream_get_contents($canais[1]);
        $erro = stream_get_contents($canais[2]);
        fclose($canais[1]);
        fclose($canais[2]);
        return ['codigo' => proc_close($processo), 'saida' => $saida, 'erro' => $erro];
    }

    /** Carrega as etapas PHP da automação sem inicializar a aplicação. */
    private function validacao(?string $raiz = null, ?callable $executar = null): \Validacao
    {
        require_once $this->raiz() . '/.github/Verificacoes/Validacao.php';
        return new \Validacao($raiz ?? $this->raiz(), $executar);
    }

    /** Prepara arquivos fictícios e confere a versão e a integridade usando somente PHP. */
    private function preparar(int $versao = 80512, bool $completa = true): array
    {
        $raiz = $this->temporario();
        $arquivos = ['Ferramentas/PHPStan/PHPStan.neon', 'Ferramentas/PHPCS/PHPCS.xml',
            'Ferramentas/PHPUnit/PHPUnit.xml', 'Testes/Apoio/Preparacao.php',
            'Aplicacao/Nucleo/Comuns/Funcoes/AutoCarregamento.php', 'Publico/index.php',
            'Configuracoes/Aplicacao/Exemplo.env', 'Testes/Unitario/CenarioTeste.php'];
        foreach ($arquivos as $arquivo) {
            $this->gravar($raiz, $arquivo, $completa ? "Conteúdo fictício.\n" : '');
        }
        foreach (['PHPStan', 'PHPCS', 'PHPCBF', 'PHPUnit'] as $ferramenta) {
            $this->gravar($raiz, "Ferramentas/$ferramenta/$ferramenta.phar", "Executável fictício.\n");
            $resumo = hash('sha256', "Executável fictício.\n");
            $this->gravar($raiz, "Documentacao/Ferramentas/$ferramenta.md",
                "| Integridade | SHA-256: `$resumo`; fonte: https://example.invalid/resumo |\n");
        }
        $erros = $this->validacao($raiz)->preparar($versao);
        return ['codigo' => $erros === [] ? 0 : 1, 'saida' => implode("\n", $erros), 'erro' => '', 'raiz' => $raiz];
    }

    /** Produz Markdown fictício com os metadados exigidos para os cenários documentais. */
    private function documento(string $status = 'Revisado', string $corpo = ''): string
    {
        return "# Documento\n\n| Redator | Data de criação | Status | Data do Status |\n"
            . "| --- | --- | --- | --- |\n| Pendente | 11/11/2026 | $status | 11/11/2026 |\n"
            . "\n## Objetivo\n\n$corpo\n";
    }

    #[Test]
    public function fluxoUsaPhpEExecutorDoProjeto(): void
    {
        $texto = file_get_contents($this->raiz() . '/.github/workflows/Validacao.yml');
        self::assertStringContainsString("->executar('classificar')", $texto);
        self::assertStringContainsString("->executar('testes')", $texto);
        self::assertStringNotContainsString('python', $texto);
        self::assertStringNotContainsString('.py', $texto);
    }

    #[Test]
    public function testesDoGithubExecutamSomenteNoPullRequestParaValidacao(): void
    {
        $texto = file_get_contents($this->raiz() . '/.github/workflows/Validacao.yml');
        preg_match('/^on:\n(.*?)(?=^\S)/ms', $texto, $gatilhos);
        self::assertSame("  pull_request:\n    branches: [Validacao]\n\n", $gatilhos[1] ?? '');
    }

    #[Test]
    public function desenvolvimentoPessoalSubstituiBranchCompartilhada(): void
    {
        $apoio = $this->apoio(executar: static fn (): array =>
            ['codigo' => 0, 'saida' => "README.md\0", 'erro' => '']);
        foreach (['Desenvolvimento_AllanMax', 'Desenvolvimento_PessoaFicticia'] as $branch) {
            $resultado = $apoio->classificar('push', ['before' => str_repeat('a', 40),
                'after' => str_repeat('b', 40), 'ref' => 'refs/heads/' . $branch]);
            self::assertSame($branch, $resultado['destino']);
            self::assertFalse($resultado['rigoroso']);
        }
        foreach (['Desenvolvimento', 'Desenvolvimento_', 'Desenvolvimento_pessoa'] as $branch) {
            $resultado = $apoio->classificar('push', ['before' => str_repeat('a', 40),
                'after' => str_repeat('b', 40), 'ref' => 'refs/heads/' . $branch]);
            self::assertSame('Outra', $resultado['destino']);
            self::assertTrue($resultado['rigoroso']);
        }
    }

    #[Test]
    public function preparaVerificacoesSemManifestoIntermediario(): void
    {
        $resultado = $this->preparar();
        self::assertSame(0, $resultado['codigo'], $resultado['saida'] . $resultado['erro']);
        self::assertSame([], $this->validacao($resultado['raiz'])->integridade(['PHPStan', 'PHPCS', 'PHPCBF', 'PHPUnit']));
    }

    #[Test]
    public function recusaPhpAnteriorAoMinimo(): void
    {
        $resultado = $this->preparar(80511);
        self::assertNotSame(0, $resultado['codigo']);
        self::assertStringContainsString('PHP 8.5.12', $resultado['saida']);
    }

    #[Test]
    public function informaTodasAsPendenciasDePreparacao(): void
    {
        $resultado = $this->preparar(completa: false);
        self::assertNotSame(0, $resultado['codigo']);
        self::assertStringContainsString('Arquivo necessário ausente ou vazio', $resultado['saida']);
        self::assertStringNotContainsString('No such file or directory', $resultado['erro']);
    }

    #[Test]
    public function reconheceSomenteMarkdownProprio(): void
    {
        self::assertTrue($this->apoio()->documental(['README.md', 'AGENTS.md',
            'Documentacao/02Estrutura.md', 'Aplicacao/Site/README.md', 'Testes/Unitario/README.md']));
    }

    #[Test]
    public function exigeAplicacaoParaCodigoEntregasMistasEDistribuicoes(): void
    {
        foreach ([[], ['Publico/index.php'], ['Documentacao/Regra.md', 'Publico/index.php'],
            ['Externos/PHPMailer-7.1.1/README.md'], ['Ferramentas/Manual.md'],
            ['.github/workflows/Validacao.yml'], ['Aplicacao/Guia.md'],
            ['Documentacao/../Publico/index.php'], ['README.MD']] as $caminhos) {
            self::assertFalse($this->apoio()->documental($caminhos), json_encode($caminhos));
        }
    }

    #[Test]
    public function alteracaoSomenteDaAutomacaoNaoExigeAplicacao(): void
    {
        $caminhos = ['.github/workflows/Validacao.yml', '.github/Verificacoes/Alteracao.php',
            '.github/workflows/Reprovacao.yml', '.github/Verificacoes/Reprovacao.php',
            'Testes/Integracao/Automacao/ReprovacaoTeste.php',
            'Ferramentas/PHPUnit/PHPUnit.xml', 'Testes/Apoio/Preparacao.php',
            '.github/Verificacoes/Validacao.php', '.githooks/pre-commit',
            '.githooks/ConferenciaDocumentosNumerados.php',
            'Testes/Integracao/Automacao/ValidacaoTeste.php', 'README.md'];
        $apoio = $this->apoio(executar: static fn (): array =>
            ['codigo' => 0, 'saida' => implode("\0", $caminhos) . "\0", 'erro' => '']);
        $resultado = $apoio->classificar('push', ['before' => str_repeat('a', 40),
            'after' => str_repeat('b', 40), 'ref' => 'refs/heads/Principal']);
        self::assertFalse($resultado['documental']);
        self::assertArrayHasKey('aplicacao', $resultado);
        self::assertFalse($resultado['aplicacao']);
    }

    #[Test]
    public function codigoDaAplicacaoOuCaminhoDesconhecidoMantemValidacaoCompleta(): void
    {
        foreach (['Publico/index.php', 'Aplicacao/Site/Controles/Entrada.php',
            'Testes/Integracao/AplicacaoTeste.php', '.github/workflows/Outra.yml'] as $caminho) {
            $apoio = $this->apoio(executar: static fn (): array => ['codigo' => 0,
                'saida' => ".github/workflows/Validacao.yml\0$caminho\0", 'erro' => '']);
            $resultado = $apoio->classificar('push', ['before' => str_repeat('a', 40),
                'after' => str_repeat('b', 40), 'ref' => 'refs/heads/Validacao']);
            self::assertArrayHasKey('aplicacao', $resultado);
            self::assertTrue($resultado['aplicacao']);
        }
    }

    #[Test]
    public function comparaAncestralComumDoPullRequest(): void
    {
        $comando = [];
        $apoio = $this->apoio(executar: static function (array $argumentos) use (&$comando): array {
            $comando = $argumentos;
            return ['codigo' => 0, 'saida' => "Documentacao/02Estrutura.md\0README.md\0", 'erro' => ''];
        });
        $dados = ['pull_request' => ['base' => ['sha' => str_repeat('a', 40), 'ref' => 'Validacao'],
            'head' => ['sha' => str_repeat('b', 40)]]];
        $resultado = $apoio->classificar('pull_request', $dados);
        self::assertTrue($resultado['documental']);
        self::assertTrue($resultado['rigoroso']);
        self::assertSame('Validacao', $resultado['destino']);
        self::assertContains(str_repeat('a', 40) . '...' . str_repeat('b', 40), $comando);
        self::assertContains('--no-renames', $comando);
        self::assertContains('-z', $comando);
    }

    #[Test]
    public function comparaTodoIntervaloDoEnvio(): void
    {
        $comando = [];
        $apoio = $this->apoio(executar: static function (array $argumentos) use (&$comando): array {
            $comando = $argumentos;
            return ['codigo' => 0, 'saida' => "Publico/index.php\0", 'erro' => ''];
        });
        $resultado = $apoio->classificar('push', ['before' => str_repeat('a', 40),
            'after' => str_repeat('b', 40), 'ref' => 'refs/heads/Desenvolvimento_PessoaFicticia']);
        self::assertFalse($resultado['documental']);
        self::assertFalse($resultado['rigoroso']);
        self::assertSame([str_repeat('a', 40), str_repeat('b', 40), '--'], array_slice($comando, -3));
    }

    #[Test]
    public function execucaoManualPrimeiroEnvioEShaInvalidoExigemAplicacao(): void
    {
        $dados = ['before' => str_repeat('0', 40), 'after' => str_repeat('b', 40), 'ref' => 'refs/heads/Principal'];
        self::assertFalse($this->apoio()->classificar('push', $dados)['documental']);
        self::assertTrue($this->apoio()->classificar('workflow_dispatch', $dados)['rigoroso']);
        $dados['before'] = '--output=arquivo';
        $executou = false;
        $apoio = $this->apoio(executar: static function () use (&$executou): array {
            $executou = true;
            return ['codigo' => 0, 'saida' => "README.md\0", 'erro' => ''];
        });
        self::assertFalse($apoio->classificar('push', $dados)['documental']);
        self::assertFalse($executou, 'O Git não deve receber identificador inválido.');
    }

    #[Test]
    public function falhaDoGitNaoDispensaAplicacao(): void
    {
        $apoio = $this->apoio(executar: static fn (): array => ['codigo' => 1, 'saida' => '', 'erro' => 'Falha fictícia.']);
        $resultado = $apoio->classificar('push', ['before' => str_repeat('a', 40),
            'after' => str_repeat('b', 40), 'ref' => 'refs/heads/Principal']);
        self::assertFalse($resultado['documental']);
        self::assertTrue($resultado['rigoroso']);
    }

    #[Test]
    public function renomeacaoDeCodigoParaMarkdownContinuaExecutavel(): void
    {
        self::assertFalse($this->apoio()->documental(['Publico/index.php', 'Documentacao/Entrada.md']));
    }

    #[Test]
    public function eventoIlegivelPublicaIndicadoresConservadores(): void
    {
        $raiz = $this->temporario();
        $this->gravar($raiz, 'Evento.json', 'JSON inválido');
        $ambiente = getenv();
        $ambiente['GITHUB_EVENT_PATH'] = $raiz . '/Evento.json';
        $ambiente['GITHUB_EVENT_NAME'] = 'pull_request';
        $ambiente['GITHUB_OUTPUT'] = $raiz . '/Saida';
        $resultado = $this->executar([PHP_BINARY, $this->raiz() . '/.github/Verificacoes/Alteracao.php'],
            $this->raiz(), $ambiente);
        self::assertSame(0, $resultado['codigo'], $resultado['erro']);
        self::assertFalse(json_decode($resultado['saida'], true, flags: JSON_THROW_ON_ERROR)['documental']);
        self::assertSame("documental=false\naplicacao=true\nrigoroso=true\ndestino=Outra\n", file_get_contents($raiz . '/Saida'));
    }

    #[Test]
    public function confereLinksEAncorasLocais(): void
    {
        $raiz = $this->temporario();
        $this->gravar($raiz, 'README.md', $this->documento(corpo: '[Seção](Documentacao/Assunto.md#seção)'));
        $this->gravar($raiz, 'Documentacao/Assunto.md', "# Assunto\n\n## Seção\n");
        self::assertSame([], $this->apoio($raiz)->conferirDocumentos(['README.md'], true));
        $this->gravar($raiz, 'Documentacao/Assunto.md', "# Assunto\n");
        self::assertNotEmpty($this->apoio($raiz)->conferirDocumentos(['README.md'], true));
    }

    #[Test]
    public function exigeRevisadoSomenteNaPromocao(): void
    {
        $raiz = $this->temporario();
        $this->gravar($raiz, 'README.md', $this->documento('Sem revisão'));
        self::assertSame([], $this->apoio($raiz)->conferirDocumentos(['README.md'], false));
        self::assertNotEmpty($this->apoio($raiz)->conferirDocumentos(['README.md'], true));
    }

    #[Test]
    public function recusaCabecalhoIncompletoEDestinoAusente(): void
    {
        $raiz = $this->temporario();
        $this->gravar($raiz, 'README.md', "# Documento\n\n[Destino](Ausente.md)\n");
        self::assertGreaterThanOrEqual(2, count($this->apoio($raiz)->conferirDocumentos(['README.md'], false)));
    }

    #[Test]
    public function ignoraModelosDeLinksEmCodigo(): void
    {
        $raiz = $this->temporario();
        $corpo = "Fórmula `[Título]([caminho]#[ancora])`.\n\n```markdown\n[Modelo](Outro.md)\n```\n";
        $this->gravar($raiz, 'README.md', $this->documento(corpo: $corpo));
        self::assertSame([], $this->apoio($raiz)->conferirDocumentos(['README.md'], true));
    }

    #[Test]
    public function naoLeDocumentoRemovido(): void
    {
        self::assertSame([], $this->apoio($this->temporario())->conferirDocumentos(['README.md'], false));
    }

    #[Test]
    public function detectaReferenciaDeEntradaParaDocumentoRemovido(): void
    {
        $raiz = $this->temporario();
        $this->gravar($raiz, 'Aplicacao/Site/README.md', "# Site\n\n[Documento](../../Documentacao/Removido.md)\n");
        $erros = $this->apoio($raiz)->conferirDocumentos(['Documentacao/Removido.md'], false);
        self::assertStringContainsString('destino inexistente', implode("\n", $erros));
    }

    #[Test]
    public function ligaAplicacaoAClassificacaoEDestino(): void
    {
        $texto = file_get_contents($this->raiz() . '/.github/workflows/Validacao.yml');
        $tarefa = explode("\n  validar:\n", $texto, 2)[1];
        $cabecalho = explode("    steps:\n", $tarefa, 2)[0];
        self::assertStringContainsString('needs: classificarAlteracao', $cabecalho);
        self::assertStringContainsString("needs.classificarAlteracao.outputs.aplicacao == 'true'", $cabecalho);
        self::assertStringContainsString("needs.classificarAlteracao.outputs.rigoroso == 'false'", $cabecalho);
        self::assertStringContainsString('image: php:8.5.12-apache', $cabecalho);
        self::assertStringContainsString('fetch-depth: 0', $texto);
    }

    #[Test]
    public function decisaoFinalExecutaMesmoQuandoDependenciasFalham(): void
    {
        $texto = file_get_contents($this->raiz() . '/.github/workflows/Validacao.yml');
        $resultado = explode("\n  conferirResultado:\n", $texto, 2)[1];
        self::assertStringContainsString('if: ${{ always() }}', $resultado);
        self::assertStringContainsString('RESULTADO_APLICACAO', $resultado);
        self::assertStringContainsString('RESULTADO_CLASSIFICACAO', $resultado);
    }

    #[Test]
    public function aplicaMatrizDeResultadosEDispensas(): void
    {
        $padrao = ['RESULTADO_CLASSIFICACAO' => 'success', 'RESULTADO_AUTOMACAO' => 'success',
            'RESULTADO_DOCUMENTACAO' => 'success', 'RESULTADO_NUMERADOS' => 'success',
            'RESULTADO_APLICACAO' => 'skipped', 'DOCUMENTAL' => 'true', 'APLICACAO' => 'false', 'RIGOROSO' => 'true'];
        $casos = [[[], true], [['RESULTADO_CLASSIFICACAO' => 'failure'], false],
            [['RESULTADO_AUTOMACAO' => 'failure'], false], [['RESULTADO_DOCUMENTACAO' => 'skipped'], false],
            [['RESULTADO_NUMERADOS' => 'failure'], false], [['RESULTADO_APLICACAO' => 'success'], false],
            [['DOCUMENTAL' => 'false', 'APLICACAO' => 'true', 'RESULTADO_APLICACAO' => 'success'], true],
            [['DOCUMENTAL' => 'false', 'APLICACAO' => 'true', 'RESULTADO_APLICACAO' => 'failure'], false],
            [['DOCUMENTAL' => 'false', 'APLICACAO' => 'true', 'RESULTADO_APLICACAO' => 'cancelled'], false],
            [['DOCUMENTAL' => 'false', 'APLICACAO' => 'true'], false],
            [['DOCUMENTAL' => 'false', 'APLICACAO' => 'true', 'RIGOROSO' => 'false', 'RESULTADO_APLICACAO' => 'failure'], true],
            [['DOCUMENTAL' => 'false', 'APLICACAO' => 'true', 'RIGOROSO' => 'false'], false],
            [['DOCUMENTAL' => 'false', 'APLICACAO' => 'true', 'RIGOROSO' => 'false', 'RESULTADO_APLICACAO' => 'cancelled'], false],
            [['DOCUMENTAL' => '', 'RESULTADO_APLICACAO' => 'success'], false],
            [['DOCUMENTAL' => 'false', 'RIGOROSO' => '', 'RESULTADO_APLICACAO' => 'success'], false],
            [['DOCUMENTAL' => 'false', 'APLICACAO' => 'false'], true],
            [['APLICACAO' => ''], false], [['APLICACAO' => 'true'], false]];
        foreach ($casos as [$alteracoes, $aprovado]) {
            $erros = $this->validacao()->resultado(array_replace($padrao, $alteracoes));
            self::assertSame($aprovado, $erros === [], json_encode($alteracoes) . implode("\n", $erros));
        }
    }

    #[Test]
    public function recusaPharAlteradoOuResumoInvalido(): void
    {
        $resultado = $this->preparar();
        $raiz = $resultado['raiz'];
        $this->gravar($raiz, 'Ferramentas/PHPUnit/PHPUnit.phar', 'Conteúdo alterado');
        self::assertNotEmpty($this->validacao($raiz)->integridade(['PHPUnit']));
        $this->gravar($raiz, 'Documentacao/Ferramentas/PHPUnit.md', 'Sem resumo');
        self::assertNotEmpty($this->validacao($raiz)->integridade(['PHPUnit']));
    }

    #[Test]
    public function executaComandosExternosComoArgumentosSemInterpretador(): void
    {
        $comandos = [];
        $apoio = $this->validacao(executar: static function (array $comando) use (&$comandos): int {
            $comandos[] = $comando;
            return 0;
        });
        self::assertSame(0, $apoio->executar('phpstan'));
        self::assertSame(['runuser', '-u', 'www-data', '--', PHP_BINARY], array_slice($comandos[0], 0, 5));
        self::assertContains('Ferramentas/PHPStan/PHPStan.phar', $comandos[0]);
    }

    #[Test]
    public function interrompeDependenciasQuandoProgramaExternoFalha(): void
    {
        $raiz = $this->temporario();
        $arquivo = var_export($this->raiz() . '/.github/Verificacoes/Validacao.php', true);
        $codigo = '<?php require ' . $arquivo . '; $chamadas = 0; '
            . '$apoio = new Validacao(getcwd(), static function (array $comando) use (&$chamadas): int '
            . '{ $chamadas++; return 1; }); $resultado = $apoio->executar("dependencias"); '
            . 'echo $chamadas; exit($resultado);';
        $this->gravar($raiz, 'Cenario.php', $codigo);
        $resultado = $this->executar([PHP_BINARY, $raiz . '/Cenario.php'], $raiz);
        self::assertSame(1, $resultado['codigo']);
        self::assertSame('1', $resultado['saida']);
        self::assertStringContainsString('Programa externo reprovado', $resultado['erro']);
    }

    #[Test]
    public function fluxoExecutaTodosOsBlocosDiretamenteComPhp(): void
    {
        $texto = file_get_contents($this->raiz() . '/.github/workflows/Validacao.yml');
        self::assertStringContainsString('shell: php {0}', $texto);
        preg_match_all('/^        run: \|\n((?:          .*\n|\n)+)/m', $texto, $blocos);
        self::assertNotEmpty($blocos[1]);
        foreach ($blocos[1] as $bloco) {
            self::assertStringStartsWith('          <?php', $bloco);
        }
        self::assertStringNotContainsString('shell: ' . 'bash', $texto);
        self::assertStringNotContainsString('.sh', $texto);
    }

    /** Cria registro e documentos fictícios, incluindo finais de linha LF e CRLF. */
    private function numerados(): string
    {
        $raiz = $this->temporario();
        $this->gravar($raiz, 'Documentacao/00Regras.md', "# Regras\r\n");
        $resumo = hash('sha256', "# Regras\n");
        $this->gravar($raiz, 'Documentacao/Ambiente/GitHub.md',
            "| `Documentacao/00Regras.md` | `$resumo` |\n");
        return $raiz;
    }

    #[Test]
    public function confereNumeradosERecusaArquivoExtraAlteradoOuAusente(): void
    {
        require_once $this->raiz() . '/.githooks/ConferenciaDocumentosNumerados.php';
        $raiz = $this->numerados();
        $apoio = new \ConferenciaDocumentosNumerados($raiz);
        self::assertSame([], $apoio->conferir());
        $registro = file_get_contents($raiz . '/Documentacao/Ambiente/GitHub.md');
        $this->gravar($raiz, 'Documentacao/Ambiente/GitHub.md', $registro . $registro);
        self::assertNotEmpty($apoio->conferir());
        $this->gravar($raiz, 'Documentacao/Ambiente/GitHub.md', $registro);
        $this->gravar($raiz, 'Documentacao/01Extra.md', '# Extra');
        self::assertNotEmpty($apoio->conferir());
        unlink($raiz . '/Documentacao/01Extra.md');
        $this->gravar($raiz, 'Documentacao/00Regras.md', '# Mudança');
        self::assertNotEmpty($apoio->conferir());
        unlink($raiz . '/Documentacao/00Regras.md');
        self::assertNotEmpty($apoio->conferir());
        unlink($raiz . '/Documentacao/Ambiente/GitHub.md');
        self::assertNotEmpty($apoio->conferir());
    }

    #[Test]
    public function ganchoConfereIndicePreservaCopiaENaoPermiteMudancaSemAutorizacao(): void
    {
        $raiz = $this->numerados();
        mkdir($raiz . '/.githooks');
        foreach (['pre-commit', 'ConferenciaDocumentosNumerados.php'] as $arquivo) {
            copy($this->raiz() . '/.githooks/' . $arquivo, $raiz . '/.githooks/' . $arquivo);
        }
        $ambiente = getenv();
        unset($ambiente['DOCUMENTOS_NUMERADOS_AUTORIZADOS']);
        foreach ([['git', 'init', '--quiet'], ['git', 'add', '.']] as $comando) {
            self::assertSame(0, $this->executar($comando, $raiz, $ambiente)['codigo']);
        }
        // Referência inicial fictícia: commit-tree não dispara ganchos da cópia real.
        $arvore = trim($this->executar(['git', 'write-tree'], $raiz)['saida']);
        $ambiente['GIT_AUTHOR_NAME'] = $ambiente['GIT_COMMITTER_NAME'] = 'Allan Max';
        $ambiente['GIT_AUTHOR_EMAIL'] = $ambiente['GIT_COMMITTER_EMAIL'] = 'allan@example.invalid';
        $commit = $this->executar(['git', 'commit-tree', $arvore, '-m', 'Referência fictícia'], $raiz, $ambiente);
        self::assertSame(0, $commit['codigo'], $commit['erro']);
        self::assertSame(0, $this->executar(['git', 'update-ref', 'HEAD', trim($commit['saida'])], $raiz)['codigo']);
        // O Git dispara o gancho PHP na cópia descartável, sem mudar core.hooksPath na cópia real.
        foreach (['pre-commit', 'ConferenciaDocumentosNumerados.php'] as $arquivo) {
            copy($raiz . '/.githooks/' . $arquivo, $raiz . '/.git/hooks/' . $arquivo);
        }
        chmod($raiz . '/.git/hooks/pre-commit', 0755);
        self::assertSame(0, $this->executar(['git', 'hook', 'run', 'pre-commit'], $raiz, $ambiente)['codigo']);
        $gancho = [PHP_BINARY, $raiz . '/.githooks/pre-commit'];
        self::assertSame(0, $this->executar($gancho, $raiz, $ambiente)['codigo']);
        $this->gravar($raiz, 'Documentacao/00Regras.md', '# Mudança não preparada');
        self::assertSame(0, $this->executar($gancho, $raiz, $ambiente)['codigo']);
        self::assertSame(0, $this->executar(['git', 'add', 'Documentacao/00Regras.md'], $raiz)['codigo']);
        $recusa = $this->executar($gancho, $raiz, $ambiente);
        self::assertNotSame(0, $recusa['codigo']);
        self::assertStringContainsString('sem autorização', $recusa['erro']);
        self::assertNotSame(0, $this->executar(['git', 'hook', 'run', 'pre-commit'], $raiz, $ambiente)['codigo']);
        self::assertSame('# Mudança não preparada', file_get_contents($raiz . '/Documentacao/00Regras.md'));
        $this->gravar($raiz, 'Documentacao/00Regras.md', "# Regras\r\n");
        self::assertSame(0, $this->executar(['git', 'add', 'Documentacao/00Regras.md'], $raiz)['codigo']);
        $registro = file_get_contents($raiz . '/Documentacao/Ambiente/GitHub.md');
        $this->gravar($raiz, 'Documentacao/Ambiente/GitHub.md', $registro . $registro);
        self::assertSame(0, $this->executar(['git', 'add', 'Documentacao/Ambiente/GitHub.md'], $raiz)['codigo']);
        $recusa = $this->executar($gancho, $raiz, $ambiente);
        self::assertNotSame(0, $recusa['codigo']);
        self::assertStringContainsString('sem autorização', $recusa['erro']);
    }
}
