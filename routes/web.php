<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoletinController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\ProductoAgricolaController;
use App\Http\Controllers\UsuarioController;

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

Route::middleware(['auth'])->group(function () {
    // Productos Agrícolas - acceso general
    Route::resource('productos-agricolas', ProductoAgricolaController::class);

    // Boletines - acceso general
    Route::resource('boletines', BoletinController::class);
});

// Operador: Validar y rechazar productos
Route::middleware(['auth', 'role:operador'])->group(function () {
    Route::post('/productos/{id}/validar', [ProductoAgricolaController::class, 'validar'])->name('productos.validar');
    Route::post('/productos/{id}/rechazar', [ProductoAgricolaController::class, 'rechazar'])->name('productos.rechazar');
});

// Administrador: Gestión de Usuarios
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('usuarios', UsuarioController::class);
});

Route::middleware(['auth', 'role:operador'])->group(function () {
    Route::get('/productos/pendientes', [ProductoAgricolaController::class, 'pendientes'])->name('productos.pendientes');
});

