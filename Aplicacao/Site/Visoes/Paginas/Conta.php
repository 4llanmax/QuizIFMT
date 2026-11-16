<?php

/**
 * Apresenta autenticação e criação opcional de conta.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {
    $pagina = $dados['pagina'];
    $url = $dados['url'];
    $jogador = $dados['jogador'];
    $campoCsrf = $dados['campoCsrf'];
    ?>
    <?php $cadastro = $pagina === 'cadastro'; ?>
<section class="tela"><div class="barra-superior"><a class="botao-icone" href="<?= $url() ?>" aria-label="Voltar">←</a><span class="marca">BOMB<b>QUIZ</b></span><span></span></div><div class="autenticacao-cabecalho"><h1><?= $cadastro ? 'Salve seu desempenho' : 'Bem-vindo de volta' ?></h1><p class="suave"><?= $cadastro ? 'Com uma conta você acompanha suas partidas e pode trocar o apelido depois.' : 'Entre para jogar com seu apelido e acompanhar sua evolução.' ?></p></div><form class="formulario" method="post" action="<?= $url($cadastro ? 'cadastro' : 'entrar') ?>"><?= $campoCsrf ?><?php if ($cadastro) :
    ?><div class="campo"><label class="rotulo" for="nome">Apelido</label><input class="entrada" name="nome" id="nome" autocomplete="nickname" minlength="3" maxlength="16" value="<?= $escapar($jogador['nome'] ?? '') ?>" required><p class="orientacao">De 3 a 16 letras, números, ponto ou _. Nomes de contas e convidados no Top 100 ficam reservados.</p></div><?php
                                                                               endif; ?><div class="campo"><label class="rotulo" for="email">E-mail</label><input class="entrada" type="email" id="email" name="email" autocomplete="email" maxlength="254" required></div><div class="campo"><label class="rotulo" for="senha">Senha</label><input class="entrada" id="senha" type="password" name="senha" minlength="8" maxlength="72" autocomplete="<?= $cadastro ? 'new-password' : 'current-password' ?>" required><p class="orientacao">De 8 a 72 bytes. Sua senha nunca aparece no ranking.</p></div><button class="botao botao-primario" type="submit"><?= $cadastro ? 'CRIAR CONTA E SALVAR' : 'ENTRAR' ?></button></form><p class="pequeno suave">Seu apelido e seus resultados ficam públicos no ranking. O e-mail identifica sua conta. <a href="<?= $url('privacidade') ?>">Como seus dados são tratados</a>.</p><a class="botao botao-secundario" href="<?= $url($cadastro ? 'entrar' : 'cadastro') ?>"><?= $cadastro ? 'Já tenho uma conta' : 'Criar conta' ?></a></section>
    <?php
};
