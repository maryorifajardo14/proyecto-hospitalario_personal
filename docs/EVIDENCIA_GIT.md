# Evidencia Git

**Repositorio:** análisis UML — módulo Expediente médico electrónico base (Maryori Rachael Fajardo Paredes)
**Rama:** `main`

## Historial legible (`git log --oneline`)

```
b332431 docs: guia breve de preparacion para la defensa oral
1bfa84f docs: declaracion transparente de uso de IA
4c29dca docs: documento final (portada, desarrollo, conclusion, bibliografia)
eef54c3 docs: evidencia de ejecucion y validacion de los diagramas
4e49d45 docs: matriz de trazabilidad requisito -> diagrama -> elemento
df09375 build: renderizar diagramas UML a PNG con PlantUML
23542dd feat(uml): diagrama de secuencia con mensajes y validaciones
35723e4 feat(uml): diagrama de actividad con decisiones y excepciones
acafaea feat(uml): diagrama de casos de uso del proceso de apertura de expediente
3b76b78 chore: estructura inicial del repositorio y README
```

## Historial con autor y fecha

```
b332431 | maryorifajardo14 <mfajardop1@miumg.edu.gt> | 2026-08-05 | docs: guia breve de preparacion para la defensa oral
1bfa84f | maryorifajardo14 <mfajardop1@miumg.edu.gt> | 2026-08-05 | docs: declaracion transparente de uso de IA
4c29dca | maryorifajardo14 <mfajardop1@miumg.edu.gt> | 2026-08-05 | docs: documento final (portada, desarrollo, conclusion, bibliografia)
eef54c3 | maryorifajardo14 <mfajardop1@miumg.edu.gt> | 2026-08-05 | docs: evidencia de ejecucion y validacion de los diagramas
4e49d45 | maryorifajardo14 <mfajardop1@miumg.edu.gt> | 2026-08-05 | docs: matriz de trazabilidad requisito -> diagrama -> elemento
df09375 | maryorifajardo14 <mfajardop1@miumg.edu.gt> | 2026-08-05 | build: renderizar diagramas UML a PNG con PlantUML
23542dd | maryorifajardo14 <mfajardop1@miumg.edu.gt> | 2026-08-05 | feat(uml): diagrama de secuencia con mensajes y validaciones
35723e4 | maryorifajardo14 <mfajardop1@miumg.edu.gt> | 2026-08-05 | feat(uml): diagrama de actividad con decisiones y excepciones
acafaea | maryorifajardo14 <mfajardop1@miumg.edu.gt> | 2026-08-05 | feat(uml): diagrama de casos de uso del proceso de apertura de expediente
3b76b78 | maryorifajardo14 <mfajardop1@miumg.edu.gt> | 2026-08-05 | chore: estructura inicial del repositorio y README
```

## Árbol de archivos versionados (`git ls-files`)

```
.gitignore
DECLARACION_IA.md
README.md
diagramas/actividad.puml
diagramas/caso_uso.puml
diagramas/exportados/actividad.png
diagramas/exportados/caso_uso.png
diagramas/exportados/secuencia.png
diagramas/secuencia.puml
docs/Documento_Final_Fajardo_Maryori.md
docs/Documento_Final_Fajardo_Maryori.pdf
docs/EVIDENCIA_EJECUCION.md
docs/EVIDENCIA_GIT.md
docs/guia_defensa_oral.md
docs/matriz_trazabilidad.md
```

## Pendiente tras crear el remoto

Este historial se generó en el repositorio local. Antes de la entrega, la estudiante debe:

1. Crear un repositorio personal en GitHub (usuario `maryorifajardo14`) y compartirlo con el docente.
2. Ejecutar `git remote add origin <URL>` y `git push -u origin main`.
3. Actualizar en [`README.md`](../README.md) y en la portada de
   [`docs/Documento_Final_Fajardo_Maryori.md`](Documento_Final_Fajardo_Maryori.md) los campos `PENDIENTE`
   con la URL real del repositorio y el hash del commit final evaluado.
4. Volver a ejecutar `git log --oneline` y actualizar este archivo si se agregan commits posteriores
   (por ejemplo, el commit que fija la URL/commit evaluado en la portada), y crear una etiqueta de entrega,
   por ejemplo:
   ```
   git tag -a entrega-modulo-expediente -m "Entrega: analisis UML expediente longitudinal"
   git push origin entrega-modulo-expediente
   ```
