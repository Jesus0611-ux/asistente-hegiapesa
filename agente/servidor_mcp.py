
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


# INICIAR EL SERVIDOR MCP
if __name__ == "__main__":
    mcp.run(transport="stdio")
