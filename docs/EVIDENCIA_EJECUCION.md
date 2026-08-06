# Evidencia de ejecución y validación de los diagramas UML

Los tres diagramas del proceso "apertura y consulta autorizada del expediente longitudinal" se validaron y
renderizaron con la herramienta **PlantUML** a partir de sus fuentes editables en [`diagramas/`](../diagramas/).

## Entorno de ejecución

```
$ java -jar plantuml.jar -version
PlantUML version 1.2024.7 (Sat Sep 07 05:18:17 CST 2024)
(GPL source distribution)
Java Runtime: Java(TM) SE Runtime Environment
JVM: Java HotSpot(TM) 64-Bit Server VM
Default Encoding: UTF-8
Language: es
Country: GT

PLANTUML_LIMIT_SIZE: 4096

Dot version: dot - graphviz version 2.44.1 (20200629.0846)
Installation seems OK. File generation OK
```

## Validación sintáctica (checkonly)

```
$ java -jar plantuml.jar -checkonly diagramas/caso_uso.puml diagramas/actividad.puml diagramas/secuencia.puml
VALIDACION_OK: las 3 fuentes .puml son sintacticamente correctas
```

Las tres fuentes `.puml` compilaron sin errores (`-checkonly` no reportó ninguna excepción de sintaxis).

## Renderizado a imagen (evidencia de ejecución)

```
$ java -jar plantuml.jar -tpng -o exportados diagramas/*.puml
```

Resultado — archivos generados en `diagramas/exportados/`:

```
actividad.png   114 938 bytes
caso_uso.png    120 583 bytes
secuencia.png   146 504 bytes
```

Cada imagen fue inspeccionada visualmente para confirmar que:

- Los actores y casos de uso del diagrama de casos de uso coinciden con los del resto de diagramas.
- Las tres decisiones y las tres rutas de excepción del diagrama de actividad se representan sin cruces
  ambiguos y con un resultado final explícito en cada rama.
- El diagrama de secuencia representa los mismos ocho participantes previstos, con los tres bloques `alt`
  correspondientes a las mismas tres validaciones del diagrama de actividad.

Esta evidencia demuestra que los diagramas no son únicamente capturas de pantalla estáticas, sino artefactos
generados de manera reproducible a partir de una fuente de texto editable versionada en el repositorio.
