"""
Configuracion de la base de datos SQLite con SQLAlchemy.
Define el motor (engine), la fabrica de sesiones y la funcion de inicializacion init_db.
"""

from typing import Generator
from sqlalchemy import create_engine
from sqlalchemy.orm import DeclarativeBase, sessionmaker, Session

from backend.core.config import DATABASE_URL

# Motor de conexion a SQLite 3 (archivo local hegiastock.db)
# check_same_thread=False permite compartir la conexion entre peticiones en FastAPI
motor = create_engine(
    DATABASE_URL,
    connect_args={"check_same_thread": False},
    echo=False,
)

# Fabrica de sesiones locales de base de datos
SesionLocal = sessionmaker(
    autocommit=False,
    autoflush=False,
    bind=motor,
)


class Base(DeclarativeBase):
    """
    Clase base declarativa para los modelos ORM de HegiaStock.
    """
    pass


# Alias convencionales para interoperabilidad
engine = motor
SessionLocal = SesionLocal


def obtener_sesion() -> Generator[Session, None, None]:
    """
    Generador de sesion para inyeccion de dependencias en FastAPI.
    """
    sesion = SesionLocal()
    try:
        yield sesion
    finally:
        sesion.close()


def init_db() -> None:
    """
    Inicializa las tablas de la base de datos creando el esquema definido en los modelos.
    """
    Base.metadata.create_all(bind=motor)
