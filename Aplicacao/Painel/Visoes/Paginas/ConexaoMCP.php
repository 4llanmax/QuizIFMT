<?php

/**
 * Apresenta identificação, troca de senha inicial e consentimento da conexão MCP.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados): string {
    $escapar = static fn (string $valor): string => \htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    \ob_start();
    ?>
    <!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>BombQUIZ · Conectar MCP</title>
    <style>body{font:16px system-ui,sans-serif;background:#0b1020;color:#edf2ff;margin:0;padding:24px}main{max-width:540px;margin:5vh auto;background:#141e36;border:1px solid #30415f;border-radius:20px;padding:28px}h1{margin-top:0}label{display:block;margin:18px 0 6px}input{box-sizing:border-box;width:100%;padding:12px;border:1px solid #697996;border-radius:8px;background:#0b1020;color:#fff;font:inherit}button{font:inherit;border:0;border-radius:8px;padding:12px 18px;cursor:pointer;background:#74dfb1;color:#071c17;margin-top:20px}.cancelar{background:#30415f;color:#fff;margin-left:8px}small{color:#b4c2df}code{overflow-wrap:anywhere}.erro{color:#ffb4b4}</style></head><body><main>
    <h1>Conectar ao BombQUIZ</h1>
    <?php if ($dados['erro'] !== '') :
        ?><p class="erro" role="alert"><?= $escapar($dados['erro']) ?></p><?php
    endif; ?>
    <?php if ($dados['pedido']) : ?>
        <p>O aplicativo <strong><?= $escapar($dados['cliente']) ?></strong> solicita acesso à gestão de perguntas e à consulta de partidas e jogadores. Administradores também gerenciam usuários MCP.</p>
        <p><small>Retorno autorizado: <code><?= $escapar($dados['redirecionamento']) ?></code></small></p>
        <form method="post" action="<?= $escapar($dados['urlBase']) ?>/admin/mcp/autorizar">
        <input type="hidden" name="csrf" value="<?= $escapar($dados['csrf']) ?>">
        <label for="email">E-mail de acesso MCP</label><input id="email" name="email" type="email" maxlength="254" autocomplete="username" required>
        <label for="senha">Senha atual</label><input id="senha" name="senha" type="password" maxlength="72" autocomplete="current-password" required>
        <label for="senha-nova">Nova senha para o primeiro acesso</label><input id="senha-nova" name="senhaNova" type="password" minlength="10" maxlength="72" autocomplete="new-password">
        <small>No primeiro acesso, escolha uma senha própria de 10 a 72 bytes. Nos próximos acessos, deixe este campo vazio para manter a senha.</small>
        <div><button name="acao" value="autorizar">Entrar e autorizar</button><button class="cancelar" name="acao" value="cancelar" formnovalidate>Cancelar</button></div>
        </form>
    <?php else :
        ?><p>Inicie a conexão pelo cliente MCP para abrir uma solicitação válida.</p><?php
    endif; ?>
    </main></body></html>
    <?php
    return \preg_replace('/>\s+</', '><', \trim(\ob_get_clean()));
};
