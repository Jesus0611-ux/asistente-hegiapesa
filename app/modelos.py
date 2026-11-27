"""
Modelos Pydantic para el asistente conversacional HegiaStock.
Define esquemas de validacion para elementos de titulo, resultados de busqueda,
respuestas estructuradas y contratos de dominio.
"""

from datetime import datetime
from typing import Literal, Optional
from pydantic import BaseModel, Field, field_validator


class ElementoTitulo(BaseModel):
    """
    Elemento individual que representa un titulo y su puntaje de similitud.
    """
    titulo: str = Field(default="", description="Titulo o encabezado del elemento")
    similitud: float = Field(
        ...,
        ge=-1.0,
        le=1.0,
        description="Puntaje de similitud coseno en el rango [-1.0, 1.0]",
    )
    contenido: Optional[str] = Field(default=None, description="Contenido textual asociado")
    fuente: Optional[str] = Field(default=None, description="Documento o fuente de origen")


class ResultadoTitulo(BaseModel):
    """
    Agrupacion de elementos de titulo recuperados y metricas de busqueda.
    """
    elementos: list[ElementoTitulo] = Field(
        default_factory=list,
        description="Lista de elementos de titulo encontrados",
    )
    puntaje_maximo: Optional[float] = Field(
        default=None,
        ge=-1.0,
        le=1.0,
        description="Puntaje maximo de similitud obtenido",
    )
    total: int = Field(default=0, ge=0, description="Total de elementos coincidentes")
    advertencia: Optional[str] = Field(
        default=None,
        min_length=1,
        description="Mensaje de advertencia sobre la recuperacion",
    )

    @field_validator("advertencia")
    @classmethod
    def validar_advertencia_no_vacia(cls, valor: Optional[str]) -> Optional[str]:
        """
        Valida que la advertencia no sea una cadena de texto vacia.
        """
        if valor is not None and not valor.strip():
            raise ValueError("La advertencia no puede estar vacia")
        return valor


class RespuestaTipoRequerido(BaseModel):
    """
    Respuesta estructurada para tipos de consulta requeridos por el asistente.
    """
    respuesta: str = Field(default="", description="Texto descriptivo de la respuesta")
    tipo_requerido: Optional[str] = Field(default=None, description="Tipo o formato exigido")
    citas_esquemas_revisados: list[str] = Field(
        default_factory=list,
        description="Lista de citas o referencias a esquemas revisados",
    )
    puntaje_maximo: Optional[float] = Field(
        default=None,
        ge=-1.0,
        le=1.0,
        description="Puntaje maximo registrado en la consulta",
    )
    similitud: Optional[float] = Field(
        default=None,
        ge=-1.0,
        le=1.0,
        description="Nivel de similitud registrado",
    )
    advertencia: Optional[str] = Field(
        default=None,
        min_length=1,
        description="Aviso o advertencia generada durante el procesamiento",
    )

    @field_validator("advertencia")
    @classmethod
    def validar_advertencia_no_vacia(cls, valor: Optional[str]) -> Optional[str]:
        """
        Valida que la advertencia no sea una cadena de texto vacia.
        """
        if valor is not None and not valor.strip():
            raise ValueError("La advertencia no puede estar vacia")
        return valor


class ConsultaStockRequest(BaseModel):
    """
    Solicitud de consulta de stock por SKU o nombre comercial.
    """
    q: str = Field(..., min_length=1, max_length=100, description="Termino de busqueda")
    modo: Literal["sku", "nombre", "auto"] = Field(
        default="auto",
        description="Modo de busqueda seleccionado",
    )


class ProductoDetalle(BaseModel):
    """
    Detalle completo de producto para presentacion en mostrador.
    """
    sku: str = Field(..., description="Codigo SKU unico")
    nombre_comercial: str = Field(..., description="Denominacion comercial")
    nombre_categoria: str = Field(..., description="Categoria del producto")
    cantidad_disponible: int = Field(..., ge=0, description="Existencia en stock no negativa")
    unidad: str = Field(..., description="Unidad de medida")
    pasillo: Optional[int] = Field(default=None, description="Numero de pasillo")
    estante: Optional[str] = Field(default=None, description="Codigo de estante")


class CrearTicketRequest(BaseModel):
    """
    Solicitud para registrar un ticket de verificacion fisica de stock.
    Sin campos de datos personales de clientes (Ley 29733).
    """
    sku: str = Field(..., min_length=1, max_length=50, description="SKU del producto a verificar")


class TicketResponse(BaseModel):
    """
    Respuesta tras la creacion o consulta de un ticket de verificacion.
    """
    ticket_id: str = Field(
        ...,
        pattern=r"^VER-\d{3,}$",
        description="Identificador correlativo con formato VER-NNN",
    )
    sku: str = Field(..., description="SKU referenciado")
    estado: str = Field(default="Pendiente", description="Estado operativo del ticket")
    creado_en: Optional[datetime] = Field(default=None, description="Fecha y hora de registro")


class ChatRequest(BaseModel):
    """
    Peticion conversacional al asistente.
    """
    mensaje: str = Field(..., min_length=1, max_length=2000, description="Mensaje de consulta")
    session_id: Optional[str] = Field(default=None, description="Identificador de sesion")


class ChatResponse(BaseModel):
    """
    Respuesta del asistente conversacional.
    """
    respuesta: str = Field(..., description="Mensaje devuelto por el asistente")
    tipo_respuesta: Optional[str] = Field(default=None, description="Clasificacion de respuesta")
    ticket_id: Optional[str] = Field(default=None, description="Ticket asociado si aplica")
    fuentes: Optional[list[str]] = Field(default=None, description="Fuentes consultadas")
    duracion_ms: Optional[int] = Field(default=None, description="Tiempo de respuesta en ms")
