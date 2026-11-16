<?php

/**
 * Prepara a sessão nativa, atende a requisição e emite a resposta do servidor.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

namespace Aplicacao\Nucleo\Comuns\Classes;

use Aplicacao\Nucleo\Transportes\Resposta;
use Closure;

final class EntradaHTTP
{
    public function __construct(private string $raiz, private Closure $construir, private array $adicionais = [])
    {
    }

    public function executar(): void
    {
        \ini_set('display_errors', '0');
        \ini_set('session.use_strict_mode', '1');
        \session_name('QUIZIFMT');
        \session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => ($_SERVER['HTTPS'] ?? '') === 'on', 'httponly' => true, 'samesite' => 'Lax']);
        if (!\session_start()) {
            (new Resposta(503, 'Não foi possível iniciar uma sessão segura. Tente novamente mais tarde.'))->enviar();
            return;
        }
        $resposta = (new Inicializacao($this->raiz, $this->adicionais))->responder($_SERVER, $_POST, \getenv(), $_SESSION, $this->construir, (int) \floor(\microtime(true) * 1000));
        if (isset($_SESSION['renovar'])) {
            \session_regenerate_id(true);
            unset($_SESSION['renovar']);
        }
        \session_write_close();
        $resposta->enviar();
    }
}
