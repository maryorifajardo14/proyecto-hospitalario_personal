# Guía breve para la defensa oral

**Estudiante:** Maryori Rachael Fajardo Paredes — Módulo: Expediente médico electrónico base
**Proceso:** Apertura y consulta autorizada del expediente longitudinal

Esta guía es un apoyo de estudio, no un guion para leer. En la defensa se debe explicar el proceso con
palabras propias, sin depender de la IA.

## 1. Resumen de una frase

"Un médico o enfermera solicita ver el historial completo de un paciente de su tenant; el sistema valida
que su sesión esté vigente, que su rol tenga permiso, y que el paciente exista en ese tenant — solo si las
tres condiciones se cumplen entrega el expediente consolidado, y **siempre**, se cumplan o no, deja un
registro de auditoría."

## 2. Preguntas que probablemente se harán y cómo responderlas

**¿Por qué el orden de validación es sesión → rol → existencia del paciente, y no al revés?**
Porque si se verificara primero si el paciente existe, un usuario sin permisos podría distinguir (comparando
un error 403 de "sin permiso" contra un 404 de "no existe") si un paciente concreto está registrado en un
tenant al que no debería tener visibilidad. Validar primero sesión y permisos evita esa fuga de información.

**¿Por qué UC6 y UC7 son `<<extend>>` y no `<<include>>`?**
Porque UC1 no depende de la denegación ni del error de paciente inexistente para completarse — esas son rutas
*alternativas y opcionales* que solo ocurren bajo una condición específica. `<<include>>` se reserva para lo
que siempre ocurre (autenticar, verificar RBAC, consolidar, auditar); `<<extend>>` para lo que ocurre solo a
veces.

**¿Por qué el registro de auditoría (UC5 / AuditService) aparece en las cuatro salidas posibles del
proceso?**
Porque la trazabilidad no debe depender de si el acceso fue exitoso o no: un intento denegado o fallido es
información de seguridad tan valiosa como uno exitoso.

**¿Qué pasa si el servicio de auditoría falla al registrar el evento?**
No está modelado explícitamente en este alcance (se asume que `AuditService` responde `ack` de forma
confiable); es una limitación declarada en la conclusión del documento. En una iteración futura se podría
agregar una rama de compensación (por ejemplo, cola de reintento) si el registro falla.

**¿Qué significa `tenant` en este contexto y por qué importa?**
Cada institución/sede del sistema es un tenant aislado; un usuario de un tenant no debe poder ver pacientes
de otro tenant, aunque tenga un rol con permisos, de ahí que la verificación RBAC y la búsqueda del paciente
siempre se acoten por `tenantId`.

## 3. Ejercicio de modificación (practicar antes de la defensa)

Se pedirá modificar **un elemento** de alguno de los tres diagramas en vivo. Ejemplos de modificaciones
razonables a practicar:

1. Agregar una nueva excepción: "el expediente está bloqueado por una investigación en curso" (nuevo caso de
   uso `<<extend>>` de UC4, nueva decisión en la actividad, nuevo bloque `alt` en la secuencia con respuesta
   `423 Locked`).
2. Cambiar el actor "Personal Clínico Autorizado" para diferenciar explícitamente "Médico" de "Enfermería"
   con permisos distintos (requeriría ajustar UC3 y la decisión de rol en la actividad).
3. Agregar un mensaje de *timeout* de base de datos en el diagrama de secuencia y su manejo.

Para cada práctica: identificar **en qué diagrama(s)** debe reflejarse el cambio y **por qué** debe
propagarse a los otros dos para mantener la trazabilidad exigida por la consigna.

## 4. Puntos de trazabilidad a tener frescos

- Los 10 requisitos funcionales (RF-01 a RF-10) y en qué elemento de cada diagrama aparecen — ver
  [`docs/matriz_trazabilidad.md`](matriz_trazabilidad.md).
- Los 4 eventos de auditoría (`ACCESO_CONCEDIDO`, `ACCESO_DENEGADO`, `PACIENTE_NO_ENCONTRADO`,
  `SESION_INVALIDA`) y en qué rama de cada diagrama se generan.
- Los 8 participantes del diagrama de secuencia y su correspondencia con los actores del caso de uso y los
  carriles de la actividad (tabla de consistencia en la matriz de trazabilidad).

## 5. Qué NO decir en la defensa

- No decir que "la IA generó todo": el proceso, sus reglas y decisiones fueron definidos por la estudiante;
  la IA fue apoyo de transcripción y formato (ver [`DECLARACION_IA.md`](../DECLARACION_IA.md)).
- No leer literalmente esta guía como respuesta — se espera una explicación con palabras propias.
