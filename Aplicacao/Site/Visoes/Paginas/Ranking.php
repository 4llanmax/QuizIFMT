<?php

/**
 * Apresenta o ranking global e a posição do jogador no Top 100.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {
    $url = $dados['url'];
    $ranking = $dados['ranking'];
    $tempoFormatado = $dados['tempoFormatado'];
    $jogador = $dados['jogador'];
    $posicao = $dados['posicao'];
    ?>
<section class="tela"><div class="barra-superior"><a class="botao-icone" href="<?= $url() ?>" aria-label="Voltar">←</a><span class="marca">BOMB<b>QUIZ</b></span><span></span></div><div class="autenticacao-cabecalho"><h1>TOP 100</h1><p class="suave">Mais acertos primeiro. No empate, vence o menor tempo acumulado nas respostas corretas.</p></div>
    <?php if ($ranking !== []) :
        ?><div class="podio"><?php foreach ([1 => 'segundo', 0 => 'primeiro', 2 => 'terceiro'] as $indice => $classe) :
    ?><?php if (isset($ranking[$indice])) :
    ?><div class="podio-jogador <?= $classe ?>"><div class="retrato"><?= $escapar(mb_substr($ranking[$indice]['nome'], 0, 1)) ?></div><span class="nome-resumo"><?= $escapar($ranking[$indice]['nome']) ?></span><span class="pontuacao"><?= $ranking[$indice]['acertos'] ?> · <?= $tempoFormatado($ranking[$indice]['tempo']) ?></span><div class="base"><?= $indice + 1 ?></div></div><?php
    endif; ?><?php
        endforeach; ?></div><?php
    endif; ?>
<div class="ranking-painel"><p class="ranking-regra">Um recorde por jogador · ranking global</p><div class="ranking-colunas"><span class="coluna-posicao">#</span><span class="coluna-nome">Jogador</span><span class="coluna-acertos">Acertos</span><span class="coluna-tempo">Tempo</span></div><?php $registros = $ranking;
$dados['registros'] = $registros;
$incluir(dirname(__DIR__) . '/Componentes/Ranking.php', $dados); ?><?php if ($jogador !== null) :
    ?><div class="proprio-barra"><span class="posicao"><?= $posicao !== null ? '#' . $posicao : '—' ?></span><span class="nome"><?= $escapar($jogador['nome']) ?><small><?= $posicao !== null ? 'Você está no Top 100' : 'Ainda fora do Top 100' ?></small></span></div><?php
endif; ?></div></section>
    <?php
};
