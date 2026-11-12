# Documentación de Integración Continua (CI/CD)
## Metodología de Sistemas II — UTN FRRe (Sede Formosa) — 2026
**Proyecto:** `laboratorio-pedidos`  
**Grupo:** Grupo 3  
**Unidad Temática:** Unidad 4 — Técnicas de optimización del ciclo de desarrollo de software  
**Trabajo Práctico:** TP N.º 4 — Integración y Entrega Continua del Proyecto  

---

## 1. Introducción y Propósito

El objetivo de este pipeline de Integración Continua (CI) es automatizar la verificación de calidad, consistencia de codificación y correctitud funcional del software ante cada cambio realizado en el repositorio.

La integración continua previene el síndrome de *"en mi máquina funciona"* y asegura que ningún cambio que degrade la estabilidad o viole las directivas arquitectónicas ingrese a la rama principal (`main`).

---

## 2. Arquitectura del Pipeline (`.github/workflows/ci.yml`)

El pipeline se encuentra implementado mediante **GitHub Actions** y se dispara de forma automática en dos eventos:
1. `push`: ante cualquier subida de código en `main` o en ramas de características (`feat/**`, `refactor/**`).
2. `pull_request`: ante cualquier solicitud de integración dirigida hacia `main`.

### Etapas del Flujo (Jobs & Steps)

```
[ Git Push / PR ]
       │
       ▼
1. Checkout del Código (actions/checkout@v4)
       │
       ▼
2. Setup de PHP 8.3 & Extensiones (shivammathur/setup-php@v2)
       │
       ▼
3. Validación de composer.json (composer validate --strict)
       │
       ▼
4. Instalación de Dependencias con Caché (actions/cache@v4)
       │
       ▼
5. Análisis de Estilo de Código (PHP_CodeSniffer - PSR-12)
       │
       ▼
6. Análisis Estático de Tipos y Errores (PHPStan Nivel 2)
       │
       ▼
7. Ejecución de Tests Unitarios Automatizados (PHPUnit 10.5)
       │
       ▼
[ Pipeline Exitoso / Notificación de Fallo ]
```

---

## 3. Herramientas Integradas

| Herramienta | Versión | Rol en el Pipeline | Comando Local |
|---|---|---|---|
| **Composer** | 2.x | Gestión de dependencias y autoloading híbrido (`classmap` + `PSR-4`) | `composer install` |
| **PHP_CodeSniffer** | ^3.9 | Validación estricta del estándar PSR-12 | `composer cs` |
| **PHPStan** | ^1.11 | Análisis estático de tipos y detección de errores sin ejecución | `composer stan` |
| **PHPUnit** | ^10.5 | Ejecución de pruebas unitarias sobre las clases refactorizadas | `composer test` |

---

## 4. Comandos para Ejecución en Entorno Local

Antes de hacer `git push`, todo desarrollador puede verificar el estado del código localmente utilizando los scripts configurados en `composer.json`:

```bash
# Instalar dependencias
composer install

# Ejecutar únicamente las pruebas unitarias
composer test

# Auditar estándar de codificación PSR-12
composer cs

# Aplicar correcciones automáticas de formato PSR-12
composer cs-fix

# Ejecutar el análisis estático con PHPStan
composer stan

# Ejecutar el conjunto completo de validaciones (recomendado antes del commit)
composer check
```

---

## 5. Política de Bloqueo de Fusión (Branch Protection Rules)

Conforme a la consigna del TP N.º 4, el repositorio debe implementar una política formal que **bloquee la fusión de un Pull Request si el pipeline de CI no finaliza con éxito**.

### Configuración en GitHub

1. En el repositorio de GitHub, navegar a **Settings** > **Branches**.
2. En la sección **Branch protection rules**, agregar o editar una regla para la rama `main`.
3. Activar las siguientes opciones:
   - ✅ **Require a pull request before merging**: Exige que todo cambio ingrese mediante PR y no directamente a `main`.
   - ✅ **Require approvals**: Al menos 1 aprobación de otro compañero del grupo (revisión cruzada obligatoria).
   - ✅ **Require status checks to pass before merging**:
     - Activar **Require branches to be up to date before merging**.
     - En el buscador de comprobaciones de estado, seleccionar el job: `Lint, Static Analysis & Tests`.
   - ✅ **Do not allow bypassing the above settings**: Aplica incluso a los administradores del repositorio.

### Impacto en el Flujo de Trabajo

- Si un commit introduce un fallo de sintaxis, rompe un test o viola una regla de tipos, GitHub bloquea el botón **Merge pull request** mostrando el estado en rojo (`Checks failed`).
- El autor del PR está obligado a corregir el error en su rama local y pushear el cambio.
- Solo cuando todos los checks pasen a color verde (`All checks have passed`) y el revisor emita el `Approve`, la integración hacia `main` quedará habilitada.

---

## 6. Procedimiento para Simular y Documentar Evidencia de Fallo (Consigna 1.7)

Para cumplir con el requisito de *"adjuntar evidencia de al menos una ejecución fallida y de su posterior corrección"*:

1. En una rama de prueba o de demostración, introducir intencionalmente una aserción fallida en un test (por ejemplo, cambiar `700.0` por `999.0` en `PricingStrategiesTest.php`).
2. Pushear la rama y abrir el PR o esperar la corrida de GitHub Actions.
3. Tomar una captura de pantalla del pipeline fallido en GitHub Actions con el paso de `PHPUnit` en rojo.
4. Corregir el test con el valor esperado (`700.0`), pushear el fix y tomar la captura del pipeline pasando en verde.
5. Adjuntar ambas evidencias en el informe final de la unidad.
