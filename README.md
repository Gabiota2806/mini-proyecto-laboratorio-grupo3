# laboratorio-pedidos — proyecto de práctica

[![CI Pipeline](https://github.com/Gabiota2806/mini-proyecto-laboratorio-grupo3/actions/workflows/ci.yml/badge.svg)](https://github.com/Gabiota2806/mini-proyecto-laboratorio-grupo3/actions/workflows/ci.yml)

Sistema de gestión de pedidos de un laboratorio de análisis clínicos.
**PHP Vanilla compatible con XAMPP**, con gestión de dependencias Composer para Testing y CI.

> ⚠️ **Este proyecto está roto a propósito.**
> Cada archivo contiene deuda técnica sembrada, marcada con comentarios:
>
> ```php
> // ❌ MAL APLICADO: qué método está mal y qué principio viola
> // ✅ FORMA CORRECTA: qué patrón corresponde y cómo se estructura
> ```
>
> El objetivo no es que funcione mejor: ya funciona. El objetivo es que
> **se pueda cambiar sin miedo**.

---

## Cómo levantarlo

1. Copiar la carpeta dentro de `C:\xampp\htdocs\`.
2. Iniciar Apache desde el panel de XAMPP (MySQL **no** hace falta).
3. Abrir: `http://localhost/mini-proyecto-laboratorio/public/index.php`

Acciones disponibles:

| URL | Qué hace |
|---|---|
| `public/index.php?accion=crear` | Crea un pedido pasando por toda la deuda |
| `public/index.php?accion=listar` | Lista pedidos desde la vista |
| `public/index.php?accion=reporte` | Genera un reporte con banderas booleanas |

La persistencia está simulada en memoria para que el proyecto arranque sin
configurar MySQL. Eso **no** es parte de la deuda a corregir.

---

## Testing e Integración Continua (CI)

Para correr la suite de herramientas de calidad localmente:

```bash
composer install          # Instala dependencias de desarrollo
composer test             # Ejecuta los tests unitarios con PHPUnit
composer cs               # Valida estándares de código PSR-12 con PHP_CodeSniffer
composer stan             # Análisis estático de tipos con PHPStan
composer check            # Ejecuta todo el conjunto (linter + stan + tests)
```

Consultar [docs/CI-CD.md](docs/CI-CD.md) para más detalles sobre la arquitectura del pipeline y las políticas de fusión requeridas en el TP 4.

---

## Mapa de deudas

| Archivo | Síntoma sembrado | Patrón / principio | Ejercicio |
|---|---|---|---|
| `public/index.php` | Requires manuales, credenciales en el código, ruteo con `if` | Autoload + config externa + tabla de rutas | — |
| `src/Database/Connection.php` | Una conexión nueva por consulta; excepción silenciada | **Singleton** | — |
| `src/Notifications/NotificationSender.php` | `if` por tipo repetido en 3 archivos | **Factory** | Ej. 2 |
| `src/Pricing/PriceCalculator.php` | `switch` con todos los algoritmos + lógica duplicada | **Strategy** | Ej. 1 |
| `src/Events/OrderEvents.php` | Avisos encadenados a mano a clases concretas | **Observer** | Ej. 5 |
| `src/Legacy/LegacyNotifier.php` | Clase de terceros modificada + copia y pega | **Adapter** | Ej. 3 |
| `src/Reports/ReportGenerator.php` | Parámetros booleanos (`boolean trap`) | **Decorator** | Ej. 4 |
| `src/Services/OrderService.php` | Método que hace de todo | **Facade** + SRP | Ej. 6 |
| `src/Controllers/OrderController.php` | SQL + negocio + HTML en el controlador | **MVC** + SRP | Ej. 7 |
| `src/Models/Order.php` | Modelo que se persiste y calcula precios | **Repository** + Strategy | — |
| `views/orders.php` | Consulta, calcula y no escapa la salida | **MVC** | Ej. 8 |

---

## La medida de la deuda de este proyecto

El descuento de obra social (**0.7**) está escrito en **cinco archivos distintos**:

```
src/Models/Order.php
src/Pricing/PriceCalculator.php   (dos veces)
src/Services/OrderService.php
src/Controllers/OrderController.php
views/orders.php
```

Cuando el laboratorio lo cambie al 25%, ese número es exactamente
cuántos lugares hay que tocar y cuántas oportunidades hay de olvidarse uno.

**Ese es el punto de toda la unidad.**

---

## Mecánica de la clase práctica

```
1. Cada grupo toma UN archivo del mapa de deudas.       (5 min)
2. Lee los comentarios ❌ y ✅.                          (5 min)
3. Refactoriza en una rama propia: feat/patron-<nombre>  (20 min)
4. Abre un Pull Request que responda tres cosas:
     - qué deuda encontró (con la línea exacta)
     - qué patrón aplicó y por qué ese y no otro
     - qué consecuencia negativa tiene su propia solución
5. Otro grupo revisa el PR y comenta.                    (10 min)
```

El PR revisado por otro grupo es evidencia del TP Integrador.

---

## Reglas del refactor

- **No se rompe funcionalidad.** Antes y después, la app hace lo mismo.
- **Un patrón por rama.** Nada de una rama con seis cambios mezclados.
- **Se borra el código viejo.** Dejar el método anterior "por las dudas" es
  deuda nueva.
- **Se documenta la consecuencia negativa.** Un patrón sin contras analizadas
  es sobreingeniería esperando su turno.
