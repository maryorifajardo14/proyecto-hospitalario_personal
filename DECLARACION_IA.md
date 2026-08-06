# Declaración de uso de Inteligencia Artificial

**Estudiante:** Maryori Rachael Fajardo Paredes
**Módulo:** Expediente médico electrónico base
**Actividad:** Análisis UML (casos de uso, actividad, secuencia) del proceso "apertura y consulta autorizada
del expediente longitudinal"
**Fecha:** 05 de agosto de 2026

## ¿Se utilizó IA?

**Sí**, como herramienta de apoyo puntual. El análisis del proceso (actores involucrados, orden de las
validaciones, decisiones y excepciones a modelar, y el criterio de qué debía quedar dentro o fuera del
alcance del módulo) fue definido por la estudiante a partir de la consigna individual asignada. La IA se usó
como asistente de apoyo en tareas mecánicas y de formato, no como autora del análisis.

## Herramienta utilizada

- **Claude Code** (modelo Claude Sonnet 5), de Anthropic, ejecutado como agente de línea de comandos.

## Propósito del uso (apoyo puntual)

- **Redacción de la sintaxis PlantUML** de los tres diagramas, a partir del proceso ya definido por la
  estudiante (actores, pasos, decisiones y excepciones), para acelerar la escritura del código UML.
- **Ejecución y renderizado** de esas fuentes con la herramienta PlantUML (instalación local de Java +
  PlantUML + Graphviz) para obtener las imágenes de evidencia.
- **Apoyo de formato** en la matriz de trazabilidad y en el documento final (portada, índice, estructura de
  secciones), y en la generación de este archivo de declaración.
- **Apoyo técnico** en la conversión del documento a formato DOCX y en la organización de la evidencia Git.

## Prompt relevante (resumen)

La estudiante compartió con la IA el enunciado de la guía de actividad y su consigna individual (módulo 10 —
Expediente médico electrónico base), e indicó el proceso a modelar y sus reglas ("apertura y consulta
autorizada del expediente longitudinal", validando sesión, rol y existencia del paciente en ese orden, con
registro de auditoría en cada salida), pidiendo apoyo para transcribir ese modelo a los tres diagramas UML,
la matriz de trazabilidad y el documento final, con datos exclusivamente ficticios.

## Partes aceptadas y partes revisadas por la estudiante

- Se aceptó el código PlantUML generado como transcripción fiel del proceso ya definido por la estudiante.
- La estudiante **revisó visualmente las tres imágenes renderizadas** para confirmar que actores, pasos,
  mensajes y excepciones son consistentes entre los tres diagramas, y **debe ajustar con sus propias
  palabras** la introducción y la conclusión del documento final antes de la entrega, de modo que reflejen su
  propio entendimiento del proceso.
- Los campos marcados como `PENDIENTE` en la portada del documento final (URL del repositorio remoto,
  commit/etiqueta evaluada) deben completarse manualmente por la estudiante una vez compartido el repositorio
  con el docente.
- Los nombres de servicios/entidades usados en el diagrama de secuencia (`AuthService`, `RBACService`,
  `ExpedienteService`, `AuditService`) deben verificarse frente a los nombres reales del proyecto grupal y
  ajustarse si difieren.

## Validación humana

La estudiante es responsable del contenido final de los tres diagramas, la matriz de trazabilidad y el
documento, y debe poder explicar cada decisión de diseño, modificar un elemento y responder preguntas en la
defensa oral **sin depender de la IA**, conforme a [`docs/guia_defensa_oral.md`](docs/guia_defensa_oral.md).

## Declaración de integridad

Se declara que ningún contenido generado con apoyo de IA fue insertado como texto oculto, blanco, engañoso,
adversarial o con instrucciones trampa. Todo el contenido de este repositorio es visible, legible y
verificable en el historial Git.
