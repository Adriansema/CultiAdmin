@extends('layouts.app')

@section('content')
    {{-- Encabezado de la página --}}
    <div class="inline-block px-10 py-6">
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl whitespace-nowrap font-bold">Detalles del producto</h1>
        </div>
        <div class="py-2">
            {!! Breadcrumbs::render('productos.show', $producto) !!}
        </div>
    </div>

    {{-- Contenedor principal de la tarjeta de detalles --}}
    <div class="container mx-auto px-4 pb-12"> {{-- Aumentado padding inferior --}}
        <div
            class="bg-white shadow-2xl rounded-2xl p-6 md:p-10 border border-gray-300 relative overflow-hidden">
            {{-- Diseño de tarjeta elevado --}}

            {{-- Ribbon de Estado (como un distintivo) --}}
            <div class="absolute top-0 right-0 mt-4 mr-4 z-10">
                <span
                    class="inline-block px-4 py-2 text-md font-extrabold text-white rounded-full shadow-lg transform rotate-3
                    {{ $producto->estado === 'aprobado' ? 'bg-green-600' : ($producto->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                    {{ ucfirst($producto->estado) }}
                </span>
            </div>

            {{-- Sección de Información General --}}
            <h2 class="text-3xl font-extrabold text-gray-800 mb-8 text-center border-b-2 border-gray-200 pb-4">
                Información General del Producto
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-6 mb-10"> {{-- Grid más flexible --}}
                <div class="flex items-center">
                    <strong class="font-semibold text-gray-700 w-32 flex-shrink-0">Tipo:</strong>
                    <span class="text-gray-900 text-lg ml-2">{{ ucfirst($producto->tipo) }}</span>
                </div>
                <div class="flex items-center md:col-span-1 lg:col-span-1">
                    <strong class="font-semibold text-gray-700 w-32 flex-shrink-0">Creado por:</strong>
                    <span class="text-gray-900 text-lg ml-2">
                        @if ($producto->user)
                            {{ $producto->user->name }}
                            @if ($producto->user->roles->isNotEmpty())
                                <span
                                    class="text-gray-500 text-sm">({{ $producto->user->roles->pluck('name')->join(', ') }})</span>
                            @endif
                        @else
                            <span class="text-gray-500">Usuario desconocido</span>
                        @endif
                    </span>
                </div>
                <div class="flex items-center">
                    <strong class="font-semibold text-gray-700 w-32 flex-shrink-0">Fecha de creación:</strong>
                    <span
                        class="text-gray-900 text-lg ml-2">{{ $producto->created_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}</span>
                </div>
                <div class="flex items-center">
                    <strong class="font-semibold text-gray-700 w-32 flex-shrink-0">Última actualización:</strong>
                    <span
                        class="text-gray-900 text-lg ml-2">{{ $producto->updated_at->locale('es')->translatedFormat('d \d\e F \d\e\l Y h:i a') }}</span>
                </div>

                {{-- Observaciones Generales (solo para Café o Mora) --}}
                @if ($producto->tipo === 'café' || $producto->tipo === 'mora')
                    <div class="md:col-span-2 lg:col-span-3 mt-4">
                        <strong class="font-semibold text-gray-700 block mb-2 text-lg">Observaciones del
                            validador/rechazador:</strong>
                        <div
                            class="bg-blue-50 p-4 rounded-lg border border-blue-200 text-gray-800 leading-relaxed shadow-inner">
                            {{ $producto->observaciones ?? 'No hay observaciones.' }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sección de Imagen del Producto --}}
            @if ($producto->imagen)
                <div class="mb-10 pb-6 border-b-2 border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">Imagen Principal del Producto</h3>
                    <div
                        class="flex justify-center items-center bg-gray-50 p-6 rounded-lg shadow-lg border border-gray-200">
                        <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto"
                            class="max-w-full h-auto max-h-[500px] object-contain rounded-lg transform transition-transform duration-300 hover:scale-105 cursor-pointer border-4 border-white shadow-md"
                            onclick="window.open(this.src, '_blank');">
                    </div>
                    <p class="text-center text-gray-500 text-sm mt-3">Haz clic en la imagen para verla en tamaño completo.
                    </p>
                </div>
            @endif

            {{-- Sección de Video del Producto General (para Café o Mora) --}}
            @if (($producto->tipo === 'café' || $producto->tipo === 'mora') && $producto->rutavideo)
                <div class="mb-10 pb-6 border-b-2 border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">Video General del Proceso/Producto</h3>
                    <div class="bg-gray-100 p-6 rounded-lg shadow-lg flex justify-center items-center">
                        @include('productos.partials.video_player', ['videoUrl' => $producto->rutavideo])
                    </div>
                </div>
            @endif

            {{-- Sección de Detalles Específicos --}}
            @if ($producto->tipo === 'café' && $producto->cafe)
                <div class="bg-orange-50 rounded-xl p-8 mb-10 shadow-lg border border-orange-200">
                    <h3 class="text-3xl font-extrabold text-orange-800 mb-6 text-center">Detalles Específicos del Café</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
                        <div class="flex items-center">
                            <strong class="font-semibold text-orange-700 w-40 flex-shrink-0">Número de página:</strong>
                            <span class="text-gray-900 text-lg ml-2">{{ $producto->cafe->numero_pagina ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center">
                            <strong class="font-semibold text-orange-700 w-40 flex-shrink-0">Clase:</strong>
                            <span class="text-gray-900 text-lg ml-2">{{ $producto->cafe->clase ?? 'N/A' }}</span>
                        </div>
                        <div class="md:col-span-2 mt-4">
                            <strong class="font-semibold text-orange-700 block mb-3 text-lg">Información detallada del
                                café:</strong>
                            <div
                                class="bg-white p-5 rounded-lg border border-orange-100 shadow-sm text-gray-800 leading-relaxed">
                                {!! nl2br(e($producto->cafe->informacion ?? 'No hay información adicional para este café.')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($producto->tipo === 'mora' && $producto->mora)
                <div class="bg-purple-50 rounded-xl p-8 mb-10 shadow-lg border border-purple-200">
                    <h3 class="text-3xl font-extrabold text-purple-800 mb-6 text-center">Detalles Específicos de la Mora
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
                        <div class="flex items-center">
                            <strong class="font-semibold text-purple-700 w-40 flex-shrink-0">Número de página:</strong>
                            <span class="text-gray-900 text-lg ml-2">{{ $producto->mora->numero_pagina ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center">
                            <strong class="font-semibold text-purple-700 w-40 flex-shrink-0">Clase:</strong>
                            <span class="text-gray-900 text-lg ml-2">{{ $producto->mora->clase ?? 'N/A' }}</span>
                        </div>
                        <div class="md:col-span-2 mt-4">
                            <strong class="font-semibold text-purple-700 block mb-3 text-lg">Información detallada de la
                                mora:</strong>
                            <div
                                class="bg-white p-5 rounded-lg border border-purple-100 shadow-sm text-gray-800 leading-relaxed">
                                {!! nl2br(e($producto->mora->informacion ?? 'No hay información adicional para esta mora.')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @elseif ($producto->tipo === 'videos' && $producto->videos)
                <div class="bg-teal-50 rounded-xl p-8 mb-10 shadow-lg border border-teal-200">
                    <h3 class="text-3xl font-extrabold text-teal-800 mb-6 text-center">Detalles Específicos del Video</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
                        <div class="flex items-center">
                            <strong class="font-semibold text-teal-700 w-40 flex-shrink-0">Autor:</strong>
                            <span class="text-gray-900 text-lg ml-2">{{ $producto->videos->autor ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center">
                            <strong class="font-semibold text-teal-700 w-40 flex-shrink-0">Título:</strong>
                            <span class="text-gray-900 text-lg ml-2">{{ $producto->videos->titulo ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center">
                            <strong class="font-semibold text-teal-700 w-40 flex-shrink-0">Subtipo de video:</strong>
                            <span class="text-gray-900 text-lg ml-2">{{ $producto->videos->tipo ?? 'N/A' }}</span>
                        </div>
                        <div class="md:col-span-2 mt-4">
                            <strong class="font-semibold text-teal-700 block mb-3 text-lg">Descripción del video:</strong>
                            <div
                                class="bg-white p-5 rounded-lg border border-teal-100 shadow-sm text-gray-800 leading-relaxed">
                                {!! nl2br(e($producto->videos->descripcion ?? 'No hay descripción disponible para este video.')) !!}
                            </div>
                        </div>
                    </div>
                    {{-- Video player para el producto de tipo 'videos' --}}
                    @if ($producto->videos->rutaVideo)
                        <div class="mt-8 pt-6 border-t border-teal-200">
                            <h3 class="text-2xl font-bold text-teal-800 mb-4 text-center">Reproductor de Video Específico
                            </h3>
                            <div class="bg-gray-100 p-6 rounded-lg shadow-lg flex justify-center items-center">
                                @include('productos.partials.video_player', [
                                    'videoUrl' => $producto->videos->rutaVideo,
                                ])
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Sección de Botones de Acción --}}
            <div class="mt-12 pt-6 border-t-2 border-gray-200 flex justify-center"> {{-- Borde superior más grueso y centrado --}}
                <a href="{{ route('productos.index') }}"
                    class="bg-[var(--color-textmarca)] hover:bg-[var(--color-texthovermarca)] py-3 px-8 rounded-full text-lg font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-300 ease-in-out transform hover:-translate-x-1 shadow-md">
                    <img src="{{ asset('images/regresar.svg') }}" alt="Regresar"
                        class="w-6 h-6 mr-3 filter brightness-0 invert">
                    <span class="whitespace-nowrap">{{ __('Volver a la lista de productos') }}</span>
                </a>
            </div>
        </div>
    </div>
@endsection
