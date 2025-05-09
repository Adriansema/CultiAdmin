@extends('layouts.app')

@section('content')
<div class="max-w-4xl p-6 mx-auto bg-white rounded shadow">
    <h1 class="mb-4 text-xl font-semibold">Detalle del Producto</h1>

    <div class="mb-4">
        <p><strong>Nombre:</strong> {{ $producto->nombre }}</p>
        <p><strong>Descripción:</strong> {{ $producto->descripcion }}</p>

        @php
            $colorClase = match($producto->estado) {
                'aprobado' => 'bg-green-600 text-white',
                'rechazado' => 'bg-red-600 text-white',
                default => 'bg-yellow-500 text-black'
            };
        @endphp

        <p><strong>Estado:</strong>
            <span class="px-2 py-1 rounded {{ $colorClase }}">
                {{ ucfirst($producto->estado) }}
            </span>
        </p>

        <p><strong>Fecha de creación:</strong> {{ $producto->created_at->format('d/m/Y H:i') }}</p>
    </div>

    @if($producto->observaciones)
        <div class="p-4 mt-4 text-red-700 border-l-4 border-red-400 bg-red-50">
            <strong>Observaciones del operador:</strong><br>
            {{ $producto->observaciones }}
        </div>
    @endif

    <a href="{{ url()->previous() }}"
       class="inline-block mt-6 text-blue-600 hover:underline">
        ← Volver al historial
    </a>
</div>
@endsection
