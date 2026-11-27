"""
Modulo de configuracion para HegiaStock.
Carga variables de entorno desde el archivo .env y las expone como constantes tipadas.
"""

import os
from pathlib import Path
from dotenv import load_dotenv

# Ruta raiz del repositorio
ruta_archivo_actual = Path(__file__).resolve()
ruta_raiz = ruta_archivo_actual.parent.parent.parent
ruta_archivo_env = ruta_raiz / ".env"

# Carga de variables de entorno desde .env si existe en el sistema
if ruta_archivo_env.exists():
    load_dotenv(dotenv_path=ruta_archivo_env)
else:
    load_dotenv()

# Variables de entorno requeridas para la Unidad 2
SQLITE_DB_PATH: str = os.getenv("SQLITE_DB_PATH", "hegiastock.db")
TZ: str = os.getenv("TZ", "America/Lima")

# Ruta absoluta al archivo SQLite local
if os.path.isabs(SQLITE_DB_PATH):
    RUTA_BD_SQLITE: str = SQLITE_DB_PATH
else:
    RUTA_BD_SQLITE: str = str(ruta_raiz / SQLITE_DB_PATH)

# Cadena de conexion para SQLAlchemy
DATABASE_URL: str = f"sqlite:///{RUTA_BD_SQLITE}"
