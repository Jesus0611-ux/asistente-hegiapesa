"""
Configuracion del asistente conversacional y motor de busqueda.
Define los parametros clave para recuperacion de fragmentos y modelos de embeddings.
"""

from typing import Optional

# Umbral minimo de similitud coseno para considerar un fragmento relevante.
# Por defecto se establece en None para no descartar resultados por puntaje preliminar.
UMBRAL_SIMILITUD: Optional[float] = None

# Cantidad maxima de fragmentos documentales que se recuperan en cada consulta.
MAX_FRAGMENTOS: int = 5

# Nombre o identificador del modelo de embeddings utilizado para vectorizacion.
# Por defecto se establece en None hasta la configuracion del proveedor en Unidad 3.
MODELO_EMBEDDINGS: Optional[str] = None
