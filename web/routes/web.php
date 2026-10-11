<?php
// Historia: H-01

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UbicacionController;

Route::get('/', function () {
    return view('welcome');
});


// HU01 — Consulta de stock disponible (H-01)
Route::get('/stock', [StockController::class, 'index'])->name('stock.index');

// Historia: H-02
Route::get('/ubicacion', [UbicacionController::class, 'show']);
