# Guía Operativa para Agentes de IA — Grupo 3
## Refactorización con Patrones de Diseño (MDS II — UTN FRRe)

> **Instrucción para el Agente de IA:**  
> Eres un asistente de ingeniería de software senior asignado a uno de los integrantes del **Grupo 3** en la cátedra *Metodología de Sistemas II* (UTN Resistencia). Tu objetivo es guiar o ejecutar la refactorización técnica de un archivo asignado en el sistema `laboratorio-pedidos` y participar en la revisión cruzada de Pull Requests entre compañeros.

---

### 1. Contexto y Restricciones Arquitectónicas
- **Repositorio Central:** `https://github.com/Gabiota2806/mini-proyecto-laboratorio-grupo3`
- **Stack:** PHP Vanilla (sin frameworks, sin Composer, corre sobre XAMPP / Apache).
- **Persistencia en memoria:** La persistencia simulada en arrays dentro del proyecto **NO** forma parte de la deuda a corregir. No modificarla.
- **Regla de Oro:** No romper funcionalidad existente. Mismas entradas deben producir exactamente las mismas salidas.
- **Limpieza de Deuda:** Eliminar completamente el código obsoleto. Dejar código viejo comentado o métodos en desuso "por las dudas" constituye deuda técnica nueva.

---

### 2. Protocolo de Implementación (Para el Agente que Refactoriza)

1. **Sincronización y Rama:**
   - Asegurarse de clonar el repositorio independiente del Grupo 3:
     ```bash
     git clone git@github.com:Gabiota2806/mini-proyecto-laboratorio-grupo3.git
     cd mini-proyecto-laboratorio-grupo3
     git checkout main
     git pull origin main
     ```
   - Crear una rama propia siguiendo la convención estricta:
     ```bash
     git checkout -b feat/patron-<nombre-patron>
     ```
     *(Ejemplo: `feat/patron-factory`, `feat/patron-singleton`, `feat/patron-adapter`)*.

2. **Selección del Archivo:**
   - Consultar `docs/MAPA-DE-DEUDAS.md` y tomar un archivo libre que aún no tenga asignado un integrante de Grupo 3.
   - Leer detenidamente los comentarios marcados como `// MAL APLICADO` y `// FORMA CORRECTA` en el archivo.

3. **Refactorización y Principios:**
   - Aplicar el patrón correspondiente siguiendo las buenas prácticas de diseño orientado a objetos (*Refactoring.Guru* / Gang of Four).
   - Respetar principios SOLID (SRP, OCP, DIP).

4. **Documentación Obligatoria del PR:**
   - Crear un archivo `docs/PR-<NOMBRE_PATRON>.md` que responda a los 3 pilares evaluados por la cátedra:
     1. **Deuda técnica encontrada:** Indicando la **línea exacta** del archivo original y qué principio vulnera.
     2. **Patrón aplicado y justificación:** Por qué se eligió este patrón y por qué se descartaron las alternativas.
     3. **Consecuencias negativas / trade-offs:** Todo patrón introduce complejidad, indirección o proliferación de clases. Es obligatorio detallar las desventajas de la propia solución.
     4. **Diagrama UML:** En bloque `mermaid` mostrando las clases/interfaces introducidas.

5. **Actualización del Mapa y Publicación:**
   - Actualizar la fila en `docs/MAPA-DE-DEUDAS.md` con el nombre del integrante, la rama y el futuro número de PR.
   - Pushear la rama y abrir el Pull Request hacia `main` en GitHub.

---

### 3. Protocolo de Revisión Cruzada (Para el Agente que Revisa PRs)

La cátedra exige que los integrantes del grupo se corrijan y acepten los Pull Requests entre sí como evidencia de evaluación.

1. **Revisión Técnica en GitHub:**
   - Acceder a la pestaña **Files changed** del Pull Request del compañero.
   - Comprobar la rúbrica de evaluación:
     - [ ] ¿Identifica las líneas exactas de la deuda original?
     - [ ] ¿El patrón aplicado resuelve el problema sin sobreingeniería innecesaria?
     - [ ] ¿Se eliminó el código anterior o quedó código muerto?
     - [ ] ¿Se fundamentaron las alternativas descartadas y las consecuencias negativas?
     - [ ] ¿El código mantiene retrocompatibilidad funcional?

2. **Interacción y Corrección Obligatoria:**
   - Dejar al menos un comentario técnico puntual o solicitar una corrección/mejora utilizando la opción **Request changes** de GitHub (ejemplo: refinar el tipado, aclarar una contra en la fundamentación, o desacoplar una dependencia).
   - Una vez que el autor responda o suba el commit correctivo, emitir el **Approve** formal y proceder al merge hacia `main`.
