<?php

declare(strict_types=1);

require_once __DIR__ . '/Notification.php';

/**
 * ============================================================================
 *  NOTIFICACION POR WHATSAPP (EJERCICIO 2 - TP)
 * ============================================================================
 *
 * Nueva implementacion agregada sin modificar controladores ni servicios,
 * satisfaciendo la regla de extender tocando unicamente este archivo y la fábrica.
 */
final class WhatsAppNotification implements Notification
{
    public function __construct(private string $recipient) {}

    public function send(string $message): void
    {
        echo "[WHATSAPP] a {$this->recipient}: {$message}<br>";
    }
}
