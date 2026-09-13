# Expediente Médico Electrónico — Avance Semana 1 a 4

Repositorio personal de evidencia para el curso, con el avance del módulo **Expediente Médico Electrónico (ASII-10)** dentro del proyecto de equipo [Sistema Hospitalario Integrado](https://github.com/compilations-teams/sistema-hospitalario-integrado-SistenasII-2026).

Autora: Maryori Fajardo (`maryorifajardo14`).

## Contenido por semana

| Semana | Entrega | Evidencia |
|---:|---|---|
| 1 | Actores, alcance, casos de uso y modelado de dominio. | [docs/semana-01-actores-alcance-casos-de-uso.md](docs/semana-01-actores-alcance-casos-de-uso.md) · [docs/module-10/semana-1-casos-de-uso.md](docs/module-10/semana-1-casos-de-uso.md) · [Diagrama de casos de uso](docs/modulos/mod10/diagramas/01-casos-de-uso.md) · [Diagrama de clases/diseño](docs/modulos/mod10/diagramas/02-clases-diseno.md) · código en `app/Domain/MedicalRecord/`, `app/Models/` y pruebas unitarias de dominio en `tests/Unit/Domain/MedicalRecord/`. |
| 2 | RF/RNF, criterios de aceptación y un principio SOLID aplicado al módulo. | [docs/semana-02-rf-rnf-criterios-aceptacion-solid.md](docs/semana-02-rf-rnf-criterios-aceptacion-solid.md) · [docs/module-10/semana-2-rf-rnf-solid.md](docs/module-10/semana-2-rf-rnf-solid.md) |
| 3 | Vista arquitectónica del módulo. | [docs/module-10/semana-3-arquitectura.md](docs/module-10/semana-3-arquitectura.md) · [ADR de arquitectura](docs/modulos/mod10/ADR-001-arquitectura.md) · [Diagrama de secuencia](docs/modulos/mod10/diagramas/03-secuencia.md) · [Diagrama de componentes](docs/modulos/mod10/diagramas/04-componentes.md) · [Vista de datos](docs/modulos/mod10/diagramas/05-vista-datos.md) |
| 4 | Diseño por capas y responsabilidades (UI, API, lógica, persistencia). | [Especificación](docs/modulos/mod10/ESPECIFICACION.md) · [Evidencia de ejecución](docs/modulos/mod10/EVIDENCIA.md) · [Borrador de PR](docs/modulos/mod10/PR_BODY.md) · código en `app/Application/MedicalRecord/`, `app/Infrastructure/MedicalRecord/`, `app/Http/` y pruebas en `tests/Feature/MedicalRecord/`. |

## Estructura relevante

```
app/
  Domain/MedicalRecord/        # entidad, reglas de autorización, puertos, excepciones (semana 1)
  Application/MedicalRecord/   # casos de uso (Actions) (semana 4)
  Infrastructure/MedicalRecord/# adaptadores Eloquent / en memoria (semana 4)
  Http/                        # controlador API, request, resource (semana 4)
  Models/                      # modelos Eloquent (semana 1)
database/                      # migraciones y factories del módulo (semana 1)
tests/                         # pruebas unitarias (semana 1), de aplicación e integración (semana 4)
docs/                          # documentación de análisis, diseño, arquitectura y evidencia por semana
```

## Contexto

Este código proviene de mi trabajo en la rama `feature/asii-10-expediente-medico-electronico-base-maryorifajardo14` del repositorio de equipo. Aquí se presenta de forma aislada, organizado en commits por semana (`semana 1` a `semana 4`) como evidencia individual de avance para el curso.

Ver [DECLARACION_IA.md](DECLARACION_IA.md) para la declaración de uso de IA de este repositorio, y [docs/module-10/DECLARACION_IA.md](docs/module-10/DECLARACION_IA.md) para la declaración específica del módulo.
