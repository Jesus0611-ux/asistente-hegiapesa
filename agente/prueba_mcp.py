
import asyncio
import sys

from pathlib import Path
from mcp import ClientSession, StdioServerParameters
from mcp.client.stdio import stdio_client


async def main():
    servidor = StdioServerParameters(
        command=sys.executable,
        args=[
            str(Path(__file__).parent / "servidor_mcp.py")
        ]
    )

    async with stdio_client(servidor) as (lectura, escritura):
        async with ClientSession(lectura, escritura) as sesion:

            await sesion.initialize()

            print("\nCONEXION MCP EXITOSA")

            herramientas = await sesion.list_tools()

            print("\nHERRAMIENTAS DISPONIBLES:")

            for herramienta in herramientas.tools:
                print("-", herramienta.name)

            resultado = await sesion.call_tool(
                "consultar_stock",
                arguments={"nombre": "Cemento"}
            )

            print("\nRESULTADO DE LA CONSULTA:")
            print(resultado.model_dump_json(indent=2))

            # PRUEBAS DE LAS NUEVAS HERRAMIENTAS MCP

            pruebas = [
                (
                    "buscar_producto_por_sku",
                    {"sku": "HEG-003"}
                ),
                (
                    "comparar_productos",
                    {
                        "producto_a": "HEG-001",
                        "producto_b": "HEG-002"
                    }
                ),
                (
                    "estadisticas_inventario",
                    {}
                ),
                (
                    "detectar_alertas_stock",
                    {"minimo": 5}
                ),
                (
                    "listar_por_categoria",
                    {"categoria": "a"}
                )
            ]

            for nombre, argumentos in pruebas:
                print(f"\nPROBANDO: {nombre}")

                resultado = await sesion.call_tool(
                    nombre,
                    arguments=argumentos
                )

                print(resultado.model_dump_json(indent=2))



if __name__ == "__main__":
    asyncio.run(main())
