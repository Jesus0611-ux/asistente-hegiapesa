<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AsistenteController extends Controller
{
    public function chat(Request $request)
    {
        $datos = $request->validate([
            'mensaje' => 'required|string|max:500',
        ]);

        if (!$request->session()->has('asistente_sesion')) {
            $request->session()->put(
                'asistente_sesion',
                (string) Str::uuid()
            );
        }

        try {
            $respuesta = Http::connectTimeout(5)
                ->timeout(60)
                ->post('http://127.0.0.1:8001/chat', [
                    'sesion' => $request->session()
                        ->get('asistente_sesion'),
                    'mensaje' => $datos['mensaje'],
                ]);

            if (!$respuesta->successful()) {
                return response()->json([
                    'respuesta' => 'El asistente no esta disponible.'
                ], 503);
            }

            return response()->json([
                'respuesta' => $respuesta->json('respuesta')
                    ?? 'No se pudo obtener una respuesta.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'respuesta' => 'No se pudo conectar con el asistente IA.'
            ], 503);
        }
    }
}