<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\AsistenteController;
use App\Http\Controllers\AuthController;

// Autenticacion
Route::get('/login', [AuthController::class, 'mostrarLogin'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware(['guest', 'throttle:5,1']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Paginas protegidas
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::get('/stock', [StockController::class, 'index'])
        ->name('stock.index');

    Route::get('/ubicacion', [UbicacionController::class, 'show']);

    Route::post('/asistente/chat', [AsistenteController::class, 'chat'])
        ->middleware('throttle:10,1')
        ->name('asistente.chat');

});