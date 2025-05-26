@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10">
    <div class="p-6 bg-white rounded shadow-lg">

        {{-- Sección de Estado del Boletín (Más Prominente) --}}
        <div class="mb-6 p-4 rounded-lg
            @if ($boletin->estado === 'aprobado') bg-green-100 text-green-800 border border-green-300
            @elseif ($boletin->estado === 'rechazado') bg-red-100 text-red-800 border border-red-300
            @elseif ($boletin->estado === 'pendiente') bg-yellow-100 text-yellow-800 border border-yellow-300
            @else bg-gray-100 text-gray-800 border border-gray-300 @endif">
            <h3 class="text-base font-semibold">Estado Actual:
                <span class="font-bold">{{ ucfirst($boletin->estado) }}</span>
            </h3>
            @if ($boletin->estado === 'rechazado' && $boletin->observaciones)
                <p class="text-sm mt-2">
                    <strong>Observación del Operador:</strong> {{ $boletin->observaciones }}
                </p>
                <div class="mt-4">
                    <a href="{{ route('boletines.edit', $boletin->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ir a Editar Boletín →
                    </a>
                </div>
            @elseif ($boletin->estado === 'aprobado')
                <p class="text-sm mt-2">¡Tu boletín ha sido aprobado y está listo para ser consumido!</p>
            @elseif ($boletin->estado === 'pendiente')
                <p class="text-sm mt-2">Tu boletín está pendiente de revisión por parte del operador.</p>
            @endif
        </div>

        {{-- Contenido principal del boletín --}}
        <h2 class="text-2xl font-bold mb-4">Detalles del Boletín</h2>
        <p class="mt-4 text-gray-700 whitespace-pre-line p-3 bg-gray-50 rounded-md">
            {{ $boletin->contenido }}
        </p>

        {{-- Si tienes un archivo asociado al boletín y quieres mostrar un enlace para verlo/descargarlo --}}
        @if ($boletin->archivo)
            <div class="mt-4 p-3 bg-gray-50 rounded-md">
                <h3 class="text-sm font-semibold text-gray-600">Archivo Adjunto:</h3>
                <a href="{{ asset('storage/' . $boletin->archivo) }}" target="_blank" class="text-blue-600 hover:underline">
                    Ver Archivo (PDF, etc.)
                </a>
            </div>
        @endif

        <div class="flex mt-6">
            <a href="{{ route('boletines.index') }}"
                class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">Volver</a>
        </div>
    </div>
</div>
@endsection
