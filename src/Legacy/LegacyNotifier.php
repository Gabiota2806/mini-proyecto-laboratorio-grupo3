<?php
/**
 * ============================================================================
 *  SISTEMA HEREDADO DEL LABORATORIO
 *  Patron aplicado: ADAPTER (estructural)
 * ============================================================================
 *
 *  Refactorizado segun metodologia Refactoring.Guru (Ejercicio 3 - TP):
 *  - LegacyNotifier se preserva intacto como clase provista por el proveedor externo.
 *  - Se elimino el metodo intrusivo send() agregado indebidamente a la clase externa.
 *  - Se elimino la clase redundante CopiaDeLegacyEnNuestroSistema (eliminacion de deuda).
 *  - LegacyNotifierAdapter implementa el contrato unificado Notification y traduce llamadas.
 * ============================================================================
 */

declare(strict_types=1);

/**
 * Contrato unificado esperado por el sistema para enviar notificaciones y avisos.
 */
if (!interface_exists('Notification')) {
    interface Notification
    {
        public function send(string $message): void;
    }
}

/**
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

/**
 * Adaptador que traduce el contrato Notification a la firma del sistema externo.
 * COMPOSICION: el adaptador TIENE una instancia de LegacyNotifier.
 */
final class LegacyNotifierAdapter implements Notification
{
    public function __construct(private LegacyNotifier $legacy) {}

    public function send(string $message): void
    {
        // Unico punto de traduccion en todo el sistema
        $this->legacy->sendMessage($message);
    }
}
