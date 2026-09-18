# ASII-10 — Expediente médico electrónico base
## Semana 5: contrato API preliminar y plan de integración

> **Entrega correspondiente a la Semana 5** del plan ASII (`README.md` raíz del proyecto de equipo, tabla "Plan semanal ASII", fila 5): *"Contrato API preliminar y plan de integración"*, con evidencia *"Endpoints, payloads, errores, permisos, rama, worktree y PR"*.
>
> El contrato de la API ya se había concretado en la "Actividad integradora de arquitectura federada y persistencia" (ver [`ESPECIFICACION.md`, sección 7](../modulos/mod10/ESPECIFICACION.md#7-contrato-de-la-api-entrega-vertical)), evaluada de forma independiente del plan semanal. Esta entrega lo consolida como el contrato formal de la Semana 5 y añade lo que faltaba: el **plan de integración** de la rama de este módulo hacia el repositorio de equipo.

## 1. Contrato de la API (consolidado)

Prefijo real de la aplicación: `/api/v1`. Todas las rutas exigen los middlewares `tenant` (cabecera `X-Tenant-ID`) y `jwt.auth` (Bearer JWT), ya documentados en la [Semana 3, sección 1](./semana-3-arquitectura.md#1-vista-de-componentes-de-alto-nivel).

### `POST /api/v1/medical-records` — abrir expediente

| | |
|---|---|
| Rol requerido | `Recepcionista`, `Admin` |
| Payload | `{ "patient_id": 42 }` |
| Acción de aplicación | `OpenMedicalRecordAction` |

| Código | Caso | Cuerpo (resumen) |
|---|---|---|
| 201 | Expediente creado | `{ "data": { "id", "record_number", "patient_id", "opened_at", "opened_by" } }` |
| 404 | Paciente no existe en el tenant | `{ "message": "Paciente no encontrado." }` |
| 409 | El paciente ya tiene expediente (RN-10-01) | `{ "message": "El paciente ya tiene un expediente.", "data": { ...expediente existente } }` |
| 403 | Rol no autorizado | `{ "message": "No tiene autorización para abrir expedientes." }` |
| 422 | `patient_id` ausente o inválido | Errores de validación estándar de Laravel |

### `GET /api/v1/medical-records/{patient}` — consultar expediente

| | |
|---|---|
| Rol requerido | `Médico`, `Enfermera`, `Admin` |
| Parámetro | `{patient}` = id del paciente (no el id del expediente) |
| Acción de aplicación | `ViewMedicalRecordAction` |

| Código | Caso | Cuerpo (resumen) |
|---|---|---|
| 200 | Expediente encontrado y usuario autorizado | `{ "data": { "id", "record_number", "patient_id", "opened_at", "opened_by", "background", ... } }` |
| 403 | Rol no autorizado | `{ "message": "No tiene autorización para consultar el expediente." }` |
| 404 | Paciente sin expediente, o paciente inexistente en el tenant | `{ "message": "Expediente no encontrado." }` |

Se etiqueta como "preliminar" porque el destino final del PR (ver sección 3) puede pedir ajustes menores de nombres o payload al integrarse con el resto del equipo; las reglas de negocio y los códigos de estado ya están fijados y probados (ver [`semana-4-diseno-por-capas.md`, sección 5](./semana-4-diseno-por-capas.md#5-pruebas-por-capa)).

## 2. Rama de trabajo

Todo el desarrollo del módulo se hizo sobre una única rama de feature, nunca directamente sobre `main`/`develop`:

```
feature/asii-10-expediente-medico-electronico-base-maryorifajardo14
```

- Nombre alineado con la convención `feature/<módulo>-<autora>` del proyecto de equipo.
- Los commits de esta rama siguen el orden por capa (`docs → dominio → aplicación → infraestructura → presentación → pruebas`), documentado en [`EVIDENCIA.md`, sección 4](../modulos/mod10/EVIDENCIA.md#4-historial-de-commits-de-esta-actividad).
- No se tocó ninguna migración ni archivo de otro módulo salvo el ajuste aditivo y documentado sobre `medical_records` (ver [`ADR-001-arquitectura.md`](../modulos/mod10/ADR-001-arquitectura.md)).

## 3. Worktree: por qué y cómo se usó

Para no perder el estado de otras tareas que tenía en curso sobre el clon principal del repositorio de equipo (rama `main`), el trabajo de este módulo se hizo en un **worktree separado**, apuntando a la misma rama de feature:

```
git worktree add ../shi-asii-10-expediente feature/asii-10-expediente-medico-electronico-base-maryorifajardo14
```

`git worktree list` muestra ambos directorios compartiendo el mismo repositorio `.git`, cada uno con su propia rama activa:

```
.../sistema-hospitalario-integrado-SistenasII-2026   [main]
.../shi-asii-10-expediente                            [feature/asii-10-expediente-medico-electronico-base-maryorifajardo14]
```

Ventaja concreta para este módulo: permitió tener el código de `main` intacto y navegable en una carpeta, mientras se compilaba/redactaba el módulo en la otra, sin `git stash` ni riesgo de mezclar cambios de dos tareas distintas en el mismo directorio de trabajo.

## 4. Plan de Pull Request

El borrador completo ya existe en [`PR_BODY.md`](../modulos/mod10/PR_BODY.md); el plan para abrirlo formalmente es:

1. **Antes de abrir el PR:** ejecutar la validación mínima pendiente (`php artisan migrate:fresh --seed`, `php artisan test`) y pegar la salida real en `EVIDENCIA.md`, sección 2 — declarado como pendiente explícito, no simulado (ver [`EVIDENCIA.md`, sección 1](../modulos/mod10/EVIDENCIA.md#1-aviso-importante-sobre-esta-evidencia)).
2. **Destino:** confirmar con el equipo si es `develop` (convención general del README raíz del proyecto de equipo) o la rama que indique el docente para la actividad integradora — se deja explícito como pendiente en el propio borrador del PR.
3. **Modo:** Pull Request en modo *draft* mientras la validación de la sección 1 siga pendiente; se promueve a "ready for review" solo después de pegar evidencia real de ejecución.
4. **Checklist de apertura** (ya completado en `PR_BODY.md`): rama de módulo correcta, sin mezclar módulos ajenos, RF/RNF y criterios de aceptación incluidos, diagramas UML incluidos, contrato de API documentado, roles/tenant/datos sensibles revisados, riesgos e integración con otros módulos anotados.
5. **Después de mergeado:** eliminar el worktree (`git worktree remove ../shi-asii-10-expediente`) una vez la rama ya no se necesite en paralelo.

## 5. Riesgos de integración con otros módulos

| Riesgo | Módulo/área afectada | Mitigación |
|---|---|---|
| Colisión de `record_number` bajo alta concurrencia entre pacientes del mismo tenant | Este módulo (ASII-10) | `lockForUpdate` al generar el correlativo (ver `ADR-001-arquitectura.md`, sección 7) |
| Los módulos clínicos (SOAP, alergias, signos vitales, prescripciones, laboratorio) referencian `medical_record_id` / `patient_id` | ASII-11, 12, 13, 15, 16 | Contrato de FKs ya fijado y documentado en [`semana-3-arquitectura.md`, sección 3](./semana-3-arquitectura.md#3-dependencias-con-el-resto-del-his); no requiere cambios de este módulo al integrarse |
| La bitácora local (`medical_record_access_logs`) todavía no alimenta la auditoría transversal real | ASII-22 (gobernanza y auditoría) | Documentado como trabajo futuro (outbox/`event_id`) en `PR_BODY.md`, sección "Riesgos y pendientes"; no bloquea esta entrega porque ASII-10 es 100% HOSPITAL |
| Ejecución real de migraciones/pruebas pendiente en un entorno con PHP/Composer/PostgreSQL | Esta rama antes de merge | Declarado explícitamente en `EVIDENCIA.md`; es el primer paso del plan de PR (sección 4, punto 1) |

## 6. Trazabilidad con semanas anteriores

| Elemento previo | Elemento de esta entrega (Semana 5) |
|---|---|
| Contrato de la API (`ESPECIFICACION.md`, sección 7) | Consolidado en la sección 1 de este documento |
| Capas de aplicación/infraestructura/presentación (Semana 4) | Lo que expone el contrato de la sección 1 |
| RN-10-01 (un solo expediente por paciente) | Código 409 del contrato |
| RN-10-03/04 (autorización y aislamiento por tenant) | Códigos 403/404 del contrato |
| Componentes downstream del diagrama de Semana 3 | Riesgos de integración (sección 5) |
