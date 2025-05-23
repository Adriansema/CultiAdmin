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

    <div class="container max-w-4xl py-4 mx-auto bg-[var(--color-formulario)] shadow-xl px-8 space-x-4 rounded-lg">
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

        {{-- Sección de Estado del Producto (Más Prominente) --}}
        <div class="mb-6 p-4 rounded-lg
            @if ($producto->estado === 'aprobado') bg-green-100 text-green-800 border border-green-300
            @elseif ($producto->estado === 'rechazado') bg-red-100 text-red-800 border border-red-300
            @elseif ($producto->estado === 'pendiente') bg-yellow-100 text-yellow-800 border border-yellow-300
            @else bg-gray-100 text-gray-800 border border-gray-300 @endif">
            <h3 class="text-base font-semibold">Estado Actual:
                <span class="font-bold">{{ ucfirst($producto->estado) }}</span>
            </h3>
            @if ($producto->estado === 'rechazado' && $producto->observaciones)
                <p class="text-sm mt-2">
                    <strong>Observación del Operador:</strong> {{ $producto->observaciones }}
                </p>
                <div class="mt-4">
                    <a href="{{ route('productos.edit', $producto->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ir a Editar Noticia →
                    </a>
                </div>
            @elseif ($producto->estado === 'aprobado')
                <p class="text-sm mt-2">¡Tu noticia ha sido aprobada y está lista para ser consumida!</p>
            @elseif ($producto->estado === 'pendiente')
                <p class="text-sm mt-2">Tu noticia está pendiente de revisión por parte del operador.</p>
            @endif
        </div>

        @if ($producto->imagen)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto"
                    class="w-full rounded shadow">
            </div>
        @endif

        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-600">Tipo de producto</h3>
            <p class="text-lg text-gray-800">{{ ucfirst($producto->tipo) }}</p>
        </div>

        {{-- El bloque de observaciones anterior se ha integrado en la sección de estado --}}

        @foreach ($campos as $key => $label)
            @if (!empty($detalles[$key]))
                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                    <h3 class="text-sm font-semibold text-gray-600">{{ $label }}</h3>
                    <p class="text-gray-800 whitespace-pre-line">{{ $detalles[$key] }}</p>
                </div>
            @endif
        @endforeach

        <div class="mt-6">
            <a href="{{ route('productos.index') }}"
                class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                Volver al listado
            </a>
        </div>
    </div>
@endsection
