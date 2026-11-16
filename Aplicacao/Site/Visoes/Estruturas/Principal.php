<?php

/**
 * Compõe a interface com dados preparados pelo controle.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {
    $prefixo = $dados['prefixo'];
    $csrf = $dados['csrf'];
    $pagina = $dados['pagina'];
    ?>
    <?php
    $url = static fn (string $destino = ''): string => $escapar($prefixo . $destino);
    $campoCsrf = '<input type="hidden" name="csrf" value="' . $escapar($csrf) . '">';
    $tempoFormatado = static fn (int|string $tempo): string => intdiv((int) round((int) $tempo / 1000), 60) . ':' . str_pad((string) ((int) round((int) $tempo / 1000) % 60), 2, '0', STR_PAD_LEFT);
    $nomesTemas = ['geografia' => 'Geografia', 'historia' => 'História', 'ciencias' => 'Ciências', 'esportes' => 'Esportes', 'cinema' => 'Cinema e TV', 'musica' => 'Música'];
    $nomesPaginas = ['inicio' => 'Inicio', 'regras' => 'Informacoes', 'privacidade' => 'Informacoes', 'rodada' => 'Rodada', 'jogo' => 'Partida', 'resultado' => 'Resultado', 'entrar' => 'Conta', 'cadastro' => 'Conta', 'conta' => 'Perfil', 'ranking' => 'Ranking'];
    $dados += ['url' => $url, 'campoCsrf' => $campoCsrf, 'tempoFormatado' => $tempoFormatado, 'nomesTemas' => $nomesTemas];
    ?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"><meta name="theme-color" content="#0A0A12"><meta name="description" content="Quiz eliminatório: um erro e… BOOM. Jogue e desarme a bomba."><title>BombQUIZ</title><link rel="manifest" href="<?= $url('Manifesto.webmanifest') ?>"><link rel="icon" href="<?= $url('Ativos/Imagens/Bomba.svg') ?>" type="image/svg+xml"><link rel="stylesheet" href="<?= $url('Ativos/CSS/Principal.min.css') ?>"><script src="<?= $url('Ativos/JavaScript/Principal.min.js') ?>" defer></script></head>
<body data-prefixo="<?= $escapar($prefixo) ?>"><main class="aplicativo<?= $pagina === 'inicio' ? ' amplo' : '' ?>">
    <?php $incluir(dirname(__DIR__) . '/Paginas/' . $nomesPaginas[$pagina] . '.php', $dados); ?>
</main><p class="aviso" role="status" aria-live="polite" data-mensagem></p><div class="vinheta" aria-hidden="true"></div></body></html>
    <?php
};
