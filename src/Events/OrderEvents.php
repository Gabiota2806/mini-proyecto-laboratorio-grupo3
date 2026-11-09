<?php
/**
 * ============================================================================
 *  AVISOS AL CREARSE UN PEDIDO
 *  Patron aplicado: OBSERVER (comportamiento)
 * ============================================================================
 *
 *  Refactorizado segun metodologia Refactoring.Guru:
 *  - Interfaz OrderObserver para tipar polimorficamente a los subscriptores.
 *  - Sujeto OrderSubject que gestiona una lista de observadores sin conocer clases concretas.
 *  - Observadores concretos desacoplados:
 *      * EmailObserver
 *      * DashboardObserver
 *      * SmsObserver (incorporado segun Ejercicio 5 sin modificar OrderSubject)
 *  - Inversion de Dependencias (DIP) y principio Abierto/Cerrado (OCP).
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../Models/Order.php';
require_once __DIR__ . '/../Notifications/NotificationSender.php';

/**
 * Contrato que deben cumplir todos los interesados en eventos de pedidos.
 */
interface OrderObserver
{
    public function update(Order $order): void;
}

/**
 * Observador encargado del envio de correo electronico ante pedidos creados.
 */
final class EmailObserver implements OrderObserver
{
    public function __construct(private string $email = 'paciente@mail.com') {}

    public function update(Order $order): void
    {
        NotificationFactory::create('email', $this->email)
            ->send("Pedido {$order->id}");
    }
}

/**
 * Observador encargado de actualizar el panel de monitoreo y dashboard clinico.
 */
final class DashboardObserver implements OrderObserver
{
    public function update(Order $order): void
    {
        echo "[DASHBOARD] actualizado para el pedido {$order->id}<br>";
    }
}

/**
 * Observador para alertas via SMS.
 * Incorporado para resolver el Ejercicio 5 del TP sin tocar OrderSubject.
 */
final class SmsObserver implements OrderObserver
{
    public function __construct(private string $phone = '3704000000') {}

    public function update(Order $order): void
    {
        NotificationFactory::create('sms', $this->phone)
            ->send("Pedido {$order->id} creado");
    }
}

/**
 * Sujeto observable que mantiene la coleccion de subscriptores.
 * Conoce unicamente la interfaz OrderObserver (DIP).
 */
final class OrderSubject
{
    /** @var array<int, OrderObserver> */
    private array $observers = [];

    public function subscribe(OrderObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    public function unsubscribe(OrderObserver $observer): void
    {
        $this->observers = array_values(
            array_filter($this->observers, fn(OrderObserver $o) => $o !== $observer)
        );
    }

    public function notify(Order $order): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($order);
        }
    }
}

/**
 * Despachador de eventos mantenido para retrocompatibilidad con codigo cliente existente.
 */
class OrderEvents
{
    private OrderSubject $subject;

    public function __construct(?OrderSubject $subject = null)
    {
        if ($subject === null) {
            $subject = new OrderSubject();
            $subject->subscribe(new EmailObserver('paciente@mail.com'));
            $subject->subscribe(new SmsObserver('3704000000'));
            $subject->subscribe(new DashboardObserver());
        }
        $this->subject = $subject;
    }

    public function pedidoCreado(Order $order): void
    {
        $this->subject->notify($order);
    }

    public function getSubject(): OrderSubject
    {
        return $this->subject;
    }
}
