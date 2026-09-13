# Declaración de uso de Inteligencia Artificial

**Estudiante:** Maryori Rachael Fajardo Paredes
**Módulo:** Expediente médico electrónico base (ASII-10)
**Actividad:** Avance de semana 1 y semana 2 — modelado de dominio, casos de uso, RF/RNF, principios SOLID, arquitectura, capa de aplicación/infraestructura/presentación y pruebas
**Fecha:** 7 de septiembre de 2026

## ¿Se utilizó IA?

Sí, la utilicé como herramienta de apoyo en distintas etapas del módulo. El análisis del proceso, las reglas de negocio y las decisiones de arquitectura son mías; usé la IA para acelerar la redacción, el formato de la documentación, la implementación del código a partir de mis decisiones ya tomadas, y para organizar este repositorio personal a partir del trabajo que ya tenía en el repositorio de equipo.

## Herramientas utilizadas

- **Claude Code** (modelo Claude Sonnet 5), como apoyo principal durante todo el módulo.
- **Gemini**, de forma puntual, para entender el uso de Git Worktree y apoyar la redacción inicial de la definición de actores.

## ¿Para qué la utilicé?

- **Organización de este repositorio:** le pedí que tomara mi trabajo ya realizado en la rama `feature/asii-10-expediente-medico-electronico-base-maryorifajardo14` del repositorio de equipo y lo organizara aquí en dos entregas (semana 1 y semana 2), con la estructura de ramas `developer` / `feature/...` pedida en el curso.
- **Implementación de código:** a partir de la arquitectura que yo definí (`docs/module-10/semana-3-arquitectura.md`), me apoyé en la herramienta para escribir las clases de dominio, aplicación, infraestructura y presentación, y para redactar las pruebas correspondientes.
- **Documentación:** apoyo en la redacción y formato de las tablas de RF/RNF, criterios de aceptación, el ejemplo del principio SOLID, el ADR de arquitectura y los diagramas.
- **Organización de commits y ramas en Git.**

## Revisión y ajustes manuales

- Revisé cada clase, migración y prueba para que los nombres de columnas, rutas y relaciones coincidieran con el esquema real del proyecto de equipo (`patients`, `users`, `medical_records`).
- Detecté y corregí, junto con la herramienta, un problema en el diseño de la bitácora de accesos (`medical_record_access_logs`): la primera versión forzaba una clave foránea obligatoria hacia `patients`, lo que habría roto el registro de intentos denegados contra un paciente inexistente.
- **Limitación que declaro explícitamente:** en el entorno donde preparé esta entrega no había PHP habilitado ni Composer/PostgreSQL disponibles, por lo que **no pude ejecutar realmente `php artisan migrate` ni `php artisan test`**. El detalle completo y los comandos pendientes de correr por mí están documentados en [`docs/modulos/mod10/EVIDENCIA.md`](docs/modulos/mod10/EVIDENCIA.md). No se presenta ninguna salida de ejecución como si fuera real.

## Validación humana y defensa

Entiendo el diseño de dominio, la arquitectura por capas y las decisiones de cada semana, y estoy en capacidad de explicarlas y defenderlas en la presentación oral sin depender de ninguna herramienta.

## Declaración de integridad

Confirmo que esta entrega es honesta y transparente, incluyendo la limitación de validación declarada arriba. Todo el proceso puede verificarse en el historial de commits de este repositorio.

Ver también las declaraciones específicas de cada semana: [`docs/module-10/DECLARACION_IA.md`](docs/module-10/DECLARACION_IA.md) y [`docs/modulos/mod10/DECLARACION_IA.md`](docs/modulos/mod10/DECLARACION_IA.md).
