# ASII-10 — Diagramas UML (actividad integradora)

Los cinco artefactos piden derivar y actualizar la vista del módulo con nombres reales del código, no copiar
los diagramas generales del diagnóstico. Formato: Mermaid (fuente editable = el propio bloque ` ```mermaid `
en cada archivo `.md`; se renderiza directamente en GitHub, GitLab, VS Code y Artifacts sin herramientas
externas).

| # | Archivo | Contenido |
|---|---|---|
| 1 | [`01-casos-de-uso.md`](./01-casos-de-uso.md) | Casos de uso acotados al módulo, actores y límite del sistema |
| 2 | [`02-clases-diseno.md`](./02-clases-diseno.md) | Clases/objetos de valor, caso de uso, interfaz Repository y adaptadores (nombres reales del código) |
| 3 | [`03-secuencia.md`](./03-secuencia.md) | Camino principal (apertura exitosa) y excepción (duplicado / acceso denegado) |
| 4 | [`04-componentes.md`](./04-componentes.md) | Componentes/capas del módulo dentro del SHI, límite HOSPITAL, sin CENTRAL |
| 5 | [`05-vista-datos.md`](./05-vista-datos.md) | Entidad-relación: `medical_records`, `medical_record_access_logs`, claves e índices |

Los diagramas de casos de uso y de componentes derivan de los ya elaborados en
[`docs/module-10/semana-1-casos-de-uso.md`](../../module-10/semana-1-casos-de-uso.md) y
[`docs/module-10/semana-3-arquitectura.md`](../../module-10/semana-3-arquitectura.md); aquí se actualizan
para reflejar exactamente las clases e infraestructura implementadas en esta entrega (Repository,
`InMemoryMedicalRecordRepository`, `medical_record_access_logs`).
