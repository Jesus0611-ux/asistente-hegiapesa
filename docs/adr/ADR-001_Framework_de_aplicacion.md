# ADR-001: Framework de aplicación

**Estado:** Propuesto

## Contexto

Gobiernan esta decisión el monolito modular con renderizado en servidor (R1–R6 en cuatro módulos), los cortes de 5 s al inventario y 4 s al RAG (R9.3, R9.4), la frontera única de solo lectura (R8), la restricción de aplicación ligera con cuentas gratuitas y cuatro integrantes, y el plazo de las semanas 7–8. La introducción de requirements.md ya nombra FastAPI. [INFERIDO: PA-01/PL-01 no constan en el material recibido; cito los drivers por su ID R.]

## Alternativas consideradas

### 1. FastAPI

**Instalación:** framework, servidor ASGI y Jinja2 para el HTML [VERIFICAR].

- **(+)** Figura en la especificación validada; un router por módulo; validación tipada de entradas; soporte asíncrono para los cortes de tiempo; el contrato HTTP sirve al agente futuro.
- **(−)** Está pensado para APIs, así que el HTML exige plantillas aparte; lo asíncrono es una curva extra para novatos.

### 2. Flask

**Instalación:** un paquete que incluye Jinja2; el despliegue requiere servidor WSGI [VERIFICAR].

- **(+)** Curva más corta; HTML en servidor de forma nativa; blueprints equivalen a módulos.
- **(−)** Se aparta de la especificación; validación y cortes de tiempo se programan a mano; el soporte asíncrono es limitado.

### 3. Python 3.11+ sin framework

**Instalación:** solo biblioteca estándar; enrutamiento y plantillas propios [VERIFICAR].

- **(+)** Cero dependencias; control total.
- **(−)** Enrutamiento, validación y seguridad de entradas quedan por construir; [INFERIDO] inviable en 10 semanas para novatos, y la generación asistida tiene menos patrones fiables.

## Decisión

Adoptar FastAPI con plantillas Jinja2, un router por módulo y renderizado en servidor.

## Consecuencias

**Positivas:** coherencia con la especificación; fronteras por módulo; contrato HTTP reutilizable en la Unidad 3.

**Negativas:** el despliegue gratuito depende de un servidor ASGI [VERIFICAR]; el equipo aprende conceptos asíncronos.

**Deuda aceptada:** plantillas sobre un framework orientado a API; mezcla de funciones síncronas y asíncronas sin convención formal; ninguna prueba de carga hasta la semana 7.

## Criterio de reversión

Cambiar a Flask si, antes de la semana 7, ocurre cualquiera de estas evidencias:

- La instalación de FastAPI y su servidor falla en el laboratorio o en la cuenta gratuita de despliegue.
- Una consulta de stock completa (ruta, plantilla, lectura simulada) no funciona en la semana 3 [SUPUESTO: umbral propuesto].
- Más de la mitad de los errores de integración registrados provienen de código asíncrono.
