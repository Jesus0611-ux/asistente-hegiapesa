<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\StockProduct;
use App\Repositories\StockProductRepository;

/**
 * Coordina las consultas de stock requeridas por la HU01.
 *
 * No valida solicitudes HTTP ni decide cómo se presentan los resultados.
 */
final class StockService
{
    /**
     * Recibe el repositorio de solo lectura del catálogo.
     *
     * @param StockProductRepository $repository Repositorio PDO para consultar productos.
     */
    public function __construct(
        private readonly StockProductRepository $repository,
    ) {
    }

    /**
     * Crea el servicio con la conexión MySQL configurada en Laravel.
     *
     * @return self Servicio listo para consultar el catálogo.
     *
     * @throws \App\Repositories\StockRepositoryException Si no se puede abrir la conexión.
     */
    public static function fromConfiguredConnection(): self
    {
        return new self(StockProductRepository::fromConfiguredConnection());
    }

    /**
     * Busca un producto por su SKU exacto.
     *
     * @param string $sku SKU validado que se desea consultar.
     *
     * @return StockProduct|null Producto encontrado o null si no existe.
     *
     * @throws \App\Repositories\StockRepositoryException Si falla la consulta.
     */
    public function findBySku(string $sku): ?StockProduct
    {
        return $this->repository->findBySku($sku);
    }

    /**
     * Busca productos por coincidencia parcial con su nombre comercial.
     *
     * @param string $commercialName Nombre o fragmento validado que se desea buscar.
     *
     * @return list<StockProduct> Productos coincidentes, o una lista vacía.
     *
     * @throws \App\Repositories\StockRepositoryException Si falla la consulta.
     */
    public function searchByCommercialName(string $commercialName): array
    {
        return $this->repository->searchByCommercialName($commercialName);
    }
}
