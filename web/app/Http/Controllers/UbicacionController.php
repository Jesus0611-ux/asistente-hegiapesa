<?php
// Historia: H-02

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    public function show(Request $request)
    {
        $request->validate([
            'sku' => ['nullable', 'string'],
            'q' => ['nullable', 'string'],
            'nombre' => ['nullable', 'string'],
        ]);

        $sku = $request->query('sku');
        $termino = $request->query('q') ?? $request->query('nombre');

        if (($sku === null || $sku === '') && ($termino === null || $termino === '')) {
            return view('ubicacion', [
                'modo' => 'formulario',
                'producto' => null,
                'coincidencias' => collect(),
                'ubicacion' => null,
                'termino' => null,
                'aviso' => null,
            ]);
        }

        if ($sku !== null && $sku !== '') {
            $producto = Producto::with('ubicacion')
                ->where('sku', $sku)
                ->first();

            if (!$producto) {
                return response()->view('ubicacion', [
                    'modo' => 'no_encontrado',
                    'producto' => null,
                    'coincidencias' => collect(),
                    'ubicacion' => null,
                    'termino' => $sku,
                    'aviso' => 'El producto no fue encontrado en el catálogo de inventario.',
                ], 404);
            }

            return $this->mostrarProducto($producto, $sku);
        }

        $coincidencias = Producto::with('ubicacion')
            ->where('nombre_comercial', 'like', '%' . $termino . '%')
            ->orderBy('nombre_comercial')
            ->orderBy('sku')
            ->get();

        if ($coincidencias->isEmpty()) {
            return response()->view('ubicacion', [
                'modo' => 'no_encontrado',
                'producto' => null,
                'coincidencias' => collect(),
                'ubicacion' => null,
                'termino' => $termino,
                'aviso' => 'El producto no fue encontrado en el catálogo de inventario.',
            ], 404);
        }

        if ($coincidencias->count() > 1) {
            return view('ubicacion', [
                'modo' => 'lista',
                'producto' => null,
                'coincidencias' => $coincidencias,
                'ubicacion' => null,
                'termino' => $termino,
                'aviso' => null,
            ]);
        }

        return $this->mostrarProducto($coincidencias->first(), $termino);
    }

    private function mostrarProducto(Producto $producto, string $termino)
    {
        $ubicacion = null;
        $aviso = null;

        if ($producto->ubicacion) {
            $ubicacion = 'Pasillo ' . $producto->ubicacion->numero_pasillo
                . ', Estante ' . $producto->ubicacion->codigo_estante;
        } else {
            $aviso = 'La ubicación no está registrada para ese producto.';
        }

        return view('ubicacion', [
            'modo' => 'detalle',
            'producto' => $producto,
            'coincidencias' => collect(),
            'ubicacion' => $ubicacion,
            'termino' => $termino,
            'aviso' => $aviso,
        ]);
    }
}
