@extends('layouts.app') 

@section('content')
     <div class="inline-block px-20 py-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl font-bold whitespace-nowrap">Ver noticias</h1>
            </div>
            <div class="py-2">
            {!! Breadcrumbs::render('noticias.show', $noticia) !!}
            </div>
        </div>

    <div class="container p-4 mx-auto">
        <div class="p-6 bg-white rounded-lg shadow-md">
            <p class="mb-2"><strong class="font-semibold">ID noticia:</strong> {{ $noticia->id_noticias }}</p>
            <p class="mb-2">
                <strong class="font-semibold">Usuario creador:</strong>
                @if ($noticia->user)
                    {{ $noticia->user->name }}
                    @if ($noticia->user->roles->isNotEmpty())
                        <span class="text-gray-600">({{ $noticia->user->roles->pluck('name')->join(', ') }})</span>
                    @endif
                @else
                    Usuario desconocido
                @endif
            </p>
            <p class="mb-2"><strong class="font-semibold">Autor acreditado:</strong> {{ $noticia->autor ?? 'N/A' }}</p>
            <p class="mb-2"><strong class="font-semibold">Tipo:</strong> {{ $noticia->tipo }}</p>
            <p class="mb-2"><strong class="font-semibold">Título:</strong> {{ $noticia->titulo ?? 'N/A' }}</p>
            <p class="mb-2"><strong class="font-semibold">Clase:</strong> {{ $noticia->clase ?? 'N/A' }}</p>

            @if ($noticia->imagen)
                <div class="mb-4">
                    <p class="mb-2"><strong class="font-semibold">Imagen:</strong></p>
                    <img src="{{ asset('storage/' . $noticia->imagen) }}" alt="Imagen de la noticia"
                        class="object-cover w-64 h-auto mt-2 rounded-lg">
                </div>
            @endif

            <p class="mb-2"><strong class="font-semibold">Información:</strong> {{ $noticia->informacion ?? 'N/A' }}</p>
            <p class="mb-2"><strong class="font-semibold">Número de página:</strong> {{ $noticia->numero_pagina }}</p>
            <p class="mb-2"><strong class="font-semibold">Estado:</strong>
                <span
                    class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $noticia->estado == 'aprobada' ? 'text-green-900' : ($noticia->estado == 'rechazada' ? 'text-red-900' : 'text-gray-900') }}">
                    <span aria-hidden="true"
                        class="absolute inset-0 opacity-50 rounded-full {{ $noticia->estado == 'aprobada' ? 'bg-green-200' : ($noticia->estado == 'rechazada' ? 'bg-red-200' : 'bg-gray-200') }}"></span>
                    <span class="relative">{{ $noticia->estado }}</span>
                </span>
            </p>
            <p class="mb-2"><strong class="font-semibold">Fecha de creación:</strong>
                {{ $noticia->created_at->format('d/m/Y H:i') }}</p>
            <p class="mb-2"><strong class="font-semibold">Última actualización:</strong>
                {{ $noticia->updated_at->format('d/m/Y H:i') }}</p>

            <div class="flex mt-6 space-x-4">
                <a href="{{ route('noticias.index') }}"
                    class="px-4 py-2 font-bold text-white bg-gray-500 rounded hover:bg-gray-700 focus:outline-none focus:shadow-outline">
                    Volver al listado
                </a>
            </div>
        </div>
    </div>
@endsection
