<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Representa los datos de un producto recuperado para una consulta de stock.
 *
 * Es un objeto de datos inmutable para el repositorio PDO y no ejecuta
 * consultas ni modifica el modelo Eloquent Producto existente.
 */
final readonly class StockProduct
{
    /**
     * Crea una representación de los datos de stock de un producto.
     *
     * @param int $productId Identificador de la tabla producto.
     * @param string $sku Código único del producto.
     * @param string $commercialName Nombre comercial mostrado al vendedor.
     * @param int $availableQuantity Cantidad disponible registrada en inventario.
     */
    public function __construct(
        public int $productId,
        public string $sku,
        public string $commercialName,
        public int $availableQuantity,
    ) {
    }
}
