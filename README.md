# Análisis UML — Expediente médico electrónico base

**Estudiante:** Maryori Rachael Fajardo Paredes
**Usuario GitHub:** `maryorifajardo14`
**Módulo oficial:** Expediente médico electrónico base
**Proceso modelado:** Apertura y consulta autorizada del expediente longitudinal

## Portada del repositorio (completar tras el push a GitHub)

| Campo | Valor |
|---|---|
| URL del repositorio | `PENDIENTE: pegar aquí la URL de GitHub tras crear el remoto y compartirlo con el docente` |
| Rama evaluada | `main` |
| Commit / etiqueta evaluada | `PENDIENTE: pegar aquí el hash corto del commit final (ver docs/EVIDENCIA_GIT.md)` |

> Ver [instrucciones para completar estos campos](docs/EVIDENCIA_GIT.md#pendiente-tras-crear-el-remoto).

## Índice del repositorio

```
proyecto-hospitalario_personal/
├── README.md                              (este archivo)
├── DECLARACION_IA.md                      (declaración transparente de uso de IA)
├── diagramas/
│   ├── caso_uso.puml                      (fuente editable UML — casos de uso)
│   ├── actividad.puml                     (fuente editable UML — actividad)
│   ├── secuencia.puml                     (fuente editable UML — secuencia)
│   └── exportados/
│       ├── caso_uso.png
│       ├── actividad.png
│       └── secuencia.png
└── docs/
    ├── Documento_Final_Fajardo_Maryori.md     (documento final — fuente editable)
    ├── Documento_Final_Fajardo_Maryori.docx   (documento final — entregable)
    ├── matriz_trazabilidad.md                 (requisito → diagrama → elemento)
    ├── EVIDENCIA_EJECUCION.md                 (validación/renderizado de los diagramas)
    ├── EVIDENCIA_GIT.md                       (historial y árbol Git)
    └── guia_defensa_oral.md                   (guía breve para la defensa oral)
```

## Cómo regenerar los diagramas

Los diagramas fuente están en [`diagramas/*.puml`](diagramas/) (formato PlantUML, texto plano editable).
Para regenerarlos como PNG:

```bash
java -jar plantuml.jar -tpng -o exportados diagramas/*.puml
```

## Cómo regenerar el documento final en DOCX

```bash
pandoc docs/Documento_Final_Fajardo_Maryori.md -o docs/Documento_Final_Fajardo_Maryori.docx --resource-path=docs
```

## Declaración de IA

Ver [`DECLARACION_IA.md`](DECLARACION_IA.md).

## Lista de comprobación de entrega

- [x] Portada e índice actualizados (ver `docs/Documento_Final_Fajardo_Maryori.md` y este README).
- [x] Consigna individual modelada en los tres diagramas UML.
- [ ] Repositorio remoto compartido con el docente, commit identificable y evidencia Git final
      (completar `docs/EVIDENCIA_GIT.md` y los campos `PENDIENTE` tras el push).
- [x] Fuentes editables (`.puml`, `.md`) y datos exclusivamente ficticios.
- [x] Declaración transparente de IA (`DECLARACION_IA.md`).
- [x] Guía de preparación para la defensa oral (`docs/guia_defensa_oral.md`).
