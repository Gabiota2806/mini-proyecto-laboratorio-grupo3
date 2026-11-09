<?php
/**
 * ============================================================================
 *  ENVIO DE NOTIFICACIONES
 *  Patron aplicado: FACTORY METHOD (creacional)
 * ============================================================================
 *
 *  Refactorizado segun metodologia Refactoring.Guru:
 *  - Interfaz comun Notification para todos los canales de envio.
 *  - NotificationFactory centraliza la creacion polimorfica segun tipo.
 *  - NotificationSender delega en la factoria, eliminando ifs duplicados en clientes.
 *  - Resolucion del Ejercicio 2 del TP: incorporar WhatsApp tocando unicamente
 *    WhatsAppNotification.php y esta factoria.
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/Notification.php';
require_once __DIR__ . '/EmailNotification.php';
require_once __DIR__ . '/SmsNotification.php';
require_once __DIR__ . '/WhatsAppNotification.php';

/**
 * Fabrica encargada de instanciar los canales de notificacion concretos.
 */
final class NotificationFactory
{
    /**
     * Instancia y devuelve el canal adecuado segun el tipo solicitado.
     *
     * @throws InvalidArgumentException si el tipo solicitado no esta soportado.
     */
    public static function create(string $type, string $destination): Notification
    {
        return match (strtolower($type)) {
            'email'    => new EmailNotification($destination),
            'sms'      => new SmsNotification($destination),
            'whatsapp' => new WhatsAppNotification($destination),
            default    => throw new InvalidArgumentException(
                "Tipo de notificacion no soportado: '{$type}'"
            ),
        };
    }
}

/**
 * Emisor de notificaciones (mantenido para no romper codigo cliente existente).
 */
class NotificationSender
{
    /**
     * Envia el mensaje delegando la creacion a NotificationFactory.
     */
    public function enviar(string $tipo, string $destino, string $mensaje): void
    {
        NotificationFactory::create($tipo, $destino)->send($mensaje);
    }
}
