<?php

declare(strict_types=1);

/**
 * Contrato unificado para el envío de notificaciones en el sistema.
 */
interface Notification
{
    public function send(string $message): void;
}
