@extends('layouts.app') {{-- Asume que tienes un layout base --}}

@section('content')
     <div class="inline-block px-20 py-6">
            <div class="flex items-center space-x-4">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl font-bold whitespace-nowrap"> Detalles de la noticia</h1>
            </div>
            <div class="py-2">
            {!! Breadcrumbs::render('noticias.show', $noticia) !!}
            </div>
        </div>

    <div class="container p-6 mx-auto">
        <div class="p-6 bg-white border border-gray-300 shadow-2xl rounded-2xl md:p-10"> {{-- Cambiado a bg-white y anadido borde --}}
            <h2 class="mb-6 text-3xl font-extrabold text-center text-gray-800">Información de la noticia</h2> {{-- Titulo mas prominente --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6"> {{-- Grid para un diseno de dos columnas --}}

                {{-- Fila para Usuario Creador --}}
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-bold text-gray-700">Usuario creador:</strong>
                    <span class="ml-2 text-gray-900">
                        @if ($noticia->user)
                            {{ $noticia->user->name }}
                            @if ($noticia->user->roles->isNotEmpty())
                                <span class="text-sm text-gray-500">({{ $noticia->user->roles->pluck('name')->join(', ') }})</span>
                            @endif
                        @else
                            Usuario desconocido
                        @endif
                    </span>
                </div>

                {{-- Fila para Autor Acreditado --}}
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-bold text-gray-700">Autor acréditado:</strong>
                    <span class="ml-2 text-gray-900">{{ $noticia->autor ?? 'N/A' }}</span>
                </div>

                {{-- Fila para Tipo --}}
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-bold text-gray-700">Tipo:</strong>
                    <span class="ml-2 text-gray-900">{{ $noticia->tipo }}</span>
                </div>

                {{-- Fila para Titulo --}}
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-bold text-gray-700">Título:</strong>
                    <span class="ml-2 text-gray-900">{{ $noticia->titulo ?? 'N/A' }}</span>
                </div>

                {{-- Fila para Clase --}}
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-bold text-gray-700">Clase:</strong>
                    <span class="ml-2 text-gray-900">{{ $noticia->clase ?? 'N/A' }}</span>
                </div>

                {{-- Fila para Estado --}}
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-bold text-gray-700">Estado:</strong>
                    <span
                        class="inline-block px-3 py-1 text-sm font-semibold text-white rounded-full {{-- Ajustado tamano y padding --}}
                        {{ $noticia->estado === 'aprobado' ? 'bg-green-600' : ($noticia->estado === 'pendiente' ? 'bg-yellow-500' : 'bg-red-600') }}">
                        {{ ucfirst($noticia->estado) }}
                    </span>
                </div>

                 {{-- Fila para Fecha de creacion --}}
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-bold text-gray-700">Fecha de creación:</strong>
                    <span class="ml-2 text-gray-900">{{ $noticia->created_at->format('d/m/Y H:i') }}</span>
                </div>



                {{-- Fila para ultima actualizacion --}}
                <div class="flex items-center">
                    <strong class="flex-shrink-0 w-40 font-bold text-gray-700">ultima actualización:</strong>
                    <span class="ml-2 text-gray-900">{{ $noticia->updated_at->format('d/m/Y H:i') }}</span>
                </div>

            </div>

             {{-- INICIO: Seccion de Observaciones de la Noticia --}}
            {{-- Se muestra si la noticia está aprobada o rechazada --}}
            @if ($noticia->estado === 'aprobado' || $noticia->estado === 'rechazado')
                <div class="pt-6 mt-8 border-t border-gray-200"> {{-- Añadido un separador visual --}}
                    <strong class="block mb-2 text-lg font-semibold text-gray-700">Observaciones:</strong>

                    {{-- Contenedor de observaciones con color dinámico basado en el estado --}}
                    <div class="p-4 leading-relaxed text-gray-800 border rounded-lg shadow-inner
                        {{ $noticia->estado === 'aprobado' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">

                        {{-- Muestra quién validó o rechazó la noticia --}}
                        @if ($noticia->estado === 'aprobado' && $noticia->validador)
                            <p class="mb-2 text-sm text-gray-600">
                                Validado por: <span class="font-semibold">{{ $noticia->validador->name }}</span>
                            </p>
                        @elseif ($noticia->estado === 'rechazado' && $noticia->rechazador)
                            <p class="mb-2 text-sm text-gray-600">
                                Rechazado por: <span class="font-semibold">{{ $noticia->rechazador->name }}</span>
                            </p>
                        @endif

                        {{-- Muestra las observaciones o un mensaje por defecto si no hay --}}
                        <p>{{ $noticia->observaciones ?? 'No hay observaciones.' }}</p>
                    </div>
                </div>
            @endif

            {{-- Seccion de Informacion (texto largo) --}}
            <div class="pt-6 mt-8 border-t border-gray-200"> {{-- Separador visual --}}
                <strong class="block mb-3 text-lg font-bold text-gray-700">Información:</strong>
                <div class="p-4 leading-relaxed text-gray-800 rounded-lg shadow-inner bg-gray-50"> {{-- Fondo suave para el texto largo --}}
                    {!! nl2br(e($noticia->informacion ?? 'N/A')) !!} {{-- Usar nl2br para saltos de linea y e() para escapar HTML --}}
                </div>
            </div>


            {{-- Seccion de Imagen --}}
            @if ($noticia->imagen)
                <div class="pt-6 mt-8 border-t border-gray-200">
                    <strong class="block mb-3 text-lg font-bold text-gray-700">Imagen asociada:</strong>
                    <div class="flex items-center justify-center p-4 rounded-lg shadow-inner bg-gray-50">
                        <img src="{{ asset('storage/' . $noticia->imagen) }}" alt="Imagen de la noticia"
                            class="object-contain h-auto max-w-full transition-transform duration-300 transform border border-gray-300 rounded-lg cursor-pointer max-h-96 hover:scale-105"
                            onclick="window.open(this.src, '_blank');"> {{-- Para abrir la imagen en una nueva pestana --}}
                    </div>
                    <p class="mt-2 text-sm text-center text-gray-500">Haz clic en la imagen para verla en tamano completo.</p>
                </div>
            @endif

            <div class="flex justify-center mt-10"> {{-- Centrar el boton --}}
                <a href="{{ route('noticias.index') }}"
                   class="bg-[var(--color-textmarca)] hover:bg-[var(--color-texthovermarca)] py-3 px-8 rounded-full text-lg font-bold text-white focus:outline-none focus:shadow-outline inline-flex items-center transition duration-200 ease-in-out transform hover:-translate-x-1">
                    <img src="{{ asset('images/regresar.svg') }}" alt="Regresar" class="w-6 h-6 mr-3">
                    <span class="whitespace-nowrap text-inherit">{{ __('Volver a la lista de noticias') }}</span>
                </a>
            </div>
        </div>
    </div>
@endsection
