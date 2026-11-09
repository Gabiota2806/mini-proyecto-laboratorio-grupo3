<?php

declare(strict_types=1);

require_once __DIR__ . '/Notification.php';

/**
 * ============================================================================
 *  NOTIFICACION POR SMS
 * ============================================================================
 *
 * Implementa el contrato Notification con la misma firma que los demas canales.
 */
final class SmsNotification implements Notification
{
    public function __construct(private string $recipient = '') {}

    public function send(string $message): void
    {
        echo "[SMS] a {$this->recipient}: {$message}<br>";
    }

    /**
     * Metodo heredado mantenido por retrocompatibilidad con codigo legacy.
     */
    public function mandarSms(string $numero, string $texto): void
    {
        $this->recipient = $numero;
        echo "[SMS] a {$numero}: {$texto}<br>";
    }
}
