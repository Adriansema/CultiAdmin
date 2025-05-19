@extends('layouts.app')

@section('title', 'Detalle del Boletín')

@section('content')
    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
            <h1 class="text-3xl whitespace-nowrap font-bold">Detalles del Boletín</h1>
        </div>
        {!! Breadcrumbs::render('operador.boletines.show', $boletin) !!}
    </div>
    <div class="container mt-4">
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
