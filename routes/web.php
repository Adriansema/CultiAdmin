<?php

// actualizacion 09/04/2025 //

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\BoletinController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\Operador\HistorialOperadorController;
use App\Http\Controllers\Operador\OperadorProductoController;
use App\Http\Middleware\Roles_Admin_Opera;


 // Estadísticas



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
        Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show'])->name('usuarios.show');
        Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::patch('/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');
        Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

        // Historial
        Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');

        // Productos
        Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::get('/productos/historial', [ProductoController::class, 'historial'])->name('productos.historial');
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

        //Estadistica
        Route::get('admin/statistics', [StatisticController::class, 'getStatistics'])->name('statistics.index');


        /*
        |--------------------------------------------------------------------------
        | OPERADOR y en OperadorController.php tambien quiero hacer lo mismo para el, de acuerdo?
        |--------------------------------------------------------------------------
        */
        Route::prefix('operador')->name('operador.')->group(function () {
            // Vista unificada de pendientes (productos y boletines)
            Route::get('/pendientes', [OperadorProductoController::class, 'pendientes'])->name('pendientes');

            // Acciones sobre productos
            Route::post('/productos/{id}/validar', [OperadorProductoController::class, 'validar'])->name('productos.validar');
            Route::post('/productos/{id}/rechazar', [OperadorProductoController::class, 'rechazar'])->name('productos.rechazar');

            // Acciones sobre boletines
            Route::post('/boletines/{id}/validar', [OperadorProductoController::class, 'validarBoletin'])->name('boletines.validar');
            Route::post('/boletines/{id}/rechazar', [OperadorProductoController::class, 'rechazarBoletin'])->name('boletines.rechazar');

            // Vista de detalles (historial) de productos y boletines
            Route::get('/productos/{producto}', [HistorialOperadorController::class, 'showProducto'])->name('productos.show');
            Route::get('/boletines/{boletin}', [HistorialOperadorController::class, 'showBoletin'])->name('boletines.show');

            // Vista de historial con filtros
            Route::get('/historial', [HistorialOperadorController::class, 'index'])->name('historial.index');
        });

    });
});

// Ruta de estadísticas pública (si decides usarla externamente)
Route::get('/statistics', [StatisticController::class, 'index'])->name('statistics.index.public');

// Fallback general
Route::fallback(function () {
    return redirect()->route('dashboard')->with('error', 'Ruta no encontrada.');
});
