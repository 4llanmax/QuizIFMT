<?php

/**
 * Verifica configuração privada, precedência e recusa de parâmetros inválidos.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\Nucleo;

use Aplicacao\Nucleo\Comuns\Classes\Parametros;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ParametrosTeste extends TestCase
{
    #[Test]
    public function leArquivoSemInterpolacaoEProcessoPrevalece(): void
    {
        $arquivo = tempnam(sys_get_temp_dir(), 'quiz-parametros-');
        try {
            file_put_contents($arquivo, "# Configuração fictícia\nBANCO_HOST=localhost\nBANCO_NOME=teste\nBANCO_USUARIO=teste\nBANCO_SENHA=\"a#b=c\"\nADMIN_SENHA_HASH=\"hash\"\n");
            $parametros = new Parametros($arquivo, ['BANCO_HOST' => '127.0.0.1'], ['ADMIN_SENHA_HASH' => 'adminSenhaHash']);
            self::assertSame('127.0.0.1', $parametros->obter('bancoHost'));
            self::assertSame('a#b=c', $parametros->obter('bancoSenha'));
            self::assertSame(3306, $parametros->obter('bancoPorta'));
            self::assertSame('/', $parametros->obter('urlPrefixo'));
        } finally {
            unlink($arquivo);
        }
    }

    #[Test]
    public function permiteSomenteProcessoSemArquivoPrivado(): void
    {
        $parametros = new Parametros(__DIR__ . '/Ausente.env', [
            'BANCO_NOME' => 'teste', 'BANCO_USUARIO' => 'teste', 'BANCO_SENHA' => '',
        ], ['ADMIN_SENHA_HASH' => 'adminSenhaHash']);
        self::assertSame('', $parametros->obter('adminSenhaHash'));
    }

    #[Test]
    public function rejeitaAusenciaValoresDsnPortaPrefixoEChaveDesconhecida(): void
    {
        foreach ([[], ['BANCO_NOME' => 'a;port=1'], ['BANCO_PORTA' => '0'], ['URL_PREFIXO' => '//externo/'], ['URL_PREFIXO' => '/quiz'], ['INVENTADA' => 'x']] as $alteracao) {
            $arquivo = tempnam(sys_get_temp_dir(), 'quiz-parametros-');
            try {
                file_put_contents($arquivo, $alteracao === ['INVENTADA' => 'x'] ? "INVENTADA=x\n" : '');
                $valores = $alteracao === [] ? [] : array_merge(['BANCO_NOME' => 'teste', 'BANCO_USUARIO' => 'teste'], $alteracao);
                try {
                    new Parametros($arquivo, $valores);
                    self::fail('A configuração inválida precisa ser recusada.');
                } catch (RuntimeException) {
                    self::assertTrue(true);
                }
            } finally {
                unlink($arquivo);
            }
        }
    }

    #[Test]
    public function aceitaAspasSimplesVazioTextoCurtoEAspasSemFechamento(): void
    {
        $arquivo = tempnam(sys_get_temp_dir(), 'quiz-parametros-');
        try {
            file_put_contents($arquivo, "BANCO_NOME=teste\nBANCO_USUARIO=teste\nBANCO_SENHA='segredo ficticio'\n");
            self::assertSame('segredo ficticio', (new Parametros($arquivo, []))->obter('bancoSenha'));
            file_put_contents($arquivo, "BANCO_NOME=teste\nBANCO_USUARIO=teste\nBANCO_SENHA=\n");
            self::assertSame('', (new Parametros($arquivo, []))->obter('bancoSenha'));
            file_put_contents($arquivo, "BANCO_NOME=teste\nBANCO_USUARIO=teste\nBANCO_SENHA=a\n");
            self::assertSame('a', (new Parametros($arquivo, []))->obter('bancoSenha'));
            file_put_contents($arquivo, "BANCO_NOME=teste\nBANCO_USUARIO=teste\nBANCO_SENHA='ab\n");
            self::assertSame("'ab", (new Parametros($arquivo, []))->obter('bancoSenha'));
        } finally {
            unlink($arquivo);
        }
    }

    #[Test]
    public function reconheceChavesAdicionaisSomentePelaDescricaoDoConsumidor(): void
    {
        $arquivo = tempnam(sys_get_temp_dir(), 'quiz-parametros-');
        try {
            file_put_contents($arquivo, "BANCO_NOME=teste\nBANCO_USUARIO=teste\nCAMPO_PUBLICO=conteudo\n");
            $parametros = new Parametros($arquivo, [], ['CAMPO_PUBLICO' => 'campoPublico']);
            self::assertSame('conteudo', $parametros->obter('campoPublico'));
        } finally {
            unlink($arquivo);
        }
    }
}
