<?php

/**
 * Confere os documentos numerados e protege as alterações preparadas no índice do Git.
 * Redator: Allan Max
 * Data de criação: 11/11/2026
 * Status: Revisado
 * Data do Status: 11/11/2026
 */

declare(strict_types=1);

final class ConferenciaDocumentosNumerados
{
    /** A raiz identifica a cópia de trabalho; o índice é lido diretamente pelo Git. */
    public function __construct(private string $raiz)
    {
    }

    /** Executa o Git diretamente, sem interpolação ou interpretador de comandos. */
    private function git(array $argumentos): string
    {
        $processo = proc_open(['git', ...$argumentos], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $canais, $this->raiz, null, ['bypass_shell' => true]);
        if (!is_resource($processo)) {
            throw new RuntimeException('Não foi possível iniciar o Git.');
        }
        $saida = stream_get_contents($canais[1]);
        $erro = stream_get_contents($canais[2]);
        fclose($canais[1]);
        fclose($canais[2]);
        if (proc_close($processo) !== 0) {
            throw new RuntimeException('Falha na leitura do índice: ' . trim($erro));
        }
        return $saida;
    }

    /** Reconhece exclusivamente documentos iniciados por algarismo na raiz de Documentacao. */
    private function numerado(string $caminho): bool
    {
        return preg_match('~\ADocumentacao/[0-9][^/]*\z~', $caminho) === 1;
    }

    /** Lê a versão de trabalho ou a preparada, sem criar cópias temporárias. */
    private function ler(string $caminho, bool $indice): string
    {
        if ($indice) {
            return $this->git(['show', ':' . $caminho]);
        }
        if (!is_file($this->raiz . '/' . $caminho) || is_link($this->raiz . '/' . $caminho)) {
            throw new RuntimeException('Arquivo ausente ou inválido: ' . $caminho);
        }
        return file_get_contents($this->raiz . '/' . $caminho);
    }

    /** Recusa alterações protegidas sem autorização; o valor autorizado nunca dispensa a integridade. */
    public function conferir(bool $indice = false): array
    {
        try {
            if ($indice && getenv('DOCUMENTOS_NUMERADOS_AUTORIZADOS') !== 'sim') {
                $mudancas = explode("\0", $this->git(['diff', '--cached', '--name-only', '--no-renames', '-z', '--']));
                $registro = $this->git(['diff', '--cached', '-U0', '--', 'Documentacao/Ambiente/GitHub.md']);
                if (array_filter($mudancas, $this->numerado(...)) !== []
                    || preg_match('/^[+-]\| `Documentacao\//m', $registro)) {
                    return ['Commit recusado: alteração de documento numerado ou de seu registro sem autorização do Gerente do projeto.'];
                }
            }
            $registro = str_replace("\r\n", "\n", $this->ler('Documentacao/Ambiente/GitHub.md', $indice));
            preg_match_all('/^\| `(Documentacao\/[^`\/]+)` \| `([0-9a-f]{64})` \|$/m', $registro, $linhas);
            $registrados = $linhas[1];
            if ($registrados === [] || count(array_unique($registrados)) !== count($registrados)) {
                return ['Registro dos documentos numerados ausente ou duplicado.'];
            }
            $existentes = $indice
                ? array_values(array_filter(explode("\0", $this->git(['ls-files', '-z', '--', 'Documentacao'])), $this->numerado(...)))
                : array_values(array_filter(array_map(
                    static fn (string $nome): string => 'Documentacao/' . $nome,
                    scandir($this->raiz . '/Documentacao') ?: [],
                ), fn (string $nome): bool => $this->numerado($nome) && is_file($this->raiz . '/' . $nome)));
            $esperados = $registrados;
            sort($esperados);
            sort($existentes);
            if ($esperados !== $existentes) {
                return ['Documentos numerados existentes diferem do registro.'];
            }
            $erros = [];
            foreach ($registrados as $numero => $nome) {
                $texto = str_replace("\r\n", "\n", $this->ler($nome, $indice));
                if (!hash_equals($linhas[2][$numero], hash('sha256', $texto))) {
                    $erros[] = 'Documento numerado diferente do registro: ' . $nome;
                }
            }
            return $erros;
        } catch (Throwable $erro) {
            return [$erro->getMessage()];
        }
    }
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    $erros = (new ConferenciaDocumentosNumerados(dirname(__DIR__)))->conferir();
    foreach ($erros as $erro) {
        fwrite(STDERR, $erro . "\n");
    }
    if ($erros === []) {
        echo "Documentos numerados idênticos ao registro.\n";
    }
    exit($erros === [] ? 0 : 1);
}
