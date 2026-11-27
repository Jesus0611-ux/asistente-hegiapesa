<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function mostrarLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nombre_usuario' => 'required|string|max:60',
            'password' => 'required|string|max:255',
        ]);

        $credenciales = [
            'nombre_usuario' => $request->nombre_usuario,
            'password' => $request->password,
            'activo' => 1,
        ];

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();

            return redirect('/');
        }

        return back()->withErrors([
            'nombre_usuario' => 'Usuario o contraseña incorrectos.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}