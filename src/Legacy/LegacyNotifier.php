<?php

declare(strict_types=1);

/**
 * ============================================================================
 * SISTEMA HEREDADO DEL LABORATORIO
 * ============================================================================
 *
 * Clase original del proveedor externo. Se mantiene cerrada y no modificable.
 */
class LegacyNotifier
{
    /**
     * Metodo original del proveedor. No lo podemos cambiar.
     */
    public function sendMessage(string $text): void
    {
        echo "[LEGACY] {$text}<br>";
    }
}
