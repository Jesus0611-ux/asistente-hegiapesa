# Stack y reglas técnicas - HegiaStock

- Framework: FastAPI con Jinja2 (Monolito modular SSR), versión estable vigente [VERIFICAR].
- Base de datos: SQLite 3 (archivo local `hegiastock.db`), codificación UTF-8.
- Servidor: Uvicorn ASGI Server; ejecución local en puerto 8000.
- Nombres: español; snake_case en base de datos; PascalCase en clases y modelos Pydantic; kebab-case en rutas.
- Secretos únicamente en .env; .env nunca se versiona.
- Todo módulo nuevo traza a una historia de requirements.md; si no traza, no se construye.
- FRONTERA: no implementar agente conversacional, modelos de IA ni MCP hasta la Unidad 3. Dejar solo el punto de extensión documentado.
- Toda tabla nueva obliga a actualizar docs/arquitectura/modelo_datos.md en el mismo commit.