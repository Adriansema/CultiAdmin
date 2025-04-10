<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\BoletinController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Admin\StatsController;




// Ruta para acceder a los datos del gráfico
Route::get('/statistics', [StatisticController::class, 'getStatistics'])->name('statistics.index');
// Ruta por defecto al acceder al sitio (por ejemplo, el cliente)
Route::get('/admin/statistics', [StatisticController::class, 'getStatistics']);
    Route::get('/', function () {
        return view('welcome');
    })->name('welcome');

    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
    ])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

   
    //  Rutas solo para administrador
    Route::middleware(['auth', 'role:administrador'])->group(function () {
        Route::resource('productos', ProductoController::class);
        Route::resource('boletines', BoletinController::class);
        Route::resource('usuarios', UsuarioController::class)->except(['show']);
    });

    //  Rutas solo para operador
    Route::middleware(['auth', 'role:operador'])->group(function () {
        Route::get('productos/pendientes', [ProductoController::class, 'pendientes'])->name('productos.pendientes');
        Route::post('productos/{id}/validar', [ProductoController::class, 'validar'])->name('productos.validar');
        Route::post('productos/{id}/rechazar', [ProductoController::class, 'rechazar'])->name('productos.rechazar');
    });

});


