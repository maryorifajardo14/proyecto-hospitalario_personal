# ASII-10 — Expediente médico electrónico base
## Semana 9: evaluación de usabilidad y accesibilidad

> **Entrega correspondiente a la Semana 9** del plan ASII (`README.md` raíz, tabla "Plan semanal ASII", fila 9): *"Evaluación de usabilidad y accesibilidad"*, con evidencia *"Checklist, hallazgos y mejoras propuestas"*.
>
> Como todavía no existe una interfaz implementada para evaluar en ejecución, esta entrega es una **evaluación heurística** (walkthrough cognitivo) sobre los user flows y wireframes diseñados en la [Semana 8](./semana-8-flujo-ux-por-rol.md), usando las 10 heurísticas de usabilidad de Nielsen y los criterios de accesibilidad WCAG 2.1 nivel AA aplicables a una interfaz clínica.

## 1. Checklist de usabilidad (heurísticas de Nielsen)

| # | Heurística | Aplica al flujo diseñado | Resultado |
|---|---|---|---|
| H1 | Visibilidad del estado del sistema | Estados de carga al enviar `POST`/`GET` (RI-07, Semana 8) | ✅ Cubierto |
| H2 | Coincidencia entre el sistema y el mundo real | Uso de "Abrir expediente" / "Consultar expediente" en vez de nombres técnicos como "crear registro" | ✅ Cubierto |
| H3 | Control y libertad del usuario | Modal de confirmación con "Cancelar" antes de abrir expediente (RI-03) | ✅ Cubierto |
| H4 | Consistencia y estándares | Mismo patrón de tarjeta/modal para éxito, 409, 403 y 404 en ambos flujos | ✅ Cubierto |
| H5 | Prevención de errores | Ocultar el botón según rol (RI-01/RI-02) **y** validar también en backend | ✅ Cubierto (defensa en profundidad) |
| H6 | Reconocimiento antes que recuerdo | El wireframe 3.4 muestra quién y cuándo se abrió el expediente sin que el usuario deba recordarlo o buscarlo aparte | ✅ Cubierto |
| H7 | Flexibilidad y eficiencia de uso | No hay atajos de teclado ni acciones masivas diseñadas todavía | ⚠️ Hallazgo H-01 |
| H8 | Diseño estético y minimalista | Wireframes solo muestran la información mínima necesaria por pantalla | ✅ Cubierto |
| H9 | Ayudar a reconocer, diagnosticar y recuperarse de errores | 409 ofrece enlace directo al expediente existente; 403/404 explican la causa en lenguaje simple | ✅ Cubierto |
| H10 | Ayuda y documentación | No hay ayuda contextual (tooltip/enlace) sobre qué significa "expediente longitudinal" para un usuario nuevo | ⚠️ Hallazgo H-02 |

## 2. Checklist de accesibilidad (WCAG 2.1 AA aplicable)

| Criterio WCAG | Descripción aplicada al diseño | Resultado |
|---|---|---|
| 1.4.3 Contraste mínimo | El wireframe 3.5 (error 403) usa un ícono de candado + texto; se debe validar que el texto sobre el color de advertencia cumpla 4.5:1 al definirse la paleta en la Semana 11 | ⚠️ Hallazgo H-03 |
| 1.4.1 Uso del color | Los estados 409/403/404 se distinguen por texto e ícono, no solo por color (ej. no depender solo de "rojo = error") | ✅ Cubierto por diseño |
| 2.1.1 Teclado | El modal de confirmación (3.2) debe poder cerrarse con `Esc` y navegarse con `Tab`/`Shift+Tab` sin atrapar el foco fuera de él | ⚠️ Hallazgo H-04 (pendiente de definir explícitamente) |
| 2.4.3 Orden del foco | Al abrir el modal de confirmación, el foco debe moverse al primer elemento interactivo del modal (no quedarse en el botón que lo abrió) | ⚠️ Hallazgo H-04 |
| 3.3.1 Identificación de errores | Los mensajes 403/404/409 identifican el error en texto plano, no solo con un código HTTP | ✅ Cubierto |
| 4.1.3 Mensajes de estado | Los toasts de éxito/error (ej. "Expediente creado") deben anunciarse a lectores de pantalla mediante una región `aria-live="polite"`, no solo aparecer visualmente | ⚠️ Hallazgo H-05 |
| 1.3.1 Información y relaciones | La tabla de historial longitudinal (3.4) debe usar encabezados de tabla semánticos (`<th>`) cuando se implemente, no solo `<div>` con estilos de tabla | ⚠️ Hallazgo H-06 |

## 3. Hallazgos y mejoras propuestas

| Código | Hallazgo | Severidad | Mejora propuesta |
|---|---|---|---|
| H-01 | No hay atajo para que un médico busque y abra el expediente de un paciente que acaba de consultar en la misma sesión (debe rehacer la búsqueda cada vez). | Baja | Agregar una sección "Consultados recientemente" en `PatientSearchPage.vue`, limitada a la sesión (sin persistir datos clínicos en `localStorage`, por RNF-10-03). |
| H-02 | No existe ayuda contextual para un usuario nuevo (ej. un médico recién contratado) sobre qué es el "expediente longitudinal" o por qué no puede abrirlo él mismo. | Baja | Agregar un ícono de información (`?`) junto al título de la vista de expediente, con un texto breve explicando el alcance del módulo (basado en la sección 2 de la [Semana 1](./semana-1-casos-de-uso.md#2-límite-del-módulo)). |
| H-03 | La paleta de color para los estados de advertencia/error aún no está definida ni validada por contraste. | Media | Definir la paleta en la Semana 11 (mockup) validando con una herramienta de contraste (ej. WebAIM Contrast Checker) que el texto sobre fondo de advertencia/error cumpla ≥ 4.5:1. |
| H-04 | El manejo de foco del teclado en el modal de confirmación no está especificado. | Alta | Agregar como regla de interacción explícita (ver sección 4) antes de implementar el modal: foco inicial en el modal al abrir, trampa de foco (`focus trap`) mientras está abierto, retorno del foco al botón que lo abrió al cerrarlo, y cierre con `Esc`. |
| H-05 | Los mensajes de éxito/error no tienen especificado un mecanismo de anuncio para lectores de pantalla. | Alta | Los toasts deben renderizarse dentro de un contenedor `aria-live="polite"` (o `role="alert"` para errores 403/409), para que un usuario con lector de pantalla se entere del resultado sin depender solo de la señal visual. |
| H-06 | El wireframe de la vista de expediente no especifica marcado semántico. | Media | Al implementar `MedicalRecordView.vue`, usar elementos semánticos (`<table>`/`<th>` o `<dl>` para metadatos clave-valor como "Abierto por"/"Abierto el"), y no solo `<div>` con clases de utilidad visual. |

## 4. Reglas de interacción añadidas a la Semana 8

Estas reglas se agregan como consecuencia directa de los hallazgos H-04 y H-05, y deben leerse junto con la tabla de la [Semana 8, sección 4](./semana-8-flujo-ux-por-rol.md#4-reglas-de-interacción):

| # | Regla | Origen |
|---|---|---|
| RI-08 | El modal de confirmación de apertura debe atrapar el foco de teclado mientras está abierto, iniciar el foco en su primer elemento interactivo, cerrarse con `Esc` y devolver el foco al elemento que lo invocó. | H-04 |
| RI-09 | Todo mensaje de resultado (éxito, 403, 404, 409) debe anunciarse mediante una región `aria-live` o `role="alert"`, además de su representación visual. | H-05 |

## 5. Trazabilidad con semanas anteriores

| Elemento previo | Elemento de esta entrega (Semana 9) |
|---|---|
| User flows y wireframes (Semana 8) | Objeto evaluado por el checklist de las secciones 1 y 2 |
| RNF-10-06 (mensajes claros sin filtrar detalles internos), Semana 2 | Confirmado como cubierto en la heurística H9 y el criterio WCAG 3.3.1 |
| RNF-10-03 (no exponer datos sensibles), Semana 2 | Restringe la mejora propuesta en H-01 (no persistir historial clínico en `localStorage`) |
| Reglas de interacción RI-01 a RI-07 (Semana 8) | Ampliadas con RI-08 y RI-09 en esta entrega |
