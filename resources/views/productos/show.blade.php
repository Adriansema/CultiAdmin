@extends('layouts.app')

@section('content')
{{-- Encabezado de la página --}}
<div class="inline-block px-10 py-6">
    <div class="flex items-center space-x-4">
        <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
        <h1 class="text-3xl font-bold whitespace-nowrap">Detalles del producto</h1>
    </div>
    <div class="py-2">
        {!! Breadcrumbs::render('productos.show', $producto) !!}
    </div>
</div>

{{-- Contenedor principal de la tarjeta de detalles --}}
<div class="container px-4 pb-12 mx-auto"> {{-- Aumentado padding inferior --}}
    <div class="relative p-6 overflow-hidden bg-white border border-gray-300 shadow-2xl rounded-2xl md:p-10">
        {{-- Diseño de tarjeta elevado --}}

        {{-- Ribbon de Estado (como un distintivo) --}}
        <div class="absolute top-0 right-0 z-10 mt-4 mr-4">
            <span
                class="py-3 px-8 rounded-full text-lg font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-300 ease-in-out transform hover:-translate-x-1 shadow-md
                {{ $producto->estado === 'aprobado' ? 'bg-green-600 hover:bg-green-700' : ($producto->estado === 'pendiente' ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-red-600 hover:bg-red-700') }}">
                {{ ucfirst($producto->estado) }}

            </span>
        </div>

        {{-- Sección de Información General --}}
        <h2 class="pb-4 mb-8 text-3xl font-extrabold text-center text-gray-800 border-b-2 border-gray-200">
            Información General del Producto
        </h2>

        <div class="grid grid-cols-1 mb-10 md:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-6"> {{-- Grid más flexible --}}
            <div class="flex items-center">
                <strong class="flex-shrink-0 w-32 font-semibold text-gray-700">Tipo:</strong>
                <span class="ml-2 text-lg text-gray-900">{{ ucfirst($producto->tipo) }}</span>
            </div>
            <div class="flex items-center md:col-span-1 lg:col-span-1">
                <strong class="flex-shrink-0 w-32 font-semibold text-gray-700">Creado por:</strong>
                <span class="ml-2 text-lg text-gray-900">
                    @if ($producto->user)
                    {{ $producto->user->name }}
                    @if ($producto->user->roles->isNotEmpty())
                    <span class="text-sm text-gray-500">({{ $producto->user->roles->pluck('name')->join(', ') }})</span>
                    @endif
                    @else
                    <span class="text-gray-500">Usuario desconocido</span>
                    @endif
                </span>
            </div>
            <div class="flex items-center">
                <strong class="flex-shrink-0 w-32 font-semibold text-gray-700">Fecha de creación:</strong>
                <span class="ml-2 text-lg text-gray-900">{{ $producto->created_at->locale('es')->translatedFormat('d
                    \d\e F \d\e\l Y h:i a') }}</span>
            </div>
            <div class="flex items-center">
                <strong class="flex-shrink-0 w-32 font-semibold text-gray-700">Última actualización:</strong>
                <span class="ml-2 text-lg text-gray-900">{{ $producto->updated_at->locale('es')->translatedFormat('d
                    \d\e F \d\e\l Y h:i a') }}</span>
            </div>

            {{-- Observaciones Generales (solo para Café o Mora) --}}
      
            @if (($producto->tipo === 'café' || $producto->tipo === 'mora') && ($producto->estado === 'aprobado' ||
            $producto->estado === 'rechazado'))
            <div class="mt-4 md:col-span-2 lg:col-span-3">
                <strong class="block mb-2 text-lg font-semibold text-gray-700">Observaciones:</strong>

                {{-- Contenedor de observaciones con color dinámico --}}
                <div class="p-4 leading-relaxed text-gray-800 border rounded-lg shadow-inner
            {{ $producto->estado === 'aprobado' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">

                    {{-- Mostrar quién validó/rechazó --}}
                    @if ($producto->estado === 'aprobado' && $producto->validador) {{-- CAMBIO AQUÍ: 'validadoPor' a
                    'validador' --}}
                    <p class="mb-2 text-sm text-gray-600">
                        Validado por: <span class="font-semibold">{{ $producto->validador->name }}</span>
                    </p>
                    @elseif ($producto->estado === 'rechazado' && $producto->rechazador) {{-- CAMBIO AQUÍ:
                    'rechazadoPor' a 'rechazador' --}}
                    <p class="mb-2 text-sm text-gray-600">
                        Rechazado por: <span class="font-semibold">{{ $producto->rechazador->name }}</span>
                    </p>
                    @endif

                    {{-- Mostrar las observaciones o el mensaje por defecto --}}
                    <p>{{ $producto->observaciones ?? 'No hay observaciones.' }}</p>
                </div>
            </div>
            @endif

        </div>

        {{-- Sección de Imagen del Producto --}}
        @if ($producto->imagen)
        <div class="pb-6 mb-10 border-b-2 border-gray-200">
            <h3 class="mb-4 text-2xl font-bold text-center text-gray-800">Imagen Principal del Producto</h3>
            <div class="flex items-center justify-center p-6 border border-gray-200 rounded-lg shadow-lg bg-gray-50">
                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto"
                    class="max-w-full h-auto max-h-[500px] object-contain rounded-lg transform transition-transform duration-300 hover:scale-105 cursor-pointer border-4 border-white shadow-md"
                    onclick="window.open(this.src, '_blank');">
            </div>
            <p class="mt-3 text-sm text-center text-gray-500">Haz clic en la imagen para verla en tamaño completo.
            </p>
        </div>
        @endif

        {{-- Sección de Video del Producto General (para Café o Mora) --}}
        @if (($producto->tipo === 'café' || $producto->tipo === 'mora') && $producto->rutavideo)
        <div class="pb-6 mb-10 border-b-2 border-gray-200">
            <h3 class="mb-4 text-2xl font-bold text-center text-gray-800">Video General del Proceso/Producto</h3>
            <div class="flex items-center justify-center p-6 bg-gray-100 rounded-lg shadow-lg">
                @include('productos.partials.video_player', ['videoUrl' => $producto->rutavideo])
            </div>
        </div>
        @endif

        {{-- Sección de Detalles Específicos --}}
        @if ($producto->tipo === 'café' && $producto->cafe)
        <div class="p-8 mb-10 border border-orange-200 shadow-lg bg-orange-50 rounded-xl">
            <h3 class="mb-6 text-3xl font-extrabold text-center text-orange-800">Detalles Específicos del Café</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-semibold text-orange-700">Número de página:</strong>
                    <span class="ml-2 text-lg text-gray-900">{{ $producto->cafe->numero_pagina ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-semibold text-orange-700">Clase:</strong>
                    <span class="ml-2 text-lg text-gray-900">{{ $producto->cafe->clase ?? 'N/A' }}</span>
                </div>
                <div class="mt-4 md:col-span-2">
                    <strong class="block mb-3 text-lg font-semibold text-orange-700">Información detallada del
                        café:</strong>
                    <div
                        class="p-5 leading-relaxed text-gray-800 bg-white border border-orange-100 rounded-lg shadow-sm">
                        {!! nl2br(e($producto->cafe->informacion ?? 'No hay información adicional para este café.')) !!}
                    </div>
                </div>
            </div>
        </div>
        @elseif ($producto->tipo === 'mora' && $producto->mora)
        <div class="p-8 mb-10 border border-purple-200 shadow-lg bg-purple-50 rounded-xl">
            <h3 class="mb-6 text-3xl font-extrabold text-center text-purple-800">Detalles Específicos de la Mora
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-semibold text-purple-700">Número de página:</strong>
                    <span class="ml-2 text-lg text-gray-900">{{ $producto->mora->numero_pagina ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-semibold text-purple-700">Clase:</strong>
                    <span class="ml-2 text-lg text-gray-900">{{ $producto->mora->clase ?? 'N/A' }}</span>
                </div>
                <div class="mt-4 md:col-span-2">
                    <strong class="block mb-3 text-lg font-semibold text-purple-700">Información detallada de la
                        mora:</strong>
                    <div
                        class="p-5 leading-relaxed text-gray-800 bg-white border border-purple-100 rounded-lg shadow-sm">
                        {!! nl2br(e($producto->mora->informacion ?? 'No hay información adicional para esta mora.')) !!}
                    </div>
                </div>
            </div>
        </div>
        @elseif ($producto->tipo === 'videos' && $producto->videos)
        <div class="p-8 mb-10 border border-teal-200 shadow-lg bg-teal-50 rounded-xl">
            <h3 class="mb-6 text-3xl font-extrabold text-center text-teal-800">Detalles Específicos del Video</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-5">
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-semibold text-teal-700">Autor:</strong>
                    <span class="ml-2 text-lg text-gray-900">{{ $producto->videos->autor ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-semibold text-teal-700">Título:</strong>
                    <span class="ml-2 text-lg text-gray-900">{{ $producto->videos->titulo ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-semibold text-teal-700">Subtipo de video:</strong>
                    <span class="ml-2 text-lg text-gray-900">{{ $producto->videos->tipo ?? 'N/A' }}</span>
                </div>
                <div class="mt-4 md:col-span-2">
                    <strong class="block mb-3 text-lg font-semibold text-teal-700">Descripción del video:</strong>
                    <div class="p-5 leading-relaxed text-gray-800 bg-white border border-teal-100 rounded-lg shadow-sm">
                        {!! nl2br(e($producto->videos->descripcion ?? 'No hay descripción disponible para este video.'))
                        !!}
                    </div>
                </div>
            </div>
            {{-- Video player para el producto de tipo 'videos' --}}
            @if ($producto->videos->rutaVideo)
            <div class="pt-6 mt-8 border-t border-teal-200">
                <h3 class="mb-4 text-2xl font-bold text-center text-teal-800">Reproductor de Video Específico
                </h3>
                <div class="flex items-center justify-center p-6 bg-gray-100 rounded-lg shadow-lg">
                    @include('productos.partials.video_player', [
                    'videoUrl' => $producto->videos->rutaVideo,
                    ])
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- Sección de Botones de Acción --}}
        <div class="flex justify-center pt-6 mt-12 border-t-2 border-gray-200"> {{-- Borde superior más grueso y
            centrado --}}
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
