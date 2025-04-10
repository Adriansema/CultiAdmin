<?php

// actualizacion 09/04/2025 //

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\BoletinController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Operador\OperadorProductoController;
use App\Http\Middleware\Roles_Admin_Opera;

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Autenticación y verificación
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRADOR Y OPERADOR
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', Roles_Admin_Opera::class])->group(function () {

        // Usuarios
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

        // Productos
        Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
        Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

        // Boletines
        Route::get('/boletines', [BoletinController::class, 'index'])->name('boletines.index');
        Route::get('/boletines/create', [BoletinController::class, 'create'])->name('boletines.create');
        Route::post('/boletines', [BoletinController::class, 'store'])->name('boletines.store');
        Route::get('/boletines/{boletin}', [BoletinController::class, 'show'])->name('boletines.show');
        Route::get('/boletines/{boletin}/edit', [BoletinController::class, 'edit'])->name('boletines.edit');
        Route::put('/boletines/{boletin}', [BoletinController::class, 'update'])->name('boletines.update');
        Route::delete('/boletines/{boletin}', [BoletinController::class, 'destroy'])->name('boletines.destroy');

        // Estadísticas
        Route::get('/admin/statistics', [StatisticController::class, 'index'])->name('statistics.index');

        /*
        |--------------------------------------------------------------------------
        | OPERADOR
        |--------------------------------------------------------------------------
        */
        Route::prefix('operador')->name('operador.')->group(function () {
            Route::get('/productos/pendientes', [OperadorProductoController::class, 'indexPendientes'])->name('productos.operador.pendientes');
            Route::post('/productos/{id}/validar', [OperadorProductoController::class, 'validar'])->name('productos.validar');
            Route::post('/productos/{id}/rechazar', [OperadorProductoController::class, 'rechazar'])->name('productos.rechazar');
        });
    });
});

// Ruta de estadísticas pública (si decides usarla externamente)
Route::get('/statistics', [StatisticController::class, 'index'])->name('statistics.index.public');

// Fallback general
Route::fallback(function () {
    return redirect()->route('dashboard')->with('error', 'Ruta no encontrada.');
});
