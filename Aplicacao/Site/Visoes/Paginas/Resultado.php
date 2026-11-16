<?php

/**
 * Apresenta o resultado do servidor e o cadastro opcional.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {
    $partida = $dados['partida'];
    $tempoFormatado = $dados['tempoFormatado'];
    $posicao = $dados['posicao'];
    $conta = $dados['conta'];
    $url = $dados['url'];
    ?>
    <?php $vitoria = $partida['situacao'] === 'vitoria';
    $titulos = ['vitoria' => 'BOMBA DESARMADA!', 'erro' => 'RESPOSTA ERRADA', 'tempo' => 'TEMPO ESGOTADO', 'abandono' => 'PARTIDA ENCERRADA']; ?>
<section class="tela<?= $vitoria ? ' vitoria' : '' ?>"><div class="resultado-destaque <?= $vitoria ? 'vitoria' : 'errada' ?>"><div class="raios" aria-hidden="true"></div><div class="brilho" aria-hidden="true"></div><?php $incluir(dirname(__DIR__) . '/Componentes/Bomba.php', $dados); ?><?php if (!$vitoria) :
    ?><span class="explosao-palavra">BOOM!</span><?php
                    endif; ?></div><div class="resultado-titulo <?= $vitoria ? 'vitoria' : 'errada' ?>"><h1><?= $titulos[$partida['situacao']] ?></h1><p class="suave"><?= $vitoria ? 'Rodada perfeita. Nenhum erro, nenhum estouro.' : 'O desafio termina aqui. Tente de novo e supere seu recorde.' ?></p></div><div class="estatisticas-triplas"><div class="estatistica"><b class="cor-sucesso"><?= $partida['acertos'] ?></b><span>acertos</span></div><div class="estatistica"><b><?= $partida['indice'] + 1 ?><i>/<?= $partida['total'] ?></i></b><span>nível</span></div><div class="estatistica"><b class="cor-faisca numero"><?= $tempoFormatado($partida['tempo']) ?></b><span>tempo total</span></div></div>
    <?php if (in_array($partida['situacao'], ['erro', 'tempo'], true)) :
        ?><div class="resposta-correta">A resposta certa era <b><?= $escapar($partida['respostaCorreta']) ?></b>.</div><?php
    endif; ?>
<a class="ranking-ligacao" href="<?= $url('ranking') ?>"><i class="icone icone-trofeu cor-faisca" aria-hidden="true"></i><span class="expandir"><?= $posicao !== null ? 'Sua posição no Top 100: #' . $posicao : 'Ver o Top 100' ?></span>→</a>
    <?php if ($conta) :
        ?><div class="salvo-aviso">✓ Partida salva no seu histórico.</div><?php
    else :
        ?><div class="cartao tracejado convite-conta"><h2>Salvar esta partida?</h2><p class="pequeno suave">Crie uma conta com e-mail e senha para acompanhar sua evolução. É opcional.</p><a class="botao botao-claro" href="<?= $url('cadastro') ?>">CRIAR CONTA E SALVAR</a></div><?php
    endif; ?>
<div class="resultado-acoes"><button class="botao-icone" type="button" aria-label="Copiar resultado" data-resultado="BombQUIZ: <?= $partida['acertos'] ?>/<?= $partida['total'] ?> acertos em <?= $tempoFormatado($partida['tempo']) ?>">↗</button><a class="botao botao-primario" href="<?= $url('rodada') ?>">JOGAR DE NOVO</a></div><a class="botao botao-ligacao" href="<?= $url() ?>">Voltar ao início</a></section>
    <?php
};
