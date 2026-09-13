# ADR-001 — Arquitectura del módulo ASII-10 (Expediente médico electrónico base)

- **Estado:** Aceptado
- **Fecha:** 2026-08-21
- **Módulo:** ASII-10 — Expediente médico electrónico base
- **Relacionado con:** [`ESPECIFICACION.md`](./ESPECIFICACION.md), `docs/module-10/semana-3-arquitectura.md`

## 1. Contexto

La actividad integradora exige que cada módulo declare explícitamente: quién es dueño del dato (CENTRAL u
HOSPITAL), cómo se referencia entre bases, cómo se estructura en capas (Domain/Application/Infrastructure/
Presentation) usando el patrón Repository, qué motor de persistencia usa y qué pasa ante una desconexión de
la red federada.

## 2. Decisión — Propiedad de datos (CENTRAL/HOSPITAL)

**Todo lo que gestiona ASII-10 es propiedad exclusiva de HOSPITAL.** No existe ninguna entidad CENTRAL en
este módulo:

| Entidad | Propietario | Referencia a otra base |
|---|---|---|
| `medical_records` | HOSPITAL | `patient_id` → `patients` (HOSPITAL, misma base, FK real permitida porque ambas tablas viven en la misma base HOSPITAL) |
| `medical_records.opened_by` | HOSPITAL | `users.id` (HOSPITAL, misma base) |
| `medical_record_access_logs` (nueva) | HOSPITAL | `patient_id`, `medical_record_id`, `user_id` — todas en la misma base HOSPITAL |

Ninguna fila de este módulo se replica a CENTRAL. Si en una fase posterior ASII-22 (gobernanza) necesita
correlacionar accesos entre hospitales, la integración se hará por `event_id`/outbox desde
`medical_record_access_logs` hacia el `cross_access_audit` de CENTRAL — **no** existe ese flujo en este MVP y
se documenta como trabajo futuro, no como código actual.

Consecuencia práctica: **este módulo no tiene comportamiento especial ante desconexión de CENTRAL**, porque
nunca la consulta. Toda la apertura y consulta del expediente ocurre y se resuelve enteramente con
transacciones locales a la base HOSPITAL del tenant.

## 3. Decisión — Patrón Repository

Se define un puerto `MedicalRecordRepository` (interfaz en `app/Domain/MedicalRecord/`) con el lenguaje del
caso de uso, no CRUD genérico:

```php
interface MedicalRecordRepository
{
    public function existsForPatient(string $tenantId, int $patientId): bool;
    public function findByPatientId(string $tenantId, int $patientId): ?MedicalRecordRecord;
    public function nextRecordNumber(string $tenantId): string;
    public function create(MedicalRecordRecord $record): MedicalRecordRecord;
}
```

Adaptadores:

- `EloquentMedicalRecordRepository` (`app/Infrastructure/MedicalRecord/`): usa el modelo Eloquent
  `MedicalRecord` ya existente en el repositorio (tabla `medical_records`, migración de otro módulo, sin
  tocar), envuelto en `DB::transaction()` para la creación.
- `InMemoryMedicalRecordRepository`: doble de prueba usado por los tests de aplicación (`OpenMedicalRecordAction`
  / `ViewMedicalRecordAction`), sin tocar la base de datos.

Se aplican los mismos puertos+adaptadores para `MedicalRecordAuditLogger` (bitácora) y
`PatientExistenceChecker` (verificación de existencia de paciente, sin acoplar el dominio al modelo Eloquent
`Patient` de ASII-03).

## 4. Decisión — Capas y dependencias

```
Presentation  (app/Http/Controllers/Api/V1/MedicalRecordController.php, Requests, Resources)
     ↓ depende de
Application   (app/Application/MedicalRecord/*Action.php)
     ↓ depende de (interfaces)
Domain        (app/Domain/MedicalRecord/*)  — sin Eloquent, sin HTTP, sin SQL
     ↑ implementado por
Infrastructure (app/Infrastructure/MedicalRecord/*)  — Eloquent/PostgreSQL
```

`MedicalRecordAuthorizationChecker` vive en Domain y es una función pura: recibe un `AuthorizationContext`
(DTO con `userId` y `roles[]`, ya resueltos por la capa de aplicación desde el usuario JWT/Spatie) y decide
permitido/denegado. Esto cumple SRP (ya documentado en `docs/module-10/semana-2-rf-rnf-solid.md`): el
dominio nunca sabe cómo se autentica ni cómo Spatie almacena roles, solo aplica la regla "¿este conjunto de
roles puede ejecutar esta operación?".

## 5. Decisión — PostgreSQL y migraciones

El motor objetivo del proyecto es PostgreSQL (`config/database.php` ya define la conexión `pgsql`); el
entorno de desarrollo/pruebas por defecto del repositorio usa SQLite (`DB_CONNECTION=sqlite` en
`.env.example` y en `phpunit.xml`). Las dos migraciones que añade este módulo se escriben con `Schema`/
`Blueprint` estándar de Laravel, sin sintaxis específica de un motor, por lo que son reproducibles en ambos:

1. `..._add_opened_by_to_medical_records_table.php`: agrega `opened_by` (FK nullable a `users`,
   `nullOnDelete`) a la tabla **existente** `medical_records`. Es un ajuste mínimo de un contrato
   compartido (la tabla la creó el módulo ASII-11 como scaffold común): se documenta aquí porque la regla
   central de ASII-10 exige que la apertura conserve autor, y hoy la tabla solo tenía `opened_at`. No se
   modifica ninguna columna existente, no se rompe ningún otro módulo que ya la use (ASII-11/12/13/15/16
   solo referencian `medical_records.id`, nunca `opened_by`).
2. `..._create_medical_record_access_logs_table.php`: tabla nueva, propiedad exclusiva de ASII-10, con
   índices por `(tenant_id, patient_id, created_at)` y `(tenant_id, action, result)` para soportar el
   filtrado que en el futuro consumirá ASII-22. `patient_id` se guarda **sin** restricción de clave foránea
   a propósito: la bitácora debe poder registrar un intento denegado por rol/autorización incluso cuando
   `patient_id` no corresponde a ningún paciente real del tenant (RN-10-02 exige auditar "toda" apertura o
   consulta, no solo las que apuntan a un paciente válido). `user_id` sí es FK a `users`, porque siempre
   corresponde al usuario ya autenticado por JWT.

No se habilita `pgvector` en este módulo: no hay ningún requisito de búsqueda semántica/embeddings sobre el
expediente base (esa necesidad es exclusiva de la variante RAG/CAG, módulo de Billy Cardona), por lo que
agregar una columna `vector` aquí violaría la regla de la actividad de no agregar columnas vectoriales sin
una decisión de privacidad aprobada.

## 6. Decisión — Transacciones

La apertura del expediente (`OpenMedicalRecordAction`) ejecuta, dentro de una única transacción de base de
datos: (a) verificación de no-duplicado, (b) generación del `record_number` correlativo, (c) creación del
registro, (d) escritura de la bitácora. Si cualquier paso falla, se revierte todo — no queda un
`record_number` "quemado" sin expediente asociado.

## 7. Riesgos conocidos y mitigación

| Riesgo | Mitigación |
|---|---|
| Condición de carrera al generar `record_number` bajo alta concurrencia (dos aperturas simultáneas del mismo tenant). | Generación dentro de la transacción + `patient_id` con restricción `unique` ya existente en la tabla actúa como segundo cierre: si la carrera crea dos filas para el mismo paciente, la restricción de base de datos rechaza la segunda. |
| `medical_record_access_logs` crece sin límite. | Fuera de alcance del MVP; se documenta como trabajo futuro (purga/archivado) a coordinar con ASII-22. |
| Falta de integración real con `cross_access_audit` de CENTRAL. | Documentado explícitamente como no implementado en este MVP (sección 2); no se simula ni se inventa una tabla CENTRAL que no existe en el repositorio. |
