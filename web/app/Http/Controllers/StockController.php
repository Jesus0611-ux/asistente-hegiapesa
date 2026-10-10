<?php
// Historia: H-01

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * GET /stock
     *
     * Parámetros de búsqueda (mutuamente excluyentes, se evalúan en orden):
     *   ?sku=xxx   → búsqueda exacta por SKU             (HU01 CA1)
     *   ?q=xxx     → búsqueda parcial por nombre_comercial (HU01 CA2, CA3)
     *   ?nombre=xx → alias de ?q (HU01 CA2, CA3)
     *
     * Respuestas:
     *   - SKU encontrado             → 200  vista con ficha del producto
     *   - SKU no encontrado          → 404  abort() (HU01 CA4 + criterio tech.md)
     *   - Nombre sin resultados      → 200  vista con aviso (HU01 CA4)
     *   - Nombre con 1 resultado     → 200  vista con ficha del producto
     *   - Nombre con N>1 resultados  → 200  vista con lista de selección (HU01 CA3)
     *   - Sin parámetros             → 200  formulario vacío
     */
    public function index(Request $request)
    {
        // --- Validación de entrada -------------------------------------------
        $request->validate([
            'sku'    => ['nullable', 'string', 'max:50'],
            'q'      => ['nullable', 'string', 'max:150'],
            'nombre' => ['nullable', 'string', 'max:150'],
        ]);

        $sku    = $request->input('sku');
        // ?nombre es alias de ?q; ?q tiene prioridad
        $nombre = $request->input('q') ?? $request->input('nombre');

        // --- Sin parámetros: mostrar solo el formulario ----------------------
        if (! $sku && ! $nombre) {
            return view('stock', [
                'modo'         => 'formulario',
                'producto'     => null,
                'coincidencias' => [],
                'termino'      => null,
                'aviso'        => null,
            ]);
        }

        // --- Búsqueda por SKU exacto (HU01 CA1) ------------------------------
        if ($sku) {
            // Consulta parametrizada con Eloquent — nunca DB::raw con input
            $producto = Producto::with(['categoria', 'unidadMedida'])
                ->where('sku', $sku)
                ->first();

            // HU01 CA4 + tech.md: recurso individual no encontrado → 404
            if (! $producto) {
                abort(404);
            }

            return view('stock', [
                'modo'          => 'detalle',
                'producto'      => $producto,
                'coincidencias' => [],
                'termino'       => $sku,
                'aviso'         => null,
            ]);
        }

        // --- Búsqueda por nombre comercial (HU01 CA2, CA3, CA4) --------------
        $coincidencias = Producto::with(['categoria', 'unidadMedida'])
            ->where('nombre_comercial', 'like', '%' . $nombre . '%')
            ->orderBy('nombre_comercial')
            ->get();

        if ($coincidencias->isEmpty()) {
            // HU01 CA4: producto no encontrado — aviso, NO abort(404)
            // (la búsqueda por nombre es una lista, no un recurso individual)
            return view('stock', [
                'modo'          => 'no_encontrado',
                'producto'      => null,
                'coincidencias' => [],
                'termino'       => $nombre,
                'aviso'         => 'No se encontró ningún producto con el nombre «' . e($nombre) . '». Revise la ortografía.',
            ]);
        }

        if ($coincidencias->count() === 1) {
            return view('stock', [
                'modo'          => 'detalle',
                'producto'      => $coincidencias->first(),
                'coincidencias' => [],
                'termino'       => $nombre,
                'aviso'         => null,
            ]);
        }

        // Múltiples coincidencias → lista de selección (HU01 CA3)
        return view('stock', [
            'modo'          => 'lista',
            'producto'      => null,
            'coincidencias' => $coincidencias,
            'termino'       => $nombre,
            'aviso'         => null,
        ]);
    }
}
