# ASII-10 — Prototipo navegable (Semana 11)

Prototipo estático de baja/media fidelidad, equivalente a uno hecho en Figma/Canva/Excalidraw
(opción "equivalente" habilitada por la consigna de la Semana 11), construido en HTML/CSS/JS
plano y versionable en Git, sin backend real ni build tools. Recorre los flujos ya diseñados en
la [Semana 8](../semana-8-flujo-ux-por-rol.md) y aplica las reglas de accesibilidad de la
[Semana 9](../semana-9-usabilidad-accesibilidad.md) y responsive de la
[Semana 10](../semana-10-responsive-movil.md).

## Cómo abrirlo

No requiere instalación ni servidor: abrir [`index.html`](./index.html) directamente en un
navegador (doble clic, o `Abrir con` → navegador).

## Mapa de pantallas

| Pantalla | Corresponde a | Caso del contrato (Semana 5) |
|---|---|---|
| [`index.html`](./index.html) | Selector de rol (simula la sesión) | — |
| [`buscar-paciente.html`](./buscar-paciente.html) | User flow, paso "Buscar paciente" (Semana 8, secciones 1 y 2) | — |
| [`ficha-recepcionista-sin-expediente.html`](./ficha-recepcionista-sin-expediente.html) | Wireframe 3.1 (Semana 8) + modal 3.2 | `POST /medical-records` → 201 |
| [`expediente-creado-exito.html`](./expediente-creado-exito.html) | Resultado de apertura exitosa | 201 |
| [`ficha-recepcionista-existente.html`](./ficha-recepcionista-existente.html) | Caso de expediente duplicado (RI-04) | `POST /medical-records` → 409 |
| [`ficha-medico.html`](./ficha-medico.html) | Wireframe 3.3 (Semana 8) | — |
| [`ficha-medico-sin-expediente.html`](./ficha-medico-sin-expediente.html) | Consulta sin expediente previo | `GET /medical-records/{patient}` → 404 |
| [`expediente-vista.html`](./expediente-vista.html) | Wireframe 3.4 (Semana 8), prioridades de pantalla (Semana 10, sección 3.1) | `GET /medical-records/{patient}` → 200 |
| [`error-403.html`](./error-403.html) | Wireframe 3.5 (Semana 8) | 403 (apertura o consulta) |

## Reglas de interacción implementadas (no solo descritas)

A diferencia de las Semanas 8-10, que documentan las reglas, este prototipo las implementa de
forma mínima para poder probarlas interactivamente:

- **RI-01/RI-02** (rol determina qué botón se muestra): implementado con `data-role` +
  `assets/app.js`, leyendo el rol elegido en `index.html`.
- **RI-03** (apertura siempre con modal de confirmación): `ficha-recepcionista-sin-expediente.html`.
- **RI-08** (modal con foco atrapado, cierre con `Esc`, retorno de foco): implementado en
  `assets/app.js`, función `initConfirmModal()`. Probar con `Tab` / `Shift+Tab` / `Esc` dentro del
  modal de apertura.
- **RI-09** (resultados anunciados por lectores de pantalla, no solo por color): regiones
  `aria-live="polite"` (éxito) y `role="alert"` (error 403) en `expediente-creado-exito.html` y
  `error-403.html`.
- **RI-11** (acordeón accesible por teclado): `expediente-vista.html`, botones con
  `aria-expanded`/`aria-controls`.
- **RI-13** (el aviso de bitácora no se retira en móvil): `expediente-vista.html`, clase
  `.audit-note`, visible en todos los breakpoints de `assets/styles.css`.

## Paleta y contraste (Hallazgo H-03, Semana 9)

Colores definidos en `assets/styles.css` (`:root`), verificados manualmente contra el criterio
WCAG 1.4.3 (contraste mínimo 4.5:1 para texto normal):

| Uso | Fondo | Texto | Contraste aproximado |
|---|---|---|---|
| Botón primario | `#1544ad` | `#ffffff` | ≈ 7.3:1 |
| Alerta de advertencia | `#fef3c7` | `#7a4a00` | ≈ 6.1:1 |
| Alerta de error | `#fee2e2` | `#8f1d1d` | ≈ 7.6:1 |
| Alerta de éxito | `#dcfce7` | `#14532d` | ≈ 7.4:1 |

## Responsive (Semana 10)

`assets/styles.css` define el layout de una sola columna por defecto (breakpoint `sm`, `< 640px`)
y activa `.record-grid` en dos columnas a partir de `640px` (`md`/`lg`). Para verificarlo:
redimensionar la ventana del navegador en `expediente-vista.html`, o abrirlo desde un teléfono.

## Qué no incluye este prototipo

- No llama a la API real (`/api/v1/medical-records`); todos los datos son estáticos, embebidos en
  cada página HTML.
- No implementa autenticación ni el middleware `tenant`/`jwt.auth`: el "rol" es solo un parámetro
  de URL/`sessionStorage` para fines de demostración.
- No reemplaza los componentes Vue propuestos en la [Semana 7](../semana-7-diseno-componentes-refactorizacion.md#1-diagrama-de-componentes-backend--frontend-propuesto);
  es un mockup previo a esa implementación, no el resultado de ella.
