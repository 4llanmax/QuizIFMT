<?php

/**
 * Apresenta regras e distribuição antes do início da contagem.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {
    $url = $dados['url'];
    $configuracao = $dados['configuracao'];
    $nomesTemas = $dados['nomesTemas'];
    $campoCsrf = $dados['campoCsrf'];
    ?>
<section class="tela"><div class="barra-superior"><a class="botao-icone" href="<?= $url() ?>" aria-label="Voltar">←</a><span class="marca">BOMB<b>QUIZ</b></span><span></span></div><div class="destaque"><?php $incluir(dirname(__DIR__) . '/Componentes/Bomba.php', $dados); ?><h1>Pronto para o desafio?</h1><p class="suave">São <?= $configuracao['total'] ?> perguntas. Um erro e a partida termina.</p></div><div class="cartao lista-temas"><?php foreach ($configuracao['distribuicao'] as $tema => $quantidade) :
    ?><div class="tema-item"><span><?= $nomesTemas[$tema] ?></span><strong><?= $quantidade ?> perguntas</strong></div><?php
                                                                               endforeach; ?></div><p class="pequeno suave">Você tem <?= $configuracao['segundos'] ?> segundos por pergunta. O cronômetro começa ao tocar no botão. O tempo continua correndo ao trocar de aba ou recarregar a página.</p><form method="post" action="<?= $url('jogar') ?>"><?= $campoCsrf ?><button class="botao botao-primario" type="submit">COMEÇAR A RODADA</button></form></section>
    <?php
};
