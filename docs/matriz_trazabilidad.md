# Matriz de trazabilidad — Requisito → Diagrama → Elemento

**Módulo:** Expediente médico electrónico base
**Proceso:** Apertura y consulta autorizada del expediente longitudinal
**Estudiante:** Maryori Rachael Fajardo Paredes

La matriz demuestra que actores, pasos, mensajes y excepciones son consistentes
entre los tres diagramas UML del proceso.

| ID | Requisito / regla de negocio | Caso de uso | Actividad | Secuencia |
|----|-------------------------------|-------------|-----------|-----------|
| RF-01 | El personal clínico autorizado debe poder solicitar la apertura del expediente longitudinal de un paciente de su tenant. | Actor **Personal Clínico Autorizado** → **UC1: Solicitar apertura y consulta del expediente longitudinal** | Swimlane *Personal Clínico Autorizado*: "Buscar/seleccionar paciente" → "Solicitar apertura del expediente longitudinal" | `Medico -> UI : seleccionar paciente y solicitar apertura de expediente`; `UI -> API : GET /pacientes/{id}/expediente` |
| RF-02 | El sistema debe validar que la sesión (token) esté vigente antes de procesar la solicitud. | **UC2: Autenticar sesión (validar token vigente)**, incluido por UC1; actor de soporte **Servicio de Autenticación** | Decisión "¿Token válido y vigente?" | `API -> Auth : validarToken(token)`; `Auth --> API : resultado(tokenValido, usuario, tenantId)` |
| RF-03 | Si la sesión no es válida, el sistema debe rechazar la solicitud y registrar el intento. | Extensión implícita de UC2 (fallo de autenticación) | Rama "no" de "¿Token válido y vigente?" → "Rechazar solicitud (401)" → "Registrar evento SESION_INVALIDA" → "Recibir mensaje 'Sesión expirada...'" | Bloque `alt Token inválido o expirado`: `API --> UI : 401 No autorizado`; `UI --> Medico : "Sesión expirada..."` |
| RF-04 | El sistema debe verificar que el rol del usuario tenga permiso para consultar expedientes en el tenant (RBAC). | **UC3: Verificar autorización (RBAC por rol y tenant)**, incluido por UC1; actor de soporte **Servicio de Autorización RBAC** | Decisión "¿Rol autorizado para consultar expedientes en el tenant?" | `API -> RBAC : verificarPermiso(usuario, rol, recurso="expediente", tenantId)`; `RBAC --> API : resultado(autorizado)` |
| RF-05 | Si el rol no está autorizado, el sistema debe denegar el acceso de forma trazable (registrando el evento en auditoría). | **UC6: Denegar acceso no autorizado** «extend» de UC3; incluye **UC5** | Rama "no" de "¿Rol autorizado...?" → "Denegar acceso (403)" → "Registrar evento ACCESO_DENEGADO" → "Recibir mensaje 'Acceso denegado'" | Bloque `alt No autorizado`: `API -> Audit : registrarEvento("ACCESO_DENEGADO", ...)`; `API --> UI : 403 Acceso denegado` |
| RF-06 | El sistema debe verificar que el paciente exista dentro del tenant del usuario autenticado. | Parte del flujo de **UC4: Consolidar historial longitudinal del paciente** | "Buscar paciente en el tenant" → decisión "¿Paciente existe en el tenant del usuario?" | `Svc -> DB : SELECT ... WHERE paciente=id AND tenant=tenantId`; `DB --> Svc : resultset` |
| RF-07 | Si el paciente no existe en el tenant, el sistema debe notificar el error de forma trazable. | **UC7: Notificar paciente inexistente en el tenant** «extend» de UC4; incluye **UC5** | Rama "no" de "¿Paciente existe...?" → "Responder 'Paciente no encontrado' (404)" → "Registrar evento PACIENTE_NO_ENCONTRADO" | Bloque `alt Paciente no encontrado`: `Svc --> API : notFound`; `API -> Audit : registrarEvento("PACIENTE_NO_ENCONTRADO", ...)`; `API --> UI : 404 Paciente no encontrado` |
| RF-08 | El sistema debe consolidar el historial longitudinal (notas SOAP, diagnósticos, alergias, signos vitales, laboratorios, prescripciones) del paciente autorizado. | **UC4: Consolidar historial longitudinal del paciente**, incluido por UC1 | "Consolidar historial longitudinal (notas SOAP, diagnósticos, alergias, signos vitales, laboratorios, prescripciones)" | `API -> Svc : obtenerExpedienteLongitudinal(pacienteId, tenantId)`; `Svc --> API : expedienteConsolidado` |
| RF-09 | Todo acceso —concedido o denegado— debe quedar registrado en la bitácora de auditoría con usuario, paciente y fecha/hora. | **UC5: Registrar evento de auditoría**, incluido por UC1, UC6 y UC7; actor de soporte **Módulo de Auditoría** | "Registrar evento ACCESO_CONCEDIDO / ACCESO_DENEGADO / PACIENTE_NO_ENCONTRADO / SESION_INVALIDA" en las cuatro salidas del flujo | `API -> Audit : registrarEvento(tipo, usuario, pacienteId, timestamp)`; `Audit --> API : ack` (repetido en cada rama) |
| RF-10 | El sistema debe responder al usuario con el expediente consolidado (éxito) o con el motivo de error correspondiente. | Objetivo final de **UC1**, alcanzado por inclusión de UC2–UC5 o por las extensiones UC6/UC7 | Actividades finales "Visualizar y consultar el expediente longitudinal" / "Recibir mensaje de error" / "Recibir mensaje 'Acceso denegado'" / "Recibir mensaje 'Sesión expirada...'" | `API --> UI : 200 OK + expedienteLongitudinal`; `UI --> Medico : mostrar expediente longitudinal consolidado` (y respuestas 401/403/404 equivalentes) |

## Consistencia de actores entre diagramas

| Actor / participante | Caso de uso | Actividad (swimlane) | Secuencia (participante) |
|---|---|---|---|
| Personal clínico autorizado | Actor primario | `Personal Clínico Autorizado` | `Medico`, `UI Expediente (Frontend SPA)` |
| Servicio de autenticación | Actor de soporte (UC2) | Implícito en "Sistema HIS (Backend)" | `AuthService (JWT)` |
| Servicio de autorización RBAC | Actor de soporte (UC3) | Implícito en "Sistema HIS (Backend)" | `RBACService` |
| Módulo de auditoría | Actor de soporte (UC5) | Swimlane `Módulo de Auditoría` | `AuditService` |
| Repositorio de datos clínicos | No modelado como actor (interno) | Implícito en "Sistema HIS (Backend)" | `ExpedienteService`, `Base de Datos` |

**Nota metodológica:** en el diagrama de casos de uso los servicios internos (autenticación, RBAC, auditoría) se
modelan como actores de soporte (`<<sistema>>`) porque son límites del sistema con los que UC1 colabora; en el
diagrama de actividad se agrupan dentro de las swimlanes "Sistema HIS (Backend)" y "Módulo de Auditoría" por
nivel de abstracción de proceso de negocio; en el diagrama de secuencia se despliegan como participantes/objetos
independientes (`AuthService`, `RBACService`, `ExpedienteService`, `AuditService`, `Base de Datos`) para mostrar
el intercambio de mensajes a nivel de diseño.
