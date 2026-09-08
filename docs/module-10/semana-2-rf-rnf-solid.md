# ASII-10 — Expediente médico electrónico base
## Semana 2: RF/RNF, criterios de aceptación y principio SOLID

> **Entrega correspondiente a la Semana 2** del plan ASII (`README.md` raíz, tabla "Plan semanal ASII", fila 2, y `docs/weekly-plan.md`): *"Definir RF/RNF, criterios de aceptación y ejemplificar al menos un principio SOLID en el módulo asignado"*, con evidencia *"Tabla RF/RNF + criterios de aceptación + ejemplo SOLID"*. Fuente de los principios SOLID: `https://mvpcluster.com/diseno-de-software-2/`.
>
> Esta entrega parte de los casos de uso UC-10-01, UC-10-02 y UC-10-03 y de las reglas de dominio RN-10-01 a RN-10-03 definidas en la [Semana 1](./semana-1-casos-de-uso.md), y se apoya en el código base ya existente del repositorio (`app/Models/MedicalRecord.php`, `database/migrations/2026_04_26_120000_create_emr_tables.php`, middleware `TenantMiddleware` y `JwtAuth`, roles Spatie definidos en `RoleSeeder`).

## 1. Requisitos Funcionales (RF)

| Código | Requisito | Caso de uso relacionado |
|---|---|---|
| RF-10-01 | El sistema debe permitir a un usuario con rol `Recepcionista` crear el expediente médico longitudinal de un paciente ya registrado. | UC-10-01 |
| RF-10-02 | El sistema debe generar automáticamente un número de expediente correlativo y único por tenant (formato `EXP-00001`) al momento de la apertura. | UC-10-01 |
| RF-10-03 | El sistema debe impedir la creación de un segundo expediente para un paciente que ya tiene uno activo, y en su lugar mostrar/enlazar el expediente existente. | UC-10-01, RN-10-01 |
| RF-10-04 | El sistema debe permitir a un usuario con rol `Médico` (u otro rol autorizado) consultar el historial longitudinal completo de un paciente. | UC-10-02 |
| RF-10-05 | El sistema debe validar, antes de ejecutar la apertura o la consulta, que el usuario tenga sesión activa (JWT válido) y el permiso/rol requerido. | UC-10-03, RN-10-03 |
| RF-10-06 | El sistema debe registrar en una bitácora cada consulta al expediente (exitosa o denegada), indicando usuario, paciente, fecha/hora y resultado. | UC-10-02, RN-10-02 |
| RF-10-07 | El sistema debe aislar los expedientes por `tenant_id`, de modo que un usuario de un hospital/tenant nunca pueda abrir ni consultar expedientes de otro tenant. | RN-10-01, RN-10-03 |

## 2. Requisitos No Funcionales (RNF)

| Código | Requisito | Categoría |
|---|---|---|
| RNF-10-01 | Toda operación sobre el expediente debe pasar por autenticación JWT (`jwt.auth`) y resolución de tenant (`TenantMiddleware`) antes de llegar a la lógica de negocio. | Seguridad |
| RNF-10-02 | El acceso a la consulta longitudinal debe estar restringido por rol/permiso (Spatie Laravel Permission), aplicando el principio de mínimo privilegio. | Seguridad |
| RNF-10-03 | Ningún dato clínico sensible del expediente debe quedar expuesto en logs de aplicación ni en mensajes de error genéricos. | Confidencialidad |
| RNF-10-04 | El registro de bitácora (RF-10-06) debe ser inmutable desde la API pública: solo se crea, nunca se edita ni se borra vía endpoints del módulo. | Auditoría / Integridad |
| RNF-10-05 | La consulta del historial longitudinal debe responder en un tiempo aceptable aun cuando el expediente acumule muchas notas SOAP, signos vitales y prescripciones (se apoya en los índices ya definidos en `medical_records`, `soap_notes` y `vital_signs`). | Rendimiento |
| RNF-10-06 | Los mensajes de error de autorización y de expediente no encontrado deben ser claros para el usuario final sin filtrar detalles internos del sistema. | Usabilidad |

## 3. Criterios de aceptación

Formato Given/When/Then, uno por cada RF principal.

| # | Dado (Given) | Cuando (When) | Entonces (Then) |
|---|---|---|---|
| CA-01 | La recepcionista tiene sesión activa y el paciente no tiene expediente previo. | Selecciona "Abrir expediente" para ese paciente. | El sistema crea el registro con `record_number` correlativo y `opened_at` con la fecha actual (RF-10-01, RF-10-02). |
| CA-02 | El paciente ya tiene un expediente activo. | La recepcionista intenta abrir uno nuevo para el mismo paciente. | El sistema rechaza la creación, muestra advertencia y redirige al expediente existente; no se crea un duplicado (RF-10-03). |
| CA-03 | El médico tiene rol autorizado y token JWT válido. | Consulta el expediente longitudinal de un paciente con expediente existente. | El sistema retorna el historial completo y registra la consulta en bitácora (RF-10-04, RF-10-06). |
| CA-04 | El médico no tiene sesión activa o su token es inválido. | Intenta consultar cualquier expediente. | El sistema responde 401 y redirige a inicio de sesión, sin ejecutar ninguna consulta a datos clínicos (RF-10-05). |
| CA-05 | El médico tiene sesión válida pero no tiene el rol/permiso requerido. | Intenta consultar un expediente. | El sistema responde 403, no expone datos clínicos y registra el intento fallido en bitácora (RF-10-05, RF-10-06, RN-10-03). |
| CA-06 | Un usuario autenticado pertenece al tenant A. | Intenta consultar (por URL o ID) un expediente del tenant B. | El sistema responde 404/403 según corresponda; nunca devuelve datos de otro tenant (RF-10-07). |

## 4. Principio SOLID aplicado al módulo

**Fuente:** `https://mvpcluster.com/diseno-de-software-2/`

**Principio elegido:** **Single Responsibility Principle (SRP)** — "una clase debe tener una única razón para cambiar".

### 4.1. Por qué aplica a ASII-10

Los propios casos de uso de la Semana 1 ya separan responsabilidades por actor: la *Recepcionista* abre el expediente (UC-10-01), el *Médico* lo consulta (UC-10-02) y el *Sistema de Seguridad* valida la autorización (UC-10-03, incluido en ambos). Diseñar el módulo con una clase que mezcle las tres cosas (crear, consultar y autorizar) violaría SRP: cambiar la regla de autorización obligaría a tocar la misma clase que crea o consulta expedientes, con riesgo de romper algo que no tenía relación con el cambio.

### 4.2. Diseño propuesto (sin mezclar responsabilidades)

| Clase / componente | Responsabilidad única | Razón para cambiar |
|---|---|---|
| `MedicalRecordController` | Recibir la petición HTTP, delegar y devolver la respuesta. No contiene lógica de negocio. | Solo cambia si cambia el contrato HTTP (rutas, formato de request/response). |
| `OpenMedicalRecordAction` | Ejecutar la apertura de expediente: validar que no exista duplicado (RN-10-01) y generar el `record_number`. | Solo cambia si cambia la regla de negocio de apertura. |
| `ViewMedicalRecordAction` | Ejecutar la consulta longitudinal y componer el historial a devolver. | Solo cambia si cambia qué información se muestra en la consulta. |
| `MedicalRecordAuthorizationChecker` | Verificar rol/permiso del usuario (equivalente al actor "Sistema de Seguridad"). | Solo cambia si cambian las reglas de quién puede abrir/consultar. |
| `MedicalRecordAuditLogger` | Registrar en bitácora cada intento (RF-10-06). | Solo cambia si cambia qué se audita o dónde se guarda. |

### 4.3. Consecuencia directa

Con este diseño, si mañana cambia la regla de "quién puede consultar el expediente" (por ejemplo, se agrega el rol `Enfermera` con acceso de solo lectura), el cambio se hace únicamente en `MedicalRecordAuthorizationChecker`, sin tocar `ViewMedicalRecordAction` ni el controlador — cumpliendo SRP y reduciendo el riesgo de regresiones en la lógica de apertura/consulta.

## 5. Trazabilidad con la Semana 1

| Elemento Semana 1 | Elemento Semana 2 relacionado |
|---|---|
| UC-10-01 (Apertura de expediente) | RF-10-01, RF-10-02, RF-10-03, CA-01, CA-02 |
| UC-10-02 (Consulta longitudinal) | RF-10-04, RF-10-06, CA-03 |
| UC-10-03 (Validar autorización) | RF-10-05, RF-10-07, RNF-10-01, RNF-10-02, CA-04, CA-05, CA-06 |
| RN-10-01 (expediente único) | RF-10-03, CA-02 |
| RN-10-02 (bitácora obligatoria) | RF-10-06, RNF-10-04 |
| RN-10-03 (autorización explícita) | RF-10-05, RF-10-07, RNF-10-02 |
