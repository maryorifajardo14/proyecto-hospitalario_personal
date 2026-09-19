# ASII-10 — Expediente médico electrónico base
## Semana 11: mockup o prototipo navegable

> **Entrega correspondiente a la Semana 11** del plan ASII (`README.md` raíz, tabla "Plan semanal ASII", fila 11): *"Mockup o prototipo navegable"*, con evidencia *"Mockup desktop/móvil en Figma, Canva, Excalidraw o equivalente"*.
>
> Esta entrega usa la opción "equivalente" habilitada por la consigna: un **prototipo navegable en HTML/CSS/JS plano**, sin backend ni build tools, versionado en Git junto con el resto de la evidencia del módulo — en vez de un archivo binario o un enlace externo a una herramienta de diseño. El prototipo completo está en [`prototipo-navegable/`](./prototipo-navegable/), empezando por [`prototipo-navegable/index.html`](./prototipo-navegable/index.html).

## 1. Por qué un prototipo HTML en vez de Figma/Canva/Excalidraw

| Criterio | Herramienta de diseño externa | Prototipo HTML/CSS/JS (elegido) |
|---|---|---|
| Versionable junto al resto del módulo (docs, código, pruebas) | No (vive en una cuenta externa) | Sí, mismo repositorio y mismos commits |
| Navegable de verdad (clic real entre pantallas) | Sí, con Figma en modo prototipo | Sí, con enlaces `<a>` reales entre archivos `.html` |
| Permite probar reglas de interacción/accesibilidad ya definidas (foco, `aria-live`) | No (una herramienta de diseño no ejecuta JavaScript real) | Sí — es la única opción que permite **implementar** RI-08/RI-09/RI-11 (Semanas 9-10) y no solo describirlas |
| Requiere cuenta o software adicional para revisarlo | Sí | No — se abre con doble clic en cualquier navegador |

## 2. Qué cubre el prototipo

El detalle completo (mapa de pantallas, reglas implementadas, paleta validada por contraste y
comportamiento responsive) está documentado en [`prototipo-navegable/README.md`](./prototipo-navegable/README.md). Resumen:

- **9 pantallas** que cubren ambos user flows de la Semana 8 (Recepcionista: apertura; Médico/Enfermera: consulta) y sus casos de error (201, 409, 404, 403).
- **Modal de confirmación con foco atrapado** (`assets/app.js`), implementando la regla RI-08 de la Semana 9, no solo describiéndola.
- **Regiones `aria-live`/`role="alert"`** para los resultados de éxito y error, implementando RI-09.
- **Acordeón accesible por teclado** en la vista de expediente, implementando RI-11 (Semana 10).
- **Layout responsive real** (`assets/styles.css`, breakpoint `640px`), verificable redimensionando la ventana del navegador, siguiendo las prioridades de pantalla definidas en la Semana 10, sección 3.1.
- **Paleta validada por contraste** (WCAG 1.4.3), respondiendo al hallazgo H-03 de la Semana 9.

## 3. Qué no incluye (limitaciones explícitas)

- No consume la API real (`/api/v1/medical-records`); los datos de los tres pacientes de ejemplo están embebidos de forma estática en cada página.
- No implementa autenticación/tenant reales: el "rol" activo se simula con un parámetro de URL y `sessionStorage`, únicamente para decidir qué botones mostrar en el prototipo.
- No reemplaza la implementación real de los componentes Vue propuestos en la [Semana 7](./semana-7-diseno-componentes-refactorizacion.md#1-diagrama-de-componentes-backend--frontend-propuesto) (`PatientRecordSummaryCard.vue`, `OpenMedicalRecordModal.vue`, `MedicalRecordView.vue`); es el mockup que antecede a esa implementación, siguiendo el orden natural análisis → diseño → prototipo → implementación ya usado en el resto del módulo.

## 4. Trazabilidad con semanas anteriores

| Elemento previo | Elemento de esta entrega (Semana 11) |
|---|---|
| User flows por rol (Semana 8, secciones 1-2) | Recorrido real de clic en clic en el prototipo (sección 2 de este documento) |
| Wireframes ASCII 3.1 a 3.5 (Semana 8) | Materializados como páginas HTML reales en `prototipo-navegable/` |
| Reglas de interacción RI-01 a RI-09 (Semanas 8-9) | Implementadas (no solo descritas) en `assets/app.js` y cada página HTML |
| Hallazgo H-03 — paleta sin validar por contraste (Semana 9) | Resuelto con la paleta documentada en `prototipo-navegable/README.md`, sección "Paleta y contraste" |
| Breakpoints y prioridades de pantalla (Semana 10) | Implementados en `assets/styles.css` y verificables redimensionando el navegador |
