<?php

/**
 * Adapta campos e sessão HTTP aos serviços do jogo e compõe respostas.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Site\Controles;

use Aplicacao\Nucleo\Comuns\Classes\Renderizacao;
use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use Aplicacao\Nucleo\Servicos\Acesso;
use Aplicacao\Nucleo\Transportes\Resposta;
use Aplicacao\Site\Servicos\Consulta;
use Aplicacao\Site\Servicos\Jogadores;
use Aplicacao\Site\Servicos\Rodadas;
use Aplicacao\Site\Validacoes\Entrada;

final class Jogo
{
    public function __construct(private Jogadores $jogadores, private Rodadas $rodadas, private Consulta $consulta, private Acesso $acesso, private Renderizacao $renderizacao, private string $prefixo)
    {
    }

    public function atender(string $operacao, array $campos, array &$sessao, int $agora, string $origem): Resposta
    {
        $jogador = $sessao['jogador'] ?? null;
        $conta = $sessao['conta'] ?? false;
        if (\in_array($operacao, ['conta', 'renomear', 'excluir'], true) && !$conta) {
            throw new FalhaHTTP(403, 'Entre na conta para acessar esta operação.');
        }
        switch ($operacao) {
            case 'identificar':
                $registro = $this->jogadores->convidado($sessao['tokenConvidado'], $campos['nome'] ?? null);
                $sessao['jogador'] = (int) $registro['identificador'];
                $sessao['conta'] = false;
                return $this->redirecionar('rodada');
            case 'autenticar':
            case 'cadastrar':
                $this->acesso->conferir('contas:' . $origem, $agora);
                if ($operacao === 'autenticar') {
                    $registro = $this->jogadores->entrar($campos['email'] ?? null, $campos['senha'] ?? null);
                } else {
                    $registro = $this->jogadores->cadastrar($campos['nome'] ?? null, $campos['email'] ?? null, $campos['senha'] ?? null, $conta ? null : $jogador);
                }
                $sessao['jogador'] = (int) $registro['identificador'];
                $sessao['conta'] = true;
                $sessao['renovar'] = true;
                $sessao['csrf'] = \bin2hex(\random_bytes(32));
                return $this->redirecionar('conta');
            case 'renomear':
                $this->jogadores->renomear($jogador, $campos['nome'] ?? null);
                return $this->redirecionar('conta');
            case 'sair':
                if (isset($sessao['partida'])) {
                    $this->rodadas->alterar($sessao['partida'], $sessao['jogadorPartida'], $sessao['partida']['identificador'], 'abandonar', [], $agora);
                }
                unset($sessao['jogador'], $sessao['conta'], $sessao['partida'], $sessao['jogadorPartida']);
                $sessao['renovar'] = true;
                $sessao['csrf'] = \bin2hex(\random_bytes(32));
                return $this->redirecionar('');
            case 'excluir':
                $this->jogadores->excluir($jogador, $campos['senha'] ?? null);
                unset($sessao['jogador'], $sessao['conta'], $sessao['partida'], $sessao['jogadorPartida']);
                $sessao['renovar'] = true;
                $sessao['csrf'] = \bin2hex(\random_bytes(32));
                return $this->redirecionar('');
            case 'jogar':
                if ($jogador === null) {
                    return $this->redirecionar('');
                }
                $partida = $this->rodadas->preparar($sessao['partida'] ?? null, $sessao['jogadorPartida'] ?? 0, $jogador, $agora);
                $sessao['partida'] = $partida->estado();
                $sessao['jogadorPartida'] = $jogador;
                return $this->redirecionar('jogo');
            case 'responder':
            case 'avancar':
            case 'abandonar':
            case 'expirar':
                if (!isset($sessao['partida'])) {
                    throw new FalhaHTTP(409, 'Não há partida ativa nesta sessão.');
                }
                $entrada = [];
                if ($operacao === 'responder') {
                    $entrada = ['indice' => Entrada::inteiro($campos['indice'] ?? null, 0, 47), 'alternativa' => Entrada::inteiro($campos['alternativa'] ?? null, 0, 4)];
                }
                $identificador = \is_string($campos['partida'] ?? null) ? $campos['partida'] : '';
                $partida = $this->rodadas->alterar($sessao['partida'], $sessao['jogadorPartida'], $identificador, $operacao, $entrada, $agora);
                $sessao['partida'] = $partida->estado();
                return $this->redirecionar('jogo');
        }
        $partidaPublica = null;
        if ($operacao === 'jogo') {
            if (!isset($sessao['partida'])) {
                return $this->redirecionar('');
            }
            $partida = $this->rodadas->consultar($sessao['partida'], $sessao['jogadorPartida'], $agora);
            $sessao['partida'] = $partida->estado();
            $partidaPublica = $partida->publicar($agora);
            $operacao = $partida->terminou() ? 'resultado' : 'jogo';
        }
        if ($operacao === 'rodada' && $jogador === null) {
            return $this->redirecionar('');
        }
        $dados = $this->consulta->apresentar($jogador, $conta);
        $dados += ['pagina' => $operacao, 'partida' => $partidaPublica, 'conta' => $conta, 'csrf' => $sessao['csrf'], 'prefixo' => $this->prefixo];
        return new Resposta(200, $this->renderizacao->renderizar(\dirname(__DIR__) . '/Visoes/Estruturas/Principal.php', $dados));
    }

    private function redirecionar(string $caminho): Resposta
    {
        return new Resposta(303, '', ['Location' => $this->prefixo . $caminho]);
    }
}
