<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\StockRepositoryException;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Atiende las solicitudes web de consulta de stock de la HU01.
 *
 * Valida la entrada y selecciona la respuesta, sin construir consultas SQL.
 */
final class StockController extends Controller
{
    /**
     * Busca productos por SKU o nombre comercial y prepara la vista de respuesta.
     *
     * @param Request $request Solicitud HTTP con los parámetros de búsqueda.
     *
     * @return Response Página Blade con el formulario y el resultado de búsqueda.
     */
    public function index(Request $request): Response
    {
        $validated = $request->validate([
            'sku' => ['nullable', 'string', 'max:50'],
            'q' => ['nullable', 'string', 'max:150'],
            'nombre' => ['nullable', 'string', 'max:150'],
        ]);

        $sku = $validated['sku'] ?? null;
        $commercialName = $validated['q'] ?? $validated['nombre'] ?? null;

        if ($sku === null && $commercialName === null) {
            return $this->stockView([
                'modo' => 'formulario',
                'producto' => null,
                'coincidencias' => [],
                'termino' => null,
                'aviso' => null,
            ]);
        }

        try {
            $service = StockService::fromConfiguredConnection();

            if ($sku !== null) {
                $product = $service->findBySku($sku);

                if ($product === null) {
                    return $this->stockView([
                        'modo' => 'no_encontrado',
                        'producto' => null,
                        'coincidencias' => [],
                        'termino' => $sku,
                        'aviso' => 'No se encontró el producto solicitado. Revise el SKU.',
                    ], Response::HTTP_NOT_FOUND);
                }

                return $this->stockView([
                    'modo' => 'detalle',
                    'producto' => $product,
                    'coincidencias' => [],
                    'termino' => $sku,
                    'aviso' => null,
                ]);
            }

            $matches = $service->searchByCommercialName($commercialName);

            if ($matches === []) {
                return $this->stockView([
                    'modo' => 'no_encontrado',
                    'producto' => null,
                    'coincidencias' => [],
                    'termino' => $commercialName,
                    'aviso' => 'No se encontró ningún producto con ese nombre. Revise la ortografía.',
                ]);
            }

            if (count($matches) === 1) {
                return $this->stockView([
                    'modo' => 'detalle',
                    'producto' => $matches[0],
                    'coincidencias' => [],
                    'termino' => $commercialName,
                    'aviso' => null,
                ]);
            }

            return $this->stockView([
                'modo' => 'lista',
                'producto' => null,
                'coincidencias' => $matches,
                'termino' => $commercialName,
                'aviso' => null,
            ]);
        } catch (StockRepositoryException) {
            Log::error('No se pudo completar una consulta de stock.');

            return $this->stockView([
                'modo' => 'error',
                'producto' => null,
                'coincidencias' => [],
                'termino' => null,
                'aviso' => 'No fue posible consultar el inventario. Intente nuevamente más tarde.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    /**
     * Renderiza la vista de stock con el código HTTP solicitado.
     *
     * @param array<string, mixed> $data Datos de presentación de la búsqueda.
     * @param int $status Código HTTP de la respuesta.
     *
     * @return Response Respuesta HTTP con la vista Blade.
     */
    private function stockView(array $data, int $status = Response::HTTP_OK): Response
    {
        return response()->view('stock', $data, $status);
    }
}
