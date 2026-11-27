<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\StockProduct;
use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;
use RuntimeException;

/**
 * Consulta el catálogo de productos con PDO y sentencias preparadas.
 *
 * Este repositorio es de solo lectura y no modifica el modelo Eloquent
 * Producto ni el esquema existente de MySQL.
 */
final class StockProductRepository
{
    /**
     * Recibe la conexión PDO que utilizará para consultar el catálogo.
     *
     * @param PDO $connection Conexión PDO configurada por Laravel.
     */
    public function __construct(
        private readonly PDO $connection,
    ) {
    }

    /**
     * Crea el repositorio usando la conexión MySQL configurada en Laravel.
     *
     * @return self Repositorio conectado a la base MySQL de la aplicación.
     *
     * @throws StockRepositoryException Si Laravel no puede abrir la conexión PDO.
     */
    public static function fromConfiguredConnection(): self
    {
        try {
            return new self(DB::connection('mysql')->getPdo());
        } catch (PDOException $exception) {
            throw new StockRepositoryException(
                'No fue posible conectar con la base de datos del catálogo.',
                previous: $exception,
            );
        }
    }

    /**
     * Busca un producto por su SKU exacto.
     *
     * @param string $sku SKU que se desea consultar.
     *
     * @return StockProduct|null Producto encontrado o null si no existe.
     *
     * @throws StockRepositoryException Si falla la consulta o el resultado no es válido.
     */
    public function findBySku(string $sku): ?StockProduct
    {
        $sql = <<<'SQL'
            SELECT id_producto, sku, nombre_comercial, cantidad_disponible
            FROM producto
            WHERE sku = :sku
            LIMIT 1
            SQL;

        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue(':sku', $sku, PDO::PARAM_STR);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);

            return $row === false ? null : $this->mapRow($row);
        } catch (PDOException $exception) {
            throw new StockRepositoryException(
                'No fue posible consultar el producto por SKU.',
                previous: $exception,
            );
        }
    }

    /**
     * Busca productos cuyo nombre comercial contiene el texto indicado.
     *
     * @param string $commercialName Texto parcial que se desea buscar.
     *
     * @return list<StockProduct> Productos coincidentes, ordenados por nombre.
     *
     * @throws StockRepositoryException Si falla la consulta o algún resultado no es válido.
     */
    public function searchByCommercialName(string $commercialName): array
    {
        $escapedName = strtr($commercialName, [
            '!' => '!!',
            '%' => '!%',
            '_' => '!_',
        ]);
        $sql = <<<'SQL'
            SELECT id_producto, sku, nombre_comercial, cantidad_disponible
            FROM producto
            WHERE nombre_comercial LIKE :commercial_name ESCAPE '!'
            ORDER BY nombre_comercial
            SQL;

        try {
            $statement = $this->connection->prepare($sql);
            $statement->bindValue(':commercial_name', '%'.$escapedName.'%', PDO::PARAM_STR);
            $statement->execute();
            $products = [];

            while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
                $products[] = $this->mapRow($row);
            }

            return $products;
        } catch (PDOException $exception) {
            throw new StockRepositoryException(
                'No fue posible consultar productos por nombre comercial.',
                previous: $exception,
            );
        }
    }

    /**
     * Convierte una fila asociativa de PDO en un objeto de producto tipado.
     *
     * @param array<string, mixed> $row Fila recuperada del catálogo.
     *
     * @return StockProduct Producto representado por la fila.
     *
     * @throws StockRepositoryException Si la fila no contiene datos válidos.
     */
    private function mapRow(array $row): StockProduct
    {
        $productId = $row['id_producto'] ?? null;
        $sku = $row['sku'] ?? null;
        $commercialName = $row['nombre_comercial'] ?? null;
        $availableQuantity = $row['cantidad_disponible'] ?? null;

        if (
            ! is_numeric($productId)
            || ! is_string($sku)
            || ! is_string($commercialName)
            || ! is_numeric($availableQuantity)
        ) {
            throw new StockRepositoryException(
                'La base de datos devolvió datos de producto con un formato no válido.',
            );
        }

        return new StockProduct(
            productId: (int) $productId,
            sku: $sku,
            commercialName: $commercialName,
            availableQuantity: (int) $availableQuantity,
        );
    }
}

/**
 * Indica que el repositorio no pudo completar una consulta del catálogo.
 */
final class StockRepositoryException extends RuntimeException
{
}
