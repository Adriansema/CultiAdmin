@extends('layouts.app')

@section('title', 'Detalle del Producto')

@section('content')
    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl whitespace-nowrap font-bold">Detalles del Producto</h1>
        </div>
        {!! Breadcrumbs::render('productos.show', $producto) !!} 
    </div>

    <div class="container max-w-4xl py-6 mx-auto">
        @php
            $campos = [
               'historia' => 'Historia',
                'productos y sus características' => 'Productos',
                'variantes' => 'Variantes',
                'enfermedades' => 'Enfermedades',
                'insumos' => 'Insumos',
            ];

            $detalles = json_decode($producto->detalles_json, true) ?? [];
        @endphp

        <!-- Imagen -->
        @if ($producto->imagen)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto"
                    class="w-full rounded shadow">
            </div>
        @endif

        <!-- Tipo -->
        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-600">Tipo de producto</h3>
            <p class="text-lg text-gray-800">{{ ucfirst($producto->tipo) }}</p>
        </div>

        <!-- Observaciones -->
        @if ($producto->observaciones)
            <div class="mb-4">
                <h3 class="text-sm font-semibold text-gray-600">Observaciones</h3>
                <p class="text-gray-800">{{ $producto->observaciones }}</p>
            </div>
        @endif

        <!-- Detalles -->
        @foreach ($campos as $key => $label)
            @if (!empty($detalles[$key]))
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-600">{{ $label }}</h3>
                    <p class="text-gray-800 whitespace-pre-line">{{ $detalles[$key] }}</p>
                </div>
            @endif
        @endforeach

        <!-- Botón volver -->
        <div class="mt-6">
            <a href="{{ route('operador.pendientes') }}"
                class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                Volver al listado
            </a>
        </div>
    </div>
@endsection

