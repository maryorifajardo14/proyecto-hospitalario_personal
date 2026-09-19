# ASII-10 — Expediente médico electrónico base
## Semana 10: adaptación responsive/móvil

> **Entrega correspondiente a la Semana 10** del plan ASII (`README.md` raíz, tabla "Plan semanal ASII", fila 10): *"Adaptación responsive/móvil"*, con evidencia *"Escenarios móviles y prioridades de pantalla"*.
>
> Parte de los dos roles definidos desde la Semana 1 (Recepcionista, Médico/Enfermera) y de los wireframes de la [Semana 8](./semana-8-flujo-ux-por-rol.md) para decidir, por rol, en qué dispositivo se usa realmente el módulo y qué debe priorizarse cuando la pantalla es pequeña.

## 1. Escenarios móviles por rol

| Rol | Dispositivo típico | Contexto real de uso | ¿Requiere adaptación móvil? |
|---|---|---|---|
| **Recepcionista** | Escritorio/laptop, en el mostrador de admisión | Uso sentado, sesión larga, formulario de apertura poco frecuente por paciente (una sola vez, RN-10-01). | Baja prioridad: el flujo de apertura puede degradarse a una sola columna en tablet, pero no es el escenario crítico. |
| **Médico** | Tablet o teléfono, moviéndose entre camas/consultorios | Necesita consultar el expediente longitudinal de pie, en pasillo o junto a la cama del paciente, con conectividad Wi-Fi hospitalaria variable. | **Alta prioridad**: es el escenario donde el diseño responsive tiene más impacto real. |
| **Enfermera** | Teléfono o tablet compartida entre el personal de turno | Consulta rápida y frecuente del expediente durante la ronda, sesiones cortas e interrumpidas. | **Alta prioridad**, igual que Médico. |
| **Admin** | Escritorio, uso administrativo esporádico | Acceso ocasional, no es un escenario de movilidad. | Baja prioridad. |

**Conclusión del escenario:** la adaptación móvil de ASII-10 debe optimizarse primero para la **vista de consulta** (`MedicalRecordView.vue`, UC-10-02), porque es la que usan Médico y Enfermera en movimiento; la vista de apertura (`OpenMedicalRecordModal.vue`, UC-10-01) puede seguir un enfoque "mobile-friendly" pero no es el caso de uso móvil primario.

## 2. Breakpoints propuestos

| Breakpoint | Ancho | Dispositivo de referencia | Prioridad de diseño |
|---|---|---|---|
| `sm` | < 640px | Teléfono en vertical (Médico/Enfermera en ronda) | Prioridad 1 — layout de una sola columna |
| `md` | 640px – 1023px | Tablet en vertical/horizontal | Prioridad 2 — layout de una o dos columnas según orientación |
| `lg` | ≥ 1024px | Escritorio (Recepcionista, Admin) | Layout completo, sin restricciones |

## 3. Prioridades de pantalla (qué se muestra primero en `sm`)

### 3.1. Vista de expediente longitudinal (`MedicalRecordView.vue`) — prioridad alta de adaptación

| Orden en `sm` (móvil) | Contenido | Justificación |
|---:|---|---|
| 1 | Nombre del paciente + número de expediente (`record_number`) | Identificación inmediata sin scroll, para confirmar que es el paciente correcto antes de leer más. |
| 2 | Aviso de bitácora (RI-06, Semana 8) | Debe seguir siendo visible aunque el espacio sea reducido — no se oculta ni se colapsa en móvil, por requisito de auditoría (RF-10-06). |
| 3 | Antecedentes / historial longitudinal (colapsable por sección) | Contenido más extenso; se prioriza en acordeones colapsados por defecto para no forzar scroll largo en pantalla pequeña. |
| 4 (al final, no oculto) | Metadatos de apertura ("Abierto por", "Abierto el") | Información de contexto, útil pero no urgente en el primer vistazo desde el pasillo. |

En `lg` (escritorio), los 4 bloques pueden mostrarse simultáneamente en dos columnas, sin necesidad de acordeones.

### 3.2. Ficha del paciente / apertura de expediente — prioridad baja de adaptación

| Orden en `sm` (móvil) | Contenido |
|---:|---|
| 1 | Datos del paciente (nombre, documento) |
| 2 | Estado del expediente (existe / no existe) |
| 3 | Botón de acción único, a todo el ancho (`Abrir expediente` o `Consultar expediente`, nunca ambos visibles a la vez según el rol, RI-01/RI-02) |

El modal de confirmación (3.2 de la Semana 8) se adapta a pantalla completa en `sm` en vez de modal centrado flotante, para evitar controles diminutos difíciles de tocar.

## 4. Reglas de interacción específicas para móvil

Estas reglas se agregan a las ya definidas en la [Semana 8, sección 4](./semana-8-flujo-ux-por-rol.md#4-reglas-de-interacción) y a las de accesibilidad de la [Semana 9](./semana-9-usabilidad-accesibilidad.md#4-reglas-de-interacción-añadidas-a-la-semana-8):

| # | Regla | Justificación |
|---|---|---|
| RI-10 | En `sm`, el botón de acción principal (abrir/consultar) debe tener un área táctil mínima de 44×44px, siguiendo el criterio de tamaño de objetivo táctil de WCAG 2.5.5. | Uso real con una mano mientras se sostiene una tablet o carpeta clínica. |
| RI-11 | El acordeón de antecedentes (sección 3.1) debe abrirse/cerrarse también por teclado (compatibilidad con `RI-08`, Semana 9), no solo por toque. | Consistencia con las reglas de accesibilidad ya definidas. |
| RI-12 | En `sm`, el modal de confirmación de apertura pasa a ocupar toda la pantalla (no un recuadro flotante pequeño), conservando el mismo contenido y las mismas reglas de foco (`RI-08`). | Evitar controles inaccesibles por tamaño reducido en pantallas pequeñas. |
| RI-13 | El aviso de bitácora (RI-06) nunca se retira del layout en `sm`, aunque se reduzca su tamaño de fuente; no se convierte en un ícono sin texto. | Es un requisito de transparencia de auditoría (RF-10-06), no un adorno visual que pueda recortarse. |

## 5. Trazabilidad con semanas anteriores

| Elemento previo | Elemento de esta entrega (Semana 10) |
|---|---|
| Actores Médico/Enfermera/Recepcionista (Semana 1) | Base de los escenarios móviles por rol (sección 1) |
| Wireframes de consulta y apertura (Semana 8) | Reordenados por prioridad de pantalla en `sm` (sección 3) |
| Reglas de interacción de accesibilidad RI-08/RI-09 (Semana 9) | Extendidas con las reglas móviles RI-10 a RI-13 (sección 4) |
| RF-10-06 / RNF-10-04 (bitácora obligatoria e inmutable) | Restringe qué contenido puede ocultarse/colapsarse en móvil (RI-13) |
