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
| PARA ROLES COMO: [SUPERADMIN] [ADMINISTRADOR] [OPERARIO] [FUNCIONARIO]
|--------------------------------------------------------------------------
*/
Breadcrumbs::for('usuarios.index', function ($trail) {
    $trail->parent('dashboard');
    $trail->push('Tabla de usuarios', route('usuarios.index'));
});

Breadcrumbs::for('usuarios.create', function ($trail) {
    $trail->parent('usuarios.index')
        ->push('Crear usuario', route('usuarios.create'));
});

Breadcrumbs::for('usuarios.show', function ($trail, $usuario) {
    $trail->parent('usuarios.index')
        ->push('Detalle de usuario', route('usuarios.show', $usuario));
});

Breadcrumbs::for('usuarios.edit', function ($trail, $usuario) {
    $trail->parent('usuarios.index')
        ->push('Editar usuario', route('usuarios.edit', $usuario));
});

Breadcrumbs::for('productos.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Tabla de productos', route('productos.index'));
});

Breadcrumbs::for('productos.create', function ($trail) {
    $trail->parent('productos.index')
        ->push('Crear producto', route('productos.create'));
});

Breadcrumbs::for('productos.show', function ($trail, $producto) {
    $trail->parent('productos.index')
        ->push('Detalle de producto', route('productos.show', $producto));
});

Breadcrumbs::for('productos.edit', function ($trail, $producto) {
    $trail->parent('productos.index')
        ->push('Editar producto', route('productos.edit', $producto));
});

Breadcrumbs::for('boletines.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Tabla de boletines', route('boletines.index'));
});

Breadcrumbs::for('boletines.create', function ($trail) {
    $trail->parent('boletines.index')
        ->push('Crear Boletin', route('boletines.create'));
});

Breadcrumbs::for('boletines.show', function ($trail, $boletin) {
    $trail->parent('boletines.index')
        ->push('Detalle de boletin', route('boletines.show', $boletin));
});

Breadcrumbs::for('boletines.edit', function ($trail, $boletin) {
    $trail->parent('boletines.index')
        ->push('Editar boletin', route('boletines.edit', $boletin));
});

Breadcrumbs::for('centroAyuda.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Centro de ayuda', route('centroAyuda.index'));
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
        ->push('Vista de usuarios', route('view-user.index'));
});

Breadcrumbs::for('view-user.create', function ($trail) {
    $trail->parent('view-user.index')
        ->push('Crear usuario', route('view-user.create'));
});

Breadcrumbs::for('view-user.show', function ($trail, $id) {
    $trail->parent('view-user.index')
        ->push('Detalle de usuario', route('view-user.show', $id));
});

Breadcrumbs::for('view-user.edit', function ($trail, $id) {
    $trail->parent('view-user.index')
        ->push('Editar usuario', route('view-user.edit', $id));
});

Breadcrumbs::for('view-user.historial', function ($trail, $id) {
    $trail->parent('view-user.show', $id)
        ->push('Historial', route('view-user.historial', $id));
});

Breadcrumbs::for('statistics.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Estadisticas', route('statistics.index'));
});

Breadcrumbs::for('noticias.index', function ($trail) {
    $trail->parent('dashboard')
        ->push('Tabla de noticias', route('noticias.index'));
});

Breadcrumbs::for('noticias.create', function ($trail) {
    $trail->parent('noticias.index')
        ->push('Crear noticia', route('noticias.create'));
});

Breadcrumbs::for('noticias.show', function ($trail, $noticia) {
    $trail->parent('noticias.index')
        ->push('Ver noticia', route('noticias.show', $noticia));
});

Breadcrumbs::for('noticias.edit', function ($trail, $noticia) {
    $trail->parent('noticias.index')
        ->push('Editar noticia', route('noticias.edit', $noticia));
});