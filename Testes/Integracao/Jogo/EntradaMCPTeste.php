<?php

/**
 * Confere descoberta autenticável pela entrada web única sem depender do endereço recebido.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Integracao\Jogo;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Testes\Apoio\BancoIsolado;

final class EntradaMCPTeste extends TestCase
{
    #[Test]
    public function emiteDescobertaPelaEntradaUnicaComOrigemConfigurada(): void
    {
        $apoio = new BancoIsolado(getenv());
        $apoio->criar();
        $ambiente = [];
        $servidor = $_SERVER;
        $sessaoCaminho = ini_get('session.save_path');
        $pasta = sys_get_temp_dir() . '/quiz-mcp-sessao-' . bin2hex(random_bytes(6));
        mkdir($pasta);
        try {
            foreach ($apoio->parametros() + ['MCP_URL_BASE' => 'https://quiz.example.invalid', 'MCP_SENHA_INICIAL_HASH' => password_hash('senha-ficticia', PASSWORD_DEFAULT)] as $chave => $valor) {
                $ambiente[$chave] = getenv($chave);
                putenv($chave . '=' . $valor);
            }
            ini_set('session.save_path', $pasta);
            $_SERVER = ['REQUEST_URI' => '/.well-known/oauth-protected-resource', 'REQUEST_METHOD' => 'GET',
                'REMOTE_ADDR' => '127.0.0.1', 'HTTPS' => 'on', 'HTTP_HOST' => 'origem-nao-confiavel.invalid'];
            ob_start();
            require dirname(__DIR__, 3) . '/Publico/index.php';
            $resposta = ob_get_clean();
            self::assertSame(200, http_response_code());
            $dados = json_decode($resposta, true, 512, JSON_THROW_ON_ERROR);
            self::assertSame('https://quiz.example.invalid/mcp', $dados['resource']);
            self::assertStringNotContainsString('origem-nao-confiavel', $resposta);
        } finally {
            $_SERVER = $servidor;
            foreach ($ambiente as $chave => $valor) {
                putenv($valor === false ? $chave : $chave . '=' . $valor);
            }
            ini_set('session.save_path', $sessaoCaminho);
            foreach (glob($pasta . '/sess_*') as $arquivo) {
                unlink($arquivo);
            }
            rmdir($pasta);
            $apoio->limpar();
        }
    }
}
