<?php

/**
 * Adapta a administração HTTP e impede operações sem sessão administrativa.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Painel\Controles;

use Aplicacao\Nucleo\Comuns\Classes\Renderizacao;
use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use Aplicacao\Nucleo\Transportes\Resposta;
use Aplicacao\Painel\Servicos\Autenticacao;
use Aplicacao\Painel\Servicos\Programacao;

final class Administracao
{
    public function __construct(private Programacao $programacao, private Autenticacao $autenticacao, private Renderizacao $renderizacao, private string $prefixo)
    {
    }

    public function atender(string $operacao, array $campos, array &$sessao, int $agora, string $origem): Resposta
    {
        if ($operacao === 'entrar') {
            $this->autenticacao->entrar($campos['senha'] ?? null, $origem, $agora);
            $sessao['administrador'] = true;
            $sessao['renovar'] = true;
            $sessao['csrf'] = \bin2hex(\random_bytes(32));
            return $this->redirecionar();
        }
        if ($operacao !== 'inicio' && !($sessao['administrador'] ?? false)) {
            throw new FalhaHTTP(403, 'Entre na administração para alterar a programação.');
        }
        if ($operacao === 'sair') {
            unset($sessao['administrador']);
            $sessao['renovar'] = true;
            $sessao['csrf'] = \bin2hex(\random_bytes(32));
            return $this->redirecionar();
        }
        if ($operacao === 'restaurar') {
            $this->programacao->restaurar();
            return $this->redirecionar();
        }
        if ($operacao === 'salvar') {
            $configuracao = ['total' => $this->inteiro($campos['total'] ?? null),
                'segundos' => $this->inteiro($campos['segundos'] ?? null), 'alerta' => $this->inteiro($campos['alerta'] ?? null)];
            $this->programacao->salvar($configuracao);
            return $this->redirecionar();
        }
        return new Resposta(200, $this->renderizacao->renderizar(\dirname(__DIR__) . '/Visoes/Paginas/Programacao.php', [
            'autenticado' => $sessao['administrador'] ?? false, 'configuracao' => $this->programacao->consultar(),
            'csrf' => $sessao['csrf'], 'prefixo' => $this->prefixo, 'capacidades' => $this->programacao->capacidades(),
        ]));
    }

    private function inteiro(mixed $valor): int|false
    {
        return \is_string($valor) || \is_int($valor) ? \filter_var($valor, FILTER_VALIDATE_INT) : false;
    }

    private function redirecionar(): Resposta
    {
        return new Resposta(303, '', ['Location' => $this->prefixo . 'admin']);
    }
}
