@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 pt-8"> {{-- Contenedor principal con padding --}}
        {{-- Encabezado y botón Volver --}}
        <div class="flex items-center mb-6">
            <a href="{{ route('productos.index') }}" class="flex items-center text-blue-600 hover:text-blue-800 mr-4 transition duration-200 ease-in-out">
                <img src="{{ asset('images/reverse.svg') }}" class="w-5 h-5 mr-1" alt="Icono Volver">
                <span class="text-base font-medium">Volver a Productos</span>
            </a>
            <h1 class="text-3xl font-extrabold text-gray-800">Detalles del Producto</h1>
        </div>

        {{-- Breadcrumbs (si los usas, asegúrate de que Breadcrumbs::render esté disponible) --}}
        @if (class_exists('Breadcrumbs')) {{-- Verificación para evitar errores si Breadcrumbs no está configurado --}}
            <div class="mb-4">
                {!! Breadcrumbs::render('productos.show', $producto) !!}
            </div>
        @endif

        <div class="bg-white shadow-xl rounded-lg p-8 border border-gray-100"> {{-- Contenedor principal de la tarjeta --}}
            {{-- Sección de Información General --}}
            <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Información General</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mb-6">
                <p class="text-gray-800"><strong class="font-semibold text-gray-700">ID:</strong> {{ $producto->id }}</p>
                <p class="text-gray-800"><strong class="font-semibold text-gray-700">Tipo:</strong> {{ $producto->tipo }}</p>
                <p class="text-gray-800"><strong class="font-semibold text-gray-700">Estado:</strong> {{ $producto->estado }}</p>
                <p class="text-gray-800"><strong class="font-semibold text-gray-700">Observaciones Generales:</strong> {{ $producto->observaciones ?? 'N/A' }}</p>
                <p class="text-gray-800"><strong class="font-semibold text-gray-700">Fecha de Creación:</strong> {{ $producto->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}</p>
                <p class="text-gray-800"><strong class="font-semibold text-gray-700">Última Actualización:</strong> {{ $producto->updated_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}</p>
            </div>

            {{-- Sección del Usuario Creador --}}
            <div class="mb-6 pb-2 border-b border-gray-200">
                <p class="text-gray-800">
                    <strong class="font-semibold text-gray-700">Usuario Creador:</strong>
                    @if ($producto->user)
                        {{ $producto->user->name }}
                        @if ($producto->user->roles->isNotEmpty())
                            <span class="text-gray-600"> ({{ $producto->user->roles->pluck('name')->join(', ') }})</span>
                        @endif
                    @else
                        <span class="text-gray-500">Usuario Desconocido</span>
                    @endif
                </p>
            </div>

            {{-- Sección de Imagen del Producto --}}
            @if ($producto->imagen)
                <div class="mb-6 pb-2 border-b border-gray-200">
                    <strong class="font-semibold text-gray-700 block mb-2">Imagen del Producto:</strong>
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto"
                        class="mt-2 w-full max-w-md h-auto object-cover rounded-lg shadow-md border border-gray-200">
                </div>
            @endif

            {{-- Sección de Video del Producto (usando el parcial) --}}
            {{-- La lógica para determinar si es YouTube o video directo está dentro del parcial --}}
            @include('productos.partials.video_player', ['videoUrl' => $producto->RutaVideo])

            {{-- Sección de Detalles Específicos (Café o Mora) --}}
            @if ($producto->tipo === 'café' && $producto->cafe)
                <h2 class="text-2xl font-bold text-gray-800 mt-6 mb-4 pb-2 border-b border-gray-200">Detalles de Café</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mb-6">
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Número de Página:</strong> {{ $producto->cafe->numero_pagina }}</p>
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Clase:</strong> {{ $producto->cafe->clase ?? 'N/A' }}</p>
                    <div class="col-span-1 md:col-span-2"> {{-- Ocupa todo el ancho en pantallas medianas/grandes --}}
                        <strong class="font-semibold text-gray-700 block mb-1">Información de Café:</strong>
                        <p class="text-gray-800 bg-gray-50 p-3 rounded-md border border-gray-100">{{ $producto->cafe->informacion }}</p>
                    </div>
                </div>
            @elseif ($producto->tipo === 'mora' && $producto->mora)
                <h2 class="text-2xl font-bold text-gray-800 mt-6 mb-4 pb-2 border-b border-gray-200">Detalles de Mora</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mb-6">
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Número de Página:</strong> {{ $producto->mora->numero_pagina }}</p>
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Clase:</strong> {{ $producto->mora->clase ?? 'N/A' }}</p>
                    <div class="col-span-1 md:col-span-2">
                        <strong class="font-semibold text-gray-700 block mb-1">Información de Mora:</strong>
                        <p class="text-gray-800 bg-gray-50 p-3 rounded-md border border-gray-100">{{ $producto->mora->informacion }}</p>
                    </div>
                </div>
            @endif

            {{-- Sección de Botones de Acción --}}
            <div class="mt-8 pt-4 border-t border-gray-200 flex space-x-4 justify-end">
                <a href="{{ route('productos.edit', $producto) }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 shadow-md">
                    Editar Producto
                </a>
                <a href="{{ route('productos.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 shadow-md">
                    Volver a la Lista
                </a>
            </div>
        </div>
    </div>
@endsection
