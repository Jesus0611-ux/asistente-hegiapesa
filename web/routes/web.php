<?php
// Historia: H-01

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;

Route::get('/', function () {
    return view('welcome');
});

// HU01 — Consulta de stock disponible (H-01)
Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
