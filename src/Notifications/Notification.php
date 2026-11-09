<?php

declare(strict_types=1);

/**
 * ============================================================================
 *  CONTRATO DE NOTIFICACION
 *  Patron: FACTORY METHOD
 * ============================================================================
 *
 * Define el contrato uniforme para cualquier canal de notificacion del sistema.
 */
interface Notification
{
    /**
     * Envia el mensaje al destinatario configurado.
     */
    public function send(string $message): void;
}
