# ASII-10 — Especificación

## Actividad integradora de arquitectura federada y persistencia

> Esta especificación corresponde a la **Actividad integradora de arquitectura federada y persistencia**
> (PostgreSQL, CENTRAL/HOSPITAL, pgvector y continuidad del proyecto final), evaluada de forma independiente
> del plan semanal ASII. Parte y es congruente con el trabajo ya entregado en
> [`docs/module-10/semana-1-casos-de-uso.md`](../../module-10/semana-1-casos-de-uso.md) (actores, casos de
> uso, reglas de dominio RN-10-01 a RN-10-03) y
> [`docs/module-10/semana-2-rf-rnf-solid.md`](../../module-10/semana-2-rf-rnf-solid.md) (RF/RNF, criterios de
> aceptación, SOLID). No repite ese contenido: lo referencia y lo concreta en un contrato y una entrega
> vertical funcional.

- **Módulo:** ASII-10 — Expediente médico electrónico base
- **Estudiante:** Maryori Rachael Fajardo Paredes (`maryorifajardo14`)
- **Rama:** `feature/asii-10-expediente-medico-electronico-base-maryorifajardo14`

## 1. Problema

Dentro del Sistema Hospitalario Integrado (SHI), cada paciente necesita un **contenedor longitudinal único**
donde se acumule su historial clínico a lo largo del tiempo. Sin este contenedor, los módulos que sí producen
contenido clínico (notas SOAP, signos vitales, alergias, prescripciones, órdenes de laboratorio) no tienen
dónde anclarse, y no existe un punto único de autorización para decidir quién puede ver el historial completo
de un paciente. ASII-10 resuelve exactamente esa base: **abrir** el expediente una sola vez por paciente y
**permitir su consulta autorizada**.

## 2. Actores

Igual que en la Semana 1 (ver tabla completa allí): **Recepcionista** (abre el expediente), **Médico**
(consulta el expediente; en esta entrega se extiende también a **Enfermera** y **Admin** como roles
autorizados de solo lectura, ver sección 5) y el **Sistema de Seguridad**, que en el código de esta entrega
se materializa como `MedicalRecordAuthorizationChecker`.

## 3. Historias de usuario

**HU-10-01 — Apertura de expediente**
> Como Recepcionista, quiero abrir el expediente longitudinal de un paciente ya registrado, para que el
> historial clínico tenga dónde acumularse desde el primer contacto del paciente con el hospital.

**HU-10-02 — Consulta longitudinal autorizada**
> Como Médico (o rol clínico autorizado), quiero consultar el expediente longitudinal de un paciente, para
> tomar decisiones clínicas informadas, sabiendo que el sistema registra y valida mi autorización antes de
> mostrarme cualquier dato.

## 4. Alcance

### Incluido en esta entrega vertical

- Apertura del expediente longitudinal (`OpenMedicalRecordAction`), con generación de `record_number`
  correlativo por tenant (`EXP-00001`) y registro del autor de la apertura (`opened_by`).
- Rechazo controlado de apertura duplicada (RN-10-01): si el paciente ya tiene expediente, no se crea uno
  nuevo; se informa el expediente existente.
- Consulta longitudinal autorizada (`ViewMedicalRecordAction`), con verificación de rol antes de leer datos.
- Verificación de autorización por rol (`MedicalRecordAuthorizationChecker`), aislada como regla de dominio
  pura, sin dependencia de HTTP ni Eloquent.
- Registro de bitácora local de cada intento —éxito o denegado— (`medical_record_access_logs`), como
  antecedente HOSPITAL para la futura integración con ASII-22 (gobernanza y auditoría transversal).
- Aislamiento por `tenant_id` en cada operación (ningún expediente ni bitácora se lee o escribe fuera del
  tenant del usuario autenticado).

### Fuera de alcance (igual que Semana 1, sin cambios)

- Gestión de usuarios/roles/tenants (ASII-01/02), registro y búsqueda de pacientes (ASII-03).
- Contenido clínico especializado: notas SOAP/diagnósticos (ASII-11), alergias (ASII-12), signos vitales
  (ASII-13), prescripciones (ASII-15), laboratorio (ASII-16/17/18/19/20).
- Edición o eliminación del expediente base; el expediente, una vez abierto, no se puede borrar ni duplicar.
- Sincronización con CENTRAL: este módulo es 100% HOSPITAL (ver ADR-001, sección "Propiedad de datos");
  no existe ninguna entidad ni flujo de este módulo que dependa de la base CENTRAL.

## 5. Reglas de negocio (regla central obligatoria de la asignación + RN de Semana 1)

| Código | Regla | Dónde se aplica en el código |
|---|---|---|
| RN-10-01 | Un paciente solo puede tener un (1) expediente longitudinal. | `OpenMedicalRecordAction` + índice único `patients.id` en `medical_records.patient_id` (ya existente) |
| RN-10-02 | Toda apertura conserva autor (`opened_by`) y fecha (`opened_at`); toda consulta se registra en bitácora con usuario, fecha y resultado. | `OpenMedicalRecordAction`, `ViewMedicalRecordAction`, `EloquentMedicalRecordAuditLogger` |
| RN-10-03 | Ningún usuario sin rol autorizado puede abrir ni consultar el expediente. | `MedicalRecordAuthorizationChecker` |
| RN-10-04 | Ninguna operación cruza el límite del tenant del usuario autenticado. | Middleware `tenant` + `jwt.auth` (ya existentes) + filtro `tenant_id` en el Repository |

Roles autorizados (guard `api`, ya definidos en `RoleSeeder`):

| Operación | Roles permitidos |
|---|---|
| Abrir expediente (`OpenMedicalRecordAction`) | `Recepcionista`, `Admin` |
| Consultar expediente (`ViewMedicalRecordAction`) | `Médico`, `Enfermera`, `Admin` |

## 6. Criterios de aceptación (Given/When/Then)

| # | Dado | Cuando | Entonces |
|---|---|---|---|
| CA-10-01 | El paciente existe en el tenant y no tiene expediente previo, y quien solicita tiene rol `Recepcionista`. | Se invoca `POST /api/v1/medical-records`. | Se crea el expediente con `record_number` correlativo, `opened_at` y `opened_by`; respuesta 201. |
| CA-10-02 | El paciente ya tiene expediente. | Se invoca `POST /api/v1/medical-records` para el mismo paciente. | No se crea un segundo registro; respuesta 409 con el expediente existente referenciado. |
| CA-10-03 | El usuario tiene rol `Médico` y el paciente tiene expediente. | Se invoca `GET /api/v1/medical-records/{patient}`. | Respuesta 200 con los datos del expediente; se registra `medical_record_access_logs` con resultado `success`. |
| CA-10-04 | El usuario autenticado no tiene ninguno de los roles autorizados. | Se invoca `GET /api/v1/medical-records/{patient}` o `POST /api/v1/medical-records`. | Respuesta 403, no se expone ningún dato clínico, se registra el intento con resultado `denied`. |
| CA-10-05 | El paciente indicado no existe en el tenant del usuario. | Se invoca cualquiera de los dos endpoints. | Respuesta 404 sin filtrar detalles internos (RNF-10-06 de Semana 2). |

## 7. Contrato de la API (entrega vertical)

Prefijo real de la aplicación: `/api/v1` (ver `bootstrap/app.php`). Todas las rutas exigen los middlewares ya
existentes `tenant` (cabecera `X-Tenant-ID`) y `jwt.auth` (Bearer JWT).

### `POST /api/v1/medical-records`

Rol requerido: `Recepcionista` o `Admin`.

Body:
```json
{ "patient_id": 42 }
```

Respuestas:

| Código | Caso | Cuerpo (resumen) |
|---|---|---|
| 201 | Expediente creado | `{ "data": { "id", "record_number", "patient_id", "opened_at", "opened_by" } }` |
| 404 | Paciente no existe en el tenant | `{ "message": "Paciente no encontrado." }` |
| 409 | El paciente ya tiene expediente | `{ "message": "El paciente ya tiene un expediente.", "data": { ...expediente existente } }` |
| 403 | Rol no autorizado | `{ "message": "No tiene autorización para abrir expedientes." }` |
| 422 | `patient_id` ausente o inválido | Errores de validación estándar de Laravel |

### `GET /api/v1/medical-records/{patient}`

Rol requerido: `Médico`, `Enfermera` o `Admin`. `{patient}` es el `id` del paciente (no el id del expediente).

Respuestas:

| Código | Caso | Cuerpo (resumen) |
|---|---|---|
| 200 | Expediente encontrado y usuario autorizado | `{ "data": { "id", "record_number", "patient_id", "opened_at", "opened_by", "background", ... } }` |
| 403 | Rol no autorizado | `{ "message": "No tiene autorización para consultar el expediente." }` |
| 404 | Paciente sin expediente, o paciente inexistente en el tenant | `{ "message": "Expediente no encontrado." }` |

## 8. Trazabilidad con semanas anteriores

| Elemento previo | Elemento de esta entrega |
|---|---|
| UC-10-01 (Semana 1) | `POST /api/v1/medical-records` → `OpenMedicalRecordAction` |
| UC-10-02 (Semana 1) | `GET /api/v1/medical-records/{patient}` → `ViewMedicalRecordAction` |
| UC-10-03 (Semana 1) | `MedicalRecordAuthorizationChecker` |
| RF-10-01..07 (Semana 2) | Ver tabla de reglas (sección 5) y contrato (sección 7) |
| SRP (Semana 2, sección 4) | Ver [`ADR-001-arquitectura.md`](./ADR-001-arquitectura.md), sección de capas |
