# Trazabilidad de la sesión 7 — HegiaStock

## 1. Objetivo

Registrar las decisiones tomadas al revisar las tareas originales de HegiaStock, indicando cuáles se mantienen, adaptan, dividen o eliminan, su relación con las historias de usuario y la forma de verificar los resultados.

## 2. Tabla de trazabilidad

| N.º tarea | Título corto | Historia (H-xx) | Decisión | Justificación | Verificación (URL y resultado esperado) |
|---|---|---|---|---|---|
| 1 | Estructura e infraestructura base | Ninguna pendiente específica | ELIMINAR · ya realizada | La estructura inicial y la configuración de Laravel ya se realizaron. No corresponde repetir la creación del proyecto ni configurar nuevamente el entorno. | `http://127.0.0.1:8000/` — comprobar que la aplicación responde, si la ruta principal está configurada. |
| 2 | Modelos y esquemas de datos | HU01, HU02, HU03 y HU05, según las tablas requeridas | DIVIDIR y ADAPTAR | La tarea original agrupa modelos y esquemas para SQLAlchemy y SQLite. Se debe dividir según las necesidades de cada funcionalidad y utilizar Eloquent con las tablas existentes de MySQL. No crear migraciones ni seeders. | Pendiente de implementación. Verificar cada funcionalidad mediante su ruta correspondiente y confirmar que consulta las tablas existentes. |
| 3 | Filtro PDP y validaciones de privacidad | HU07 | ADAPTAR | La protección de datos personales es un requisito del proyecto, pero la implementación debe adecuarse a Laravel. Conservar únicamente las validaciones y controles necesarios, sin incorporar componentes innecesarios de la arquitectura anterior. | Pendiente de definir las pruebas. Resultado esperado: no registrar ni persistir datos personales de clientes en los componentes cubiertos por HU07. |
| 4 | Consulta de stock | HU01 | DIVIDIR y ADAPTAR | Es la primera funcionalidad prioritaria. La tarea original separa repositorio, servicio, ruteador y plantilla; se debe convertir en una rebanada vertical pequeña que atraviese ruta, controlador, modelo y vista Blade. | `http://127.0.0.1:8000/stock?sku=HEG-001` — mostrar los datos del producto existente. La consulta de un producto inexistente debe responder con HTTP 404 cuando se solicite un recurso individual. Pendiente de implementar y probar. |
| 5 | Consulta de ubicación | HU02 | DIVIDIR y ADAPTAR | La funcionalidad sigue siendo pertinente, pero debe implementarse con Laravel y Eloquent. Dividirla en incrementos pequeños que incluyan la consulta y su presentación. | `http://127.0.0.1:8000/ubicacion?sku=HEG-001` — mostrar la ubicación del producto si existe y está registrada. Pendiente de implementar y verificar. |
| 6 | Consulta por categoría | HU03 | DIVIDIR y ADAPTAR | La consulta por categoría corresponde a una historia de usuario existente. Se debe adaptar a las tablas reales y conservar los criterios de aceptación definidos en requirements.md. | `http://127.0.0.1:8000/categoria?nombre=Construccion` — mostrar los productos de esa categoría que cumplan los criterios de HU03. Pendiente de implementar y verificar. |
| 7 | Punto de control de módulos de consulta | HU01, HU02 y HU03 | ELIMINAR como tarea independiente | Es una tarea de verificación general, no una funcionalidad de usuario independiente. Las pruebas deben incorporarse a cada historia y a su respectiva tarea de implementación. No crear `database/seed.sql`, porque se deben utilizar los datos existentes. | Verificar las rutas de HU01, HU02 y HU03 después de implementar cada funcionalidad. Registrar los resultados en sus pruebas correspondientes. |
| 8 | Tickets de verificación | HU05 y HU07 | DIVIDIR y ADAPTAR | La creación y consulta de tickets debe conservar los criterios de HU05 y los controles de privacidad de HU07. Se debe dividir en incrementos pequeños y utilizar la estructura existente de MySQL. | `http://127.0.0.1:8000/tickets` — comprobar la lista de tickets. Para la creación, verificar el envío del formulario y la generación del ticket según HU05. Pendiente de implementar y probar. |
| 9 | Ruteador de salud y aplicación principal | Sin historia específica identificada | ELIMINAR como tarea independiente | La tarea original pertenece a la estructura de FastAPI y no se ha identificado una historia de usuario específica para mantenerla como funcionalidad independiente. Las comprobaciones técnicas necesarias se realizarán durante la validación de la aplicación Laravel. | Verificar que Laravel inicia y que las rutas implementadas responden. No se asigna una URL de salud independiente mientras no exista un requisito que la justifique. |
| 10 | Plantillas y estilos generales | HU01, HU02, HU03 y HU05, según corresponda | ELIMINAR como tarea independiente e integrar | La interfaz y los estilos son necesarios, pero la tarea agrupa cambios generales sin constituir una rebanada vertical. Las vistas y sus validaciones de interfaz deben desarrollarse dentro de cada historia. | Verificar las vistas de cada historia en su URL correspondiente y comprobar que los formularios, etiquetas y mensajes se muestran correctamente. |
| 11 | Verificación final de la Unidad 2 | HU01, HU02, HU03, HU05, HU07 y HU09, según alcance | DIVIDIR e integrar | La comprobación final agrupa pruebas funcionales, privacidad, configuración y rendimiento. Las pruebas deben relacionarse con cada historia y ejecutarse conforme se implementa, en lugar de dejar toda la validación para una única tarea final. | Ejecutar las pruebas de las historias implementadas, verificar que `.env` no esté versionado y comprobar los criterios de aceptación aplicables. Registrar los resultados reales; no dar por aprobadas pruebas aún no ejecutadas. |

## 3. Hallazgos

1. **Diferencia en los identificadores:** la guía utiliza el formato `H-xx`, mientras que `requirements.md` identifica las historias como `HU01`, `HU02`, etc. Se conservan los identificadores que existen en el archivo de requisitos; se debe confirmar si la docente exige una normalización del formato.

2. **Diferencia tecnológica:** `tasks.md` fue elaborado para FastAPI, SQLAlchemy y SQLite, pero las reglas técnicas actuales de la sesión 7 establecen Laravel, Eloquent y MySQL. Las tareas pendientes deben adaptarse a la tecnología vigente.

3. **Tareas demasiado amplias:** los modelos, los módulos y la interfaz se encuentran separados por capas técnicas. Deben reorganizarse en incrementos verticales pequeños y verificables.

4. **Tareas de infraestructura y verificación:** la creación inicial del proyecto ya está realizada. Los puntos de control, la interfaz general y las pruebas finales deben integrarse en las tareas relacionadas con las historias correspondientes cuando sea posible.

5. **Unidad 3:** las tareas diferidas de MCP, RAG, Gemini, clasificación de intenciones y orquestación conversacional permanecen fuera del alcance de esta sesión, conforme a la planificación original.

## 4. Orden de implementación propuesto

La primera tarea funcional será **HU01 — Consulta de stock disponible**, porque permite verificar el acceso a los datos existentes y construir una funcionalidad completa de extremo a extremo. Después se abordarán HU02, HU03 y HU05, manteniendo los controles de privacidad de HU07 y las verificaciones aplicables a cada incremento.

Las URL de la tabla son direcciones previstas para verificar las funcionalidades una vez implementadas; no significan que esas rutas ya existan ni que las pruebas hayan sido aprobadas.
