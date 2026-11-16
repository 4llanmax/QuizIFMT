<?php

/**
 * Confere encaminhamento de transportes HTTP e preservação do CSRF dos formulários.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\Nucleo;

use Aplicacao\Nucleo\Comuns\Classes\Roteador;
use Aplicacao\Nucleo\Comuns\Classes\RoteadorHTTP;
use Aplicacao\Nucleo\Comuns\Classes\Web;
use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use Aplicacao\Nucleo\Transportes\Resposta;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EncaminhamentoHTTPTeste extends TestCase
{
    #[Test]
    public function encaminhaCabecalhosCorpoEFormularioAoTransporteResponsavel(): void
    {
        $recebido = [];
        $controle = static function (array $servidor, string $corpo, array $campos, array &$sessao, int $agora) use (&$recebido): Resposta {
            $recebido = [$servidor, $corpo, $campos, $agora];
            $sessao['transporte'] = true;
            return new Resposta(202);
        };
        $rota = new RoteadorHTTP(['/integracao' => $controle]);
        $web = new Web(['/integracao' => $rota]);
        $sessao = [];
        $servidor = ['HTTP_AUTHORIZATION' => 'Bearer ficticio', 'REQUEST_URI' => '/integracao?state=ficticio'];
        self::assertSame(202, $web->atender('POST', '/integracao', ['campo' => 'valor'], $sessao, 1000, 'origem', $servidor, '{"id":1}')->status);
        self::assertSame('Bearer ficticio', $recebido[0]['HTTP_AUTHORIZATION']);
        self::assertSame('POST', $recebido[0]['REQUEST_METHOD']);
        self::assertSame('/integracao?state=ficticio', $recebido[0]['REQUEST_URI']);
        self::assertSame('{"id":1}', $recebido[1]);
        self::assertSame(['campo' => 'valor'], $recebido[2]);
        self::assertSame(1000, $recebido[3]);
        self::assertTrue($sessao['transporte']);
        self::assertSame(404, $web->atender('GET', '/integracao/ausente', [], $sessao, 1000, 'origem')->status);
    }

    #[Test]
    public function conservaProtecaoDosFormularios(): void
    {
        $roteador = new Roteador(['/' => ['POST' => 'inicio']], static fn (): Resposta => new Resposta(204));
        $web = new Web(['/' => $roteador]);
        $sessao = [];
        self::assertSame(403, $web->atender('POST', '/', [], $sessao, 1000, 'origem')->status);
        self::assertSame(204, $web->atender('POST', '/', ['csrf' => $sessao['csrf']], $sessao, 1000, 'origem')->status);
        self::assertSame(403, $web->atender('POST', '/', ['csrf' => []], $sessao, 1000, 'origem')->status);
        self::assertSame(403, $web->atender('POST', '/', ['csrf' => 'incorreto'], $sessao, 1000, 'origem')->status);
    }

    #[Test]
    public function preencheContextoAusenteSemInferirOrigemDeCabecalhosDoCliente(): void
    {
        $roteador = new RoteadorHTTP(['/externo' => static function (array $servidor): Resposta {
            self::assertSame('GET', $servidor['REQUEST_METHOD']);
            self::assertSame('/externo', $servidor['REQUEST_URI']);
            self::assertSame('origem', $servidor['REMOTE_ADDR']);
            return new Resposta(200);
        }]);
        $sessao = [];
        self::assertSame(200, (new Web(['/externo' => $roteador]))->atender('GET', '/externo', [], $sessao, 0, 'origem')->status);
        $this->expectException(FalhaHTTP::class);
        $roteador->atender('/inexistente', [], '', [], $sessao, 0);
    }
}
