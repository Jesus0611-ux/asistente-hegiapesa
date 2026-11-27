# agente.py - Servicio del agente: recibe un mensaje y devuelve la respuesta
import os
import traceback
from dotenv import load_dotenv
from fastapi import FastAPI
from pydantic import BaseModel
from google import genai
from google.genai import types
from mcp import ClientSession, StdioServerParameters
from mcp.client.stdio import stdio_client

CARPETA = os.path.dirname(os.path.abspath(__file__))
load_dotenv(os.path.join(CARPETA, '.env'))

cliente = genai.Client(api_key=os.environ['GEMINI_API_KEY'])
MODELO = os.environ['MODELO']
INSTRUCCIONES = open(os.path.join(CARPETA, 'instrucciones.txt'), encoding='utf-8').read()

# Forma de encender el servidor MCP
servidor = StdioServerParameters(command='python', args=[os.path.join(CARPETA, 'servidor_mcp.py')])

historiales = {}   # memoria de cada conversación, por número de sesión
app = FastAPI()


class Entrada(BaseModel):
    sesion: str
    mensaje: str


async def responder(historial):
    '''Pregunta al modelo y ejecuta en el servidor MCP las herramientas que pida.'''
    async with stdio_client(servidor) as (lectura, escritura):
        async with ClientSession(lectura, escritura) as sesion_mcp:
            await sesion_mcp.initialize()

            # 1. Lee las herramientas del servidor MCP y se las describe al modelo
            lista = await sesion_mcp.list_tools()
            declaraciones = [
                types.FunctionDeclaration(
                    name=h.name,
                    description=h.description or '',
                    parameters_json_schema=h.inputSchema,
                )
                for h in lista.tools
            ]
            config = types.GenerateContentConfig(
                system_instruction=INSTRUCCIONES,
                tools=[types.Tool(function_declarations=declaraciones)],
            )

            contenidos = list(historial[-11:])
            for _ in range(5):   # máximo 5 vueltas de herramientas por pregunta
                resultado = await cliente.aio.models.generate_content(
                    model=MODELO, contents=contenidos, config=config)

                # 2. Si el modelo no pide herramientas, ya tenemos la respuesta
                llamadas = resultado.function_calls
                if not llamadas:
                    return resultado.text

                # 3. Ejecuta cada herramienta pedida y le devuelve el dato al modelo
                contenidos.append(resultado.candidates[0].content)
                partes = []
                for llamada in llamadas:
                    print('Herramienta usada:', llamada.name, dict(llamada.args or {}))
                    dato = await sesion_mcp.call_tool(llamada.name, dict(llamada.args or {}))
                    texto = '\n'.join(c.text for c in dato.content if hasattr(c, 'text'))
                    partes.append(types.Part.from_function_response(
                        name=llamada.name, response={'resultado': texto}))
                contenidos.append(types.Content(role='user', parts=partes))
    return None


@app.post('/chat')
async def chat(entrada: Entrada):
    historial = historiales.setdefault(entrada.sesion, [])
    historial.append(types.Content(role='user', parts=[types.Part(text=entrada.mensaje)]))
    try:
        texto = await responder(historial)
        texto = texto or 'No pude elaborar una respuesta. Intente con otra pregunta.'
    except Exception:
        traceback.print_exc()   # el detalle del error se ve en la terminal
        texto = 'El asistente no está disponible en este momento. Intente de nuevo en un minuto.'
    historial.append(types.Content(role='model', parts=[types.Part(text=texto)]))
    return {'respuesta': texto}
