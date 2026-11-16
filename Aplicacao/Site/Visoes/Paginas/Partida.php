<?php

/**
 * Apresenta a pergunta atual e o prazo informado pelo servidor.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {
    $url = $dados['url'];
    $campoCsrf = $dados['campoCsrf'];
    $partida = $dados['partida'];
    $nomesTemas = $dados['nomesTemas'];
    $tempoFormatado = $dados['tempoFormatado'];
    ?>
<section class="tela jogo" aria-label="Partida"><div class="painel-jogo"><form method="post" action="<?= $url('abandonar') ?>"><?= $campoCsrf ?><input type="hidden" name="partida" value="<?= $escapar($partida['identificador']) ?>"><button class="botao-icone" type="submit" aria-label="Encerrar partida">×</button></form><div class="nivel"><small>NÍVEL</small><b><?= str_pad((string) ($partida['indice'] + 1), 2, '0', STR_PAD_LEFT) ?><i>/<?= $partida['total'] ?></i></b></div><span class="perigo-aviso">VAI EXPLODIR!</span><div class="placar"><span><?= $partida['acertos'] ?> acertos</span></div><button class="botao-icone" type="button" data-som aria-label="Ativar som" aria-pressed="false">♫</button></div>
<div class="progresso" role="img" aria-label="Pergunta <?= $partida['indice'] + 1 ?> de <?= $partida['total'] ?>" style="grid-template-columns:repeat(<?= $partida['total'] ?>,minmax(0,1fr))"><?php for ($indice = 0; $indice < $partida['total']; $indice++) :
    ?><i class="<?= $indice < $partida['indice'] ? 'concluido' : ($indice === $partida['indice'] ? 'atual' : '') ?>"></i><?php
                                                       endfor; ?></div>
    <?php if ($partida['situacao'] === 'jogando') : ?>
<div class="cronometro" role="timer" aria-label="Tempo restante" data-restante="<?= $partida['restante'] ?>" data-segundos="<?= $partida['segundos'] ?>" data-alerta="<?= $partida['alerta'] ?>"><div class="bomba"><svg viewBox="0 0 96 92" aria-hidden="true"><circle cx="46" cy="46" r="32" fill="#1B1B28" stroke="#FFC93C" stroke-width="5"/><path d="M28 36 A20 20 0 0 1 40 26" stroke="#F5F3EE" stroke-width="3" fill="none" opacity="0.35"/><rect x="77" y="39" width="14" height="14" rx="3" fill="#2A2A3C" stroke="#F5F3EE" stroke-width="3"/></svg><span class="digitos" data-digitos><?= (int) ceil($partida['restante'] / 1000) ?></span></div><div class="pavio" aria-hidden="true"><div class="corda" data-pavio></div><div class="faisca"></div><span class="pavio-rotulo">PAVIO</span><span class="pavio-tempo"><?= $partida['segundos'] ?>s</span></div></div>
    <?php else :
        ?><div class="salvo-aviso" role="status">✓ Resposta correta! O pavio está apagado.</div><?php
    endif; ?>
<div class="pergunta-cartao"><div class="pergunta-topo"><span class="pergunta-tema"><?= $escapar($nomesTemas[$partida['pergunta']['tema']] ?? $partida['pergunta']['tema']) ?></span><span class="pergunta-etiqueta">Eliminatória</span></div><h1 class="pergunta-texto"><?= $escapar($partida['pergunta']['enunciado']) ?></h1></div>
    <?php if ($partida['situacao'] === 'jogando') :
        ?><form class="alternativas" method="post" action="<?= $url('responder') ?>"><?= $campoCsrf ?><input type="hidden" name="partida" value="<?= $escapar($partida['identificador']) ?>"><input type="hidden" name="indice" value="<?= $partida['indice'] ?>"><?php foreach ($partida['pergunta']['alternativas'] as $indice => $alternativa) :
    ?><button class="alternativa" type="submit" name="alternativa" value="<?= $indice ?>" data-alternativa="<?= $indice ?>"><span class="letra"><?= 'ABCDE'[$indice] ?></span><span class="texto"><?= $escapar($alternativa) ?></span></button><?php
        endforeach; ?></form>
<form method="post" action="<?= $url('expirar') ?>" data-expiracao hidden><?= $campoCsrf ?><input type="hidden" name="partida" value="<?= $escapar($partida['identificador']) ?>"><button type="submit">Conferir tempo</button></form><noscript><p class="pequeno suave">O servidor confere o prazo ao responder. Sem JavaScript, o número de segundos não se atualiza automaticamente.</p></noscript>
    <?php else :
        ?><div class="resposta-correta">✓ <?= $escapar($partida['respostaCorreta']) ?></div><form method="post" action="<?= $url('avancar') ?>"><?= $campoCsrf ?><input type="hidden" name="partida" value="<?= $escapar($partida['identificador']) ?>"><button class="botao botao-primario" type="submit">PRÓXIMA PERGUNTA →</button></form><?php
    endif; ?>
<div class="jogo-rodape"><span>Tempo acumulado <strong class="numero"><?= $tempoFormatado($partida['tempo']) ?></strong></span><span>Toque ou use A–E</span></div></section>
    <?php
};
