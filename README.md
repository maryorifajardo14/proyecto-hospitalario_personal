# Expediente Médico Electrónico — Avance Semana 1 y 2

Repositorio personal de evidencia para el curso, con el avance del módulo **Expediente Médico Electrónico (ASII-10)** dentro del proyecto de equipo [Sistema Hospitalario Integrado](https://github.com/compilations-teams/sistema-hospitalario-integrado-SistenasII-2026).

Autora: Maryori Fajardo (`maryorifajardo14`).

## Contenido por semana

| Semana | Entrega | Evidencia |
|---:|---|---|
| 1 | Actores, alcance, casos de uso y modelado de dominio. | [docs/semana-01-actores-alcance-casos-de-uso.md](docs/semana-01-actores-alcance-casos-de-uso.md) · [docs/module-10/semana-1-casos-de-uso.md](docs/module-10/semana-1-casos-de-uso.md) · [Diagrama de casos de uso](docs/modulos/mod10/diagramas/01-casos-de-uso.md) · [Diagrama de clases/diseño](docs/modulos/mod10/diagramas/02-clases-diseno.md) · código en `app/Domain/MedicalRecord/`, `app/Models/` y pruebas unitarias de dominio en `tests/Unit/Domain/MedicalRecord/`. |
| 2 | RF/RNF, criterios de aceptación, principios SOLID, arquitectura, capa de aplicación/infraestructura/presentación y pruebas. | [docs/semana-02-rf-rnf-criterios-aceptacion-solid.md](docs/semana-02-rf-rnf-criterios-aceptacion-solid.md) · [docs/module-10/semana-2-rf-rnf-solid.md](docs/module-10/semana-2-rf-rnf-solid.md) · [ADR de arquitectura](docs/modulos/mod10/ADR-001-arquitectura.md) · [Especificación](docs/modulos/mod10/ESPECIFICACION.md) · [Evidencia de ejecución](docs/modulos/mod10/EVIDENCIA.md) · código en `app/Application/MedicalRecord/`, `app/Infrastructure/MedicalRecord/`, `app/Http/` y pruebas en `tests/Feature/MedicalRecord/`. |

## Estructura relevante

```
app/
  Domain/MedicalRecord/        # entidad, reglas de autorización, puertos, excepciones
  Application/MedicalRecord/   # casos de uso (Actions)
  Infrastructure/MedicalRecord/# adaptadores Eloquent / en memoria
  Http/                        # controlador API, request, resource
  Models/                      # modelos Eloquent
database/                      # migraciones y factories del módulo
tests/                         # pruebas unitarias, de aplicación e integración
docs/                          # documentación de análisis, diseño y evidencia por semana
```

## Contexto

Este código proviene de mi trabajo en la rama `feature/asii-10-expediente-medico-electronico-base-maryorifajardo14` del repositorio de equipo. Aquí se presenta de forma aislada, organizado en dos commits (`semana 1` y `semana 2`) como evidencia individual de avance para el curso.

Ver [DECLARACION_IA.md](docs/module-10/DECLARACION_IA.md) para la declaración de uso de IA en este módulo.
