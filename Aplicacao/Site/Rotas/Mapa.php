<?php

/**
 * Declara exclusivamente as associações HTTP do Site.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

return [
    '/' => ['GET' => 'inicio'],
    '/como-funciona' => ['GET' => 'regras'],
    '/privacidade' => ['GET' => 'privacidade'],
    '/identificar' => ['POST' => 'identificar'],
    '/rodada' => ['GET' => 'rodada'],
    '/jogar' => ['POST' => 'jogar'],
    '/jogo' => ['GET' => 'jogo'],
    '/responder' => ['POST' => 'responder'],
    '/avancar' => ['POST' => 'avancar'],
    '/abandonar' => ['POST' => 'abandonar'],
    '/expirar' => ['POST' => 'expirar'],
    '/ranking' => ['GET' => 'ranking'],
    '/entrar' => ['GET' => 'entrar', 'POST' => 'autenticar'],
    '/cadastro' => ['GET' => 'cadastro', 'POST' => 'cadastrar'],
    '/conta' => ['GET' => 'conta'],
    '/apelido' => ['POST' => 'renomear'],
    '/sair' => ['POST' => 'sair'],
    '/excluir-conta' => ['POST' => 'excluir'],
];
