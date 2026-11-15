# Casos de Prueba — laboratorio-pedidos

- **Grupo:** Grupo 3 — Metodología de Sistemas II, UTN FRRe (sede Formosa)
- **Unidad:** U5 — Testing de software
- **Alcance:** código refactorizado con patrones (Strategy y Decorator) y smoke test de la aplicación.
- **Ambiente:** PHP 8.3, PHPUnit 10.5. Local: `composer test`. CI: GitHub Actions (`.github/workflows/ci.yml`) en cada push y PR.
- **Fecha de ejecución:** 14/11/2026

Cada funcionalidad tiene **al menos un caso positivo** (camino feliz: datos válidos, flujo esperado)
y **al menos un caso negativo** (camino alternativo: datos inválidos o inesperados, y el sistema
tiene que responder bien al error). La columna *Test automatizado* indica qué test de PHPUnit
ejecuta el caso; los casos de sistema (CP-SIS) son manuales.

## Resumen

| Funcionalidad | Casos positivos | Casos negativos | Estado |
|---|---|---|---|
| F1. Calcular precio según tipo de paciente | CP-STR-01 | CP-STR-02, CP-STR-09 | ⚠️ 1 defecto (DEF-01) |
| F2. Cambiar la estrategia en tiempo de ejecución | CP-STR-03 | CP-STR-04 | ✅ |
| F3. Calcular precio con IVA | CP-STR-05 | CP-STR-06 | ✅ |
| F4. Método puente `calcular(monto, tipo)` | CP-STR-07 | CP-STR-08 | ✅ |
| F5. Generar un reporte (base o con un agregado) | CP-DEC-01 | CP-DEC-02 | ✅ |
| F6. Combinar agregados en un reporte | CP-DEC-03, CP-DEC-04 | CP-DEC-05, CP-DEC-06 | ✅ |
| F7. Rutas de la aplicación (smoke test) | CP-SIS-01 | CP-SIS-02 | ✅ |

**Totales:** 17 casos (8 positivos, 9 negativos) · 16 pasan · 1 falla (defecto DEF-01 reportado).

> **Adapter (`LegacyNotifierAdapter`)**: sus casos quedan pendientes hasta que se mergee el PR #6.

---

## Strategy — `src/Pricing/PriceCalculator.php`

### F1. Calcular precio según tipo de paciente

#### CP-STR-01 · Positivo · Obra social aplica 30% de descuento
| Campo | Valor |
|---|---|
| Descripción | Una estrategia de obra social cobra el 70% del monto. |
| Precondiciones | Ninguna. |
| Datos de entrada | Estrategia `InsuranceStrategy` (descuento por defecto) · monto `1000.0` |
| Pasos | 1. Crear `new InsuranceStrategy()`. 2. Llamar `calculate(1000.0)`. |
| Resultado esperado | `700.0` |
| Resultado obtenido | `700.0` |
| Prioridad · Estado | Alta · ✅ Pasa |
| Test automatizado | `PricingStrategiesTest::testInsuranceStrategyAppliesDefaultThirtyPercentDiscount` |

#### CP-STR-02 · Negativo · Monto no numérico
| Campo | Valor |
|---|---|
| Descripción | Si el monto llega como texto, el sistema lo rechaza en lugar de calcular cualquier cosa. |
| Precondiciones | El código que llama declara `strict_types=1`. |
| Datos de entrada | Estrategia `InsuranceStrategy` · monto `'abc'` |
| Pasos | 1. Crear `new InsuranceStrategy()`. 2. Llamar `calculate('abc')`. |
| Resultado esperado | Se lanza `TypeError`. |
| Resultado obtenido | `TypeError` |
| Prioridad · Estado | Media · ✅ Pasa |
| Test automatizado | `PricingNegativeCasesTest::testNonNumericAmountIsRejected` |

#### CP-STR-09 · Negativo · Monto negativo
| Campo | Valor |
|---|---|
| Descripción | Un pedido no puede tener un monto negativo: el cálculo debería rechazarlo. |
| Precondiciones | Ninguna. |
| Datos de entrada | Estrategia `InsuranceStrategy` · monto `-1000.0` |
| Pasos | 1. Crear `new InsuranceStrategy()`. 2. Llamar `calculate(-1000.0)`. |
| Resultado esperado | Se rechaza el monto (excepción `InvalidArgumentException`). |
| Resultado obtenido | Devuelve `-700.0` sin error. |
| Prioridad · Estado | Media · ❌ **Falla** → defecto **DEF-01** |
| Test automatizado | No se automatiza: escribir un test que espere `-700.0` convertiría el defecto en comportamiento esperado. Se agrega cuando se corrija. |

### F2. Cambiar la estrategia en tiempo de ejecución

#### CP-STR-03 · Positivo · Cambio dinámico de estrategia
| Campo | Valor |
|---|---|
| Descripción | El mismo `PriceCalculator` cambia de algoritmo sin crear otro objeto. |
| Precondiciones | `PriceCalculator` creado sin estrategia (por defecto, particular). |
| Datos de entrada | Monto `1000.0` · estrategias: particular → obra social → prepaga |
| Pasos | 1. `calculate(1000.0)`. 2. `setStrategy(new InsuranceStrategy())` y `calculate(1000.0)`. 3. `setStrategy(new PrepaidStrategy())` y `calculate(1000.0)`. |
| Resultado esperado | `1000.0`, luego `700.0`, luego `800.0` |
| Resultado obtenido | `1000.0`, `700.0`, `800.0` |
| Prioridad · Estado | Alta · ✅ Pasa |
| Test automatizado | `PriceCalculatorTest::testSetStrategyDynamicallySwitchesCalculation` |

#### CP-STR-04 · Negativo · Objeto que no es una estrategia
| Campo | Valor |
|---|---|
| Descripción | El contexto solo acepta objetos que cumplen la interfaz `PricingStrategy`. |
| Precondiciones | `PriceCalculator` creado. |
| Datos de entrada | Un objeto `stdClass` |
| Pasos | 1. Llamar `setStrategy(new stdClass())`. |
| Resultado esperado | Se lanza `TypeError`. |
| Resultado obtenido | `TypeError` |
| Prioridad · Estado | Media · ✅ Pasa |
| Test automatizado | `PricingNegativeCasesTest::testCalculatorRejectsObjectThatIsNotAStrategy` |

### F3. Calcular precio con IVA

#### CP-STR-05 · Positivo · Obra social con IVA 21%
| Campo | Valor |
|---|---|
| Descripción | El IVA se aplica sobre el precio ya descontado. |
| Precondiciones | `PriceCalculator` con `InsuranceStrategy`. |
| Datos de entrada | Monto `1000.0` · IVA por defecto (21%) |
| Pasos | 1. Llamar `calculateWithVat(1000.0)`. |
| Resultado esperado | `847.0` (1000 × 0.7 × 1.21) |
| Resultado obtenido | `847.0` |
| Prioridad · Estado | Alta · ✅ Pasa |
| Test automatizado | `PriceCalculatorTest::testCalculateWithVat` |

#### CP-STR-06 · Negativo · Tasa de IVA en cero
| Campo | Valor |
|---|---|
| Descripción | Con una tasa inesperada de 0%, no se aplica recargo. |
| Precondiciones | `PriceCalculator` con `InsuranceStrategy`. |
| Datos de entrada | Monto `1000.0` · IVA `0.0` |
| Pasos | 1. Llamar `calculateWithVat(1000.0, 0.0)`. |
| Resultado esperado | `700.0` |
| Resultado obtenido | `700.0` |
| Prioridad · Estado | Baja · ✅ Pasa |
| Test automatizado | `PricingNegativeCasesTest::testZeroVatRateReturnsAmountWithoutSurcharge` |

### F4. Método puente `calcular(monto, tipo)`

#### CP-STR-07 · Positivo · Tipo "jubilado"
| Campo | Valor |
|---|---|
| Descripción | El código viejo que pasa el tipo como texto sigue funcionando. |
| Precondiciones | `PriceCalculator` creado. |
| Datos de entrada | Monto `1000.0` · tipo `'jubilado'` |
| Pasos | 1. Llamar `calcular(1000.0, 'jubilado')`. |
| Resultado esperado | `500.0` |
| Resultado obtenido | `500.0` |
| Prioridad · Estado | Alta · ✅ Pasa |
| Test automatizado | `PriceCalculatorTest::testLegacyBridgeMethodCalcular` |

#### CP-STR-08 · Negativo · Tipo de paciente desconocido
| Campo | Valor |
|---|---|
| Descripción | Un tipo que no existe se cobra como particular (sin descuento), no rompe la ejecución. |
| Precondiciones | `PriceCalculator` creado. |
| Datos de entrada | Monto `1000.0` · tipo `'desconocido'` |
| Pasos | 1. Llamar `calcular(1000.0, 'desconocido')`. |
| Resultado esperado | `1000.0` |
| Resultado obtenido | `1000.0` |
| Prioridad · Estado | Media · ✅ Pasa |
| Test automatizado | `PricingNegativeCasesTest::testUnknownPatientTypeIsChargedAsPrivate` |

---

## Decorator — `src/Reports/Report.php`

### F5. Generar un reporte (base o con un agregado)

#### CP-DEC-01 · Positivo · Reporte base
| Campo | Valor |
|---|---|
| Descripción | El reporte base genera el contenido sin agregados, y un decorador solo también es un `Report`. |
| Precondiciones | Ninguna. |
| Datos de entrada | Contenido `'Pedido 1'` |
| Pasos | 1. `(new BasicReport('Pedido 1'))->generate()`. 2. Envolverlo en `WatermarkDecorator` y llamar `generate()`. |
| Resultado esperado | `Reporte: Pedido 1` · luego `Reporte: Pedido 1 + marca de agua` |
| Resultado obtenido | Igual al esperado |
| Prioridad · Estado | Alta · ✅ Pasa |
| Test automatizado | `ReportTest::testBasicReportWithoutExtras`, `ReportTest::testEachDecoratorIsAlsoAReport` |

#### CP-DEC-02 · Negativo · El decorador recibe algo que no es un reporte
| Campo | Valor |
|---|---|
| Descripción | Un decorador solo puede envolver un `Report`; un texto suelto se rechaza. |
| Precondiciones | Ninguna. |
| Datos de entrada | El texto `'Pedido 1'` en lugar de un `Report` |
| Pasos | 1. `new PdfReportDecorator('Pedido 1')`. |
| Resultado esperado | Se lanza `TypeError`. |
| Resultado obtenido | `TypeError` |
| Prioridad · Estado | Media · ✅ Pasa |
| Test automatizado | `ReportTest::testDecoratorRejectsSomethingThatIsNotAReport` |

### F6. Combinar agregados en un reporte

#### CP-DEC-03 · Positivo · Reporte del día (los tres agregados)
| Campo | Valor |
|---|---|
| Descripción | Mismo texto que generaba el antiguo `generate('Pedidos del dia', true, true, true)` (prueba de regresión). |
| Precondiciones | Orden acordado: firma → PDF → marca de agua. |
| Datos de entrada | Contenido `'Pedidos del dia'` |
| Pasos | 1. Envolver `BasicReport` con `DigitalSignatureDecorator`, `PdfReportDecorator` y `WatermarkDecorator`. 2. Llamar `generate()`. |
| Resultado esperado | `Reporte: Pedidos del dia + firma digital + PDF + marca de agua` |
| Resultado obtenido | Igual al esperado |
| Prioridad · Estado | Alta · ✅ Pasa |
| Test automatizado | `ReportTest::testDailyReportUsedByOrderController` |

#### CP-DEC-04 · Positivo · Reporte de pedido (firma + PDF)
| Campo | Valor |
|---|---|
| Descripción | Mismo texto que generaba el antiguo `generate("Pedido 1", true, true, false)` (prueba de regresión). |
| Precondiciones | Ninguna. |
| Datos de entrada | Contenido `'Pedido 1'` |
| Pasos | 1. Envolver `BasicReport` con `DigitalSignatureDecorator` y `PdfReportDecorator`. 2. Llamar `generate()`. |
| Resultado esperado | `Reporte: Pedido 1 + firma digital + PDF` |
| Resultado obtenido | Igual al esperado |
| Prioridad · Estado | Alta · ✅ Pasa |
| Test automatizado | `ReportTest::testOrderReportUsedByOrderService` |

#### CP-DEC-05 · Negativo · Orden de decoradores invertido
| Campo | Valor |
|---|---|
| Descripción | Si se arma en otro orden, el resultado cambia. Es la consecuencia negativa documentada del patrón. |
| Precondiciones | Ninguna. |
| Datos de entrada | Contenido `'X'` · orden PDF → firma (invertido) |
| Pasos | 1. Armar firma → PDF y generar. 2. Armar PDF → firma y generar. 3. Comparar. |
| Resultado esperado | `Reporte: X + firma digital + PDF` y `Reporte: X + PDF + firma digital` (distintos) |
| Resultado obtenido | Igual al esperado |
| Prioridad · Estado | Media · ✅ Pasa |
| Test automatizado | `ReportTest::testDecoratorOrderChangesTheResult` |

#### CP-DEC-06 · Negativo · Mismo decorador aplicado dos veces
| Campo | Valor |
|---|---|
| Descripción | Limitación conocida: nada impide duplicar un agregado. El test la deja registrada. |
| Precondiciones | Ninguna. |
| Datos de entrada | Contenido `'X'` · `PdfReportDecorator` dos veces |
| Pasos | 1. `new PdfReportDecorator(new PdfReportDecorator(new BasicReport('X')))`. 2. Llamar `generate()`. |
| Resultado esperado | `Reporte: X + PDF + PDF` |
| Resultado obtenido | Igual al esperado |
| Prioridad · Estado | Baja · ✅ Pasa |
| Test automatizado | `ReportTest::testSameDecoratorTwiceDuplicatesTheExtra` |

---

## Sistema — `public/index.php` (smoke test manual)

#### CP-SIS-01 · Positivo · Ruta de reporte
| Campo | Valor |
|---|---|
| Descripción | Después del refactor, la pantalla de reporte muestra lo mismo que antes. |
| Precondiciones | XAMPP con Apache iniciado y el proyecto en `htdocs`. |
| Datos de entrada | URL `public/index.php?accion=reporte` |
| Pasos | 1. Abrir la URL en el navegador. |
| Resultado esperado | `Reporte: Pedidos del dia + firma digital + PDF + marca de agua` |
| Resultado obtenido | Igual al esperado |
| Prioridad · Estado | Alta · ✅ Pasa |
| Test automatizado | Manual |

#### CP-SIS-02 · Negativo · Acción inexistente
| Campo | Valor |
|---|---|
| Descripción | Una acción que no existe no rompe la aplicación. |
| Precondiciones | XAMPP con Apache iniciado y el proyecto en `htdocs`. |
| Datos de entrada | URL `public/index.php?accion=inexistente` |
| Pasos | 1. Abrir la URL en el navegador. |
| Resultado esperado | Mensaje `Accion no encontrada` |
| Resultado obtenido | Igual al esperado |
| Prioridad · Estado | Media · ✅ Pasa |
| Test automatizado | Manual |

---

## Defectos encontrados

#### DEF-01 · El cálculo de precio acepta montos negativos
| Campo | Valor |
|---|---|
| Título | `PricingStrategy::calculate()` acepta un monto negativo y devuelve un precio negativo |
| Reportado por | Valeria (Grupo 3) |
| Caso que lo detectó | CP-STR-09 |
| Descripción | Ninguna estrategia valida el monto de entrada. Un pedido con monto negativo genera un precio negativo, es decir, el laboratorio le "debe" al paciente. |
| Pasos de reproducción | 1. `(new InsuranceStrategy())->calculate(-1000.0)` |
| Resultado esperado | Excepción `InvalidArgumentException` (monto inválido). |
| Resultado obtenido | `-700.0` |
| Entorno | PHP 8.3.6 (CLI) · rama `main` (commit `1883365`) |
| Severidad · Prioridad | Media · Media |
| Estado | Abierto. La validación de datos de entrada le corresponde al validador del refactor de Facade (`OrderService`). |
