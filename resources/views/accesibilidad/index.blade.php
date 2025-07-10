@extends('layouts.app')

@section('content')
    <div class="inline-block px-20 py-6">
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono">
            <h1 class="text-3xl whitespace-nowrap font-bold">Accesibilidad de la aplicacion</h1>
        </div>
        <div class="py-2">
            {!! Breadcrumbs::render('accesibilidad.index') !!}
        </div>
    </div>

    <div class="container text-center">
        <div class="space-y-6">
            <div>
                <h2 class="mb-2 text-xl font-semibold">Opciones de Accesibilidad</h2>
                <p>Proporcionamos opciones para mejorar tu experiencia visual y de lectura.</p>
            </div>

            <div class="mt-8 space-y-4">

                <div>
                    <button id="toggle-contrast"
                        class="px-4 py-2 text-white bg-gray-800 rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        aria-pressed="false">
                        Activar/Desactivar Contraste Alto
                    </button>
                </div>

                <div class="flex items-center justify-center space-x-4">
                    <button id="increase-font"
                        class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Aumentar Tamano de Fuente
                    </button>
                    <button id="decrease-font"
                        class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Disminuir Tamano de Fuente
                    </button>
                </div>

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