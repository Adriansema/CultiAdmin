@extends('layouts.app')

@section('title', 'Detalle del Producto')

@section('content')
<div class="container max-w-4xl py-6 mx-auto">
    <div class="p-6 bg-white rounded shadow">
        <h3 class="text-xl font-bold">{{ $producto->nombre }}</h3>
        <p class="mt-2 text-gray-700 whitespace-pre-line">{{ $producto->descripcion }}</p>

        @if($producto->imagen)
            <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto" class="mt-4 rounded shadow w-96">
        @endif

        <div class="mt-4">
            @php
                $estadoColor = match($producto->estado) {
                    'pendiente' => 'bg-yellow-500',
                    'validado' => 'bg-green-600',
                    'rechazado' => 'bg-red-600',
                    default => 'bg-gray-400',
                };
            @endphp
            <span class="px-2 py-1 text-white rounded {{ $estadoColor }}">
                {{ ucfirst($producto->estado) }}
            </span>
        </div>

        @if($producto->observaciones)
            <div class="p-4 mt-4 border border-red-200 rounded bg-red-50">
                <h4 class="font-semibold text-red-600">Observaciones del operador:</h4>
                <p class="mt-2 text-red-800">{{ $producto->observaciones }}</p>
            </div>
        @endif
    </div>
</div>
@endsection
