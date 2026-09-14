# ASII-10 — Expediente médico electrónico base
## Semana 4: diseño por capas y responsabilidades

> **Entrega correspondiente a la Semana 4** del plan ASII (`README.md` raíz del proyecto de equipo, tabla "Plan semanal ASII", fila 4): *"Diseño por capas y responsabilidades"*, con evidencia *"UI, API, lógica, persistencia y objetos reutilizables"*.
>
> Esta entrega implementa los componentes que la [Semana 3](./semana-3-arquitectura.md) ya había ubicado en el diagrama de la vista arquitectónica: cada caja de ese diagrama corresponde aquí a una clase real, dentro de la capa que le toca.

## 1. Capas y responsabilidades

| Capa | Ubicación en el código | Responsabilidad | Clases |
|---|---|---|---|
| Dominio (semana 1, sin cambios) | `app/Domain/MedicalRecord/` | Entidad, reglas de autorización y puertos (interfaces), sin depender de Laravel ni de la base de datos. | `MedicalRecordRecord`, `AuthorizationContext`, `MedicalRecordAuthorizationChecker` |
| **Aplicación** | `app/Application/MedicalRecord/` | Orquesta un caso de uso completo: valida, llama al dominio, persiste y audita. No conoce HTTP. | `OpenMedicalRecordAction`, `ViewMedicalRecordAction` |
| **Infraestructura** | `app/Infrastructure/MedicalRecord/` | Implementa los puertos del dominio contra un motor real (Eloquent/PostgreSQL) o uno de pruebas (memoria). | `EloquentMedicalRecordRepository`, `EloquentMedicalRecordAuditLogger`, `EloquentPatientExistenceChecker`, `InMemoryMedicalRecordRepository` |
| **Presentación (API)** | `app/Http/` | Traduce HTTP ⇄ Action: recibe la petición, valida el request, llama a la Action y da forma a la respuesta JSON. | `MedicalRecordController`, `OpenMedicalRecordRequest`, `MedicalRecordResource` |
| Persistencia | `app/Models/`, `database/migrations/`, `database/factories/` | Esquema y modelos Eloquent (semana 1, sin cambios en esta semana). | `MedicalRecord`, `MedicalRecordAccessLog` |

`app/Providers/AppServiceProvider.php` es el punto donde se conecta cada puerto del dominio con su implementación de infraestructura (binding de interfaces), y `routes/api.php` expone las rutas que consume la capa de presentación.

## 2. Objetos reutilizables: puertos e infraestructura intercambiable

La capa de aplicación depende únicamente de **interfaces** definidas en el dominio (`MedicalRecordRepository`, `PatientExistenceChecker`, `MedicalRecordAuditLogger`), no de Eloquent directamente. Eso permite reutilizar la misma `OpenMedicalRecordAction` / `ViewMedicalRecordAction` con dos implementaciones distintas del mismo contrato:

- `Eloquent*` — implementación real contra PostgreSQL, usada en producción.
- `InMemoryMedicalRecordRepository` — implementación en memoria, usada en las pruebas de la capa de aplicación (`tests/Feature/MedicalRecord/OpenMedicalRecordActionTest.php`, `ViewMedicalRecordActionTest.php`) sin tocar base de datos.

Es la misma idea de "no repetir responsabilidades que ya pertenecen a otro componente" que se señaló en la [Semana 3, sección 4](./semana-3-arquitectura.md#4-vista-transversal-cross-cutting), aplicada ahora a nivel de objetos: un solo caso de uso, varias implementaciones intercambiables de su dependencia.

## 3. API (UI ⇄ backend)

El contrato completo de la API (endpoints, roles requeridos, cuerpos de petición/respuesta y códigos de estado) ya quedó definido en la especificación de la actividad integradora — no se repite aquí, se referencia: ver [`ESPECIFICACION.md`, sección 7](../modulos/mod10/ESPECIFICACION.md#7-contrato-de-la-api-entrega-vertical).

En resumen, los dos endpoints expuestos en `routes/api.php` son:

| Endpoint | Acción de aplicación | Rol requerido |
|---|---|---|
| `POST /api/v1/medical-records` | `OpenMedicalRecordAction` | Recepcionista, Admin |
| `GET /api/v1/medical-records/{patient}` | `ViewMedicalRecordAction` | Médico, Enfermera, Admin |

## 4. Flujo de una petición a través de las capas

El diagrama de secuencia detallado (petición → middleware → controlador → Action → dominio → infraestructura → respuesta) ya se documentó en la Semana 3: ver [`diagramas/03-secuencia.md`](../modulos/mod10/diagramas/03-secuencia.md).

## 5. Pruebas por capa

| Capa probada | Archivo de prueba | Qué verifica |
|---|---|---|
| Dominio | `tests/Unit/Domain/MedicalRecord/MedicalRecordAuthorizationCheckerTest.php`, `MedicalRecordRecordTest.php` | Reglas de autorización y de la entidad, sin infraestructura. |
| Aplicación (con dobles) | `tests/Feature/MedicalRecord/OpenMedicalRecordActionTest.php`, `ViewMedicalRecordActionTest.php` | Las Actions orquestan correctamente usando `InMemoryMedicalRecordRepository` y los fakes en `tests/Support/MedicalRecord/`. |
| Integración HTTP + BD | `tests/Feature/MedicalRecord/MedicalRecordApiTest.php` | Apertura, consulta autorizada y acceso denegado a través de la API real. |

La evidencia de ejecución (y la limitación de entorno para correrlas realmente) está declarada en [`EVIDENCIA.md`](../modulos/mod10/EVIDENCIA.md).

## 6. Trazabilidad con semanas anteriores

| Elemento previo | Elemento de esta entrega (Semana 4) |
|---|---|
| `OpenMedicalRecordAction` / `ViewMedicalRecordAction` (ubicadas en el diagrama, Semana 3) | Implementadas en `app/Application/MedicalRecord/` |
| Componente "Persistencia" (Semana 3, diagrama) | `app/Infrastructure/MedicalRecord/Eloquent*` |
| Componente "Gateway API" (Semana 3, diagrama) | `app/Http/Controllers/Api/V1/MedicalRecordController.php` + `routes/api.php` |
| RN-10-01 a RN-10-04 (Semana 1/2) | Verificadas en las pruebas de la sección 5 |
