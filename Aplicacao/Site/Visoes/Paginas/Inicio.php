<?php

/**
 * Apresenta o jogo e os cinco melhores resultados globais.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {
    $url = $dados['url'];
    $conta = $dados['conta'];
    $jogador = $dados['jogador'];
    $configuracao = $dados['configuracao'];
    $ranking = $dados['ranking'];
    $campoCsrf = $dados['campoCsrf'];
    ?>
<section class="tela" aria-labelledby="titulo-inicio"><div class="inicio-brilho" aria-hidden="true"></div>
<div class="barra-superior"><button class="etiqueta" type="button" data-instalar><i class="icone icone-instalacao" aria-hidden="true"></i> Instalar app</button><div class="direita"><button class="botao-icone" type="button" data-som aria-label="Ativar som" aria-pressed="false"><i class="icone icone-som" aria-hidden="true"></i></button><a class="etiqueta" href="<?= $url($conta ? 'conta' : 'entrar') ?>"><?= $conta ? 'Minha conta' : 'Entrar' ?></a></div></div>
<div class="inicio-grade"><div class="inicio-coluna"><div class="destaque"><?php $incluir(dirname(__DIR__) . '/Componentes/Bomba.php', $dados); ?><h1 class="marca-grande" id="titulo-inicio">BOMB<b>QUIZ</b></h1><p class="chamada">Um erro e… <strong>BOOM.</strong> Chegue às <?= $configuracao['total'] ?> e desarme a bomba.</p></div>
<div class="regras"><span class="etiqueta"><span><?= $configuracao['total'] ?></span> perguntas</span><span class="etiqueta"><span><?= $configuracao['segundos'] ?></span> segundos cada</span><span class="etiqueta"><span class="erro">1</span> erro = fim</span></div>
    <?php if ($conta) :
        ?><div class="cartao inicio-formulario"><div class="saudacao"><span class="avatar"><?= $escapar(mb_substr($jogador['nome'], 0, 1)) ?></span><div class="expandir"><p class="pequeno suave">Jogando como</p><strong><?= $escapar($jogador['nome']) ?></strong></div><a href="<?= $url('conta') ?>">Minha conta</a></div><a class="botao botao-primario" href="<?= $url('rodada') ?>">ACENDER O PAVIO →</a></div>
    <?php else :
        ?><form class="cartao inicio-formulario" method="post" action="<?= $url('identificar') ?>"><?= $campoCsrf ?><label class="rotulo" for="nome">Como quer ser chamado?</label><input class="entrada" id="nome" name="nome" type="text" placeholder="Seu nome no jogo" minlength="2" maxlength="16" autocomplete="nickname" value="<?= $escapar($jogador['nome'] ?? '') ?>" required><button class="botao botao-primario" type="submit">ACENDER O PAVIO →</button><p class="pequeno suave">Sem cadastro para jogar. No final, crie uma conta se quiser salvar seu desempenho.</p></form><?php
    endif; ?></div>
<div class="cartao"><div class="topo-cabecalho"><h2><i class="icone icone-trofeu cor-faisca" aria-hidden="true"></i> Melhores jogadores</h2><a href="<?= $url('ranking') ?>">Ver Top 100</a></div><?php $registros = array_slice($ranking, 0, 5);
$dados['registros'] = $registros;
$incluir(dirname(__DIR__) . '/Componentes/Ranking.php', $dados); ?></div></div>
<footer class="rodape-ligacoes"><a href="<?= $url('como-funciona') ?>">Como funciona</a><a href="<?= $url('privacidade') ?>">Privacidade</a><a href="<?= $url('admin') ?>">Administração</a></footer></section>
    <?php
};
