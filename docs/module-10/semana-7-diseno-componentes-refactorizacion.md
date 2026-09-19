# ASII-10 — Expediente médico electrónico base
## Semana 7: diseño de componentes backend/frontend y propuesta de refactorización

> **Entrega correspondiente a la Semana 7** del plan ASII (`README.md` raíz del proyecto de equipo, tabla "Plan semanal ASII", fila 7): *"Diseño de componentes backend/frontend"*, con evidencia *"Diagrama de componentes + propuesta de refactorización"*.
>
> El backend de ASII-10 ya está implementado (Semanas 1-6); el frontend aún no existe en el repositorio de equipo (ver `README.md` raíz, tabla "Módulos clínicos: pendientes"). Esta entrega extiende el diagrama de componentes de la Semana 3/`04-componentes.md` agregando los componentes de **frontend propuestos** (todavía no implementados) y documenta una propuesta de refactorización sobre el backend real ya existente.

## 1. Diagrama de componentes backend + frontend (propuesto)

```mermaid
flowchart TB
    subgraph FE["Frontend (Vue 3 + Pinia + Axios) — PROPUESTO, no implementado aún"]
        PageBuscar["PatientSearchPage.vue"]
        PageFicha["PatientRecordSummaryCard.vue"]
        PageAbrir["OpenMedicalRecordModal.vue"]
        PageExpediente["MedicalRecordView.vue"]
        StoreEMR["medicalRecordStore (Pinia)"]
        ApiClient["medicalRecordApi.js (Axios)"]
    end

    subgraph GW["Gateway API — /api/v1 (tenant + jwt.auth, ya existentes)"]
        MwTenant["TenantMiddleware"]
        MwJwt["JwtAuth"]
    end

    subgraph PRES["Presentation (implementado)"]
        Ctrl["MedicalRecordController"]
        Req["OpenMedicalRecordRequest"]
        Res["MedicalRecordResource"]
    end

    subgraph APP["Application (implementado)"]
        OpenAction["OpenMedicalRecordAction"]
        ViewAction["ViewMedicalRecordAction"]
    end

    subgraph DOM["Domain (implementado)"]
        AuthChecker["MedicalRecordAuthorizationChecker"]
        Entity["MedicalRecordRecord"]
        RepoPort["MedicalRecordRepository (puerto)"]
        AuditPort["MedicalRecordAuditLogger (puerto)"]
        PatientPort["PatientExistenceChecker (puerto)"]
    end

    subgraph INFRA["Infrastructure (implementado)"]
        EloquentRepo["EloquentMedicalRecordRepository"]
        EloquentAudit["EloquentMedicalRecordAuditLogger"]
        EloquentPatient["EloquentPatientExistenceChecker"]
    end

    PageBuscar --> PageFicha
    PageFicha -- "rol Recepcionista/Admin" --> PageAbrir
    PageFicha -- "rol Médico/Enfermera/Admin" --> PageExpediente
    PageAbrir --> StoreEMR
    PageExpediente --> StoreEMR
    StoreEMR --> ApiClient
    ApiClient -- "POST /medical-records" --> MwTenant
    ApiClient -- "GET /medical-records/{patient}" --> MwTenant
    MwTenant --> MwJwt --> Ctrl
    Ctrl --> Req
    Ctrl --> OpenAction
    Ctrl --> ViewAction
    Ctrl --> Res
    OpenAction --> RepoPort & AuditPort & PatientPort & AuthChecker
    ViewAction --> RepoPort & AuditPort & AuthChecker
    RepoPort <|.. EloquentRepo
    AuditPort <|.. EloquentAudit
    PatientPort <|.. EloquentPatient

    classDef pendiente fill:#fef3c7,stroke:#b45309;
    classDef listo fill:#e2f0fd,stroke:#1a56db;
    class FE pendiente;
    class PRES,APP,DOM,INFRA listo;
```

### 1.1. Justificación de los componentes de frontend propuestos

| Componente | Responsabilidad única | Por qué es un componente separado |
|---|---|---|
| `PatientSearchPage.vue` | Buscar un paciente ya registrado (consume el módulo ASII-03, fuera de alcance de ASII-10). | Es el punto de entrada compartido; no conoce nada de expedientes. |
| `PatientRecordSummaryCard.vue` | Mostrar si el paciente tiene o no expediente, y decidir qué acción ofrecer según el rol del usuario en sesión. | Es el único lugar donde se decide "¿muestro el botón de abrir o el de consultar?" — mantiene esa regla de UI fuera de los otros dos componentes. |
| `OpenMedicalRecordModal.vue` | Confirmar y ejecutar la apertura de expediente (UC-10-01), incluyendo el estado de error 409 (expediente duplicado). | Solo cambia si cambia el flujo de apertura; no debe conocer cómo se renderiza el historial. |
| `MedicalRecordView.vue` | Mostrar el historial longitudinal ya abierto (UC-10-02), incluyendo los estados 403/404. | Solo cambia si cambia qué se muestra del expediente; no ejecuta la apertura. |
| `medicalRecordStore` (Pinia) | Mantener el estado del expediente actual y orquestar las llamadas a `medicalRecordApi.js`, desacoplando los componentes de la forma exacta del payload HTTP. | Aplica el mismo principio (SRP) que ya se usó en el backend en la Semana 2: un componente de Vue no debería saber construir cabeceras `X-Tenant-ID` o interpretar códigos HTTP directamente. |
| `medicalRecordApi.js` | Encapsular las llamadas Axios a `POST /medical-records` y `GET /medical-records/{patient}`, incluida la cabecera `X-Tenant-ID` y el Bearer JWT. | Es el único punto que conoce el contrato real de la Semana 5; si cambia una URL o un payload, solo se toca este archivo. |

Esta separación replica en el frontend el mismo criterio de diseño aplicado al backend en la Semana 2 (SRP): cada componente tiene una única razón para cambiar, y la decisión de "qué botón mostrar según el rol" vive en un solo lugar (`PatientRecordSummaryCard.vue`), no repetida en cada pantalla.

## 2. Propuesta de refactorización (backend real, ya implementado)

A diferencia de la sección 1 (frontend, aún no implementado), esta sección analiza código **real** del repositorio para identificar duplicación evitable, sin modificarlo todavía: se documenta como propuesta, a validar junto con la ejecución pendiente de pruebas (ver [`EVIDENCIA.md`](../modulos/mod10/EVIDENCIA.md)).

### 2.1. Duplicación detectada

En `app/Http/Controllers/Api/V1/MedicalRecordController.php`, los métodos `open()` y `show()` repiten la misma estructura:

```php
$tenant = $request->attributes->get('tenant');
try {
    $record = $this->xxxAction->handle((string) $tenant->id, ..., $this->authorizationContext($request));
} catch (MedicalRecordAccessDeniedException $exception) {
    return response()->json(['message' => $exception->getMessage()], 403);
} catch (...) { ... }
```

Y en ambas Actions (`OpenMedicalRecordAction::handle`, `ViewMedicalRecordAction::handle`), se repite el mismo patrón "autorizar → si falla, auditar como denegado y relanzar":

```php
try {
    $this->authorization->authorizeOpen($context); // o authorizeView
} catch (MedicalRecordAccessDeniedException $exception) {
    $this->auditLogger->logOpen($tenantId, $patientId, $context->userId, 'denied', $exception->getMessage());
    throw $exception;
}
```

### 2.2. Refactorización propuesta

| Duplicación | Propuesta | Beneficio |
|---|---|---|
| Extracción de `$tenant` en cada método del controlador | Método privado único `tenantId(Request $request): string`, reutilizado por `open()` y `show()` (ya existe `authorizationContext()` con ese mismo propósito para el contexto de usuario; falta el equivalente para el tenant). | Un solo lugar que sabe leer el tenant desde `$request->attributes`; si cambia esa convención (por ejemplo, se agrega resolución por subdominio), se cambia una sola vez. |
| El bloque `try/catch` de mapeo de excepciones de dominio a códigos HTTP, repetido en `open()` y `show()` | Registrar `MedicalRecordAccessDeniedException`, `MedicalRecordNotFoundException`, `PatientNotFoundException` y `MedicalRecordAlreadyExistsException` en el manejador global de excepciones de Laravel (`bootstrap/app.php` / `App\Exceptions\Handler`), con su propio método `render()`. | El controlador queda con una sola línea por método (llamar a la Action), sin duplicar el mapeo excepción→código HTTP; sigue siendo el principio de controlador delgado ya declarado en la Semana 2, llevado un paso más lejos. |
| El patrón "autorizar → capturar → auditar como denegado → relanzar", repetido en ambas Actions | Extraer un pequeño colaborador `AuditedAuthorization` (o método protegido en una clase base de Action) que reciba el closure de autorización y el closure de auditoría, para no repetir el `try/catch` en cada Action. | Si mañana se agrega una tercera Action (por ejemplo, `UpdateMedicalRecordBackgroundAction`, mencionada como ejercicio en la Semana 6), no se vuelve a copiar el mismo bloque de 5 líneas. |

### 2.3. Por qué se documenta como propuesta y no se aplica todavía

Aplicar esta refactorización cambia el comportamiento observable solo si se hace mal (por ejemplo, si el `render()` global no reproduce exactamente los mismos cuerpos de respuesta ya fijados en el contrato de la Semana 5). Como la ejecución real de la suite de pruebas (`php artisan test`) sigue pendiente en el entorno de preparación de esta entrega (ver [`EVIDENCIA.md`, sección 1](../modulos/mod10/EVIDENCIA.md#1-aviso-importante-sobre-esta-evidencia)), aplicar el cambio ahora sin poder correr `MedicalRecordApiTest.php` para confirmar que ningún código de respuesta cambió sería un riesgo no verificado. La propuesta queda lista para ejecutarse en cuanto se disponga de un entorno con PHP/Composer/PostgreSQL, como primer paso antes de abrir el PR (ver [`semana-5-contrato-api-plan-integracion.md`, sección 4](./semana-5-contrato-api-plan-integracion.md#4-plan-de-pull-request)).

## 3. Trazabilidad con semanas anteriores

| Elemento previo | Elemento de esta entrega (Semana 7) |
|---|---|
| Diagrama de componentes de la Semana 3 (`04-componentes.md`) | Base extendida con el bloque de frontend propuesto (sección 1) |
| Principio SOLID (SRP) aplicado al backend, Semana 2 | Reaplicado a los componentes de frontend propuestos (sección 1.1) y usado como criterio para la refactorización (sección 2.2) |
| Controlador delgado, Semana 4 | Se propone llevarlo un paso más allá delegando el mapeo de excepciones al manejador global (sección 2.2) |
| Limitación de validación real declarada en `EVIDENCIA.md` | Razón explícita para no aplicar aún la refactorización (sección 2.3) |
