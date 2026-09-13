<!--
Borrador listo para pegar en la descripción del Pull Request (modo borrador / draft).
Sigue la plantilla .github/pull_request_template.md ya completada para esta actividad.
Antes de abrir el PR: reemplazar los "(pendiente)" con la salida real de EVIDENCIA.md.
-->

# PR de módulo ASII — Actividad integradora (ASII-10)

## Resumen

Entrega vertical mínima del módulo ASII-10 (Expediente médico electrónico base): apertura del expediente
longitudinal de un paciente y su consulta autorizada, con Repository pattern, capas Domain/Application/
Infrastructure/Presentation, migraciones PostgreSQL reversibles y bitácora local de accesos. Corresponde a la
"Actividad integradora de arquitectura federada y persistencia" (no a una semana del plan ASII regular).

## Módulo asignado

- Módulo ASII: `ASII-10 — Expediente médico electrónico base`
- Estudiante: Maryori Rachael Fajardo Paredes
- GitHub: `maryorifajardo14`
- Rama: `feature/asii-10-expediente-medico-electronico-base-maryorifajardo14`
- Destino del PR: *(confirmar con el equipo: `develop` según README raíz, o la rama que indique el docente
  para esta actividad)*

## Alcance

### Incluido

- Apertura de expediente (`POST /api/v1/medical-records`) con generación de `record_number` correlativo y
  registro del autor (`opened_by`).
- Rechazo de apertura duplicada (409) — RN-10-01.
- Consulta longitudinal autorizada (`GET /api/v1/medical-records/{patient}`) — RN-10-03.
- Autorización por rol aislada en `MedicalRecordAuthorizationChecker` (dominio puro).
- Bitácora local `medical_record_access_logs` de cada intento (éxito/denegado).
- ADR, especificación y 5 diagramas UML en `docs/modulos/mod10/`.

### Fuera de alcance

- Contenido clínico especializado (notas SOAP, alergias, signos vitales, prescripciones, laboratorio): otros
  módulos.
- Sincronización con CENTRAL: este módulo es 100% HOSPITAL (ver ADR-001, sección 2).
- Edición o eliminación del expediente base.

## Evidencia obligatoria

- [x] El PR viene desde la rama de módulo ya existente en `origin` (`feature/asii-10-...`).
- [x] No trabajé sobre `main`.
- [x] No mezclé cambios de otro módulo (solo un ajuste aditivo documentado sobre `medical_records`, ver ADR).
- [ ] Issue del módulo vinculado *(agregar `Closes #` si existe issue de GitHub para ASII-10)*.
- [x] Incluí RF/RNF y criterios de aceptación (`ESPECIFICACION.md`, más lo ya entregado en `docs/module-10/`).
- [x] Incluí evidencia de diseño: 5 diagramas UML en `docs/modulos/mod10/diagramas/`.
- [x] Documenté endpoints, payloads, respuestas, errores y permisos (`ESPECIFICACION.md`, sección 7).
- [ ] Capturas o demo *(no aplica: módulo sin UI en esta entrega; API cubierta por pruebas automatizadas)*.
- [ ] Ejecuté validaciones locales y pegué los resultados abajo *(pendiente — ver `EVIDENCIA.md`, sección 2)*.
- [x] Revisé roles, permisos, tenant y datos clínicos sensibles (roles Recepcionista/Médico/Enfermera/Admin,
      aislamiento por `tenant_id` en repositorio y checker de autorización).
- [x] Dejé notas de integración con otros módulos y riesgos pendientes (ver `ADR-001-arquitectura.md`,
      sección 7, y más abajo).

## Validación ejecutada

*(pendiente de completar con salida real — ver `docs/modulos/mod10/EVIDENCIA.md`, sección 2, antes de abrir
el PR)*

```bash
# comando: php artisan migrate:fresh --seed
# resultado: (pendiente)

# comando: php artisan test
# resultado: (pendiente)
```

## Contrato API

| Método | Ruta | Permiso/Rol | Descripción |
|---|---|---|---|
| POST | `/api/v1/medical-records` | `Recepcionista`, `Admin` | Abre el expediente longitudinal de un paciente |
| GET | `/api/v1/medical-records/{patient}` | `Médico`, `Enfermera`, `Admin` | Consulta el expediente longitudinal (autorizada y auditada) |

## Evidencia visual

No aplica: módulo sin UI en esta entrega vertical (backend + pruebas).

## Riesgos y pendientes

- Validación de ejecución real (migraciones/pruebas) pendiente de correr en un entorno con PHP/Composer/
  PostgreSQL disponibles (ver `EVIDENCIA.md`, sección 1).
- Generación de `record_number` puede colisionar bajo alta concurrencia entre distintos pacientes del mismo
  tenant (mitigado parcialmente con `lockForUpdate`; ver `ADR-001-arquitectura.md`, sección 7).
- Integración real de `medical_record_access_logs` con la auditoría transversal de ASII-22 queda para una
  fase posterior (outbox/event_id), documentada pero no implementada en este MVP.

## Checklist final del estudiante

- [x] El PR es pequeño y revisable (una capa por commit, 10 commits sustantivos).
- [ ] El código compila o las limitaciones están explicadas *(sintaxis revisada manualmente; ejecución real
      pendiente, ver arriba)*.
- [x] La documentación del módulo está actualizada (`docs/modulos/mod10/` + índice en `docs/module-10/README.md`).
- [x] No hay credenciales, tokens ni datos sensibles reales en el PR.
- [ ] Solicité revisión solo después de completar la evidencia mínima *(pendiente hasta correr la validación)*.
