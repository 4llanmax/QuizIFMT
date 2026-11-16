<?php

/**
 * Aplica permissões às operações de usuários expostas pelo MCP.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\API\Servicos;

use Closure;
use DomainException;

final class FerramentasUsuarios
{
    public function __construct(private Closure $consultar, private Closure $criar, private Closure $desativar, private Closure $trocar)
    {
    }

    public function listar(): array
    {
        $texto = ['type' => 'string'];
        $definicoes = [
            'usuarios_listar' => ['Lista os usuários do MCP. Exige administração.', [], [], true],
            'usuarios_criar' => ['Cria usuário com a senha inicial configurada e exige troca na primeira autorização. Exige administração.', ['email' => $texto], ['email'], false],
            'usuarios_desativar' => ['Desativa um usuário e revoga seu acesso. Exige administração e confirmação explícita.', ['identificador' => ['type' => 'integer', 'minimum' => 1], 'confirmar' => ['type' => 'boolean', 'const' => true]], ['identificador', 'confirmar'], false],
            'senha_trocar' => ['Troca a senha do próprio usuário após conferir a senha atual.', ['atual' => $texto, 'nova' => $texto], ['atual', 'nova'], false],
        ];
        $ferramentas = [];
        foreach ($definicoes as $nome => [$descricao, $campos, $obrigatorios, $leitura]) {
            $ferramentas[] = ['name' => $nome, 'description' => $descricao,
                'inputSchema' => ['type' => 'object', 'properties' => (object) $campos, 'required' => $obrigatorios, 'additionalProperties' => false],
                'annotations' => ['readOnlyHint' => $leitura, 'destructiveHint' => !$leitura, 'openWorldHint' => false]];
        }
        return $ferramentas;
    }

    public function executar(string $nome, array $argumentos, array $usuario): array
    {
        if ($nome !== 'senha_trocar' && !($usuario['administrador'] ?? false)) {
            throw new DomainException('Somente administradores gerenciam usuários do MCP.');
        }
        $operador = (int) $usuario['identificador'];
        if ($nome === 'usuarios_listar') {
            return ['usuarios' => ($this->consultar)()];
        }
        if ($nome === 'usuarios_criar') {
            $email = $argumentos['email'] ?? null;
            if (!\is_string($email) || \strlen($email) > 254 || !\filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new DomainException('Informe um e-mail válido.');
            }
            return ($this->criar)($email, $operador);
        }
        if ($nome === 'usuarios_desativar') {
            $identificador = $argumentos['identificador'] ?? null;
            if (!\is_int($identificador) || $identificador < 1 || ($argumentos['confirmar'] ?? false) !== true) {
                throw new DomainException('Informe o identificador e confirme a desativação.');
            }
            ($this->desativar)($identificador, $operador);
            return ['desativado' => true];
        }
        if ($nome === 'senha_trocar') {
            if (!\is_string($argumentos['atual'] ?? null) || !\is_string($argumentos['nova'] ?? null)) {
                throw new DomainException('Informe a senha atual e a nova senha.');
            }
            ($this->trocar)($operador, $argumentos['atual'], $argumentos['nova']);
            return ['alterada' => true];
        }
        throw new DomainException('Ferramenta desconhecida.');
    }
}
