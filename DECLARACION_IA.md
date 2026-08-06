# Declaración de uso de Inteligencia Artificial

**Estudiante:** Maryori Rachael Fajardo Paredes
**Módulo:** Expediente médico electrónico base
**Actividad:** Análisis UML (casos de uso, actividad, secuencia) del proceso "apertura y consulta autorizada del expediente longitudinal"
**Fecha:** 05 de agosto de 2026

## ¿Se utilizó IA?

**Sí**, la utilicé como una herramienta de apoyo. El análisis del proceso (quiénes son los actores, el orden de las validaciones, las decisiones y las excepciones) lo definí yo a partir de la tarea asignada. Usé la IA únicamente para agilizar la escritura del código y darle formato al documento, no para que hiciera el análisis lógico por mí.

## Herramienta utilizada

- **Claude Code** (modelo Claude Sonnet 5), ejecutado en la terminal.

## ¿Para qué la utilicé?

- **Generación de código UML:** Le pasé la lógica del proceso que yo ya había estructurado y le pedí que me ayudara a escribir la sintaxis en PlantUML para armar los tres diagramas más rápido.
- **Formato y estructura:** Me apoyé en la herramienta para darle formato a la matriz de trazabilidad y armar la estructura básica de este documento (portada e índice).
- **Apoyo técnico:** Me ayudó con comandos específicos para convertir el documento final a PDF y para organizar mis commits en Git.

## Resumen del Prompt utilizado

Le expliqué a la IA de qué trataba la actividad (el módulo de expediente médico) y le detallé las reglas de negocio que yo definí para el proceso de apertura y consulta. Le indiqué exactamente qué quería validar (sesión, rol y existencia del paciente) y cómo debían registrarse las auditorías. A partir de esa instrucción, le pedí que me generara el código de PlantUML utilizando datos ficticios.

## Revisión y ajustes manuales

- Acepté el código de PlantUML porque reflejaba correctamente el proceso que yo diseñé.
- **Revisé visualmente las imágenes generadas** para asegurarme de que los actores, mensajes y excepciones tuvieran sentido y fueran consistentes entre los tres diagramas.
- Redacté la introducción y la conclusión del documento final **con mis propias palabras** para plasmar mi entendimiento del tema.
- Dejé pendiente completar manualmente los enlaces al repositorio remoto una vez que lo comparta. 
- Revisé que los nombres de los servicios en los diagramas (como `AuthService`, `RBACService`, etc.) coincidan con la arquitectura real de nuestro proyecto grupal.

## Validación humana y Defensa

Entiendo perfectamente cada parte de los diagramas y de la matriz. Estoy preparada para explicar mis decisiones de diseño, modificar cualquier elemento si me lo solicitan y defender mi trabajo en la presentación oral sin depender de ninguna herramienta, tal como lo indica la guía de evaluación.

## Declaración de integridad

Confirmo que mi entrega es honesta y transparente. No he incluido texto oculto ni engañoso. Todo mi trabajo y el proceso de creación se puede verificar claramente en mi historial de commits en Git.