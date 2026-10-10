# Implementation Plan: HegiaStock — Sesión 7 (Unidad 2)

## Overview

Plan de implementación depurado para la Sesión 7 de HegiaStock (Inversiones Hegiapesa S.A.C.).
El plan utiliza la tecnología vigente según `tech.md` (sección «Reglas de la sesión 7»): **Laravel 11 / PHP 8.2+**, **Eloquent ORM** y la base de datos **MySQL existente** (`hegiastock` en XAMPP / MariaDB). Las tareas de infraestructura ya realizadas (creación del proyecto Laravel en `web/`, configuración de `.env`, base de datos con tablas y datos) quedan excluidas del plan.

Las funcionalidades se estructuran como **rebanadas verticales completas** (ruta → controlador → modelo Eloquent → vista Blade) orientadas a historias de usuario, priorizando en primer lugar la consulta de stock (HU01). El modelo `Producto` creado en la Tarea 1 es prerequisito de las Tareas 2, 3 y 4.

> **Nota sobre discrepancia de identificadores (Hallazgo de trazabilidad):**
> La guía de la sesión 7 emplea el formato `[H-xx]` (p. ej. `H-01`, `H-02`), mientras que `requirements.md` y `design.md` emplean `HU01`, `HU02`, etc. En este plan se conservan ambos identificadores explícitamente (ej. `HU01 [guía: H-01]`) para garantizar la trazabilidad bidireccional, registrando la unificación formal como **pendiente de confirmación con la docente**.

---

## Reglas y Fronteras Técnicas (Sesión 7)

1. **Ubicación del código:** La aplicación Laravel reside en `web/`. Todas las rutas de implementación son relativas a `web/`.
2. **Base de datos existente:** La base de datos `hegiastock` ya cuenta con tablas y datos poblados en MySQL. **Prohibido crear migraciones o seeders**.
3. **Modelos Eloquent:** Operar sobre tablas existentes mediante `protected $table` y configurar `public $timestamps = false` en tablas sin marcas de tiempo estándar.
4. **Consultas seguras:** Uso exclusivo de Eloquent o Query Builder parametrizado. Prohibido el uso de `DB::raw` con datos de usuario o concatenación SQL.
5. **Validación y errores HTTP:** Todo parámetro de entrada se valida. Si el recurso consultado no existe, responder con `abort(404)`.
6. **Comentarios de cabecera:** Todo archivo nuevo debe iniciar con `// Historia: H-xx`.
7. **Frontera de Unidad 2:** Quedan estrictamente excluidos el agente conversacional, modelos de IA (Gemini), ChromaDB/RAG y el protocolo MCP hasta la Unidad 3 (ADR-004).

---

## Tasks

- [x] 1. Implementar consulta vertical de stock disponible (HU01 [guía: H-01])
  - **Historia de usuario:** HU01 — Consulta de stock disponible [guía: H-01]
  - **Objetivo:** Construir la primera rebanada vertical completa que permita al Vendedor consultar en tiempo real el stock disponible de productos por código SKU exacto o coincidencia de nombre comercial sobre las tablas existentes de MySQL, devolviendo HTTP 404 si el recurso solicitado no existe.
  - [x] 1.1 Crear modelos Eloquent para stock sobre tablas existentes
    - Creado `web/app/Models/Producto.php` (`protected $table = 'producto'`, `$primaryKey = 'id_producto'`, relaciones `categoria()` BelongsTo y `unidadMedida()` BelongsTo, `$timestamps = false`).
    - Creado `web/app/Models/Categoria.php` (`protected $table = 'categoria'`, `$primaryKey = 'id_categoria'`, `$timestamps = false`).
    - Creado `web/app/Models/UnidadMedida.php` (`protected $table = 'unidad_medida'`, `$primaryKey = 'id_unidad'`, `$timestamps = false`).
    - _Requisitos: HU01 CA1, CA2_
  - [x] 1.2 Implementar ruta y controlador `StockController` con validación y manejo de 404
    - Creado `web/app/Http/Controllers/StockController.php` con método `index(Request $request)`.
    - Ruta `GET /stock` registrada en `web/routes/web.php` con nombre `stock.index`.
    - SKU por parámetro exacto: `abort(404)` si no existe.
    - Nombre comercial: búsqueda `LIKE` parametrizada; aviso si vacío; lista si múltiples; ficha si único.
    - _Requisitos: HU01 CA1, CA2, CA3, CA4_
  - [x] 1.3 Diseñar vista Blade de consulta de stock
    - Creado `web/resources/views/stock.blade.php` con formulario accesible (fuente 16 px, contraste WCAG AA, `<label>` asociados con `for`/`id`, `role="search"`, `aria-live="polite"`).
    - Renderiza ficha de producto (SKU, nombre comercial, categoría, cantidad disponible y unidad de medida).
    - Renderiza lista de selección en caso de múltiples coincidencias con enlaces directos al detalle por SKU.
    - _Requisitos: HU01 CA1, CA3_
  - **Verificación manual — ejecutada:**
    - `/stock` → Formulario visible.
    - `/stock?sku=HEG-001` → Producto encontrado.
    - `/stock?sku=INEXISTENTE` → HTTP 404.
    - `/stock?q=cemento` → Búsqueda por nombre funcional.
  - **Archivos creados/modificados:**
    - `web/app/Models/Producto.php` — nuevo
    - `web/app/Models/Categoria.php` — nuevo
    - `web/app/Models/UnidadMedida.php` — nuevo
    - `web/app/Http/Controllers/StockController.php` — nuevo
    - `web/resources/views/stock.blade.php` — nuevo
    - `web/routes/web.php` — modificado (agregada ruta `/stock`)

- [ ] 2. Implementar consulta vertical de ubicación física de producto (HU02 [guía: H-02])
  - **Historia de usuario:** HU02 — Consulta de ubicación física de producto [guía: H-02]
  - **Objetivo:** Permitir al Vendedor consultar el pasillo y estante de un producto mediante una rebanada vertical en Laravel, formateando la respuesta exactamente como «Pasillo {N}, Estante {código}» y respondiendo con 404 si el producto o su ubicación no existen.
  - [ ] 2.1 Crear modelo Eloquent `Ubicacion` y relacionarlo con `Producto`
    - Crear `web/app/Models/Ubicacion.php` (`protected $table = 'ubicacion'`, clave primaria `id_ubicacion`, relación `producto()`, `$timestamps = false`).
    - Agregar relación `ubicacion()` en `Producto.php` (`hasOne(Ubicacion::class, 'id_producto')`).
    - _Requisitos: HU02 CA1_
  - [ ] 2.2 Implementar ruta y controlador `UbicacionController`
    - Crear `web/app/Http/Controllers/UbicacionController.php` con método `show(Request $request)`.
    - Registrar ruta `GET /ubicacion` en `web/routes/web.php`.
    - Validar parámetro `sku`: si el producto no existe o no tiene ubicación registrada, responder con `abort(404)` o vista con aviso expreso (HU02 CA3, CA4).
    - Formatear la ubicación con el patrón estricto «Pasillo {numero_pasillo}, Estante {codigo_estante}» (HU02 CA6).
    - _Requisitos: HU02 CA1, CA3, CA4, CA6_
  - [ ] 2.3 Diseñar vista Blade de ubicación
    - Crear `web/resources/views/ubicacion.blade.php` mostrando la ubicación física destacada del producto consultado.
    - _Requisitos: HU02 CA1, CA6_
  - **Verificación:**
    - `http://127.0.0.1:8000/ubicacion?sku=HEG-001` → Retorna HTTP 200 con texto «Pasillo {N}, Estante {código}».
    - `http://127.0.0.1:8000/ubicacion?sku=INEXISTENTE` → Retorna HTTP 404 o aviso de ubicación no registrada.

- [ ] 3. Implementar consulta y filtrado de productos por categoría (HU03 [guía: H-03])
  - **Historia de usuario:** HU03 — Filtro de productos por categoría [guía: H-03]
  - **Objetivo:** Filtrar y listar hasta 10 productos pertenecientes a una categoría con existencias disponibles (`cantidad_disponible > 0`), ordenados de mayor a menor stock disponible.
  - [ ] 3.1 Implementar ruta y controlador `CategoriaController`
    - Crear `web/app/Http/Controllers/CategoriaController.php` con método `index(Request $request)`.
    - Registrar ruta `GET /categoria` en `web/routes/web.php`.
    - Validar parámetro `nombre`: buscar la categoría por nombre en tabla `categoria`. Si no existe, responder con `abort(404)` (HU03 CA3).
    - Consultar productos activos con `cantidad_disponible > 0`, ordenados por `cantidad_disponible` descendente y limitados a 10 registros (`limit(10)`) (HU03 CA1, CA2).
    - Manejar caso de categoría existente pero sin stock disponible (aviso informativo, HU03 CA4).
    - _Requisitos: HU03 CA1, CA2, CA3, CA4_
  - [ ] 3.2 Diseñar vista Blade de productos por categoría
    - Crear `web/resources/views/categoria.blade.php` con tabla de resultados (SKU, nombre comercial, cantidad disponible y unidad).
    - Mostrar aviso si la categoría cuenta con más de 10 productos disponibles (HU03 CA2).
    - _Requisitos: HU03 CA1, CA2_
  - **Verificación:**
    - `http://127.0.0.1:8000/categoria?nombre=Construccion` → Retorna HTTP 200 con hasta 10 productos con stock > 0 ordenados descendentemente.
    - `http://127.0.0.1:8000/categoria?nombre=NoExiste` → Retorna HTTP 404.
    - Categoría con stock 0 → Retorna vista con aviso «no hay existencias disponibles en esta categoría».

- [ ] 4. Implementar módulo vertical de tickets de verificación física con control PDP (HU05 [guía: H-05] y HU07 [guía: H-07])
  - **Historia de usuario:** HU05 — Registro de solicitud de verificación física [guía: H-05] / HU07 — Protección de Datos Personales [guía: H-07]
  - **Objetivo:** Registrar y listar solicitudes de verificación física ante discrepancias de stock, asignando correlativo único «VER-NNN» de forma atómica y garantizando la estricta ausencia de datos personales de clientes (Ley N.° 29733).
  - [ ] 4.1 Crear modelos Eloquent para tickets sobre tablas existentes
    - Crear `web/app/Models/TicketVerificacion.php` (`protected $table = 'ticket_verificacion'`, clave primaria `id_ticket`, relación `estadoTicket()`, `$timestamps = false`).
    - Crear `web/app/Models/EstadoTicket.php` (`protected $table = 'estado_ticket'`, clave primaria `id_estado`, `$timestamps = false`).
    - _Requisitos: HU05 CA1, HU07 CA1_
  - [ ] 4.2 Implementar generación atómica de correlativo `VER-NNN` y guardado seguro
    - En `web/app/Http/Controllers/TicketController.php`, implementar transacción de base de datos (`DB::transaction`) para obtener el correlativo máximo actual de `numero_correlativo` e incrementar de forma continua sin reinicio diario (`VER-{N:03d}`).
    - Validar que el SKU exista en la tabla `producto` antes de registrar; si es inválido, rechazar con error (HU05 CA3).
    - Estado inicial automático: `Pendiente` (HU05 CA1).
    - _Requisitos: HU05 CA1, CA3, CA6_
  - [ ] 4.3 Aplicar controles de Protección de Datos Personales (HU07 [guía: H-07])
    - Restringir la solicitud `StoreTicketRequest` o validación en controlador para aceptar exclusivamente el campo `sku`.
    - Prohibir estrictamente cualquier campo de datos personales de clientes (DNI, nombres de clientes, teléfonos, tarjetas) en formularios, modelos y logs (HU07 CA1, CA2, CA4).
    - _Requisitos: HU07 CA1, CA2, CA3, CA4_
  - [ ] 4.4 Implementar rutas y vistas Blade de tickets
    - Registrar rutas en `web/routes/web.php`: `GET /tickets` (lista), `POST /tickets` (registro con redirección), `GET /tickets/{numero_correlativo}` (detalle).
    - Crear `web/resources/views/tickets/index.blade.php` con formulario de solicitud (solo campo SKU) y listado de tickets existentes.
    - Crear `web/resources/views/tickets/show.blade.php` con detalle del ticket generado.
    - _Requisitos: HU05 CA1, CA2_
  - **Verificación:**
    - `http://127.0.0.1:8000/tickets` → Retorna HTTP 200 con la lista de tickets de verificación.
    - `POST /tickets` con SKU válido → Redirige a detalle y muestra ticket creado con correlativo `VER-NNN` y estado `Pendiente`.
    - `POST /tickets` con SKU inexistente → Rechaza con error de validación HTTP 422 o mensaje informativo.
    - Inspección de BD y logs: 0 datos personales de clientes registrados (Ley N.° 29733).

- [ ] 5. Ejecutar verificaciones de privacidad y rendimiento (HU07 [guía: H-07] y HU09 [guía: H-09])
  - **Historia de usuario:** HU07 — Protección de Datos Personales [guía: H-07] / HU09 — Rendimiento de Consultas [guía: H-09]
  - **Objetivo:** Validar transversalmente que la aplicación Laravel cumpla con las restricciones de protección de datos (cero datos de clientes) y tiempos de respuesta rápidos en mostrador.
  - [ ] 5.1 Verificación de privacidad (HU07 [guía: H-07])
    - Auditar que ninguna vista Blade, formulario, respuesta JSON o consulta Eloquent solicite, procese o exponga datos de clientes finales (DNI, teléfono, nombres).
    - Confirmar que el archivo `.env` en `web/.env` no se encuentre versionado en Git.
    - _Requisitos: HU07 CA1, CA2, CA4_
  - [ ] 5.2 Verificación de rendimiento de consultas en Laravel (HU09 [guía: H-09])
    - Comprobar que las consultas de stock (HU01) y ubicación (HU02) respondan en un tiempo ≤ 3 s (HU09 CA1).
    - Comprobar que las consultas por categoría (HU03) respondan en un tiempo ≤ 4 s (HU03 CA1).
    - Registrar los tiempos de respuesta obtenidos en las pruebas locales.
    - _Requisitos: HU09 CA1_
  - **Verificación:**
    - Inspección de código y vistas: cero campos PDP en formularios o tablas.
    - Medición de peticiones a `/stock`, `/ubicacion` y `/categoria`: tiempo de respuesta ≤ 3 s en stock/ubicación y ≤ 4 s en categoría.
    - `git status` en la raíz y en `web/`: `.env` no aparece en seguimiento de Git.

---

## [UNIDAD 3 — NO IMPLEMENTAR EN ESTA SESIÓN] Tareas diferidas por ADR-004 y tech.md

Las siguientes tareas corresponden a capacidades de Inteligencia Artificial y protocolos externos que quedan formalmente diferidas a la Unidad 3. Se listan aquí únicamente como referencia del alcance futuro; no representan trabajo de la Sesión 7. La arquitectura de Unidad 3 puede diferir de la arquitectura Laravel de Sesión 7 (ver ADR-004):

- **[U3-T1] Implementar `ClienteMCP`** — Conexión de solo lectura al servidor MCP de inventario (`stdio`/`SSE`). Satisface: HU01 CA5/CA6, HU08, HU09 CA3.
- **[U3-T2] Implementar `GeminiClient`** — Integración con Google Gemini API para lenguaje natural y síntesis conversacional. Satisface: HU04 CA1, HU06 CA1.
- **[U3-T3] Implementar `MotorRAG`** — Indexación y búsqueda vectorial en ChromaDB para guías de discrepancia (HU04) y políticas comerciales (HU06). Satisface: HU04, HU06, HU09 CA2/CA4.
- **[U3-T4] Implementar `ClasificadorIntencion`** — Enrutamiento conversacional con LLM para clasificar consultas del usuario.
- **[U3-T5] Implementar `OrquestadorConversacion`** — Coordinación entre interfaz de chat, MCP, RAG y Gemini.
- **[U3-T6] Pruebas de integración de rendimiento de IA** — Validación de p95 ≤ 3 s en MCP y p95 ≤ 2 s en RAG.

---

## Matriz de Trazabilidad y Decisiones de la Sesión 7

Conforme a `docs/trazabilidad_s7.md`, la siguiente tabla resume la evolución del plan original de tareas:

| N.º original | Título original | Historia | Decisión | Justificación en Sesión 7 |
|---|---|---|---|---|
| 1 | Estructura e infraestructura base | Transversal | **ELIMINADA** | La estructura de Laravel (`web/`) y `.env` ya están creados y configurados. No corresponde rehacer el proyecto. |
| 2 | Modelos y esquemas de datos | HU01, HU02, HU03, HU05 | **DIVIDIDA Y ADAPTADA** | Se eliminó la configuración de SQLAlchemy/SQLite/Pydantic. Se crearon tareas verticales con modelos Eloquent sobre tablas MySQL existentes (`Producto`, `Ubicacion`, `Categoria`, `TicketVerificacion`). |
| 3 | Filtro PDP y validaciones de privacidad | HU07 | **ADAPTADA** | Se eliminaron pruebas sintéticas de Hypothesis para Python; se integró el control estricto de campos en la tarea de tickets (Tarea 4) y verificación de privacidad (Tarea 5). |
| 4 | Módulo de Stock | HU01 | **DIVIDIDA Y ADAPTADA (PRIORIDAD 1)** | Se convirtió en rebanada vertical completa (Tarea 1) en Laravel: modelos Eloquent + controlador + vista Blade; respuesta 404 para producto inexistente. |
| 5 | Módulo de Ubicación | HU02 | **DIVIDIDA Y ADAPTADA** | Rebanada vertical completa (Tarea 2) en Laravel: modelo Eloquent con relación + controlador + vista Blade; formato estricto «Pasillo {N}, Estante {código}». |
| 6 | Módulo de Categoría | HU03 | **DIVIDIDA Y ADAPTADA** | Rebanada vertical completa (Tarea 3) en Laravel: consulta con límite 10, stock > 0, orden desc + vista Blade. |
| 7 | Punto de control de consulta | HU01, HU02, HU03 | **ELIMINADA COMO INDEPENDIENTE** | Las verificaciones se integraron dentro de cada tarea vertical. Se prohíbe crear `seed.sql` porque se trabaja con MySQL existente. |
| 8 | Módulo de Tickets | HU05, HU07 | **DIVIDIDA Y ADAPTADA** | Rebanada vertical completa (Tarea 4) en Laravel: modelos Eloquent + generación atómica de correlativo `VER-NNN` + validaciones PDP + vistas Blade. |
| 9 | Ruteador de salud y main.py | Transversal | **ELIMINADA** | Componente específico de FastAPI. No existe historia de usuario que justifique un endpoint de salud independiente en esta sesión. |
| 10 | Frontend estático (templates + CSS) | Transversal | **ELIMINADA COMO INDEPENDIENTE** | Las vistas Blade y sus estilos se desarrollan e integran dentro de cada historia de usuario vertical. |
| 11 | Checkpoint final Unidad 2 | HU01, HU02, HU03, HU05, HU07, HU09 | **DIVIDIDA E INTEGRADA** | Las comprobaciones funcionales se integraron en cada tarea vertical (1–4) y las no funcionales en la Tarea 5 (privacidad y rendimiento). |

---

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 1, "tasks": ["1.1", "1.2", "1.3"],
      "nota": "Tarea 1 completa antes de iniciar Tareas 2, 3 o 4; el modelo Producto.php es prerequisito compartido." },
    { "id": 2, "tasks": ["2.1", "2.2", "2.3"],
      "nota": "HU02 — puede iniciarse en paralelo con HU03 una vez que Producto.php existe." },
    { "id": 2, "tasks": ["3.1", "3.2"],
      "nota": "HU03 — puede iniciarse en paralelo con HU02 una vez que Producto.php existe." },
    { "id": 3, "tasks": ["4.1", "4.2", "4.3", "4.4"],
      "nota": "HU05/HU07 — depende de Producto.php (Tarea 1.1)." },
    { "id": 4, "tasks": ["5.1", "5.2"],
      "nota": "Verificaciones transversales; se ejecutan al finalizar cada tarea vertical." }
  ]
}
```

---

## Historial de cambios

| Fecha | Origen / Hallazgo | Cambio realizado |
|---|---|---|
| 2026-10-09 | Auditoría Sesión 7 (`docs/trazabilidad_s7.md`, `tech.md`) | Depuración integral del plan: eliminación de infraestructura ya realizada (Tarea 1 original) y tareas FastAPI/SQLite (9, 10); adaptación de Tareas 2–8 a rebanadas verticales Laravel/Eloquent/MySQL; reordenamiento priorizando HU01 como primera tarea con respuesta 404; incorporación explícita de discrepancia `HUxx` vs `[H-xx]`; exclusión formal de componentes de Unidad 3. |
| 2026-10-02 | C7, C8, C9 de auditoría inicial | Diferimiento de MCP/RAG/Gemini a Unidad 3; ajuste preliminar de dependencias. |
| 2026-10-09 | Depuración Sesión 7 (instrucción directa) | Corrección de Overview: eliminada referencia a FastAPI/SQLite; aclarado prerequisito de Tarea 1 sobre Tareas 2-4; nota de contexto en sección Unidad 3 sobre posible diferencia de arquitectura; grafo de dependencias documentado con notas de prerequisito. Solo se modificó `tasks.md`. |
