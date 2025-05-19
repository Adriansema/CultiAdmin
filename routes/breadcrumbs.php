<?php

use Diglactic\Breadcrumbs\Breadcrumbs;

/*
|--------------------------------------------------------------------------
| DASHBOARD BASE
|--------------------------------------------------------------------------
*/
Breadcrumbs::for('dashboard', function ($trail) {
    $trail->push('Inicio', route('dashboard'));
});

/*
|--------------------------------------------------------------------------
| ADMINISTRADOR
|--------------------------------------------------------------------------
*/
Breadcrumbs::for('usuarios.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Gestión de Usuarios', route('usuarios.index'));
});

Breadcrumbs::for('usuarios.create', function ($trail) {
    $trail->parent('usuarios.index')
        ->push('Crear Usuario', route('usuarios.create'));
});

Breadcrumbs::for('usuarios.show', function ($trail, $usuario) {
    $trail->parent('usuarios.index')
        ->push('Detalle de Usuario', route('usuarios.show', $usuario));
});

Breadcrumbs::for('usuarios.edit', function ($trail, $usuario) {
    $trail->parent('usuarios.index')
        ->push('Editar Usuario', route('usuarios.edit', $usuario));
});

Breadcrumbs::for('productos.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Gestión de Productos', route('productos.index'));
});

Breadcrumbs::for('productos.create', function ($trail) {
    $trail->parent('productos.index')
        ->push('Crear Producto', route('productos.create'));
});

Breadcrumbs::for('productos.show', function ($trail, $producto) {
    $trail->parent('productos.index')
        ->push('Detalle de Producto', route('productos.show', $producto));
});

Breadcrumbs::for('productos.edit', function ($trail, $producto) {
    $trail->parent('productos.index')
        ->push('Editar Producto', route('productos.edit', $producto));
});

Breadcrumbs::for('boletines.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Gestión de Boletines', route('boletines.index'));
});

Breadcrumbs::for('boletines.create', function ($trail) {
    $trail->parent('boletines.index')
        ->push('Crear Boletín', route('boletines.create'));
});

Breadcrumbs::for('boletines.show', function ($trail, $boletin) {
    $trail->parent('boletines.index')
        ->push('Detalle de Boletín', route('boletines.show', $boletin));
});

Breadcrumbs::for('boletines.edit', function ($trail, $boletin) {
    $trail->parent('boletines.index')
        ->push('Editar Boletín', route('boletines.edit', $boletin));
});

Breadcrumbs::for('historial.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Historial', route('historial.index'));
});

Breadcrumbs::for('centroAyuda.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Centro de Ayuda', route('centroAyuda.index'));
});

Breadcrumbs::for('centroAyuda.contactForm', function ($trail) {
    $trail->parent('centroAyuda.index')
        ->push('Contacto', route('centroAyuda.contactForm'));
});

Breadcrumbs::for('accesibilidad.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Accesibilidad', route('accesibilidad.index'));
});

Breadcrumbs::for('view-user.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Vista de Usuarios', route('view-user.index'));
});

Breadcrumbs::for('view-user.create', function ($trail) {
    $trail->parent('view-user.index')
        ->push('Crear Usuario', route('view-user.create'));
});

Breadcrumbs::for('view-user.show', function ($trail, $id) {
    $trail->parent('view-user.index')
        ->push('Detalle de Usuario', route('view-user.show', $id));
});

Breadcrumbs::for('view-user.edit', function ($trail, $id) {
    $trail->parent('view-user.index')
        ->push('Editar Usuario', route('view-user.edit', $id));
});

Breadcrumbs::for('view-user.historial', function ($trail, $id) {
    $trail->parent('view-user.show', $id)
        ->push('Historial', route('view-user.historial', $id));
});

Breadcrumbs::for('statistics.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Estadísticas', route('statistics.index'));
});

/*
|--------------------------------------------------------------------------
| OPERADOR
|--------------------------------------------------------------------------
*/

// Página principal del operador (pendientes)
Breadcrumbs::for('operador.pendientes', function ($trail) {
    $trail->parent('dashboard'); // Asumiendo que ya tienes 'dashboard' definido
    $trail->push('Pendientes', route('operador.pendientes'));
});

// Detalle del producto pendiente
Breadcrumbs::for('operador.productos.show', function ($trail, $producto) {
    $trail->parent('operador.pendientes');
    $trail->push('Producto', route('operador.productos.show', $producto));
});

// Detalle del boletín pendiente
Breadcrumbs::for('operador.boletines.show', function ($trail, $boletin) {
    $trail->parent('operador.pendientes');
    $trail->push('Boletines', route('operador.boletines.show', $boletin));
});
