
import os
import sys
import mysql.connector

from pathlib import Path
from dotenv import load_dotenv
from mcp.server.fastmcp import FastMCP

load_dotenv(Path(__file__).parent / ".env")

mcp = FastMCP("HegiaStock-MCP")


# CONEXION A LA BASE DE DATOS
def consultar(sql, parametros=()):
    conexion = mysql.connector.connect(
        host=os.environ["DB_HOST"],
        port=int(os.environ["DB_PORT"]),
        user=os.environ["DB_USER"],
        password=os.environ["DB_PASSWORD"],
        database=os.environ["DB_NAME"],
        charset="utf8mb4",
        connection_timeout=10
    )

    cursor = conexion.cursor(dictionary=True)

    try:
        cursor.execute(sql, parametros)
        return cursor.fetchall()
    finally:
        cursor.close()
        conexion.close()


# HERRAMIENTA 1: CONSULTAR STOCK
@mcp.tool()
def consultar_stock(nombre: str) -> list[dict]:
    """Consulta el stock de un producto por nombre o SKU."""

    print("[MCP] consultar_stock", file=sys.stderr)

    if not nombre.strip():
        return []

    return consultar(
        """
        SELECT sku, nombre_comercial,
               cantidad_disponible AS stock
        FROM producto
        WHERE activo = 1
          AND (nombre_comercial LIKE %s OR sku = %s)
        LIMIT 10
        """,
        (f"%{nombre.strip()}%", nombre.strip())
    )


# HERRAMIENTA 2: CONSULTAR UBICACION
@mcp.tool()
def consultar_ubicacion(nombre: str) -> list[dict]:
    """Consulta el pasillo y estante de un producto."""

    print("[MCP] consultar_ubicacion", file=sys.stderr)

    if not nombre.strip():
        return []

    return consultar(
        """
        SELECT p.sku, p.nombre_comercial,
               u.numero_pasillo, u.codigo_estante
        FROM producto p
        LEFT JOIN ubicacion u
            ON p.id_producto = u.id_producto
        WHERE p.activo = 1
          AND (p.nombre_comercial LIKE %s OR p.sku = %s)
        LIMIT 10
        """,
        (f"%{nombre.strip()}%", nombre.strip())
    )


# HERRAMIENTA 3: STOCK BAJO
@mcp.tool()
def productos_stock_bajo(minimo: int = 5) -> list[dict]:
    """Lista productos activos con stock menor o igual
    al limite indicado, incluyendo productos agotados."""

    print("[MCP] productos_stock_bajo", file=sys.stderr)

    return consultar(
        """
        SELECT sku, nombre_comercial,
               cantidad_disponible AS stock
        FROM producto
        WHERE activo = 1
          AND cantidad_disponible <= %s
        ORDER BY cantidad_disponible ASC
        LIMIT 20
        """,
        (max(0, minimo),)
    )


# HERRAMIENTA 4: RESUMEN DEL INVENTARIO
@mcp.tool()
def resumen_inventario() -> list[dict]:
    """Devuelve el resumen general del inventario.
    Stock bajo significa tener entre 1 y 5 unidades."""

    print("[MCP] resumen_inventario", file=sys.stderr)

    return consultar(
        """
        SELECT
            COUNT(*) AS total_productos,
            COUNT(CASE WHEN activo = 1
                THEN 1 END) AS productos_activos,
            COUNT(CASE WHEN activo = 1
                AND cantidad_disponible > 0
                THEN 1 END) AS disponibles,
            COUNT(CASE WHEN activo = 1
                AND cantidad_disponible = 0
                THEN 1 END) AS agotados,
            COUNT(CASE WHEN activo = 1
                AND cantidad_disponible BETWEEN 1 AND 5
                THEN 1 END) AS stock_bajo
        FROM producto
        """
    )


# ============================================================
# NUEVAS HERRAMIENTAS MCP - AMPLIACION DE HEGIASTOCK
# ============================================================

# HERRAMIENTA 5: BUSCAR PRODUCTO POR SKU
@mcp.tool()
def buscar_producto_por_sku(sku: str) -> list[dict]:
    """Busca un producto mediante su codigo SKU exacto."""

    if not sku.strip():
        return []

    return consultar(
        """
        SELECT
            p.sku,
            p.nombre_comercial,
            p.cantidad_disponible AS stock,
            um.simbolo AS unidad_medida,
            c.nombre AS categoria,
            u.numero_pasillo,
            u.codigo_estante
        FROM producto p
        LEFT JOIN categoria c
            ON p.id_categoria = c.id_categoria
        LEFT JOIN unidad_medida um
            ON p.id_unidad = um.id_unidad
        LEFT JOIN ubicacion u
            ON p.id_producto = u.id_producto
        WHERE p.sku = %s
          AND p.activo = 1
        LIMIT 5
        """,
        (sku.strip(),)
    )


# HERRAMIENTA 6: LISTAR PRODUCTOS POR CATEGORIA
@mcp.tool()
def listar_por_categoria(categoria: str) -> list[dict]:
    """Lista los productos activos de una categoria."""

    if not categoria.strip():
        return []

    return consultar(
        """
        SELECT
            c.nombre AS categoria,
            p.sku,
            p.nombre_comercial,
            p.cantidad_disponible AS stock,
            um.simbolo AS unidad_medida
        FROM categoria c
        INNER JOIN producto p
            ON c.id_categoria = p.id_categoria
        LEFT JOIN unidad_medida um
            ON p.id_unidad = um.id_unidad
        WHERE c.activo = 1
          AND p.activo = 1
          AND c.nombre LIKE %s
        ORDER BY p.nombre_comercial
        LIMIT 50
        """,
        (f"%{categoria.strip()}%",)
    )


# HERRAMIENTA 7: COMPARAR DOS PRODUCTOS
@mcp.tool()
def comparar_productos(producto_a: str, producto_b: str) -> dict:
    """Compara las existencias de dos productos por nombre o SKU."""

    if not producto_a.strip() or not producto_b.strip():
        return {
            "error": "Debes indicar los dos productos que deseas comparar."
        }

    sql = """
        SELECT
            sku,
            nombre_comercial,
            cantidad_disponible AS stock,
            um.simbolo AS unidad_medida
        FROM producto p
        LEFT JOIN unidad_medida um
            ON p.id_unidad = um.id_unidad
        WHERE p.activo = 1
          AND (
              p.sku = %s
              OR p.nombre_comercial LIKE %s
          )
        LIMIT 5
    """

    resultado_a = consultar(
        sql,
        (producto_a.strip(), f"%{producto_a.strip()}%")
    )

    resultado_b = consultar(
        sql,
        (producto_b.strip(), f"%{producto_b.strip()}%")
    )

    return {
        "producto_a": resultado_a,
        "producto_b": resultado_b
    }


# HERRAMIENTA 8: ESTADISTICAS DEL INVENTARIO
@mcp.tool()
def estadisticas_inventario() -> dict:
    """Calcula indicadores generales y estadisticas por categoria."""

    resumen = consultar(
        """
        SELECT
            COUNT(*) AS total_registros,
            COALESCE(SUM(CASE
                WHEN cantidad_disponible > 0 THEN 1
                ELSE 0
            END), 0) AS disponibles,
            COALESCE(SUM(CASE
                WHEN cantidad_disponible = 0 THEN 1
                ELSE 0
            END), 0) AS agotados,
            COALESCE(SUM(CASE
                WHEN cantidad_disponible BETWEEN 1 AND 5 THEN 1
                ELSE 0
            END), 0) AS stock_bajo,
            COALESCE(SUM(cantidad_disponible), 0) AS total_unidades
        FROM producto
        WHERE activo = 1
        """
    )

    categorias = consultar(
        """
        SELECT
            c.nombre AS categoria,
            COUNT(p.id_producto) AS cantidad_productos,
            COALESCE(SUM(p.cantidad_disponible), 0)
                AS total_unidades
        FROM categoria c
        LEFT JOIN producto p
            ON c.id_categoria = p.id_categoria
            AND p.activo = 1
        WHERE c.activo = 1
        GROUP BY c.id_categoria, c.nombre
        ORDER BY cantidad_productos DESC
        """
    )

    return {
        "resumen": resumen[0] if resumen else {},
        "por_categoria": categorias
    }


# HERRAMIENTA 9: DETECTAR ALERTAS DE STOCK
@mcp.tool()
def detectar_alertas_stock(minimo: int = 5) -> list[dict]:
    """
    Detecta productos agotados y con stock bajo.
    Por defecto, stock bajo significa entre 1 y 5 unidades.
    """

    minimo = max(0, min(int(minimo), 1000000))

    return consultar(
        """
        SELECT
            p.sku,
            p.nombre_comercial,
            p.cantidad_disponible AS stock,
            um.simbolo AS unidad_medida,
            c.nombre AS categoria,
            u.numero_pasillo,
            u.codigo_estante,
            CASE
                WHEN p.cantidad_disponible = 0
                    THEN 'AGOTADO'
                ELSE 'STOCK BAJO'
            END AS estado_alerta
        FROM producto p
        LEFT JOIN unidad_medida um
            ON p.id_unidad = um.id_unidad
        LEFT JOIN categoria c
            ON p.id_categoria = c.id_categoria
        LEFT JOIN ubicacion u
            ON p.id_producto = u.id_producto
        WHERE p.activo = 1
          AND p.cantidad_disponible <= %s
        ORDER BY p.cantidad_disponible ASC,
                 p.nombre_comercial ASC
        LIMIT 100
        """,
        (minimo,)
    )


# ============================================================
# FIN DE LAS NUEVAS HERRAMIENTAS MCP
# ============================================================

# INICIAR EL SERVIDOR MCP
if __name__ == "__main__":
    mcp.run(transport="stdio")
