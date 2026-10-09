# Producto · HegiaStock: asistente conversacional para consulta y verificación de stock

Redacta todos los documentos en español.

## Propósito
Resolver el descalce entre el registro de inventario en mostrador y la existencia real de mercadería en el almacén de Inversiones Hegiapesa S.A.C.
Permitir la consulta inmediata de stock, ubicación física y políticas comerciales para eliminar demoras en la atención y reducir la pérdida de ventas.

## Usuarios
- **Vendedor de mostrador (Usuario principal):** Consultar en tiempo real la disponibilidad, ubicación física de productos y políticas de devolución durante la atención al cliente.
- **Almacenero:** Recibir y revisar las solicitudes de verificación física registradas cuando existan dudas sobre la existencia de stock en pasillo.
- **Administrador / Gerente:** Consultar el registro de incidencias por discrepancias de inventario y revisar las guías normativas de operación.

## Lo que hace
- Consultar el stock disponible por código SKU o nombre comercial del producto. En Unidad 2 los datos se leen desde SQLite 3; en Unidad 3 se leerán mediante la conexión MCP de solo lectura.
- Consultar la ubicación física exacta (pasillo y estante) de un producto en el almacén. En Unidad 2 desde SQLite 3; en Unidad 3 mediante la conexión MCP de solo lectura.
- Filtrar y listar productos pertenecientes a una categoría específica con sus existencias actualizadas.
- Responder consultas sobre guías normativas operativas y procedimientos institucionales ante diferencias de inventario mediante RAG. **[PENDIENTE Unidad 3 — ver ADR-004]**
- Responder consultas sobre políticas comerciales, de devolución y garantía mediante RAG. **[PENDIENTE Unidad 3 — ver ADR-004]**
- Registrar solicitudes formales de verificación física para productos con discrepancia de stock.

## Lo que NO hace
- NO integra ni emite comprobantes de pago, boletas, facturas ni se conecta con sistemas POS de venta.
- NO incluye módulo de comercio electrónico (e-commerce) ni interfaz de venta directa en línea para clientes finales.
- NO utiliza sensores IoT, cámaras ni sistemas de telemetría para conteo automático de mercadería en estantes.

## Reglas no negociables
- **Protección de Datos Personales (Ley N.° 29733):** Queda estrictamente prohibido solicitar, procesar o almacenar datos personales de clientes finales (DNI, nombres, teléfonos, registros financieros).
- **Integridad de Inventario (Solo Lectura):** Todas las consultas a la base de datos de inventario se realizan exclusivamente a través de la interfaz MCP en modo SOLO LECTURA. Ninguna funcionalidad puede ejecutar operaciones de modificación (`INSERT`, `UPDATE`, `DELETE`) en la base de datos.
- **Verificabilidad y No Falsificación:** Si una consulta no devuelve resultados en la base de datos o repositorio documental, el sistema debe informar expresamente que la información no existe, sin inventar datos ni cifras.


---

## Historial de cambios

| Fecha | Hallazgo auditoría | Cambio realizado |
|---|---|---|
| 2026-10-02 | C14 — "Lo que hace" citaba MCP y RAG como activos en Unidad 2, contradiciendo design.md y steering tech.md | Aclaradas las 5 funcionalidades: las 3 de inventario distinguen Unidad 2 (SQLite) vs Unidad 3 (MCP); RAG y políticas marcadas [PENDIENTE Unidad 3] con referencia a ADR-004 |
