@extends('layouts.app')

@section('title', 'Detalle del Boletín')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Detalle del Boletín</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Asunto:</strong> {{ $boletin->asunto }}</p>
            <p><strong>Contenido:</strong></p>
            <div class="border rounded p-3 bg-light mt-2" style="white-space: pre-wrap;">
                {{ $boletin->contenido }}
            </div>
        </div>
    </div>

    <a href="{{ route('operador.pendientes') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
