<?php

declare(strict_types=1);

/**
 * Adaptador que traduce el contrato Notification a la firma del sistema externo.
 * COMPOSICION: el adaptador TIENE una instancia de LegacyNotifier.
 */
final class LegacyNotifierAdapter implements Notification
{
    public function __construct(
        private LegacyNotifier $legacy
    ) {
    }

    public function send(string $message): void
    {
        // Unico punto de traduccion en todo el sistema
        $this->legacy->sendMessage($message);
    }
}
