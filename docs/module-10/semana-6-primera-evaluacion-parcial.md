# ASII-10 — Expediente médico electrónico base
## Semana 6: primera evaluación parcial — defensa teórica y caso práctico arquitectónico

> **Entrega correspondiente a la Semana 6** del plan ASII (`README.md` raíz del proyecto de equipo, tabla "Plan semanal ASII", fila 6): *"Primera evaluación parcial"*, con evidencia *"Defensa teórica y caso práctico arquitectónico"*.
>
> A diferencia de las semanas 1 a 5, esta entrega no agrega código ni diagramas nuevos: es una guía de estudio que consolida todo lo entregado sobre el módulo ASII-10 (dominio, RF/RNF, arquitectura, capas, contrato de API) para presentar la defensa teórica y resolver un caso práctico arquitectónico en vivo. Es apoyo de estudio, no un guion para leer.

## 1. Mapa de lo entregado hasta ahora

| Semana | Qué cubre | Documento |
|---:|---|---|
| 1 | Actores, casos de uso, modelado de dominio | [`semana-1-casos-de-uso.md`](./semana-1-casos-de-uso.md) |
| 2 | RF/RNF, criterios de aceptación, principio SOLID | [`semana-2-rf-rnf-solid.md`](./semana-2-rf-rnf-solid.md) |
| 3 | Vista arquitectónica y dependencias con el HIS | [`semana-3-arquitectura.md`](./semana-3-arquitectura.md) |
| 4 | Diseño por capas y responsabilidades | [`semana-4-diseno-por-capas.md`](./semana-4-diseno-por-capas.md) |
| 5 | Contrato de API y plan de integración (rama/worktree/PR) | [`semana-5-contrato-api-plan-integracion.md`](./semana-5-contrato-api-plan-integracion.md) |
| — | ADR, especificación, diagramas y evidencia de la actividad integradora | [`docs/modulos/mod10/`](../modulos/mod10/) |

## 2. Resumen de una frase

"Un médico o enfermera solo puede ver el expediente longitudinal de un paciente de su propio tenant si su sesión es válida y su rol está autorizado; una recepcionista abre ese expediente una única vez por paciente; y **toda** solicitud —se conceda o se deniegue— queda registrada en una bitácora, sin que el sistema filtre información al usuario no autorizado sobre si el paciente existe o no."

## 3. Preguntas probables de la defensa teórica

**¿Por qué el módulo se dividió en capas Domain / Application / Infrastructure / Presentation, y no todo junto en el controlador?**
Porque cada capa tiene una única razón para cambiar (SRP, ver [`semana-2-rf-rnf-solid.md`](./semana-2-rf-rnf-solid.md)): las reglas de negocio (dominio) no deben cambiar si cambia el motor de base de datos (infraestructura), ni si cambia el formato de la respuesta HTTP (presentación). La prueba concreta de que la separación funciona es que las Actions de aplicación se probaron con `InMemoryMedicalRecordRepository` sin tocar Eloquent ni PostgreSQL (ver [`semana-4-diseno-por-capas.md`, sección 2](./semana-4-diseno-por-capas.md#2-objetos-reutilizables-puertos-e-infraestructura-intercambiable)).

**¿Por qué la capa de aplicación depende de interfaces (`MedicalRecordRepository`, `PatientExistenceChecker`, `MedicalRecordAuditLogger`) y no directamente de las clases Eloquent?**
Es el patrón Repository combinado con inversión de dependencias: el dominio define el contrato, la infraestructura lo implementa. Permite tener dos implementaciones intercambiables del mismo contrato (`Eloquent*` para producción, `InMemory*` para pruebas) sin duplicar la lógica de la Action.

**¿Por qué `MedicalRecordAuthorizationChecker` es una clase de dominio y no simplemente un `if` dentro del controlador?**
Porque la regla "quién puede abrir/consultar el expediente" (RN-10-03) es una regla de negocio, no un detalle de HTTP: debe poder probarse de forma aislada (`MedicalRecordAuthorizationCheckerTest`) y reutilizarse igual si mañana el mismo caso de uso se expone por otro canal (CLI, cola de eventos, etc.), no solo por la API REST actual.

**¿Por qué la apertura duplicada de un expediente responde 409 y no 422 o 500?**
409 (Conflict) es semánticamente correcto porque el problema no es un error de validación del payload (422) ni una falla del servidor (500): es un conflicto de estado — el recurso que se intenta crear ya existe. Además, la respuesta 409 devuelve el expediente existente, para que el cliente pueda usarlo sin tener que volver a consultarlo.

**¿Por qué la bitácora de auditoría registra tanto los accesos exitosos como los denegados?**
Porque la trazabilidad de seguridad no depende de si el acceso fue exitoso: un intento denegado (alguien sin rol autorizado intentando ver un expediente) es información de seguridad tan valiosa —o más— que uno exitoso. Se aplicó en las cuatro salidas posibles de las dos Actions (201/409 en apertura, 200/403 en consulta).

**¿Por qué el aislamiento por `tenant_id` se aplica en el repositorio y no solo en el middleware?**
El middleware `tenant` valida que la petición traiga una cabecera `X-Tenant-ID` válida, pero eso no basta: si el repositorio no filtrara también por `tenant_id` al buscar el expediente o el paciente, un usuario autenticado en un tenant podría, por error de programación en otra capa, terminar leyendo datos de otro tenant. Es defensa en profundidad, no una validación redundante.

**¿Por qué este módulo no depende ni sincroniza con la base CENTRAL?**
Porque ASII-10 es 100% HOSPITAL (ver `ADR-001-arquitectura.md`, sección de propiedad de datos): el expediente longitudinal es un dato operativo propio de cada sede/hospital, no un dato maestro que deba compartirse entre sedes. Solo produce eventos de auditoría que, en una fase futura, alimentarán ASII-22.

**¿Qué pasa si dos peticiones de apertura para el mismo paciente llegan al mismo tiempo?**
Es el riesgo de concurrencia ya declarado en el ADR: se mitiga con `lockForUpdate` al generar el `record_number` correlativo, pero es una limitación reconocida, no un caso 100% resuelto bajo cualquier carga (ver `ADR-001-arquitectura.md`, sección 7, y `semana-5-contrato-api-plan-integracion.md`, sección 5).

## 4. Caso práctico arquitectónico (para practicar antes de la defensa)

Es probable que se pida modificar o extender **un elemento concreto** de la arquitectura en vivo. Ejercicios razonables para practicar, con el razonamiento esperado en cada uno:

1. **Agregar un tercer endpoint: `PATCH /api/v1/medical-records/{patient}/background`** (actualizar antecedentes del paciente).
   - ¿Qué capa se toca primero? Dominio: ¿es una operación nueva o cabe dentro de `MedicalRecordRecord` existente? ¿Necesita una regla de autorización distinta (¿puede editar Enfermera o solo Médico?).
   - Aplicación: nueva Action (`UpdateMedicalRecordBackgroundAction`) o extender `ViewMedicalRecordAction`? (Respuesta esperada: nueva Action — una Action por caso de uso, no mezclar lectura con escritura).
   - Infraestructura: ¿el `MedicalRecordRepository` ya tiene un método de actualización o hay que agregarlo a la interfaz y a ambas implementaciones (`Eloquent*` e `InMemory*`)?
   - Presentación: nueva ruta, nuevo Request de validación, ¿reutiliza `MedicalRecordResource`?
   - Trazabilidad: qué documento de qué semana habría que actualizar (contrato de la Semana 5, tabla de capas de la Semana 4).

2. **El expediente necesita bloquearse mientras hay una investigación en curso** (nueva excepción de negocio).
   - Dominio: nueva excepción (`MedicalRecordLockedException`) y una nueva regla en `MedicalRecordAuthorizationChecker` o en la propia entidad.
   - Contrato de API: nuevo código de respuesta (¿`423 Locked`?) que hay que agregar a las tablas de la Semana 5.
   - Auditoría: ¿este intento bloqueado se registra igual que un acceso denegado, o como un evento distinto?

3. **Un segundo hospital (tenant) reporta que ve expedientes de otro tenant.**
   - Diagnóstico esperado: revisar primero si el filtro por `tenant_id` falta en el repositorio (no solo confiar en el middleware) — es el mismo razonamiento de la pregunta de defensa de la sección 3 sobre defensa en profundidad.
   - Dónde se prueba: qué test de `tests/Feature/MedicalRecord/MedicalRecordApiTest.php` debería existir para detectar esto (y si no existe, es un hueco de cobertura a declarar).

Para cada ejercicio, lo que se evalúa no es "la respuesta correcta" sino identificar **en qué capa entra el cambio primero** y **qué otros documentos/pruebas hay que actualizar en cascada** para no romper la trazabilidad exigida desde la Semana 1.

## 5. Puntos de trazabilidad a tener frescos

- Las 4 reglas de negocio RN-10-01 a RN-10-04 (sección 5 de `ESPECIFICACION.md`) y en qué clase o middleware se aplica cada una.
- Los 4 resultados posibles de cada Action (201/404/409/403/422 en apertura; 200/403/404 en consulta) y su correspondencia con los criterios de aceptación CA-10-01 a CA-10-05.
- Las dos implementaciones intercambiables de cada puerto del dominio (`Eloquent*` vs `InMemory*`/fakes) y en qué prueba se usa cada una.
- La rama, el worktree y el estado del PR (Semana 5): qué falta antes de que deje de ser borrador.
- La limitación de validación real declarada en `EVIDENCIA.md` (no se ejecutaron `migrate`/`test` en el entorno donde se preparó la entrega) — es importante poder explicar *qué* falta validar y *por qué*, no ocultarlo.

## 6. Qué no decir en la defensa

- No decir que "la IA generó todo": el análisis del proceso, las reglas de negocio y las decisiones de arquitectura son propias; el apoyo de la herramienta se limitó a redacción, formato e implementación a partir de decisiones ya tomadas (ver [`DECLARACION_IA.md`](../../DECLARACION_IA.md)).
- No presentar la validación de ejecución (migraciones, pruebas) como si ya se hubiera corrido realmente — la limitación está declarada explícitamente y debe explicarse así si se pregunta.
- No leer literalmente este documento como respuesta — se espera una explicación con palabras propias, usando el vocabulario ya fijado en las semanas anteriores (Action, puerto, Repository, tenant, bitácora).
