<?php

/**
 * Apresenta posições já ordenadas pelo serviço.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {
    $registros = $dados['registros'];
    $jogador = $dados['jogador'];
    $tempoFormatado = $dados['tempoFormatado'];
    ?>
    <?php if ($registros === []) :
        ?><p class="vazio">Ninguém no ranking ainda. Seja o primeiro!</p><?php
    endif; ?>
    <?php foreach ($registros as $indice => $registro) : ?>
<div class="ranking-linha<?= $jogador !== null && (int) $registro['identificador'] === (int) $jogador['identificador'] ? ' proprio' : '' ?>"><span class="ranking-posicao"><?= $indice + 1 ?></span><span class="ranking-nome"><?= $escapar($registro['nome']) ?><?php if ((bool) $registro['registrado']) :
    ?><i class="icone icone-verificado verificado" role="img" aria-label="Conta registrada"></i><?php
                         endif; ?></span><span class="ranking-acertos"><?= $registro['acertos'] ?></span><span class="ranking-tempo"><?= $tempoFormatado($registro['tempo']) ?></span></div>
    <?php endforeach; ?>
    <?php
};
