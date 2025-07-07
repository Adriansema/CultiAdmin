@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
     <div class="inline-block px-20 py-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl whitespace-nowrap font-bold"> Detalles de noticias</h1>
            </div>
            <div class="py-2">
            {!! Breadcrumbs::render('noticias.show', $noticia) !!}
            </div>
        </div>

    <div class="container mx-auto p-6">
        <div class="bg-white shadow-2xl rounded-2xl p-6 md:p-10 border border-gray-300"> {{-- Cambiado a bg-white y añadido borde --}}
            <h2 class="text-3xl font-extrabold text-gray-800 mb-6 text-center">Información de la noticia</h2> {{-- Título más prominente --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6"> {{-- Grid para un diseño de dos columnas --}}

                {{-- Fila para Usuario Creador --}}
                <div class="flex items-center">
                    <strong class="font-bold text-gray-700 w-40 flex-shrink-0">Usuario creador:</strong>
                    <span class="text-gray-900 ml-2">
                        @if ($noticia->user)
                            {{ $noticia->user->name }}
                            @if ($noticia->user->roles->isNotEmpty())
                                <span class="text-gray-500 text-sm">({{ $noticia->user->roles->pluck('name')->join(', ') }})</span>
                            @endif
                        @else
                            Usuario desconocido
                        @endif
                    </span>
                </div>

                {{-- Fila para Autor Acreditado --}}
                <div class="flex items-center">
                    <strong class="font-bold text-gray-700 w-40 flex-shrink-0">Autor acreditado:</strong>
                    <span class="text-gray-900 ml-2">{{ $noticia->autor ?? 'N/A' }}</span>
                </div>

                {{-- Fila para Tipo --}}
                <div class="flex items-center">
                    <strong class="font-bold text-gray-700 w-40 flex-shrink-0">Tipo:</strong>
                    <span class="text-gray-900 ml-2">{{ $noticia->tipo }}</span>
                </div>

                {{-- Fila para Título --}}
                <div class="flex items-center">
                    <strong class="font-bold text-gray-700 w-40 flex-shrink-0">Título:</strong>
                    <span class="text-gray-900 ml-2">{{ $noticia->titulo ?? 'N/A' }}</span>
                </div>

                {{-- Fila para Clase --}}
                <div class="flex items-center">
                    <strong class="font-bold text-gray-700 w-40 flex-shrink-0">Clase:</strong>
                    <span class="text-gray-900 ml-2">{{ $noticia->clase ?? 'N/A' }}</span>
                </div>

                {{-- Fila para Número de página --}}
                <div class="flex items-center">
                    <strong class="font-bold text-gray-700 w-40 flex-shrink-0">Número de página:</strong>
                    <span class="text-gray-900 ml-2">{{ $noticia->numero_pagina }}</span>
                </div>

                {{-- Fila para Estado --}}
                <div class="flex items-center">
                    <strong class="font-bold text-gray-700 w-40 flex-shrink-0">Estado:</strong>
                    <span
                        class="inline-block px-3 py-1 text-sm font-semibold text-white rounded-full {{-- Ajustado tamaño y padding --}}
                        {{ $noticia->estado === 'aprobado' ? 'bg-green-600' : ($noticia->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                        {{ ucfirst($noticia->estado) }}
                    </span>
                </div>

                 {{-- Fila para Fecha de creación --}}
                <div class="flex items-center">
                    <strong class="font-bold text-gray-700 w-40 flex-shrink-0">Fecha de creación:</strong>
                    <span class="text-gray-900 ml-2">{{ $noticia->created_at->format('d/m/Y H:i') }}</span>
                </div>

                {{-- Fila para Última actualización --}}
                <div class="flex items-center">
                    <strong class="font-bold text-gray-700 w-40 flex-shrink-0">Última actualización:</strong>
                    <span class="text-gray-900 ml-2">{{ $noticia->updated_at->format('d/m/Y H:i') }}</span>
                </div>

            </div>

            {{-- Sección de Información (texto largo) --}}
            <div class="mt-8 pt-6 border-t border-gray-200"> {{-- Separador visual --}}
                <strong class="font-bold text-gray-700 block mb-3 text-lg">Información:</strong>
                <div class="bg-gray-50 p-4 rounded-lg text-gray-800 leading-relaxed shadow-inner"> {{-- Fondo suave para el texto largo --}}
                    {!! nl2br(e($noticia->informacion ?? 'N/A')) !!} {{-- Usar nl2br para saltos de línea y e() para escapar HTML --}}
                </div>
            </div>

            {{-- Sección de Imagen --}}
            @if ($noticia->imagen)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <strong class="font-bold text-gray-700 block mb-3 text-lg">Imagen asociada:</strong>
                    <div class="flex justify-center items-center bg-gray-50 p-4 rounded-lg shadow-inner">
                        <img src="{{ asset('storage/' . $noticia->imagen) }}" alt="Imagen de la noticia"
                            class="max-w-full h-auto max-h-96 object-contain rounded-lg border border-gray-300 transform transition-transform duration-300 hover:scale-105 cursor-pointer"
                            onclick="window.open(this.src, '_blank');"> {{-- Para abrir la imagen en una nueva pestaña --}}
                    </div>
                    <p class="text-center text-gray-500 text-sm mt-2">Haz clic en la imagen para verla en tamaño completo.</p>
                </div>
            @endif

            <div class="mt-10 flex justify-center"> {{-- Centrar el botón --}}
                <a href="{{ route('noticias.index') }}"
                   class="bg-[var(--color-textmarca)] hover:bg-[var(--color-texthovermarca)] py-3 px-8 rounded-full text-lg font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-200 ease-in-out transform hover:-translate-x-1">
                    <img src="{{ asset('images/regresar.svg') }}" alt="Regresar" class="w-6 h-6 mr-3">
                    <span class="whitespace-nowrap text-inherit">{{ __('Volver a la lista de noticias') }}</span>
                </a>
            </div>
        </div>
    </div>
@endsection