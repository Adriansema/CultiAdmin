@extends('layouts.app')


@section('content')
<div class="container text-center">
    <h1 class="mb-6 text-2xl font-bold">Accesibilidad de la Aplicación</h1>

    <div class="space-y-6">
        <div>
            <h2 class="mb-2 text-xl font-semibold">Opciones de Accesibilidad</h2>
            <p>Proporcionamos opciones para mejorar tu experiencia visual y de lectura.</p>
        </div>

        <div class="mt-8 space-y-4">

            <!-- Botón para Contraste Alto -->
            <div>
                <button id="toggle-contrast"
                    class="px-4 py-2 text-white bg-gray-800 rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    aria-pressed="false">
                    Activar/Desactivar Contraste Alto
                </button>
            </div>

            <!-- Botón para Aumentar Fuente -->
            <div class="flex items-center justify-center space-x-4">
                <button id="increase-font"
                    class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Aumentar Tamaño de Fuente
                </button>
                <button id="decrease-font"
                    class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Disminuir Tamaño de Fuente
                </button>
            </div>

            <!-- Botón para Modo Oscuro -->
            <div>
                <button id="toggle-dark-mode"
                    class="px-4 py-2 text-white bg-black rounded hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Activar/Desactivar Modo Oscuro
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="module" src="{{ asset('js/accesibilidad.js') }}"></script>
@endsection
