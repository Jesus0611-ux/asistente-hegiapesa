# Implementation Plan: HegiaStock

## Overview

Implementación del asistente conversacional HegiaStock para Inversiones Hegiapesa S.A.C.
El plan sigue el orden de dependencias entre componentes: infraestructura y modelos primero,
servicios de capa media después, y API + frontend al final. Las pruebas de propiedad con
Hypothesis acompañan a cada componente para validar corrección durante el desarrollo.

---

## Tasks

- [ ] 1. Configurar estructura del proyecto e infraestructura base
  - Crear la estructura de directorios según el diseño: `backend/`, `backend/routers/`, `backend/modules/stock/`, `backend/modules/ubicacion/`, `backend/modules/categoria/`, `backend/modules/tickets/`, `backend/core/`, `backend/tests/unit/`, `backend/tests/integration/`, `templates/`, `static/`, `database/`, `docs/`
  - Crear `requirements.txt` con dependencias fijadas para Unidad 2 (sin componentes de IA/MCP — ver ADR-004): `fastapi[standard]==0.115.*` [VERIFICAR versión en PyPI], `uvicorn[standard]==0.32.*` [VERIFICAR], `sqlalchemy==2.0.*` [VERIFICAR], `pydantic==2.10.*` [VERIFICAR], `jinja2==3.1.*` [VERIFICAR], `python-dotenv==1.0.*` [VERIFICAR], `pytest==8.*` [VERIFICAR], `pytest-asyncio==0.24.*` [VERIFICAR], `httpx==0.27.*` [VERIFICAR]
  - Crear `.env.example` con las variables de entorno de Unidad 2: `SQLITE_DB_PATH`, `TZ=America/Lima` — sin claves de IA ni MCP
  - Crear `backend/core/config.py` que cargue variables de entorno con `python-dotenv` y las exponga como constantes tipadas
  - Crear `backend/core/database.py` con el engine SQLite, la sesión y la función `init_db()`
  - Crear archivos `__init__.py` en todos los paquetes Python
  - _Requisitos: R1–R5, R7, R9 (infraestructura transversal Unidad 2)_

- [ ] 2. Implementar modelos de base de datos y schemas Pydantic
  - [ ] 2.1 Implementar configuración de SQLite con SQLAlchemy
    - Crear modelos ORM en `backend/core/database.py` para las tablas del diseño: `producto`, `categoria`, `unidad_medida`, `ubicacion`, `ticket_verificacion`, `estado_ticket`, `rol`, `usuario`, `audit_log`
    - Incluir comentario explícito en el modelo: «Sin campos de datos personales de clientes (Ley 29733)»
    - _Requisitos: R5.1, R5.6, R7.1_

  - [ ] 2.2 Implementar schemas Pydantic
    - Crear `backend/core/schemas.py` con `ConsultaStockRequest`, `ProductoDetalle`, `CrearTicketRequest`, `TicketResponse` según contratos definidos en design.md sección 5.2
    - _Requisitos: R1, R2, R5.2_

- [ ] 3. Implementar Filtro PDP (Ley N.° 29733)
  - [ ] 3.1 Implementar `PDPFilter`
    - Crear `backend/core/pdp_filter.py` con los patrones regex para DNI (8 dígitos), RUC, teléfono, email y tarjeta definidos en el diseño (sección 3.8)
    - Implementar `sanitizar_para_log(texto: str) -> str` que reemplaza coincidencias con `[DATO_PERSONAL]`
    - Implementar `validar_ticket_sin_pdp(ticket: dict) -> bool` que verifica que los campos sean exactamente `{id, numero_correlativo, ticket_id, sku, estado, fecha_creacion}`
    - _Requisitos: R7.1, R7.2, R7.3, R7.4_

  - [ ]* 3.2 Escribir prueba de propiedad P11 — El pipeline no persiste datos personales embebidos
    - **Propiedad 11: El pipeline no persiste datos personales embebidos en consultas**
    - **Valida: Requisitos 7.1, 7.3, 7.4**
    - Para todo mensaje que contenga un dato personal generado (DNI ficticio, teléfono, email), `sanitizar_para_log()` no debe retener el dato original en la cadena resultante
    - Usar `hypothesis` con estrategias que inyecten los patrones PDP en strings arbitrarios
    - Configurar `@settings(max_examples=200)`; tag: `# Feature: hegia-stock, Property 11: El pipeline no persiste datos personales`
    - _Requisitos: R7.1, R7.3, R7.4_

  - [ ]* 3.3 Escribir prueba de propiedad P8 — Los tickets no contienen campos de datos personales
    - **Propiedad 8: Los tickets no contienen campos de datos personales**
    - **Valida: Requisitos 5.7, 7.1, 7.2**
    - Para todo dict de ticket, `validar_ticket_sin_pdp()` debe retornar `True` si y solo si el conjunto de claves es exactamente el permitido y ningún valor coincide con los patrones PDP
    - Tag: `# Feature: hegia-stock, Property 8: Los tickets no contienen campos de datos personales`
    - _Requisitos: R5.7, R7.1, R7.2_

- [ ] 4. Implementar Módulo de Stock (HU01)
  - [ ] 4.1 Implementar `RepositorioStock`
    - Crear `backend/modules/stock/repository.py` con métodos `obtener_por_sku(sku)` y `buscar_por_nombre(nombre)` usando SELECT sobre tabla `producto` con JOIN a `categoria` y `unidad_medida`
    - _Requisitos: R1.1, R1.2, R1.3, R1.4_
  - [ ] 4.2 Implementar `ServicioStock`
    - Crear `backend/modules/stock/service.py` con lógica de búsqueda: SKU exacto vs. LIKE en nombre; retorna `ProductoDetalle` o lista para múltiples coincidencias
    - _Requisitos: R1.1, R1.2, R1.3, R1.4_
  - [ ] 4.3 Implementar `RuteadorStock`
    - Crear `backend/routers/stock.py` con `GET /stock`; valida parámetros con Pydantic; devuelve `TemplateResponse("stock.html")`
    - _Requisitos: R1.1, R1.2, R1.3, R1.4, R9.1_
  - [ ] 4.4 Crear plantilla `templates/stock.html`
    - Formulario de búsqueda + resultado único + lista de coincidencias + mensaje no encontrado
    - Atributos ARIA (`role="main"`, labels asociados), fuente ≥ 16 px
    - _Requisitos: R1.1, R1.2, R1.3, R1.4_

- [ ] 5. Implementar Módulo de Ubicación (HU02)
  - [ ] 5.1 Implementar `RepositorioUbicacion`
    - Crear `backend/modules/ubicacion/repository.py` con `obtener_por_sku(sku)` JOIN `producto` → `ubicacion`
    - _Requisitos: R2.1, R2.3, R2.4_
  - [ ] 5.2 Implementar `ServicioUbicacion`
    - Crear `backend/modules/ubicacion/service.py`; formatea «Pasillo {N}, Estante {código}»
    - _Requisitos: R2.1, R2.6_
  - [ ] 5.3 Implementar `RuteadorUbicacion` + plantilla `ubicacion.html`
    - `GET /ubicacion?sku=...`; `TemplateResponse("ubicacion.html")`
    - _Requisitos: R2.1, R2.3, R2.4, R2.6_

- [ ] 6. Implementar Módulo de Categoría (HU03)
  - [ ] 6.1 Implementar `RepositorioCategoria`
    - Crear `backend/modules/categoria/repository.py`: SELECT productos con `cantidad_disponible > 0` filtrado por categoría, ORDER BY cantidad DESC, LIMIT 10
    - _Requisitos: R3.1, R3.2, R3.3, R3.4_
  - [ ] 6.2 Implementar `ServicioCategoria` + `RuteadorCategoria` + plantilla `categoria.html`
    - `GET /categoria?nombre=...`; maneja categoría no encontrada y sin stock
    - _Requisitos: R3.1, R3.2, R3.3, R3.4_

- [ ] 7. Checkpoint — Verificar módulos de consulta (HU01, HU02, HU03)
  - Ejecutar pruebas unitarias de repositorios y servicios; verificar respuesta HTML en navegador con datos de `database/seed.sql` (**PENDIENTE del equipo: crear seed.sql con datos de prueba representativos**)
  - Confirmar que ninguna ruta devuelve datos de clientes ni expone PDP

- [ ] 8. Implementar Módulo de Tickets (HU05)
  - [ ] 8.1 Implementar `FiltroPDP`
    - Crear `backend/core/pdp_filter.py` con patrones regex para DNI (8 dígitos), RUC, teléfono, email y tarjeta
    - Implementar `sanitizar_para_log(texto: str) -> str`
    - Implementar `validar_ticket_sin_pdp(ticket: dict) -> bool`
    - _Requisitos: R7.1, R7.2, R7.3, R7.4_
  - [ ] 8.2 Implementar `RepositorioTickets`
    - Crear `backend/modules/tickets/repository.py` con INSERT usando `BEGIN IMMEDIATE` para garantizar unicidad del correlativo; métodos `crear`, `obtener`, `listar`
    - _Requisitos: R5.1, R5.4, R5.6_
  - [ ] 8.3 Implementar `ServicioTickets`
    - Crear `backend/modules/tickets/service.py`; valida SKU contra `RepositorioStock`; genera `VER-NNN` con `f"VER-{correlativo:03d}"`; aplica `FiltroPDP.validar_ticket_sin_pdp` antes de persistir
    - _Requisitos: R5.1, R5.2, R5.3, R5.6, R5.7_
  - [ ] 8.4 Implementar `RuteadorTickets` + plantillas `tickets.html` y `ticket_detalle.html`
    - `GET /tickets` (lista con filtro por estado), `POST /tickets` (crea ticket → redirect 303), `GET /tickets/{ticket_id}` (detalle)
    - _Requisitos: R5.1, R5.2_

- [ ] 9. Implementar `RuteadorSalud` y `main.py`
  - Crear `backend/routers/health.py` con `GET /health` que verifica SQLite y retorna JSON
  - Crear `backend/main.py` que instancia la app FastAPI, registra todos los routers y llama a `init_db()` en el lifespan
  - Montar directorio `static/` con `StaticFiles`
  - _Requisitos: Transversal_

- [ ] 10. Implementar frontend estático (templates + CSS)
  - Crear `static/styles.css`: fuente ≥ 16 px, contraste WCAG AA, diseño responsivo
  - Revisar todas las plantillas con atributos ARIA: `role="main"`, `aria-live="polite"` donde aplique, `<label>` asociados a inputs
  - _Requisitos: R1–R5 (transversal UI)_

- [ ] 11. Checkpoint final — Verificar la implementación completa de Unidad 2
  - Ejecutar `pytest backend/tests/ -v --tb=short` y confirmar que todos los tests pasan
  - Verificar que `.env` no está versionado (`.gitignore`) y que `.env.example` tiene solo las variables de Unidad 2
  - Verificar que ninguna ruta expone datos personales de clientes
  - Consultar al usuario si hay ajustes antes de cerrar la Unidad 2

---

## [UNIDAD 3 — NO IMPLEMENTAR HASTA UNIDAD 3] Tareas diferidas por ADR-004

Las siguientes tareas quedan excluidas de la Unidad 2. Se ejecutarán en la Unidad 3 tras activar el punto de extensión documentado en `design.md` sección 6.

- **[U3-T1] Implementar `ClienteMCP`** — `backend/services/mcp_client.py` con `READ_ONLY_TOOLS`, `call_tool()`, métodos de alto nivel y conexión `stdio`/`SSE`. Satisface: R1.5, R1.6, R8, R9.3.
- **[U3-T2] Implementar `GeminiClient`** — `backend/services/gemini_client.py` con reintentos y circuit breaker. Satisface: R4.1, R6.1.
- **[U3-T3] Implementar `MotorRAG`** — `backend/services/rag_engine.py` con ChromaDB, embeddings y recuperación. Satisface: R4, R6, R9.2, R9.4.
- **[U3-T4] Implementar `ClasificadorIntencion`** — clasificación de intención con Gemini. Satisface: R1–R6 (enrutamiento conversacional).
- **[U3-T5] Implementar `OrquestadorConversacion`** — coordina MCP, RAG, Gemini y Tickets. Satisface: R1–R6.
- **[U3-T6] Pruebas de integración de rendimiento** — p95 ≤ 3 s MCP, p95 ≤ 2 s RAG (R9.1, R9.2).

> Referencia: ADR-004 — criterio de reversión permite adelantar [U3-T1] a partir de la Semana 5 si el Servidor MCP real está disponible.

---

## Notes

- Las sub-tareas marcadas con `*` son opcionales y pueden omitirse para obtener un MVP más rápido; sin embargo, se recomienda ejecutarlas para validar las 11 propiedades de corrección del diseño.
- Las pruebas de integración (tarea 14) requieren que el Servidor_MCP esté configurado y que `GOOGLE_API_KEY` sea válida; se ejecutan con `pytest backend/tests/ --integration -v`.
- Cada prueba de propiedad Hypothesis usa `@settings(max_examples=200)` configurado en `conftest.py` con el perfil `"ci"`.
- El correlativo VER-NNN **nunca** se reinicia por día; el número sigue creciendo en toda la vida de la base de datos.
- Los únicos datos que pueden aparecer en `audit_log` y `tickets_verificacion` son los especificados en el diseño; cualquier campo adicional violaría la Ley N.° 29733.
- La migración de SQLite a PostgreSQL en el futuro solo requiere cambiar la cadena de conexión en `database.py`.

---

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["2.1", "2.2"] },
    { "id": 1, "tasks": ["2.3", "3.1"] },
    { "id": 2, "tasks": ["3.2", "3.3", "4.1"] },
    { "id": 3, "tasks": ["4.2", "4.3", "4.4", "6.1"] },
    { "id": 4, "tasks": ["6.2", "7.1", "8.1"] },
    { "id": 5, "tasks": ["7.2", "7.3", "7.4", "8.2", "8.3", "8.4"] },
    { "id": 6, "tasks": ["10.1"] },
    { "id": 7, "tasks": ["10.2", "10.3", "11.1"] },
    { "id": 8, "tasks": ["11.2"] },
    { "id": 9, "tasks": ["11.3", "11.4", "11.5", "11.6", "12.1"] },
    { "id": 10, "tasks": ["12.2"] },
    { "id": 11, "tasks": ["12.3", "13.1"] },
    { "id": 12, "tasks": ["13.2"] },
    { "id": 13, "tasks": ["14.1", "14.2", "14.3", "14.4"] }
  ]
}
```

---

## Historial de cambios

| Fecha | Hallazgo auditoría | Cambio realizado |
|---|---|---|
| 2026-10-02 | C7 — Tareas 4 (MCPClient), 6 (GeminiClient), 7 (MotorRAG) contradicen frontera de Unidad 2 | Eliminadas del flujo principal y consolidadas en sección [UNIDAD 3] al final del archivo |
| 2026-10-02 | C8 — Estructura de carpetas incompatible con design.md | Tarea 1 reescrita con rutas `backend/routers/`, `backend/modules/`, `templates/`, `static/` alineadas al C4 L3 |
| 2026-10-02 | C8 — Tareas 10–14 (orquestador, API chat, frontend SPA) incompatibles con arquitectura SSR de Unidad 2 | Reemplazadas por tareas de módulos SSR (4–10) que implementan ruteadores Jinja2 según design.md |
| 2026-10-02 | C9 — Versiones de dependencias sin fuente | Marcadas [VERIFICAR versión en PyPI]; eliminadas dependencias de IA/MCP de Unidad 2 |
