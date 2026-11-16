<?php

/**
 * Explica as regras e os dados utilizados pelo jogo.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {
    $url = $dados['url'];
    $pagina = $dados['pagina'];
    $configuracao = $dados['configuracao'];
    ?>
<section class="tela"><div class="barra-superior"><a class="botao-icone" href="<?= $url() ?>" aria-label="Voltar">←</a><span class="marca">BOMB<b>QUIZ</b></span><span></span></div>
    <?php if ($pagina === 'regras') :
        ?><h1>Como funciona</h1><ul class="regras-lista"><li>Cada rodada tem <?= $configuracao['total'] ?> perguntas sorteadas de forma equilibrada entre as categorias com perguntas ativas. A diferença entre categorias é de no máximo uma pergunta; os temas que recebem a pergunta extra são sorteados.</li><li>Você tem <?= $configuracao['segundos'] ?> segundos por pergunta. Há cinco alternativas e apenas uma correta.</li><li>Errou ou deixou o tempo acabar? A bomba explode e a partida termina.</li><li>O ranking usa mais acertos e, no empate, menor tempo somado somente nas respostas corretas. Persistindo o empate, vale o resultado mais antigo.</li><li>A conta é opcional. Após criá-la, você acompanha seu histórico e pode trocar o apelido.</li><li>Recarregar a página ou mudar de aba não reinicia o tempo. Cada resposta é conferida no servidor.</li></ul>
    <?php else :
        ?><h1>Privacidade</h1><div class="cartao"><h2>Dados utilizados</h2><p>O jogo armazena seu nome ou apelido e os resultados para formar o ranking público. Se você criar uma conta, também armazena seu e-mail e uma representação protegida da senha. Seu e-mail e sua senha não aparecem no ranking.</p><p>Um identificador de sessão mantém sua partida e seu acesso. A preferência de som fica no navegador. O jogo não usa serviços externos de análise ou publicidade.</p></div><div class="cartao"><h2>Controle dos dados</h2><p>Na conta, você consulta as últimas 300 partidas, altera o apelido e pode excluir a conta e os resultados após confirmar a senha. O cadastro pode vincular a última partida de convidado desta sessão à conta.</p><p>Resultados de convidados ficam associados à sessão do navegador. Sem essa sessão, o jogo não consegue comprovar pelo navegador a titularidade desses resultados.</p><p>Os dados permanecem no banco até a exclusão. Contadores de acesso guardam somente uma chave opaca e são eliminados após 24 horas, durante novas tentativas. Os registros técnicos da hospedagem seguem os procedimentos de sua administração.</p></div><?php
    endif; ?><a class="botao botao-primario" href="<?= $url() ?>">VOLTAR AO JOGO</a></section>
    <?php
};
