<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    // Historia: H-02
    public function show(Request $request)
    {
        $request->validate([
            'sku' => ['required', 'string'],
        ]);

        $producto = Producto::with('ubicacion')
            ->where('sku', $request->query('sku'))
            ->first();

        if (!$producto || !$producto->ubicacion) {
            abort(404);
        }

        $ubicacion = 'Pasillo ' . $producto->ubicacion->numero_pasillo
            . ', Estante ' . $producto->ubicacion->codigo_estante;

        return view('ubicacion', compact('producto', 'ubicacion'));
    }
}
