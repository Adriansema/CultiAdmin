{{--
@extends('layouts.app')

@section('content')
    <h2 class="mb-4 text-2xl font-bold">Detalles del Boletín</h2>
    <div class="max-w-3xl mx-auto mt-10">
        <div class="p-6 bg-[var(--color-gris1)] rounded-xl shadow-lg">

             Sección de Estado del Boletín (Más Prominente)
            <div
                class="mb-6 p-4 rounded-lg
                @if ($boletin->estado === 'aprobado') bg-green-100 text-green-800 border border-green-300
                @elseif ($boletin->estado === 'rechazado') bg-red-100 text-red-800 border border-red-300
                @elseif ($boletin->estado === 'pendiente') bg-yellow-100 text-yellow-800 border border-yellow-300
                @else bg-gray-100 text-gray-800 border border-gray-300 @endif">
                <h3 class="text-base font-semibold">Estado Actual:
                    <span class="font-bold">{{ ucfirst($boletin->estado) }}</span>
                </h3>
                @if ($boletin->estado === 'rechazado' && $boletin->observaciones)
                    <p class="mt-2 text-sm">
                        <strong>Observación del Operador:</strong> {{ $boletin->observaciones }}
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('boletines.edit', $boletin->id) }}"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Ir a Editar Boletín →
                        </a>
                    </div>
                @elseif ($boletin->estado === 'aprobado')
                    <p class="mt-2 text-sm">¡Tu boletín ha sido aprobado y está listo para ser consumido!</p>
                @elseif ($boletin->estado === 'pendiente')
                    <p class="mt-2 text-sm">Tu boletín está pendiente de revisión por parte del operador.</p>
                @endif
            </div>

            @if ($boletin->archivo)
                <div class="flex justify-between gap-4 mt-4"> {{-- Contenedor principal con flexbox y gap para la separación --}}

                    {{-- Div del Contenido (Izquierda) --}
                    <div class="flex-grow p-3 rounded-md bg-gray-50"> {{-- flex-grow para ocupar espacio y estilos de tarjeta --}
                        <p class="text-black whitespace-pre-line">
                            {{ $boletin->contenido }}
                        </p>
                    </div>

                    {{-- Div del Icono (Derecha) --}
                    <div class="flex flex-col items-center justify-center p-3 rounded-md bg-gray-50 flex-shrink-2">
                        {{-- Estilos de tarjeta y centrado --}
                        <h3 class="text-xs font-semibold text-black ">Archivo Adjunto:</h3> {{-- Oculto visualmente, para accesibilidad --}
                        <a href="{{ asset('storage/' . $boletin->archivo) }}" target="_blank"
                            class="flex flex-col items-center text-blue-600 transition-transform duration-300 ease-in-out transform hover:underline hover:scale-105">
                            <img src="{{ asset('images/PDF.svg') }}" alt="Icono PDF" class="mb-1 cursor-pointer w-14 h-14">
                            {{-- <span>Ver Archivo (PDF, etc.)</span> --}} {{-- Texto del enlace debajo del icono --}
                        </a>
                    </div>

                </div>
            @else
                {{-- Si no hay archivo, solo mostrar el contenido ocupando todo el ancho --}
                <div class="p-3 mt-4 rounded-md bg-gray-50">
                    <p class="text-gray-700 whitespace-pre-line">
                        {{ $boletin->contenido }}
                    </p>
                </div>
            @endif

            <div class="flex mt-6">
                <a href="{{ route('boletines.index') }}"
                    class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">Volver</a>
            </div>
        </div>
    </div>
@endsection
--}}


