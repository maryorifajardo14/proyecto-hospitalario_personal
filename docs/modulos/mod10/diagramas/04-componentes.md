# 4. Diagrama de componentes/capas — ASII-10 dentro del SHI

Actualiza `docs/module-10/semana-3-arquitectura.md` con los nombres reales de esta entrega y el límite
explícito CENTRAL/HOSPITAL (ver `ADR-001-arquitectura.md`, sección 2: este módulo es 100% HOSPITAL).

```mermaid
flowchart TB
    subgraph GW["Gateway API — /api/v1 (tenant + jwt.auth, ya existentes)"]
        MwTenant["TenantMiddleware"]
        MwJwt["JwtAuth"]
    end

    subgraph PRES["Presentation"]
        Ctrl["MedicalRecordController"]
        Req["OpenMedicalRecordRequest"]
        Res["MedicalRecordResource"]
    end

    subgraph APP["Application"]
        OpenAction["OpenMedicalRecordAction"]
        ViewAction["ViewMedicalRecordAction"]
    end

    subgraph DOM["Domain (sin HTTP, sin Eloquent, sin SQL)"]
        AuthChecker["MedicalRecordAuthorizationChecker"]
        Entity["MedicalRecordRecord"]
        RepoPort["MedicalRecordRepository (puerto)"]
        AuditPort["MedicalRecordAuditLogger (puerto)"]
        PatientPort["PatientExistenceChecker (puerto)"]
    end

    subgraph INFRA["Infrastructure — HOSPITAL / PostgreSQL"]
        EloquentRepo["EloquentMedicalRecordRepository"]
        FakeRepo["InMemoryMedicalRecordRepository (tests)"]
        EloquentAudit["EloquentMedicalRecordAuditLogger"]
        EloquentPatient["EloquentPatientExistenceChecker"]
        subgraph DB["Base HOSPITAL (por tenant)"]
            TMedical[("medical_records")]
            TLog[("medical_record_access_logs")]
            TPatients[("patients — ASII-03")]
            TUsers[("users — ASII-01")]
        end
    end

    subgraph EXT["Dependencias upstream (mismo repo, otros módulos)"]
        RbacMod["ASII-02 RBAC (roles Spatie)"]
    end

    subgraph CONS["Consumidores downstream"]
        AuditMod["ASII-22 Gobernanza (futuro, vía outbox)"]
    end

    MwTenant --> MwJwt --> Ctrl
    Ctrl --> Req
    Ctrl --> OpenAction
    Ctrl --> ViewAction
    Ctrl --> Res

    OpenAction --> RepoPort
    OpenAction --> AuditPort
    OpenAction --> PatientPort
    OpenAction --> AuthChecker
    ViewAction --> RepoPort
    ViewAction --> AuditPort
    ViewAction --> AuthChecker
    AuthChecker -.consulta roles resueltos por.-> RbacMod

    RepoPort <|.. EloquentRepo
    RepoPort <|.. FakeRepo
    AuditPort <|.. EloquentAudit
    PatientPort <|.. EloquentPatient

    EloquentRepo --> TMedical
    EloquentAudit --> TLog
    EloquentPatient --> TPatients
    TMedical -. opened_by .-> TUsers

    TLog -."evento futuro (outbox/event_id)".-> AuditMod

    classDef central fill:#fde2e2,stroke:#b3261e;
    classDef hospital fill:#e2f0fd,stroke:#1a56db;
    class DB,INFRA hospital;
```

**Límite CENTRAL/HOSPITAL:** no hay ningún nodo CENTRAL en este diagrama porque el módulo no lo necesita
(ver ADR-001, sección 2). El único cruce con otro dominio de datos es de solo lectura hacia `patients`
(ASII-03) y `users` (ASII-01), ambas en la misma base HOSPITAL del tenant — nunca una FK remota entre bases.
