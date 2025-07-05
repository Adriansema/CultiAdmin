@extends('layouts.app')

@section('content')
    <div class="inline-block px-20 py-6">
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl font-bold whitespace-nowrap">Ver producto</h1>
        </div>
        <div class="py-2">
            {!! Breadcrumbs::render('productos.show', $producto) !!}
        </div>
    </div>

    <div class="container px-4 pt-8 mx-auto"> {{-- Contenedor principal con padding --}}
        <div class="p-8 bg-white border border-gray-100 rounded-lg shadow-xl"> {{-- Contenedor principal de la tarjeta --}}
            {{-- Sección de Información General --}}
            <h2 class="pb-2 mb-4 text-2xl font-bold text-gray-800 border-b border-gray-200">Información general</h2>
            <div class="grid grid-cols-1 mb-6 md:grid-cols-2 gap-x-8 gap-y-4">
                <p class="text-gray-800"><strong class="font-semibold text-gray-700">ID:</strong> {{ $producto->id }}</p>
                <p class="text-gray-800"><strong class="font-semibold text-gray-700">Tipo:</strong> {{ $producto->tipo }}
                </p>
                <p class="text-gray-800"><strong class="font-semibold text-gray-700">Estado:</strong>
                    {{ $producto->estado }}</p>
                {{-- Fragmento de código para Observaciones Generales, visible solo para Café o Mora --}}
                @if ($producto->tipo === 'café' || $producto->tipo === 'mora')
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Observaciones generales:</strong>
                        {{ $producto->observaciones ?? 'N/A' }}</p>
                @endif
                <p class="text-gray-800"><strong class="font-semibold text-gray-700">Fecha de Creación:</strong>
                    {{ $producto->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}</p>
                <p class="text-gray-800"><strong class="font-semibold text-gray-700">Última Actualización:</strong>
                    {{ $producto->updated_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}</p>
            </div>

            {{-- Sección del Usuario Creador --}}
            <div class="pb-2 mb-6 border-b border-gray-200">
                <p class="text-gray-800">
                    <strong class="font-semibold text-gray-700">Usuario creador:</strong>
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
                <div class="pb-2 mb-6 border-b border-gray-200">
                    <strong class="block mb-2 font-semibold text-gray-700">Imagen del producto:</strong>
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto"
                        class="object-cover w-full h-auto max-w-md mt-2 border border-gray-200 rounded-lg shadow-md">
                </div>
            @endif

            {{-- Sección de Video del Producto (para Café o Mora) --}}
            @if (($producto->tipo === 'café' || $producto->tipo === 'mora') && $producto->RutaVideo)
                <h2 class="pb-2 mt-6 mb-4 text-2xl font-bold text-gray-800 border-b border-gray-200">Video del producto
                    General</h2>
                @include('productos.partials.video_player', ['videoUrl' => $producto->RutaVideo])
            @endif

            {{-- Sección de Detalles Específicos (Café, Mora o Videos) --}}
            @if ($producto->tipo === 'café' && $producto->cafe)
                <h2 class="pb-2 mt-6 mb-4 text-2xl font-bold text-gray-800 border-b border-gray-200">Detalles de café</h2>
                <div class="grid grid-cols-1 mb-6 md:grid-cols-2 gap-x-8 gap-y-4">
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Número de página:</strong>
                        {{ $producto->cafe->numero_pagina ?? 'N/A' }}</p>
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Clase:</strong>
                        {{ $producto->cafe->clase ?? 'N/A' }}</p>
                    <div class="col-span-1 md:col-span-2"> {{-- Ocupa todo el ancho en pantallas medianas/grandes --}}
                        <strong class="block mb-1 font-semibold text-gray-700">Información de café:</strong>
                        <p class="p-3 text-gray-800 border border-gray-100 rounded-md bg-gray-50">
                            {{ $producto->cafe->informacion ?? 'N/A' }}</p>
                    </div>
                </div>
            @elseif ($producto->tipo === 'mora' && $producto->mora)
                <h2 class="pb-2 mt-6 mb-4 text-2xl font-bold text-gray-800 border-b border-gray-200">Detalles de mora</h2>
                <div class="grid grid-cols-1 mb-6 md:grid-cols-2 gap-x-8 gap-y-4">
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Número de página:</strong>
                        {{ $producto->mora->numero_pagina ?? 'N/A' }}</p>
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Clase:</strong>
                        {{ $producto->mora->clase ?? 'N/A' }}</p>
                    <div class="col-span-1 md:col-span-2">
                        <strong class="block mb-1 font-semibold text-gray-700">Información de mora:</strong>
                        <p class="p-3 text-gray-800 border border-gray-100 rounded-md bg-gray-50">
                            {{ $producto->mora->informacion ?? 'N/A' }}</p>
                    </div>
                </div>
            @elseif ($producto->tipo === 'videos' && $producto->videos)
                <h2 class="pb-2 mt-6 mb-4 text-2xl font-bold text-gray-800 border-b border-gray-200">Detalles de video</h2>
                <div class="grid grid-cols-1 mb-6 md:grid-cols-2 gap-x-8 gap-y-4">
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Autor:</strong>
                        {{ $producto->videos->autor ?? 'N/A' }}</p>
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Título:</strong>
                        {{ $producto->videos->titulo ?? 'N/A' }}</p>
                    <p class="text-gray-800"><strong class="font-semibold text-gray-700">Subtipo de video:</strong>
                        {{ $producto->videos->tipo ?? 'N/A' }}</p> {{-- 'tipo' en Video es el subtipo --}}
                    <div class="col-span-1 md:col-span-2">
                        <strong class="block mb-1 font-semibold text-gray-700">Descripción:</strong>
                        <p class="p-3 text-gray-800 border border-gray-100 rounded-md bg-gray-50">
                            {{ $producto->videos->descripcion ?? 'N/A' }}</p>
                    </div>
                </div>
                {{-- Video player para el producto de tipo 'videos' --}}
                @if ($producto->videos->rutaVideo)
                    <h3 class="pb-2 mt-4 mb-3 text-xl font-semibold text-gray-800 border-b border-gray-200">Video específico
                    </h3>
                    @include('productos.partials.video_player', [
                        'videoUrl' => $producto->videos->rutaVideo,
                    ])
                @endif
            @endif

            {{-- Sección de Botones de Acción --}}
            <div class="flex justify-start pt-4 mt-8 space-x-4 border-t border-gray-200">
                <a href="{{ route('productos.index') }}"
                    class="px-4 py-2 font-bold text-white transition duration-300 ease-in-out transform bg-gray-500 rounded-lg shadow-md hover:bg-gray-600 hover:scale-105">
                    Volver a la lista
                </a>
            </div>
        </div>
    </div>
@endsection
