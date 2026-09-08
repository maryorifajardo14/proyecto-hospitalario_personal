# ASII-10 — Evidencia

## 1. Aviso importante sobre esta evidencia

Esta entrega se preparó con Claude Code en una máquina donde **PHP está bloqueado por una directiva de
control de aplicaciones de Windows**, y donde no hay Composer, servidor PostgreSQL ni `gh` CLI instalados.
Por lo tanto, **no pude ejecutar `php artisan migrate`, `php artisan test` ni abrir el Pull Request** desde
aquí; el código se escribió y se revisó manualmente (sintaxis, imports, tipos, contratos entre capas) con la
atención que exige la actividad, pero **la validación real de la sección 10 de la guía queda pendiente y
debe correrla la estudiante antes de abrir el PR**, pegando la salida real debajo de cada comando.

No se reporta ninguna salida de comando como si se hubiera ejecutado: donde no hay evidencia real, se deja
explícito que falta.

## 2. Comandos de validación mínima (a ejecutar por la estudiante)

```bash
php -v
php artisan --version
composer install
cp .env.example .env   # si no existe ya
php artisan key:generate
php artisan jwt:secret
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan test
git status --short
git log --oneline --decorate --graph -n 20
git worktree list
```

Para validar específicamente contra PostgreSQL (motor objetivo de la actividad), cambiar en `.env`:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hospital_his
DB_USERNAME=postgres
DB_PASSWORD=...
```

y repetir `php artisan migrate:fresh --seed` contra esa base. La sentencia `pgvector` de la sección 10 de la
guía (`SELECT extversion FROM pg_extension WHERE extname = 'vector';`) **no aplica a este módulo** (ver
`ADR-001-arquitectura.md`, sección 5): ASII-10 no usa pgvector.

### Pegar aquí la salida real antes de abrir el PR

```
php -v
# (pendiente: pegar salida real)

php artisan --version
# (pendiente: pegar salida real)

php artisan migrate:fresh --seed
# (pendiente: pegar salida real)

php artisan test
# (pendiente: pegar salida real, en particular de:
#   tests/Unit/Domain/MedicalRecord/MedicalRecordAuthorizationCheckerTest.php
#   tests/Unit/Domain/MedicalRecord/MedicalRecordRecordTest.php
#   tests/Feature/MedicalRecord/OpenMedicalRecordActionTest.php
#   tests/Feature/MedicalRecord/ViewMedicalRecordActionTest.php
#   tests/Feature/MedicalRecord/MedicalRecordApiTest.php)
```

## 3. Árbol de archivos de esta entrega

```
docs/modulos/mod10/
├── ESPECIFICACION.md
├── ADR-001-arquitectura.md
├── EVIDENCIA.md
├── DECLARACION_IA.md
├── PR_BODY.md
└── diagramas/
    ├── README.md
    ├── 01-casos-de-uso.md
    ├── 02-clases-diseno.md
    ├── 03-secuencia.md
    ├── 04-componentes.md
    └── 05-vista-datos.md

app/Domain/MedicalRecord/
├── MedicalRecordRecord.php
├── AuthorizationContext.php
├── MedicalRecordRepository.php
├── MedicalRecordAuditLogger.php
├── PatientExistenceChecker.php
├── MedicalRecordAuthorizationChecker.php
└── Exceptions/
    ├── MedicalRecordAlreadyExistsException.php
    ├── MedicalRecordNotFoundException.php
    ├── MedicalRecordAccessDeniedException.php
    └── PatientNotFoundException.php

app/Application/MedicalRecord/
├── OpenMedicalRecordAction.php
└── ViewMedicalRecordAction.php

app/Infrastructure/MedicalRecord/
├── EloquentMedicalRecordRepository.php
├── InMemoryMedicalRecordRepository.php
├── EloquentMedicalRecordAuditLogger.php
└── EloquentPatientExistenceChecker.php

app/Http/Controllers/Api/V1/MedicalRecordController.php
app/Http/Requests/OpenMedicalRecordRequest.php
app/Http/Resources/MedicalRecordResource.php
app/Models/MedicalRecord.php            (modificado: opened_by, relaciones)
app/Models/MedicalRecordAccessLog.php   (nuevo)
app/Providers/AppServiceProvider.php    (modificado: bindings)
routes/api.php                          (modificado: rutas de medical-records)

database/migrations/
├── 2026_08_21_090000_add_opened_by_to_medical_records_table.php
└── 2026_08_21_090100_create_medical_record_access_logs_table.php
database/factories/MedicalRecordFactory.php (modificado: opened_by)

tests/Unit/Domain/MedicalRecord/
├── MedicalRecordAuthorizationCheckerTest.php
└── MedicalRecordRecordTest.php
tests/Feature/MedicalRecord/
├── OpenMedicalRecordActionTest.php
├── ViewMedicalRecordActionTest.php
└── MedicalRecordApiTest.php
tests/Support/MedicalRecord/
├── FakeMedicalRecordAuditLogger.php
└── FakePatientExistenceChecker.php
```

(41 archivos nuevos/modificados en total — ver `git diff --stat 0cf55cd..HEAD` para el detalle exacto de
líneas por archivo.)

## 4. Historial de commits de esta actividad

```
* 56ed0c5 test(asii-10): prueba de integracion HTTP+DB - apertura, consulta autorizada y acceso denegado
* f9af6ab test(asii-10): pruebas de aplicacion con dobles - open y view medical record actions
* 634b505 test(asii-10): pruebas de dominio - autorizacion y regla de la entidad MedicalRecordRecord
* 5420174 feat(asii-10): presentacion - controlador, request, resource, rutas y bindings
* 4b53fb4 feat(asii-10): infraestructura - migraciones, modelos Eloquent y adaptadores PostgreSQL
* 7605cfc feat(asii-10): capa de aplicacion - OpenMedicalRecordAction y ViewMedicalRecordAction
* 1816738 feat(asii-10): capa de dominio - entidad, puertos, reglas de autorizacion y excepciones
* eb746af docs(asii-10): cinco diagramas UML actualizados con nombres reales del codigo
* e9d87e1 docs(asii-10): ADR-001 arquitectura, propiedad de datos y decisiones PostgreSQL
* 9271998 docs(asii-10): especificacion de la actividad integradora
* 0cf55cd (origin) docs(asii-10): semana 2 y 3 - RF/RNF, SOLID y vista arquitectonica   <- punto de partida
```

10 commits sustantivos sobre el punto de partida ya publicado en `origin`, cada uno con un incremento
completo y revisable (documentación → dominio → aplicación → infraestructura → presentación → pruebas),
siguiendo el flujo Domain → Application → Infrastructure → Presentation pedido en la sección 5 de la guía.

`git worktree list` (ejecutado en este entorno):

```
C:/Users/pared/OneDrive/Documentos/.../sistema-hospitalario-integrado-SistenasII-2026   406eb81 [main]
C:/Users/pared/OneDrive/Documentos/.../shi-asii-10-expediente                            56ed0c5 [feature/asii-10-expediente-medico-electronico-base-maryorifajardo14]
```

`git status --short`: limpio (sin cambios sin commitear) al momento de escribir esta evidencia.

## 5. Cobertura de la evidencia mínima específica del módulo (sección 14.10 de la guía)

| Evidencia pedida | Dónde queda cubierta |
|---|---|
| Apertura duplicada | `OpenMedicalRecordActionTest::test_rechaza_apertura_duplicada_para_el_mismo_paciente`, `MedicalRecordApiTest::test_rechaza_apertura_duplicada_para_el_mismo_paciente` |
| Consulta autorizada | `ViewMedicalRecordActionTest::test_consulta_autorizada_retorna_el_expediente`, `MedicalRecordApiTest::test_recepcionista_abre_expediente_y_medico_lo_consulta` |
| Acceso denegado | `MedicalRecordAuthorizationCheckerTest` (4 casos), `ViewMedicalRecordActionTest::test_deniega_consulta_sin_rol_autorizado`, `MedicalRecordApiTest::test_deniega_consulta_a_usuario_sin_rol_autorizado`, `MedicalRecordApiTest::test_rechaza_consulta_sin_token` |

## 6. Pendiente antes de abrir el Pull Request

1. Ejecutar los comandos de la sección 2 y pegar la salida real (reemplazando los `# (pendiente...)`).
2. Si algún test falla por un detalle no verificable sin ejecución real (nombres de columnas, casts,
   versiones de paquetes), corregirlo y volver a commitear — son ajustes esperables de una entrega escrita
   sin poder correr PHP en este entorno.
3. `git push` de la rama y apertura del Pull Request en modo borrador (ver `PR_BODY.md`).
