<?php

/**
 * Lê parâmetros privados sem interpolação e valida a configuração técnica compartilhada.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Comuns\Classes;

use RuntimeException;

final class Parametros
{
    private array $valores;

    /** O processo prevalece sobre o arquivo; chaves externas conservam a grafia de ambiente. */
    public function __construct(string $arquivo, array $processo, array $adicionais = [])
    {
        $chaves = ['BANCO_HOST' => '127.0.0.1', 'BANCO_PORTA' => '3306', 'BANCO_NOME' => '',
            'BANCO_USUARIO' => '', 'BANCO_SENHA' => '', 'URL_PREFIXO' => '/'];
        foreach ($adicionais as $externo => $interno) {
            $chaves[$externo] = '';
        }
        $origem = [];
        if (\is_file($arquivo)) {
            foreach (\file($arquivo, FILE_IGNORE_NEW_LINES) as $linha) {
                $linha = \trim($linha);
                if ($linha === '' || \str_starts_with($linha, '#')) {
                    continue;
                }
                if (!\preg_match('/^([A-Z_]+)=(.*)$/u', $linha, $partes) || !\array_key_exists($partes[1], $chaves)) {
                    throw new RuntimeException('A configuração contém uma linha desconhecida ou inválida.');
                }
                $valor = \trim($partes[2]);
                if (\strlen($valor) >= 2 && (($valor[0] === '"' && \str_ends_with($valor, '"')) || ($valor[0] === "'" && \str_ends_with($valor, "'")))) {
                    $valor = \substr($valor, 1, -1);
                }
                $origem[$partes[1]] = $valor;
            }
        }
        $dados = \array_replace($chaves, $origem, \array_intersect_key($processo, $chaves));
        if (!\preg_match('/^[A-Za-z0-9_.:-]+$/D', $dados['BANCO_HOST']) || !\preg_match('/^[A-Za-z0-9_]+$/D', $dados['BANCO_NOME']) || $dados['BANCO_USUARIO'] === '') {
            throw new RuntimeException('Informe host, nome e usuário válidos para o banco.');
        }
        $porta = \filter_var($dados['BANCO_PORTA'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
        if ($porta === false || !\preg_match('~^/(?:[a-zA-Z0-9_-]+/)*$~D', $dados['URL_PREFIXO'])) {
            throw new RuntimeException('Porta do banco ou prefixo público inválido.');
        }
        $this->valores = ['bancoHost' => $dados['BANCO_HOST'], 'bancoPorta' => $porta,
            'bancoNome' => $dados['BANCO_NOME'], 'bancoUsuario' => $dados['BANCO_USUARIO'],
            'bancoSenha' => $dados['BANCO_SENHA'], 'urlPrefixo' => $dados['URL_PREFIXO']];
        foreach ($adicionais as $externo => $interno) {
            $this->valores[$interno] = $dados[$externo];
        }
    }

    /** Retorna uma chave interna reconhecida; uma chave inexistente é falha de programação. */
    public function obter(string $chave): string|int
    {
        return $this->valores[$chave];
    }
}
