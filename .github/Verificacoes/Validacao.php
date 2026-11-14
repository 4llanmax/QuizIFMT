<?php

/**
 * Executa as verificações e prepara o ambiente isolado do GitHub Actions usando PHP.
 * Redator: Allan Max
 * Data de criação: 11/11/2026
 * Status: Revisado
 * Data do Status: 11/11/2026
 */

declare(strict_types=1);

final class Validacao
{
    /** @var Closure Executor de programas; recebe argumentos separados e retorna o código de término. */
    private Closure $processo;

    /** A raiz pertence à cópia verificada; o executor é substituível nos cenários isolados. */
    public function __construct(private string $raiz, ?callable $executar = null)
    {
        $this->processo = Closure::fromCallable($executar ?? $this->processo(...));
    }

    /** Executa programas diretamente e preserva seus fluxos e códigos de término. */
    private function processo(array $comando): int
    {
        $processo = proc_open($comando, [0 => STDIN, 1 => STDOUT, 2 => STDERR],
            $canais, $this->raiz, null, ['bypass_shell' => true]);
        if (!is_resource($processo)) {
            throw new RuntimeException('Não foi possível iniciar: ' . $comando[0]);
        }
        return proc_close($processo);
    }

    /** Interrompe a preparação imediatamente quando um programa externo falha. */
    private function exigir(array $comando): void
    {
        if (($this->processo)($comando) !== 0) {
            throw new RuntimeException('Programa externo reprovado: ' . $comando[0]);
        }
    }

    /** Compara os PHARs com os registros SHA-256, sem arquivos de manifesto intermediários. */
    public function integridade(array $ferramentas): array
    {
        $erros = [];
        foreach ($ferramentas as $ferramenta) {
            $executavel = $this->raiz . "/Ferramentas/$ferramenta/$ferramenta.phar";
            $documento = $this->raiz . "/Documentacao/Ferramentas/$ferramenta.md";
            if (!is_file($executavel) || !is_file($documento)) {
                $erros[] = "Instalação ou documento ausente para $ferramenta.";
                continue;
            }
            preg_match_all('/^\| Integridade \| SHA-256: `([0-9a-f]{64})`; fonte: https:\/\/[^|]+ \|$/m',
                str_replace("\r\n", "\n", file_get_contents($documento)), $resumos);
            if (count($resumos[1]) !== 1) {
                $erros[] = "Registro de integridade inválido para $ferramenta.";
            } elseif (!hash_equals($resumos[1][0], hash_file('sha256', $executavel))) {
                $erros[] = "Integridade divergente para $ferramenta.";
            }
        }
        return $erros;
    }

    /** Confere requisitos antes de alterar o ambiente; a versão recebida permite testar o limite mínimo. */
    public function preparar(int $versao = PHP_VERSION_ID): array
    {
        $erros = $versao >= 80512 ? [] : ['O projeto exige PHP 8.5.12 ou superior.'];
        foreach (['Ferramentas/PHPStan/PHPStan.neon', 'Ferramentas/PHPCS/PHPCS.xml',
            'Ferramentas/PHPUnit/PHPUnit.xml', 'Testes/Apoio/Preparacao.php',
            'Aplicacao/Nucleo/Comuns/Funcoes/AutoCarregamento.php', 'Publico/index.php',
            'Configuracoes/Aplicacao/Exemplo.env'] as $arquivo) {
            if (!is_file($this->raiz . '/' . $arquivo) || filesize($this->raiz . '/' . $arquivo) === 0) {
                $erros[] = 'Arquivo necessário ausente ou vazio: ' . $arquivo;
            }
        }
        $cenario = false;
        foreach (['Testes/Unitario', 'Testes/Integracao'] as $pasta) {
            if (!is_dir($this->raiz . '/' . $pasta)) {
                continue;
            }
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
                $this->raiz . '/' . $pasta, FilesystemIterator::SKIP_DOTS,
            )) as $arquivo) {
                $cenario = $cenario || ($arquivo->isFile() && str_ends_with($arquivo->getFilename(), 'Teste.php'));
            }
        }
        if (!$cenario) {
            $erros[] = 'Não há cenários de teste com o sufixo Teste.php.';
        }
        return array_merge($erros, $this->integridade(['PHPStan', 'PHPCS', 'PHPCBF', 'PHPUnit']));
    }

    /** Avalia os resultados e as dispensas por conteúdo, recusando indicadores ausentes ou incoerentes. */
    public function resultado(array $dados): array
    {
        foreach (['CLASSIFICACAO', 'AUTOMACAO', 'DOCUMENTACAO', 'NUMERADOS'] as $tarefa) {
            if (($dados['RESULTADO_' . $tarefa] ?? '') !== 'success') {
                return ['Uma conferência obrigatória falhou ou não foi executada.'];
            }
        }
        foreach (['DOCUMENTAL', 'APLICACAO', 'RIGOROSO'] as $chave) {
            if (!in_array($dados[$chave] ?? '', ['true', 'false'], true)) {
                return ['A classificação não publicou os indicadores obrigatórios.'];
            }
        }
        $aplicacao = $dados['RESULTADO_APLICACAO'] ?? '';
        if ($dados['DOCUMENTAL'] === 'true' && $dados['APLICACAO'] === 'true') {
            return ['Os indicadores de conteúdo estão inconsistentes.'];
        }
        if ($dados['APLICACAO'] === 'false') {
            return $aplicacao === 'skipped' ? [] : ['Dispensa por conteúdo inconsistente com o resultado da aplicação.'];
        }
        if ($dados['RIGOROSO'] === 'false') {
            return in_array($aplicacao, ['success', 'failure'], true)
                ? [] : ['As verificações da aplicação foram canceladas ou ignoradas sem dispensa.'];
        }
        return $aplicacao === 'success' ? [] : ['As verificações obrigatórias da aplicação não foram aprovadas.'];
    }

    /** Copia arquivos para o contêiner; links simbólicos e a pasta interna do Git são excluídos. */
    private function copiar(string $origem, string $destino): void
    {
        if (!is_dir($destino) && !mkdir($destino, 0755, true)) {
            throw new RuntimeException('Não foi possível criar: ' . $destino);
        }
        foreach (new DirectoryIterator($origem) as $arquivo) {
            if ($arquivo->isDot() || $arquivo->getFilename() === '.git') {
                continue;
            }
            if ($arquivo->isLink()) {
                throw new RuntimeException('Link simbólico não aceito na cópia de validação.');
            }
            $alvo = $destino . '/' . $arquivo->getFilename();
            if ($arquivo->isDir()) {
                $this->copiar($arquivo->getPathname(), $alvo);
            } elseif (!copy($arquivo->getPathname(), $alvo)) {
                throw new RuntimeException('Falha ao copiar: ' . $arquivo->getPathname());
            }
        }
    }

    /** Grava somente arquivos de configuração do ambiente efêmero do contêiner. */
    private function gravar(string $caminho, string $texto): void
    {
        if (file_put_contents($caminho, $texto) === false) {
            throw new RuntimeException('Não foi possível gravar: ' . $caminho);
        }
    }

    /** Prepara Apache, TLS e permissões POSIX exclusivamente no contêiner descartável de validação. */
    private function ambiente(): void
    {
        $this->exigir(['apt-get', 'update']);
        $this->exigir(['apt-get', 'install', '-y', '--no-install-recommends', 'git', 'openssl', 'ca-certificates', 'iproute2']);
        // Endereço reservado à documentação, associado ao loopback isolado do contêiner.
        $this->exigir(['ip', 'addr', 'add', '203.0.113.5/32', 'dev', 'lo']);
        $this->exigir(['a2enmod', 'rewrite', 'headers', 'ssl']);
        // Certificado e chave efêmeros: nenhum valor privado é versionado ou usado fora dos testes.
        $this->exigir(['openssl', 'req', '-x509', '-newkey', 'rsa:2048', '-nodes', '-days', '2',
            '-keyout', '/etc/ssl/private/exemplo.local.key', '-out', '/usr/local/share/ca-certificates/exemplo.local.crt',
            '-subj', '/CN=exemplo.local', '-addext', 'subjectAltName=DNS:exemplo.local']);
        $this->exigir(['update-ca-certificates']);
        if (file_put_contents('/etc/hosts', "\n127.0.0.1 exemplo.local\n", FILE_APPEND) === false) {
            throw new RuntimeException('Não foi possível definir o endereço local de teste.');
        }
        $destino = '/var/www/EstruturaPHP';
        $this->copiar($this->raiz, $destino);
        if (!copy($this->raiz . '/Configuracoes/Aplicacao/Exemplo.env', $destino . '/Configuracoes/Aplicacao/.env')) {
            throw new RuntimeException('Não foi possível preparar os parâmetros fictícios.');
        }
        $this->gravar('/etc/apache2/sites-available/000-default.conf', <<<'APACHE'
<VirtualHost *:80>
    ServerName exemplo.local
    Redirect permanent / https://exemplo.local/
</VirtualHost>
<VirtualHost *:443>
    ServerName exemplo.local
    DocumentRoot /var/www/EstruturaPHP/Publico
    SSLEngine on
    SSLCertificateFile /usr/local/share/ca-certificates/exemplo.local.crt
    SSLCertificateKeyFile /etc/ssl/private/exemplo.local.key
    <Directory /var/www/EstruturaPHP/Publico>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
TraceEnable Off

APACHE);
        $arquivos = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($destino,
            FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
        $permissoes = static function (string $caminho, bool $pasta): void {
            if (!chmod($caminho, $pasta ? 0755 : 0644) || !chown($caminho, 'www-data') || !chgrp($caminho, 'www-data')) {
                throw new RuntimeException('Não foi possível preparar permissões: ' . $caminho);
            }
        };
        $permissoes($destino, true);
        foreach ($arquivos as $arquivo) {
            $permissoes($arquivo->getPathname(), $arquivo->isDir());
        }
        $this->exigir(['apache2ctl', 'configtest']);
        $this->exigir(['apache2ctl', 'start']);
    }

    /** Executa uma etapa nomeada; falhas de preparação e de programas preservam a reprovação. */
    public function executar(string $etapa): int
    {
        try {
            $erros = [];
            switch ($etapa) {
                case 'dependencias':
                    $this->exigir(['apt-get', 'update']);
                    $this->exigir(['apt-get', 'install', '-y', '--no-install-recommends', 'git']);
                    break;
                case 'classificar':
                case 'documentacao':
                    return ($this->processo)([PHP_BINARY, '.github/Verificacoes/Alteracao.php',
                        ...($etapa === 'documentacao' ? ['--conferir-documentos'] : [])]);
                case 'numerados':
                    return ($this->processo)([PHP_BINARY, '.githooks/ConferenciaDocumentosNumerados.php']);
                case 'extensoes':
                    foreach (['dom', 'filter', 'json', 'libxml', 'mbstring', 'xmlwriter'] as $extensao) {
                        if (!extension_loaded($extensao)) {
                            $erros[] = 'Extensão obrigatória ausente: ' . $extensao;
                        }
                    }
                    break;
                case 'integridade':
                    $erros = $this->integridade(['PHPUnit']);
                    break;
                case 'testes':
                    return ($this->processo)([PHP_BINARY, 'Ferramentas/PHPUnit/PHPUnit.phar',
                        '--configuration', 'Ferramentas/PHPUnit/PHPUnit.xml']);
                case 'preparacao':
                    $erros = $this->preparar();
                    break;
                case 'ambiente':
                    if (PHP_OS_FAMILY !== 'Linux' || !is_dir('/etc/apache2')) {
                        throw new RuntimeException('A preparação do servidor exige o contêiner PHP com Apache.');
                    }
                    $this->ambiente();
                    break;
                case 'somas':
                    $erros = (new self('/var/www/EstruturaPHP'))->integridade(['PHPStan', 'PHPCS', 'PHPCBF', 'PHPUnit']);
                    break;
                case 'phpstan':
                case 'phpcs':
                case 'phpunit':
                    $comandos = [
                        'phpstan' => ['Ferramentas/PHPStan/PHPStan.phar', 'analyse', '--configuration',
                            'Ferramentas/PHPStan/PHPStan.neon', '--memory-limit=512M'],
                        'phpcs' => ['Ferramentas/PHPCS/PHPCS.phar', '--standard=Ferramentas/PHPCS/PHPCS.xml'],
                        'phpunit' => ['Ferramentas/PHPUnit/PHPUnit.phar', '--configuration',
                            'Ferramentas/PHPUnit/PHPUnit.xml', '--display-skipped', '--fail-on-skipped', '--colors=never'],
                    ];
                    if ($etapa === 'phpunit') {
                        putenv('ESTRUTURA_URL=https://exemplo.local');
                    }
                    // O diretório da cópia e a identidade POSIX são aplicados antes de iniciar a ferramenta.
                    $this->raiz = '/var/www/EstruturaPHP';
                    return ($this->processo)(['runuser', '-u', 'www-data', '--', PHP_BINARY, ...$comandos[$etapa]]);
                case 'resultado':
                    $dados = getenv();
                    $erros = $this->resultado($dados);
                    if ($erros === [] && ($dados['APLICACAO'] ?? '') === 'true' && ($dados['RIGOROSO'] ?? '') === 'false') {
                        echo "::warning::Verificações da aplicação em Desenvolvimento são informativas; conferir antes de promover.\n";
                    }
                    break;
                default:
                    throw new InvalidArgumentException('Etapa desconhecida: ' . $etapa);
            }
            foreach ($erros as $erro) {
                echo '::error::' . $erro . "\n";
            }
            return $erros === [] ? 0 : 1;
        } catch (Throwable $erro) {
            fwrite(STDERR, $erro->getMessage() . "\n");
            return 1;
        }
    }
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    exit((new Validacao(dirname(__DIR__, 2)))->executar($argv[1] ?? ''));
}
