<?php

/**
 * Identifica a área por descrições externas e aplica proteção comum contra requisições indevidas.
 * Redator: Allan Max
 * Data de criação: 15/11/2026
 * Status: Sem revisão
 * Data do Status: 15/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Comuns\Classes;

use Aplicacao\Nucleo\Excecoes\FalhaHTTP;
use Aplicacao\Nucleo\Transportes\Resposta;
use DomainException;

final class Web
{
    public function __construct(private array $areas, private string $prefixo = '/')
    {
    }

    public function atender(string $metodo, string $caminho, array $campos, array &$sessao, int $agora, string $origem): Resposta
    {
        $sessao['csrf'] ??= \bin2hex(\random_bytes(32));
        $sessao['tokenConvidado'] ??= \bin2hex(\random_bytes(32));
        try {
            if ($metodo === 'POST' && (!\is_string($campos['csrf'] ?? null) || !\hash_equals($sessao['csrf'], $campos['csrf']))) {
                throw new FalhaHTTP(403, 'A sessão do formulário expirou. Abra a página novamente.');
            }
            foreach ($this->areas as $prefixo => $area) {
                if ($prefixo === '/' || $caminho === $prefixo || \str_starts_with($caminho, $prefixo . '/')) {
                    return $area->atender($metodo, $caminho, $campos, $sessao, $agora, $origem);
                }
            }
            throw new FalhaHTTP(404, 'Página não encontrada.');
        } catch (FalhaHTTP | DomainException $falha) {
            $status = $falha instanceof FalhaHTTP ? $falha->status : 422;
            $texto = \htmlspecialchars($falha->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $prefixo = \htmlspecialchars($this->prefixo, ENT_QUOTES, 'UTF-8');
            return new Resposta($status, '<!doctype html><html lang="pt-BR"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>BombQUIZ · Aviso</title><link rel="stylesheet" href="' . $prefixo . 'Ativos/CSS/Principal.min.css"><main class="aplicativo"><section class="tela"><h1>Não foi possível continuar</h1><p role="alert">' . $texto . '</p><a class="botao botao-primario" href="' . $prefixo . '">Voltar ao início</a></section></main></html>');
        }
    }
}
