<?php

// actualizacion 06/06/2025 - Integración de permisos con Spatie y Laravel Policies

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserEstado;
use App\Http\Controllers\PqrsController;
use App\Http\Middleware\Roles_Admin_Opera; // Este middleware lo mantendremos para un control de acceso inicial
use App\Http\Controllers\BoletinController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\CentroAyudaController;
use App\Http\Controllers\ExportarCsvController;
use App\Http\Controllers\AccesibilidadController;
use App\Http\Controllers\Operador\OperadorProductoController;
use App\Models\Producto; // Importar el modelo Producto para el middleware 'can'
use App\Models\Boletin;  // Importar el modelo Boletin para el middleware 'can'
use App\Models\User;     // Importar el modelo User para el middleware 'can'

// Rutas públicas (no necesitan autenticación ni permisos)
// Estas rutas NO deben estar dentro del middleware 'auth'
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

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

    // Dashboard (visible para cualquier usuario autenticado y activo)
    // La autorización para el contenido del dashboard se puede hacer dentro del DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Este middleware Roles_Admin_Opera::class es un control de acceso inicial
    // para usuarios que tienen el rol de administrador u operador.
    Route::middleware([Roles_Admin_Opera::class])->group(function () {

        // -------------------------------------------------------------------
        // Módulo: Productos (Cultivos)
        // Las Policies (`ProductoPolicy`) ya se encargan de los permisos específicos.
        // Se usa middleware 'can' que delega a la Policy.
        // -------------------------------------------------------------------
        Route::prefix('productos')->name('productos.')->group(function () {
            Route::get('/', [ProductoController::class, 'index'])->name('index')
                 ->middleware('can:viewAny,'.Producto::class); // ver_productos

            Route::get('/create', [ProductoController::class, 'create'])->name('create')
                 ->middleware('can:create,'.Producto::class); // crear_productos

            Route::post('/', [ProductoController::class, 'store'])->name('store')
                 ->middleware('can:create,'.Producto::class); // crear_productos

            Route::post('/importar-csv', [ProductoController::class, 'importarCSV'])->name('importar.csv')
                 ->middleware('can:import,'.Producto::class); // importar_productos

            Route::get('/exportar-csv', [ProductoController::class, 'exportarCSV'])->name('exportarCSV')
                 ->middleware('can:export,'.Producto::class); // exportar_productos

            Route::get('/{producto}/edit', [ProductoController::class, 'edit'])->name('edit')
                 ->middleware('can:update,producto'); // editar_productos (Pasa la instancia de producto)

            Route::get('/{producto}', [ProductoController::class, 'show'])->name('show')
                 ->middleware('can:view,producto'); // ver_productos (Pasa la instancia de producto)

            Route::put('/{producto}', [ProductoController::class, 'update'])->name('update')
                 ->middleware('can:update,producto'); // editar_productos (Pasa la instancia de producto)

            Route::delete('/{producto}', [ProductoController::class, 'destroy'])->name('destroy')
                 ->middleware('can:delete,producto'); // eliminar_productos (Pasa la instancia de producto)
        });


        // -------------------------------------------------------------------
        // Módulo: Boletines
        // Asumo que tienes una `BoletinPolicy` similar a `ProductoPolicy`.
        // -------------------------------------------------------------------
        Route::prefix('boletines')->name('boletines.')->group(function () {
            Route::get('/', [BoletinController::class, 'index'])->name('index')
                 ->middleware('can:viewAny,'.Boletin::class); // ver_boletines

            Route::get('/create', [BoletinController::class, 'create'])->name('create')
                 ->middleware('can:create,'.Boletin::class); // crear_boletines

            Route::post('/', [BoletinController::class, 'store'])->name('store')
                 ->middleware('can:create,'.Boletin::class); // crear_boletines

            Route::post('/importar-pdf', [BoletinController::class, 'importarPdf'])->name('importarPdf')
                 ->middleware('can:import,'.Boletin::class); // importar_boletines

            Route::get('/exportar-csv', [BoletinController::class, 'exportarCSV'])->name('exportarCSV')
                 ->middleware('can:export,'.Boletin::class); // exportar_boletines

            Route::get('/{boletin}/edit', [BoletinController::class, 'edit'])->name('edit')
                 ->middleware('can:update,boletin'); // editar_boletines

            Route::put('/{boletin}', [BoletinController::class, 'update'])->name('update')
                 ->middleware('can:update,boletin'); // editar_boletines

            Route::delete('/{boletin}', [BoletinController::class, 'destroy'])->name('destroy')
                 ->middleware('can:delete,boletin'); // eliminar_boletines

            Route::get('/{boletin}', [BoletinController::class, 'show'])->name('show')
                 ->middleware('can:view,boletin'); // ver_boletines
        });

        // -------------------------------------------------------------------
        // Módulo: Usuarios (Gestión de Usuarios)
        // Las Policies (`UserPolicy`) ya se encargan de los permisos específicos.
        // -------------------------------------------------------------------
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [UsuarioController::class, 'index'])->name('index')
                 ->middleware('can:viewAny,'.User::class); // ver_lista_usuarios

            Route::get('/create', [UsuarioController::class, 'create'])->name('create')
                 ->middleware('can:create,'.User::class); // crear_usuarios

            Route::post('/', [UsuarioController::class, 'store'])->name('store')
                 ->middleware('can:create,'.User::class); // crear_usuarios

            Route::post('/importar-csv', [UsuarioController::class, 'importarCsv'])->name('importarCsv')
                 ->middleware('can:import,'.User::class); // importar_usuarios

            Route::get('/exportar', [UsuarioController::class, 'exportarCSV'])->name('exportar')
                 ->middleware('can:export,'.User::class); // exportar_usuarios

            Route::get('/{usuario}', [UsuarioController::class, 'show'])->name('show')
                 ->middleware('can:view,usuario'); // ver lista de usuarios

            Route::get('/{usuario}/edit', [UsuarioController::class, 'edit'])->name('edit')
                 ->middleware('can:update,usuario'); // editar usuarios (update en la Policy)

            Route::put('/{usuario}', [UsuarioController::class, 'update'])->name('update')
                 ->middleware('can:update,usuario'); // editar usuarios (update en la Policy)

            // Asumo que 'toggle' se gestiona por 'activar usuarios' o 'desactivar usuarios'
            Route::patch('/{usuario}/toggle', [UsuarioController::class, 'toggle'])->name('toggle')
                 ->middleware('can:toggle,usuario'); // Delega a UserPolicy@toggle

            Route::delete('/{usuario}', [UsuarioController::class, 'destroy'])->name('destroy')
                 ->middleware('can:delete,usuario'); // eliminar usuarios
        });

        // Ruta API para el filtrado de usuarios (Protegida por Policy en el controlador)
        // No se añade 'can' middleware aquí, ya que el controlador tiene la lógica de autorización.
        Route::get('/api/usuarios-filtrados', [UsuarioController::class, 'getFilteredUsers'])->name('api.usuarios.filtrados');


        // -------------------------------------------------------------------
        // Módulo: Estadísticas (si fueran solo para admin)
        // La Policy en StatisticController.php protegerá este acceso.
        // -------------------------------------------------------------------
        Route::get('admin/statistics', [StatisticController::class, 'getStatistics'])->name('statistics.index')
             ->middleware('can:viewAny,\App\Models\Statistic'); // Asumo un modelo Statistic y un permiso.

        // -------------------------------------------------------------------
        // Módulo: Generar usuarios masivos (Solo para propósitos de desarrollo/prueba)
        // La Policy en ExportarCsvController.php protegerá este acceso.
        // -------------------------------------------------------------------
        Route::get('/generar-csv', [ExportarCsvController::class, 'generarCsv'])->name('generarCsv.general')
             ->middleware('can:generateCsv,\App\Models\User'); // O el modelo/permiso apropiado

        /*
        |--------------------------------------------------------------------------
        | Módulo: OPERADOR (Rutas específicas para el flujo del operador)
        |--------------------------------------------------------------------------
        */
        // Las Policies (`ProductoPolicy` y `BoletinPolicy` con los métodos `validate` y `reject`)
        // ya se encargan de los permisos específicos para el rol de operador.
        Route::prefix('operador')->name('operador.')->group(function () {
            Route::get('/pendientes', [OperadorProductoController::class, 'pendientes'])->name('pendientes')
                 ->middleware('can:viewAnyPending,'.Producto::class); // Asumo un permiso 'view_productos_pendientes' y un método 'viewAnyPending' en ProductoPolicy

            Route::get('/productos/{producto}', [OperadorProductoController::class, 'showProducto'])->name('productos.show')
                 ->middleware('can:view,producto'); // ver_productos

            Route::post('/productos/{producto}/validar', [OperadorProductoController::class, 'validar'])->name('productos.validar')
                 ->middleware('can:validate,producto'); // validar_productos

            Route::post('/productos/{producto}/rechazar', [OperadorProductoController::class, 'rechazar'])->name('productos.rechazar')
                 ->middleware('can:reject,producto'); // rechazar_productos

            // Boletines para el operador
            Route::get('/boletines/{boletin}', [OperadorProductoController::class, 'showBoletin'])->name('boletines.show')
                 ->middleware('can:view,boletin'); // ver_boletines

            Route::post('/boletines/{boletin}/validar', [OperadorProductoController::class, 'validarBoletin'])->name('boletines.validar')
                 ->middleware('can:validate,boletin'); // validar_boletines

            Route::post('/boletines/{boletin}/rechazar', [OperadorProductoController::class, 'rechazarBoletin'])->name('boletines.rechazar')
                 ->middleware('can:reject,boletin'); // rechazar_boletines
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
