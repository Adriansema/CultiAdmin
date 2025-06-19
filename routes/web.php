<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserEstado;
use App\Http\Controllers\PqrsController;
use App\Http\Controllers\BoletinController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\CentroAyudaController;
use App\Http\Controllers\ExportarCsvController;
use App\Http\Controllers\AccesibilidadController;
use App\Http\Controllers\PendienteBolController;
use App\Http\Controllers\PendienteProController;

// ------------------------------------------------------------------------------------
// Rutas PÚBLICAS (No requieren autenticación)
// ------------------------------------------------------------------------------------

// Rutas para PQRS
Route::prefix('pqrs')->name('pqrs.')->group(function () {
     Route::get('/crear', [PqrsController::class, 'create'])->name('create');
     Route::post('/store', [PqrsController::class, 'store'])->name('store');
});

// Ruta para verificar si el correo existe 
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

// Ruta de Estadística publica
Route::get('/public-statistics', [StatisticController::class, 'index'])->name('statistics.index.public');

// Ruta para generar masivos usuarios
Route::get('/generar-csv', [ExportarCsvController::class, 'generarCsv'])->name('generarCsv.general');

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

     // --- Módulo de PRODUCTOS ---
     Route::prefix('producto')->name('productos.')->group(function () {
          Route::get('/', [ProductoController::class, 'index'])->name('index')->middleware('can:crear producto');
          Route::get('/create', [ProductoController::class, 'create'])->name('create');
          Route::post('/', [ProductoController::class, 'store'])->name('store');
          Route::post('/importar-csv', [ProductoController::class, 'importarCSV'])->name('importarcsv');
          Route::get('/exportar-csv', [ProductoController::class, 'exportarCSV'])->name('exportarCSV');
          Route::get('/{producto}/edit', [ProductoController::class, 'edit'])->name('edit')->middleware('can:editar producto');
          Route::get('/{producto}', [ProductoController::class, 'show'])->name('show');
          Route::put('/{producto}', [ProductoController::class, 'update'])->name('update')->middleware('can:actualizar producto');
          Route::delete('/{producto}', [ProductoController::class, 'destroy'])->name('destroy')->middleware('can:eliminar producto');
     });

     // --- Módulo de BOLETINES ---
     Route::prefix('boletin')->name('boletines.')->group(function () {
          Route::get('/', [BoletinController::class, 'index'])->name('index')->middleware('can:crear boletin');
          Route::get('/create', [BoletinController::class, 'create'])->name('create');
          Route::post('/', [BoletinController::class, 'store'])->name('store');
          Route::post('/importar-pdf', [BoletinController::class, 'importarPdf'])->name('importarPdf');
          Route::get('/exportar-csv', [BoletinController::class, 'exportarCSV'])->name('exportarCSV');
          Route::get('/{boletin}/edit', [BoletinController::class, 'edit'])->name('edit')->middleware('can:editar boletin');
          Route::put('/{boletin}', [BoletinController::class, 'update'])->name('update')->middleware('can:actualizar boletin');
          Route::delete('/{boletin}', [BoletinController::class, 'destroy'])->name('destroy')->middleware('can:eliminar boletin');
     });

     // --- Módulo de PENDIENTES ( PRODUCTOS Y BOLETINES ) ---
     Route::prefix('pendiente')->name('pendientes.')->group(function () {
          Route::get('/productos', [PendienteProController::class, 'index'])->name('productos.index')->middleware('can:ver productos pendiente');

          // Ruta para la página de Boletines Pendientes
          Route::get('/boletines', [PendienteBolController::class, 'index'])->name('boletines.index')->middleware('can:ver boletines pendiente');


          // Rutas para la validación/revisión de productos (si las manejas aquí)
          Route::get('/productos/{producto}', [PendienteProController::class, 'show'])->name('prouctos.show');
          Route::post('/productos/{producto}/validar', [PendienteProController::class, 'validar'])->name('productos.validar');
          Route::post('/productos/{producto}/rechazar', [PendienteProController::class, 'rechazar'])->name('productos.rechazar');

          // Rutas para la validación/revisión de boletines (si las manejas aquí)
          Route::get('/boletines/{boletin}', [PendienteBolController::class, 'show'])->name('boletines.show');
          Route::post('/boletines/{boletin}/validar', [PendienteBolController::class, 'validarBoletin'])->name('boletines.validar');
          Route::post('/boletines/{boletin}/rechazar', [PendienteBolController::class, 'rechazarBoletin'])->name('boletines.rechazar');
     });

     // --- Módulo de USUARIOS ---
     Route::prefix('usuario')->name('usuarios.')->group(function () {
          Route::get('/', [UsuarioController::class, 'index'])->name('index')->middleware('can:crear usuario');
          Route::get('/create', [UsuarioController::class, 'create'])->name('create');
          Route::post('/', [UsuarioController::class, 'store'])->name('store');
          Route::post('/importar-csv', [UsuarioController::class, 'importarCsv'])->name('importarCsv');
          Route::get('/exportar', [UsuarioController::class, 'exportarCSV'])->name('exportar');
          Route::get('/{usuario}/edit', [UsuarioController::class, 'edit'])->name('edit')->middleware('can:editar usuario');
          Route::put('/{usuario}', [UsuarioController::class, 'update'])->name('update')->middleware('can:actualizar usuario');
          Route::patch('/{usuario}/toggle', [UsuarioController::class, 'toggle'])->name('toggle');
     });

     // --- Módulo de NOTICIAS ---
     Route::prefix('noticia')->name('noticias.')->group(function () {
          Route::get('/', [NoticiaController::class, 'index'])->name('index')->middleware('can:crear noticia');
          Route::get('/create', [NoticiaController::class, 'create'])->name('create');
          Route::post('/', [NoticiaController::class, 'store'])->name('store');
          Route::get('/{noticia}', [NoticiaController::class, 'show'])->name('show');
          Route::get('/{noticia}/edit', [NoticiaController::class, 'edit'])->name('edit')->middleware('can:editar noticia');
          Route::put('/{noticia}', [NoticiaController::class, 'update'])->name('update')->middleware('can:actualizar noticia');
          Route::delete('/{noticia}', [NoticiaController::class, 'destroy'])->name('destroy')->middleware('can:eliminar noticia');
     });

     // Ruta de Estadística protegida  
     Route::get('/admin/statistics', [StatisticController::class, 'getStatistics'])->name('statistics.index')->middleware('can:ver estadisticas');
});

// ------------------------------------------------------------------------------------
// Fallback general (para rutas no encontradas)
// ------------------------------------------------------------------------------------
Route::fallback(function () {
     if (!Auth::check()) {
          // Si el usuario no está autenticado, redirige a la página de login
          return redirect()->route('login');
     }
     // Si el usuario está autenticado pero la ruta no se encuentra, redirige al dashboard
     return redirect()->route('dashboard')->with('error', 'La página solicitada no existe o no tienes permiso para acceder a ella.');
});
