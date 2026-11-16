<?php

/**
 * Confere permissões e validação das ferramentas de usuários do MCP.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Testes\Unitario\API;

use Aplicacao\API\Servicos\FerramentasUsuarios;
use DomainException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FerramentasUsuariosTeste extends TestCase
{
    #[Test]
    public function executaUsuariosSomenteComPermissaoEConfirmaDesativacao(): void
    {
        $efeitos = [];
        $servico = new FerramentasUsuarios(
            static fn (): array => [['identificador' => 1, 'email' => 'admin@example.invalid']],
            static function (string $email, int $criador) use (&$efeitos): array {
                $efeitos[] = [$email, $criador];
                return ['identificador' => 2, 'email' => $email];
            },
            static function (int $identificador, int $operador) use (&$efeitos): void {
                $efeitos[] = [$identificador, $operador];
            }
        );
        $admin = ['identificador' => 1, 'administrador' => true];
        self::assertSame(['usuarios_listar', 'usuarios_criar', 'usuarios_desativar'], array_column($servico->listar(), 'name'));
        self::assertStringNotContainsString('exige troca', $servico->listar()[1]['description']);
        self::assertCount(1, $servico->executar('usuarios_listar', [], $admin)['usuarios']);
        self::assertSame(2, $servico->executar('usuarios_criar', ['email' => 'novo@example.invalid'], $admin)['identificador']);
        self::assertTrue($servico->executar('usuarios_desativar', ['identificador' => 2, 'confirmar' => true], $admin)['desativado']);
        self::assertSame([['novo@example.invalid', 1], [2, 1]], $efeitos);
        foreach (
            [
            ['usuarios_listar', [], ['identificador' => 2]],
            ['usuarios_criar', ['email' => 'novo@example.invalid'], ['identificador' => 2]],
            ['usuarios_criar', ['email' => []], $admin],
            ['usuarios_criar', ['email' => 'invalido'], $admin],
            ['usuarios_criar', ['email' => str_repeat('a', 255) . '@example.invalid'], $admin],
            ['usuarios_desativar', ['identificador' => 2], $admin],
            ['usuarios_desativar', ['identificador' => 2, 'confirmar' => 'true'], $admin],
            ['usuarios_desativar', ['identificador' => '2', 'confirmar' => true], $admin],
            ['usuarios_desativar', ['identificador' => 0, 'confirmar' => true], $admin],
            ['senha_trocar', ['atual' => 'anterior-ficticia', 'nova' => 'nova-ficticia'], $admin],
            ['senha_trocar', ['atual' => 'anterior-ficticia', 'nova' => 'nova-ficticia'], ['identificador' => 2]],
            ['desconhecida', [], $admin],
            ] as [$nome, $argumentos, $usuario]
        ) {
            try {
                $servico->executar($nome, $argumentos, $usuario);
                self::fail('A operação inválida precisa ser recusada.');
            } catch (DomainException) {
                self::assertCount(2, $efeitos);
            }
        }
    }
}
