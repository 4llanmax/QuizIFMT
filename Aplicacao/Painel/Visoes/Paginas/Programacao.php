<?php

/**
 * Apresenta autenticação administrativa e programação das novas rodadas.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {
    $prefixo = $dados['prefixo'];
    $autenticado = $dados['autenticado'];
    $csrf = $dados['csrf'];
    $configuracao = $dados['configuracao'];
    $capacidades = $dados['capacidades'];
    ?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>BombQUIZ · Administração</title><link rel="stylesheet" href="<?= $escapar($prefixo) ?>Ativos/CSS/Principal.min.css"></head><body><main class="aplicativo"><section class="tela"><div class="barra-superior"><a class="botao-icone" href="<?= $escapar($prefixo) ?>" aria-label="Voltar">←</a><span class="marca">BOMB<b>QUIZ</b></span><span>Administração</span></div>
    <?php if (!$autenticado) :
        ?><h1>Acesso administrativo</h1><p class="suave">Entre para programar a distribuição das próximas rodadas.</p><form class="formulario" method="post" action="<?= $escapar($prefixo) ?>admin/entrar"><input type="hidden" name="csrf" value="<?= $escapar($csrf) ?>"><label class="rotulo" for="senha">Senha administrativa</label><input class="entrada" type="password" id="senha" name="senha" autocomplete="current-password" required><button class="botao botao-primario" type="submit">ENTRAR</button></form>
    <?php else :
        ?><h1>Programação da rodada</h1><p class="pequeno suave">As alterações valem para novas partidas. Cada pergunta tem cinco alternativas e apenas uma correta.</p><form class="formulario" method="post" action="<?= $escapar($prefixo) ?>admin/salvar"><input type="hidden" name="csrf" value="<?= $escapar($csrf) ?>"><div class="administracao-grade"><?php foreach (['total' => ['Perguntas por rodada', 5, 48], 'segundos' => ['Segundos por pergunta', 10, 120], 'alerta' => ['Alerta em segundos', 3, 30]] as $campo => [$rotulo, $minimo, $maximo]) :
    ?><div class="campo"><label class="rotulo" for="<?= $campo ?>"><?= $rotulo ?></label><input class="entrada" type="number" id="<?= $campo ?>" name="<?= $campo ?>" value="<?= $configuracao[$campo] ?>" min="<?= $minimo ?>" max="<?= $maximo ?>" required></div><?php
        endforeach; ?></div><div class="cartao"><h2>Distribuição automática</h2><p class="pequeno suave">As perguntas são divididas de forma equilibrada entre as categorias com perguntas ativas. Os temas que recebem uma pergunta extra são sorteados a cada rodada.</p><?php foreach ($capacidades as $tema => $quantidade) :
    ?><div class="administracao-linha"><span class="nome-resumo expandir"><?= $escapar($configuracao['nomesTemas'][$tema] ?? $tema) ?><small><?= $quantidade ?> perguntas no acervo</small></span></div><?php
        endforeach; ?></div><button class="botao botao-primario" type="submit">SALVAR PROGRAMAÇÃO</button></form><form method="post" action="<?= $escapar($prefixo) ?>admin/restaurar"><input type="hidden" name="csrf" value="<?= $escapar($csrf) ?>"><button class="botao botao-secundario" type="submit">RESTAURAR PROGRAMAÇÃO PADRÃO</button></form><form method="post" action="<?= $escapar($prefixo) ?>admin/sair"><input type="hidden" name="csrf" value="<?= $escapar($csrf) ?>"><button class="botao botao-ligacao" type="submit">Sair da administração</button></form><?php
    endif; ?></section></main></body></html>
    <?php
};
