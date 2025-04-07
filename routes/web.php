<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoletinController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\InformacionController;
use App\Http\Controllers\StatisticController;

// Ruta para acceder a los datos del gráfico
Route::get('/statistics', [StatisticController::class, 'index'])->name('statistics.index');

Route::get('/admin/statistics', [StatisticController::class, 'index'])->name('statistics.index');


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Rutas CRUD de Información
Route::resource('informaciones', InformacionController::class);

// Rutas CRUD de Boletines
Route::resource('boletines', BoletinController::class);

// Rutas CRUD de Comentarios (incluyendo filtrado por mes)
Route::resource('comentarios', ComentarioController::class);
Route::get('comentarios/filtrar/{mes}', [ComentarioController::class, 'filtrarPorMes'])->name('comentarios.filtrar');

// Rutas CRUD de Clientes
Route::resource('clientes', ClienteController::class);
