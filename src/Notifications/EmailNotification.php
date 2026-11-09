<?php

declare(strict_types=1);

require_once __DIR__ . '/Notification.php';

/**
 * ============================================================================
 *  NOTIFICACION POR EMAIL
 * ============================================================================
 *
 * Implementa el contrato Notification desacoplado de firmas arbitrarias.
 */
final class EmailNotification implements Notification
{
    public function __construct(private string $recipient = '') {}

    public function send(string $message): void
    {
        echo "[EMAIL] para {$this->recipient} | Pedido del laboratorio: {$message}<br>";
    }

    /**
     * Metodo heredado mantenido por retrocompatibilidad con codigo legacy.
     */
    public function enviarEmail(string $destinatario, string $asunto, string $cuerpo): void
    {
        $this->recipient = $destinatario;
        echo "[EMAIL] para {$destinatario} | {$asunto}: {$cuerpo}<br>";
    }
}
