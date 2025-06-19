@extends('layouts.app')

@section('content')
    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Volver">
            <h1 class="text-3xl whitespace-nowrap font-bold">Detalles del Producto</h1>
        </div>
        {!! Breadcrumbs::render('productos.show', $producto) !!}
    </div>

    <div class="container mx-auto p-4">
        <div class="bg-white shadow-md rounded-lg p-6">
            <p class="mb-2"><strong class="font-semibold">ID:</strong> {{ $producto->id }}</p>
            <p class="mb-2"><strong class="font-semibold">Tipo:</strong> {{ $producto->tipo }}</p>
            <p class="mb-2"><strong class="font-semibold">Estado:</strong> {{ $producto->estado }}</p>
            <p class="mb-2"><strong class="font-semibold">Observaciones Generales:</strong>
                {{ $producto->observaciones ?? 'N/A' }}</p>

            @if ($producto->imagen)
                <div class="mb-4">
                    <strong class="font-semibold">Imagen del Producto:</strong>
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto"
                        class="mt-2 w-64 h-auto object-cover rounded-lg">
                </div>
            @endif

            @if ($producto->user)
                <p class="mb-2">
                    <strong class="font-semibold">Usuario Creador:</strong>
                    @if ($producto->user)
                        {{ $producto->user->name }}
                        {{-- MODIFICACIÓN AQUÍ: Acceder a los nombres de los roles y unirlos --}}
                        @if ($producto->user->roles->isNotEmpty())
                            {{-- Opcional: Verificar si el usuario tiene roles antes de intentar mostrarlos --}}
                            <span class="text-gray-600">({{ $producto->user->roles->pluck('name')->join(', ') }})</span>
                        @endif
                    @else
                        Usuario Desconocido
                    @endif
                </p>
            @endif


            @if ($producto->tipo === 'café' && $producto->cafe)
                <h2 class="text-xl font-semibold mt-4 mb-2">Detalles de Café</h2>
                <p class="mb-2"><strong class="font-semibold">Número de Página:</strong>
                    {{ $producto->cafe->numero_pagina }}</p>
                <p class="mb-2"><strong class="font-semibold">Clase:</strong> {{ $producto->cafe->clase ?? 'N/A' }}</p>
                <p class="mb-2"><strong class="font-semibold">Información de Café:</strong>
                    {{ $producto->cafe->informacion }}</p>
            @elseif ($producto->tipo === 'mora' && $producto->mora)
                <h2 class="text-xl font-semibold mt-4 mb-2">Detalles de Mora</h2>
                <p class="mb-2"><strong class="font-semibold">Número de Página:</strong>
                    {{ $producto->mora->numero_pagina }}</p>
                <p class="mb-2"><strong class="font-semibold">Clase:</strong> {{ $producto->mora->clase ?? 'N/A' }}</p>
                <p class="mb-2"><strong class="font-semibold">Información de Mora:</strong>
                    {{ $producto->mora->informacion }}</p>
            @endif

            <div class="mt-6 flex space-x-4">
                <a href="{{ route('productos.edit', $producto) }}"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Editar Producto
                </a>

                <a href="{{ route('productos.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Volver a la Lista
                </a>
            </div>
        </div>
    </div>
@endsection
