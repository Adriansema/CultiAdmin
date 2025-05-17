@extends('layouts.app')

@section('title', 'Detalle del Producto')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Detalle del Producto</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Nombre:</strong> {{ $producto->nombre }}</p>
            <p><strong>Descripción:</strong> {{ $producto->descripcion }}</p>
            <p><strong>Estado:</strong> {{ ucfirst($producto->estado) }}</p>

            @if ($producto->imagen)
                <div class="mt-3">
                    <strong>Imagen:</strong><br>
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto" class="img-fluid rounded" style="max-width: 300px;">
                </div>
            @endif
        </div>
    </div>

    <a href="{{ route('operador.pendientes') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
