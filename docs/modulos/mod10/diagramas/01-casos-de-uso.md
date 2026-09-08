# 1. Diagrama de casos de uso — ASII-10

Deriva de `docs/module-10/semana-1-casos-de-uso.md`, sin cambios en actores ni casos de uso (siguen siendo
los tres validados en Semana 1); se agrega el límite explícito del sistema (recuadro) y se anota qué caso de
uso corresponde a qué endpoint real del contrato (ver `ESPECIFICACION.md`, sección 7).

```mermaid
flowchart LR
    RECEPCIONISTA["Recepcionista"]
    MEDICO["Médico / Enfermera / Admin"]
    SEGURIDAD["Sistema de Seguridad\n(MedicalRecordAuthorizationChecker)"]

    subgraph SHI["Límite del sistema — ASII-10 Expediente médico electrónico base"]
        UC01(["UC-10-01 Apertura de expediente\nPOST /api/v1/medical-records"])
        UC02(["UC-10-02 Consulta longitudinal\nGET /api/v1/medical-records/{patient}"])
        UC03(["UC-10-03 Validar autorización"])
    end

    RECEPCIONISTA --> UC01
    MEDICO --> UC02

    UC01 -. "«include»" .-> UC03
    UC02 -. "«include»" .-> UC03

    SEGURIDAD --> UC03
```

**Narrativa (sin cambios respecto a Semana 1):** la recepcionista abre el expediente una sola vez por
paciente; el rol clínico autorizado lo consulta cuantas veces lo necesite; ambos casos de uso incluyen
obligatoriamente la validación de autorización.
