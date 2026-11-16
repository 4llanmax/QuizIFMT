<?php

/**
 * Associa os caminhos estruturados da API aos atendimentos MCP e OAuth.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return [
    '/.well-known/oauth-authorization-server' => 'oauth',
    '/.well-known/oauth-protected-resource' => 'oauth',
    '/.well-known/oauth-protected-resource/mcp' => 'oauth',
    '/oauth/registrar' => 'oauth',
    '/oauth/token' => 'oauth',
    '/oauth/revogar' => 'oauth',
    '/mcp' => 'mcp',
];
