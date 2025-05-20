<?php

// actualizacion 09/04/2025 //

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Middleware\CheckUserEstado;
use App\Http\Middleware\Roles_Admin_Opera;
use App\Http\Controllers\BoletinController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ViewUserController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\ExportarCsvController;
use App\Http\Controllers\CentroAyudaController;
use App\Http\Controllers\AccesibilidadController;
use App\Http\Controllers\Operador\OperadorProductoController;

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('auth.login')->with('inactivo', session('inactivo'));
})->name('login');

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
    Route::middleware(['auth', CheckUserEstado::class])->group(function () {
        Route::middleware([Roles_Admin_Opera::class])->group(function () {

            // Usuarios
            Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
            Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
            Route::post('/usuarios/importar-csv', [UsuarioController::class, 'importarCsv'])->name('usuarios.importarCsv');
            Route::get('/usuarios/exportar', [UsuarioController::class, 'exportarCSV'])->name('usuarios.exportar');
            Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
            Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show'])->name('usuarios.show');
            Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
            Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
            Route::patch('/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggle'])->name('usuarios.toggle');
            Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

            // Historial
            Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');

            // Productos rutas sin parámetros
            Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
            Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
            Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');

            // Rutas específicas fijas antes que las dinámicas
            Route::get('/productos/cafe', [ProductoController::class, 'cafe'])->name('productos.cafe');
            Route::get('/productos/mora', [ProductoController::class, 'mora'])->name('productos.mora');
            Route::post('/productos/importar-csv', [ProductoController::class, 'importarCSV'])->name('productos.importar.csv');
            Route::get('/productos/generar-csv', [ProductoController::class, 'generarCSV'])->name('productos.generarCSV');

            // Rutas con parámetros al final
            Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
            Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
            Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
            Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

            // Boletines
          // Primero las rutas específicas
Route::resource('boletines', BoletinController::class);
Route::post('boletines/importar-pdf', [BoletinController::class, 'importarPdf'])->name('boletines.importarPdf');
Route::get('/boletines/cafe', [BoletinController::class, 'cafe'])->name('boletines.cafe');
Route::get('/boletines/mora', [BoletinController::class, 'mora'])->name('boletines.mora');

// Luego las rutas estándar
Route::get('/boletines', [BoletinController::class, 'index'])->name('boletines.index');
Route::get('/boletines/create', [BoletinController::class, 'create'])->name('boletines.create');
Route::post('/boletines', [BoletinController::class, 'store'])->name('boletines.store');

// Finalmente, las rutas con parámetros dinámicos (al final SIEMPRE)
Route::get('/boletines/{boletin}/edit', [BoletinController::class, 'edit'])->name('boletines.edit');
Route::put('/boletines/{boletin}', [BoletinController::class, 'update'])->name('boletines.update');
Route::delete('/boletines/{boletin}', [BoletinController::class, 'destroy'])->name('boletines.destroy');
Route::get('/boletines/{boletin}', [BoletinController::class, 'show'])->name('boletines.show');


            //Estadistica
            Route::get('admin/statistics', [StatisticController::class, 'getStatistics'])->name('statistics.index');

            // Vista de Usuarios
            Route::get('view-user', [ViewUserController::class, 'index'])->name('view-user.index');
            Route::get('view-user/create', [ViewUserController::class, 'create'])->name('view-user.create');
            Route::post('view-user', [ViewUserController::class, 'store'])->name('view-user.store');
            Route::get('view-user/{id}', [ViewUserController::class, 'show'])->name('view-user.show');
            Route::get('view-user/{id}/edit', [ViewUserController::class, 'edit'])->name('view-user.edit');
            Route::put('view-user/{id}', [ViewUserController::class, 'update'])->name('view-user.update');
            Route::delete('view-user/{id}', [ViewUserController::class, 'destroy'])->name('view-user.destroy');
            Route::get('view-user/{id}/historial', [ViewUserController::class, 'historial'])->name('view-user.historial');

            //Centro de Ayuda de la Aplicación___ routes/web.php
            Route::get('/centro-ayuda', [CentroAyudaController::class, 'index'])->name('centroAyuda.index');
            Route::get('/search-faq', [CentroAyudaController::class, 'searchFaq'])->name('search.faq');
            Route::get('/centro-ayuda/contacto', [CentroAyudaController::class, 'showContactForm'])->name('centroAyuda.contactForm');
            Route::post('/centro-ayuda/contact-submit', [CentroAyudaController::class, 'submitContact'])->name('contact.submit');

            //Acesibilidad de la Aplicación
            Route::get('/accesibilidad', [AccesibilidadController::class, 'index'])->name('accesibilidad.index');

            //Generador de archivo csv
            Route::get('/generar-csv', [ExportarCsvController::class, 'generarCsv'])->middleware('auth');

            /*
        |--------------------------------------------------------------------------
        | OPERADOR y en OperadorController.php tambien quiero hacer lo mismo para el, de acuerdo?
        |--------------------------------------------------------------------------
        */
            Route::prefix('operador')->name('operador.')->group(function () {
                Route::get('/pendientes', [OperadorProductoController::class, 'pendientes'])->name('pendientes');

                Route::get('/productos/{producto}', [OperadorProductoController::class, 'showProducto'])->name('productos.show');

                Route::get('/boletines/{boletin}', [OperadorProductoController::class, 'showBoletin'])->name('boletines.show');

                Route::post('/productos/{producto}/validar', [OperadorProductoController::class, 'validar'])->name('productos.validar');
                Route::post('/productos/{producto}/rechazar', [OperadorProductoController::class, 'rechazar'])->name('productos.rechazar');

                Route::post('/boletines/{boletin}/validar', [OperadorProductoController::class, 'validarBoletin'])->name('boletines.validar');
                Route::post('/boletines/{boletin}/rechazar', [OperadorProductoController::class, 'rechazarBoletin'])->name('boletines.rechazar');
            });
        });
    });
});

// Ruta de estadísticas pública (si decides usarla externamente)
Route::get('/statistics', [StatisticController::class, 'index'])->name('statistics.index.public');

// Fallback general
Route::fallback(function () {
    return redirect()->route('dashboard')->with('error', 'Ruta no encontrada.');
});
