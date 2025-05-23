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

    <div class="container max-w-4xl py-4 mx-auto bg-[var(--color-formulario)] shadow-xl px-8 space-x-4">
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
            <div
                class="mb-4 shadow-xl rounded-lg space-x-4 px-2 py-4
        @if ($producto->estado == 'aprobado') bg-green-300
        @elseif ($producto->estado == 'rechazado')
            bg-red-300
        @elseif ($producto->estado == 'pendiente') {{-- ¡Nueva condición para 'pendiente'! --}}
            bg-yellow-200 {{-- Puedes ajustar el color según tu preferencia --}}
        @else
            bg-gray-200 {{-- Color por defecto si el estado no es reconocido --}} @endif">
                <h3 class="text-sm font-semibold text-gray-600">Observaciones</h3>
                <p class="text-gray-800">{{ $producto->observaciones }}</p>

                {{-- Aquí mostramos el estado de la observación --}}
                @if ($producto->estado)
                    <p class="text-xs text-gray-700 mt-2">Estado:
                        <span
                            class="font-bold
                    @if ($producto->estado == 'aprobado') text-green-700
                    @elseif ($producto->estado == 'rechazado')
                        text-red-700
                    @elseif ($producto->estado == 'pendiente') {{-- Color de texto para 'pendiente' --}}
                        text-yellow-700 {{-- Puedes ajustar el color de texto también --}} @endif
                ">
                            {{ ucfirst($producto->estado) }}
                        </span>
                    </p>
                @endif
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
            <a href="{{ route('productos.index') }}"
                class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                Volver al listado
            </a>
        </div>
    </div>
@endsection
