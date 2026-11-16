<?php

/**
 * Apresenta a bomba vetorial com pavio aceso.
 * Redator: Allan Max
 * Data de criação: 16/11/2026
 * Status: Sem revisão
 * Data do Status: 16/11/2026
 */

declare(strict_types=1);

return static function (array $dados, \Closure $escapar, \Closure $incluir): void {

    ?>
<svg class="destaque-bomba" viewBox="0 0 120 120" role="img" aria-label="Bomba com pavio aceso"><path d="M84 24 C92 10 102 16 106 7" stroke="#C98A4B" stroke-width="5" fill="none" stroke-linecap="round"/><rect x="64" y="26" width="24" height="16" rx="4" fill="#2A2A3C" stroke="#F5F3EE" stroke-width="4" transform="rotate(40 76 34)"/><circle cx="54" cy="70" r="40" fill="#1B1B28" stroke="#F5F3EE" stroke-width="4"/><path d="M30 58 A28 28 0 0 1 48 40" stroke="#F5F3EE" stroke-width="4" fill="none" stroke-linecap="round" opacity="0.45"/><text x="55" y="88" text-anchor="middle" font-family="Arial Black, sans-serif" font-size="46" fill="#FF6B1A">?</text><g class="centelha"><circle cx="106" cy="7" r="12" fill="#FF6B1A" opacity="0.35"/><circle cx="106" cy="7" r="6" fill="#FFC93C"/></g></svg>
    <?php
};
