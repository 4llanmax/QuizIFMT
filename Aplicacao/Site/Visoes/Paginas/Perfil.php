<?php

/**
 * Apresenta indicadores, evolução, histórico e edição da conta.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {
    $url = $dados['url'];
    $campoCsrf = $dados['campoCsrf'];
    $jogador = $dados['jogador'];
    $historico = $dados['historico'];
    $temas = $dados['temas'];
    $posicao = $dados['posicao'];
    $melhor = $dados['melhor'];
    $tempoFormatado = $dados['tempoFormatado'];
    $nomesTemas = $dados['nomesTemas'];
    ?>
    <?php $quantidade = count($historico);
    $acertos = array_sum(array_column($historico, 'acertos'));
    $tempo = array_sum(array_column($historico, 'tempo')); ?>
<section class="tela"><div class="barra-superior"><a class="marca" href="<?= $url() ?>">BOMB<b>QUIZ</b></a><div class="direita"><a class="botao-icone" href="<?= $url('ranking') ?>" aria-label="Ranking"><i class="icone icone-trofeu" aria-hidden="true"></i></a><form method="post" action="<?= $url('sair') ?>"><?= $campoCsrf ?><button class="etiqueta" type="submit">Sair</button></form></div></div><div class="cartao perfil"><span class="avatar grande"><?= $escapar(mb_substr($jogador['nome'], 0, 1)) ?></span><div class="identificacao"><b><?= $escapar($jogador['nome']) ?><i class="icone icone-verificado verificado" aria-label="Conta registrada"></i></b><span class="pequeno suave">Jogador desde <?= $escapar(substr($jogador['criado_em'], 0, 10)) ?></span></div></div><a class="botao botao-primario" href="<?= $url('rodada') ?>">JOGAR AGORA</a><div class="estatisticas-duplas"><div class="indicador"><span>Posição atual</span><b class="cor-faisca"><?= $posicao !== null ? '#' . $posicao : '—' ?></b><small>no Top 100</small></div><div class="indicador"><span>Recorde</span><b class="cor-sucesso"><?= $melhor['acertos'] ?? 0 ?></b><small><?= $melhor !== null ? 'em ' . $tempoFormatado($melhor['tempo']) : 'Sem resultado no Top 100' ?></small></div><div class="indicador"><span>Partidas no histórico</span><b><?= $quantidade ?></b><small>Média de <?= $quantidade > 0 ? number_format($acertos / $quantidade, 1, ',', '.') : '0' ?> acertos</small></div><div class="indicador"><span>Resposta média</span><b><?= $acertos > 0 ? number_format($tempo / $acertos / 1000, 1, ',', '.') . 's' : '—' ?></b><small>nas respostas corretas</small></div></div>
<div class="cartao"><div class="secao-cabecalho"><h2>Últimas 10 partidas</h2><span class="pequeno suave">acertos por partida</span></div><?php if ($historico === []) :
    ?><p class="vazio">Jogue uma partida para ver sua evolução.</p><?php
                                                                                                                                         else :
                                                                                                                                                ?><div class="barras"><?php foreach (array_reverse(array_slice($historico, 0, 10)) as $registro) :
    ?><div class="barra"><span><?= $registro['acertos'] ?></span><i class="<?= $registro['motivo'] === 'vitoria' ? 'vitoria' : 'tempo' ?>" style="height:<?= max(3, (int) round((int) $registro['acertos'] / (int) $registro['total'] * 100)) ?>px"></i></div><?php
                                                                                                                                                endforeach; ?></div><?php
                                                                                                                                         endif; ?></div>
<div class="cartao"><div class="secao-cabecalho"><h2>Desempenho por tema</h2></div><?php if ($temas === []) :
    ?><p class="vazio">Sem respostas ainda.</p><?php
                                                                                   endif; ?><?php foreach ($temas as $tema => [$corretas, $respondidas]) :
    ?><?php $percentual = (int) round($corretas / $respondidas * 100); ?><div class="tema-barra"><div class="topo"><span><?= $escapar($nomesTemas[$tema] ?? $tema) ?> · <?= $respondidas ?> perguntas</span><span><?= $percentual ?>%</span></div><div class="trilho"><i style="width:<?= $percentual ?>%;background:var(--sucesso)"></i></div></div><?php
                                                                                   endforeach; ?></div>
<div class="cartao"><div class="secao-cabecalho"><h2>Histórico</h2></div><?php if ($historico === []) :
    ?><p class="vazio">Nenhuma partida ainda.</p><?php
                                                                         endif; ?><?php foreach (array_slice($historico, 0, 6) as $registro) :
    ?><div class="historico-linha"><span class="meio"><b><?= $registro['acertos'] ?> acertos · nível <?= $registro['nivel'] ?></b><small><?= $escapar($registro['criado_em']) ?> · <?= $escapar($registro['motivo']) ?></small></span><span class="tempo-resumo"><?= $tempoFormatado($registro['tempo']) ?></span></div><?php
                                                                         endforeach; ?></div>
<details><summary>Trocar apelido</summary><form class="formulario" method="post" action="<?= $url('apelido') ?>"><?= $campoCsrf ?><label class="rotulo" for="novo-nome">Novo apelido</label><input class="entrada" id="novo-nome" name="nome" minlength="3" maxlength="16" value="<?= $escapar($jogador['nome']) ?>" required><button class="botao botao-secundario" type="submit">SALVAR APELIDO</button></form></details>
<details><summary>Meus dados e exclusão da conta</summary><p class="pequeno suave">A exclusão remove sua conta e todos os resultados associados. A operação é definitiva.</p><form class="formulario exclusao" method="post" action="<?= $url('excluir-conta') ?>"><?= $campoCsrf ?><label class="rotulo" for="senha-exclusao">Confirme sua senha para excluir</label><input class="entrada" type="password" id="senha-exclusao" name="senha" autocomplete="current-password" required><button class="botao botao-secundario" type="submit">EXCLUIR CONTA E RESULTADOS</button></form></details></section>
    <?php
};
