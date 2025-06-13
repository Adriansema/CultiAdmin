<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserEstado;
use App\Http\Controllers\PqrsController;
use App\Http\Middleware\Roles_Admin_Opera;
use App\Http\Controllers\BoletinController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\CentroAyudaController;
use App\Http\Controllers\ExportarCsvController;
use App\Http\Controllers\AccesibilidadController;
use App\Http\Controllers\Operador\OperadorProductoController;
/* Route::get('/', function () {
     return view('welcome');
})->name('welcome'); */

// Rutas para PQRS (Diseñadas para ser públicas, aceptan envíos de invitados)
Route::prefix('pqrs')->name('pqrs.')->group(function () {
     Route::get('/crear', [PqrsController::class, 'create'])->name('create');
     Route::post('/store', [PqrsController::class, 'store'])->name('store');
});

// Ruta para verificar si el correo existe (pública, sin autenticación)
Route::post('/check-email', [UsuarioController::class, 'checkEmailExists'])->name('check-email');

// Rutas de Centro de Ayuda
Route::prefix('centro-ayuda')->name('centroAyuda.')->group(function () {
     Route::get('/', [CentroAyudaController::class, 'index'])->name('index');
     Route::get('/search-faq', [CentroAyudaController::class, 'searchFaq'])->name('search.faq');
     Route::get('/contacto', [CentroAyudaController::class, 'showContactForm'])->name('contactForm');
     Route::post('/contact-submit', [CentroAyudaController::class, 'submitContact'])->name('contact.submit');
});

// Ruta de Accesibilidad
Route::get('/accesibilidad', [AccesibilidadController::class, 'index'])->name('accesibilidad.index');

// Ruta de estadísticas (pública, si es que esta es pública)
Route::get('/statistics', [StatisticController::class, 'index'])->name('statistics.index.public');


// ------------------------------------------------------------------------------------
// Grupo de rutas que requieren AUTENTICACIÓN y verificación de correo electrónico
// ------------------------------------------------------------------------------------
Route::middleware([
     'auth:sanctum',
     config('jetstream.auth_session'),
     'verified',
     CheckUserEstado::class // Verifica el estado activo/inactivo del usuario
])->group(function () {

     Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
     
     Route::middleware([Roles_Admin_Opera::class])->group(function () {
          Route::prefix('producto')->name('productos.')->group(function () {
               Route::get('/', [ProductoController::class, 'index'])->name('index');
               Route::get('/create', [ProductoController::class, 'create'])->name('create');
               Route::post('/', [ProductoController::class, 'store'])->name('store');
               Route::post('/importar-csv', [ProductoController::class, 'importarCSV'])->name('importar.csv');
               Route::get('/exportar-csv', [ProductoController::class, 'exportarCSV'])->name('exportarCSV');
               Route::get('/{producto}/edit', [ProductoController::class, 'edit'])->name('edit');
               Route::get('/{producto}', [ProductoController::class, 'show'])->name('show');
               Route::put('/{producto}', [ProductoController::class, 'update'])->name('update');
               Route::delete('/{producto}', [ProductoController::class, 'destroy'])->name('destroy');
          });

          Route::prefix('boletin')->name('boletines.')->group(function () {
               Route::get('/', [BoletinController::class, 'index'])->name('index');
               Route::get('/create', [BoletinController::class, 'create'])->name('create');
               Route::post('/', [BoletinController::class, 'store'])->name('store');
               Route::post('/importar-pdf', [BoletinController::class, 'importarPdf'])->name('importarPdf');
               Route::get('/exportar-csv', [BoletinController::class, 'exportarCSV'])->name('exportarCSV');
               Route::get('/{boletin}/edit', [BoletinController::class, 'edit'])->name('edit');
               Route::put('/{boletin}', [BoletinController::class, 'update'])->name('update');
               Route::delete('/{boletin}', [BoletinController::class, 'destroy'])->name('destroy');
               Route::get('/{boletin}', [BoletinController::class, 'show'])->name('show');
          });

          Route::prefix('usuario')->name('usuarios.')->group(function () {
               Route::get('/', [UsuarioController::class, 'index'])->name('index');
               Route::get('/create', [UsuarioController::class, 'create'])->name('create');
               Route::post('/', [UsuarioController::class, 'store'])->name('store');
               Route::post('/importar-csv', [UsuarioController::class, 'importarCsv'])->name('importarCsv');
               Route::get('/exportar', [UsuarioController::class, 'exportarCSV'])->name('exportar');
               Route::get('/{usuario}', [UsuarioController::class, 'show'])->name('show');
               /* Route::get('/{usuario}/edit', [UsuarioController::class, 'edit'])->name('edit'); */
               Route::put('/{usuario}', [UsuarioController::class, 'update'])->name('update');
               Route::patch('/{usuario}/toggle', [UsuarioController::class, 'toggle'])->name('toggle');
               Route::delete('/{usuario}', [UsuarioController::class, 'destroy'])->name('destroy');
          });

          Route::prefix('noticia')->name('noticias.')->group(function () {
               Route::get('noticias', [NoticiaController::class, 'index'])->name('noticias.index');
               Route::get('noticias/create', [NoticiaController::class, 'create'])->name('noticias.create');
               Route::post('noticias', [NoticiaController::class, 'store'])->name('noticias.store');
               Route::get('noticias/{noticia}', [NoticiaController::class, 'show'])->name('noticias.show');
               Route::get('noticias/{noticia}/edit', [NoticiaController::class, 'edit'])->name('noticias.edit');
               Route::put('noticias/{noticia}', [NoticiaController::class, 'update'])->name('noticias.update');
               Route::delete('noticias/{noticia}', [NoticiaController::class, 'destroy'])->name('noticias.destroy');
          });

          Route::get('admin/statistics', [StatisticController::class, 'getStatistics'])->name('statistics.index');
          Route::get('/generar-csv', [ExportarCsvController::class, 'generarCsv'])->name('generarCsv.general');

          Route::prefix('operador')->name('operador.')->group(function () {
               Route::get('/pendientes', [OperadorProductoController::class, 'pendientes'])->name('pendientes');

               Route::get('/productos/{producto}', [OperadorProductoController::class, 'showProducto'])->name('productos.show');
               Route::post('/productos/{producto}/validar', [OperadorProductoController::class, 'validar'])->name('productos.validar');
               Route::post('/productos/{producto}/rechazar', [OperadorProductoController::class, 'rechazar'])->name('productos.rechazar');

               // Boletines para el operador
               Route::get('/boletines/{boletin}', [OperadorProductoController::class, 'showBoletin'])->name('boletines.show');
               Route::post('/boletines/{boletin}/validar', [OperadorProductoController::class, 'validarBoletin'])->name('boletines.validar');
               Route::post('/boletines/{boletin}/rechazar', [OperadorProductoController::class, 'rechazarBoletin'])->name('boletines.rechazar');
          });
     });
});

// Fallback general (asegúrate de que el 'dashboard' sea accesible o redirige a 'welcome')
Route::fallback(function () {
     // Si un usuario no autenticado llega aquí, podría redirigir a 'welcome'
     if (!Auth::check()) {
          return redirect()->route('welcome');
     }
     // Si un usuario autenticado llega aquí, podría redirigir al dashboard con un error
     return redirect()->route('dashboard')->with('error', 'Ruta no encontrada.');
});
