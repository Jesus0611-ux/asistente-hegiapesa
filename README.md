
# HegiaStock - HegiaBot

## Descripción del proyecto

HegiaStock es un sistema de gestión y consulta de inventario
orientado a Inversiones Hegiapesa S.A.C.

HegiaBot es un asistente de inteligencia artificial integrado
en el sistema web que permite consultar información del
inventario mediante preguntas en lenguaje natural.

Proyecto académico de Inteligencia Artificial.
Universidad Nacional de Cañete.
Escuela Profesional de Ingeniería de Sistemas.

## Funcionalidades

- Inicio de sesión de usuarios.
- Panel de control del inventario.
- Consulta de stock disponible.
- Consulta de ubicación física de productos.
- Identificación de productos con stock bajo.
- Resumen general del inventario.
- Asistente conversacional integrado.
- Conversaciones con contexto por sesión.

## Tecnologías utilizadas

- Laravel y PHP 8.2.
- Python 3.11.
- FastAPI y Uvicorn.
- Google Gemini API.
- Model Context Protocol (MCP).
- MySQL mediante XAMPP.
- HTML, CSS y JavaScript.

## Arquitectura

Usuario -> Interfaz Laravel -> FastAPI -> Gemini
-> Servidor MCP -> Base de datos MySQL.

El navegador se comunica con Laravel, que actúa como
intermediario entre el usuario y el agente de IA.

## Herramientas MCP

| Herramienta | Función |
|---|---|
| consultar_stock | Consultar existencias de productos |
| consultar_ubicacion | Consultar pasillo y estante |
| productos_stock_bajo | Identificar productos con pocas existencias |
| resumen_inventario | Obtener indicadores del inventario |

## Requisitos

- Git.
- Python 3.11.
- PHP 8.2 o compatible.
- Composer.
- XAMPP con MySQL.
- Una clave API de Google Gemini.

## Instalación

### 1. Clonar el repositorio

    git clone -b develop https://github.com/Jesus0611-ux/asistente-hegiapesa.git
    cd asistente-hegiapesa

### 2. Base de datos

Iniciar MySQL desde XAMPP.

Importar database/schema.sql para crear la estructura.

Importar database/datos_demo.sql para cargar los datos
de demostración.

La configuración local utiliza la base hegiastock
y el puerto MySQL 3307.

Crear un usuario de base de datos exclusivo para el
agente, con permisos SELECT sobre las tablas producto
y ubicacion.

Configurar una cuenta de acceso a la aplicación web
con una contraseña almacenada mediante bcrypt.

### 3. Configurar el agente Python

    cd agente
    python -m venv .venv
    .\.venv\Scripts\Activate.ps1
    python -m pip install -r requirements.txt
    Copy-Item .env.example .env

Completar las credenciales de MySQL y Gemini en el
archivo agente/.env.

### 4. Ejecutar el agente

    python -m uvicorn agente:app --host 127.0.0.1 --port 8001

Documentación de la API:

http://127.0.0.1:8001/docs

### 5. Configurar Laravel

Abrir una segunda terminal desde la raíz del proyecto.

    cd web
    composer install
    Copy-Item .env.example .env
    php artisan key:generate

Configurar las variables de conexión a MySQL:

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3307
    DB_DATABASE=hegiastock
    SESSION_DRIVER=file
    CACHE_STORE=file

Completar DB_USERNAME y DB_PASSWORD con las
credenciales locales correspondientes.

### 6. Ejecutar la aplicación web

    php -S 127.0.0.1:8000 -t public

Ingresar desde el navegador:

http://127.0.0.1:8000

Mantener el agente Python y Laravel ejecutándose
simultáneamente.

## Ejemplos de preguntas

- ¿Cuánto cemento tenemos?
- ¿Dónde está ubicado el taladro eléctrico?
- ¿Qué productos tienen stock bajo?
- Dame un resumen del inventario.
- ¿Y dónde está ubicado?

## Seguridad

- Credenciales almacenadas en archivos .env.
- Archivos privados excluidos mediante .gitignore.
- Usuario de base de datos de solo lectura para el agente.
- Consultas SQL parametrizadas.
- Acceso al chat mediante autenticación.
- Protección CSRF.
- Validación de mensajes.
- Restricción de preguntas fuera del contexto del inventario.
- El asistente no puede modificar las existencias.

## Datos de demostración

La base de datos contiene 20 productos de demostración
con información de existencias y ubicaciones físicas.

## Documentación

La carpeta docs contiene la documentación académica,
la ficha del asistente y los casos de prueba.

## Finalidad

Demostrar la integración de inteligencia artificial
generativa y herramientas MCP con una aplicación web
y una base de datos relacional.
