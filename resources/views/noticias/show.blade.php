@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Detalles de la Noticia</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <p class="mb-2"><strong class="font-semibold">ID Noticia:</strong> {{ $noticia->id_noticias }}</p>
        <p class="mb-2">
            <strong class="font-semibold">Usuario Creador:</strong>
            @if ($noticia->user)
                {{ $noticia->user->name }} {{-- ¡CORREGIDO AQUÍ! --}}
            @else
                Usuario Desconocido
            @endif
        </p>
        <p class="mb-2"><strong class="font-semibold">Autor Acreditado:</strong> {{ $noticia->autor ?? 'N/A' }}</p> {{-- ¡MOSTRANDO EL AUTOR! --}}
        <p class="mb-2"><strong class="font-semibold">Tipo:</strong> {{ $noticia->tipo }}</p>
        <p class="mb-2"><strong class="font-semibold">Título:</strong> {{ $noticia->titulo ?? 'N/A' }}</p>
        <p class="mb-2"><strong class="font-semibold">Clase:</strong> {{ $noticia->clase ?? 'N/A' }}</p>

        @if ($noticia->imagen)
            <div class="mb-4">
                <strong class="font-semibold">Imagen:</strong>
                <img src="{{ asset('storage/' . $noticia->imagen) }}" alt="Imagen de la noticia" class="mt-2 w-64 h-auto object-cover rounded-lg">
            </div>
        @endif

        <p class="mb-2"><strong class="font-semibold">Información:</strong> {{ $noticia->informacion ?? 'N/A' }}</p>
        <p class="mb-2"><strong class="font-semibold">Número de Página:</strong> {{ $noticia->numero_pagina }}</p>
        <p class="mb-2"><strong class="font-semibold">Estado:</strong>
            <span class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $noticia->estado == 'aprobada' ? 'text-green-900' : ($noticia->estado == 'rechazada' ? 'text-red-900' : 'text-gray-900') }}">
                <span aria-hidden="true" class="absolute inset-0 opacity-50 rounded-full {{ $noticia->estado == 'aprobada' ? 'bg-green-200' : ($noticia->estado == 'rechazada' ? 'bg-red-200' : 'bg-gray-200') }}"></span>
                <span class="relative">{{ $noticia->estado }}</span>
            </span>
        </p>
        <p class="mb-2"><strong class="font-semibold">Fecha de Creación:</strong> {{ $noticia->created_at->format('d/m/Y H:i') }}</p>
        <p class="mb-2"><strong class="font-semibold">Última Actualización:</strong> {{ $noticia->updated_at->format('d/m/Y H:i') }}</p>

        <div class="mt-6 flex space-x-4">
            <a href="{{ route('noticias.noticias.edit', $noticia->id_noticias) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Editar Noticia
            </a>
            <a href="{{ route('noticias.noticias.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Volver al Listado
            </a>
        </div>
    </div>
</div>
@endsection

