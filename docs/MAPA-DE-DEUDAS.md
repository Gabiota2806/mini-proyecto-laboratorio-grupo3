# Mapa de deudas — laboratorio-pedidos

Planilla de trabajo para la clase práctica. Cada grupo completa **una fila**
durante la ronda de refactor y la usa como descripción de su Pull Request.

| Archivo | Síntoma sembrado | Principio violado | Patrón que lo repara | Grupo | Rama | PR |
|---|---|---|---|---|---|---|
| `public/index.php` | Requires manuales, credenciales, ruteo con `if` | OCP + configuración en código | Autoload + tabla de rutas | | | |
| `src/Database/Connection.php` | Conexión nueva por consulta, excepción silenciada | Gestión de recursos | Singleton | | | |
| `src/Models/Order.php` | El modelo persiste y calcula precios | SRP | Repository + Strategy | | | |
| `src/Pricing/PriceCalculator.php` | `switch` con todos los algoritmos, duplicado | OCP + DRY | Strategy | Grupo 3 | `feat/patron-strategy` | [#1](https://github.com/Gabiota2806/mini-proyecto-laboratorio-grupo3/pull/1) |
| `src/Notifications/NotificationSender.php` | `if` por tipo repetido en 3 archivos | OCP + DIP | Factory | | | |
| `src/Legacy/LegacyNotifier.php` | Clase de terceros modificada + copia y pega | DRY + límites del sistema | Adapter | | | |
| `src/Reports/ReportGenerator.php` | Banderas booleanas (`boolean trap`) | OCP | Decorator | | | |
| `src/Events/OrderEvents.php` | Avisos encadenados a clases concretas | DIP | Observer | | | |
| `src/Services/OrderService.php` | Método que hace de todo | SRP | Facade | | | |
| `src/Controllers/OrderController.php` | SQL + negocio + HTML juntos | SRP + MVC | MVC | | | |
| `views/orders.php` | Consulta, calcula y no escapa | MVC + seguridad | MVC | | | |

## Referencias

- **SRP** — una clase, un motivo de cambio.
- **OCP** — abierto a extensión, cerrado a modificación.
- **DIP** — depender de abstracciones, no de implementaciones concretas.
- **DRY** — el mismo conocimiento no se escribe dos veces.

## Métrica del proyecto

El descuento de obra social (`0.7`) aparece en **5 archivos**.
Contar cuántos quedan después del refactor: esa diferencia es el trabajo hecho.
