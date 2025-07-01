@extends('layouts.app')


@section('content')
    <div class="container text-center">
        <div class="inline-block px-8 py-10">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Nuevo Usuario">
                <h1 class="text-3xl whitespace-nowrap font-bold">Accesibilidad</h1>
            </div>
            {!! Breadcrumbs::render('accesibilidad.index') !!}
        </div>

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

    {{-- Este bloque puede ir en cualquier parte de tu vista Blade donde quieras el generador --}}
        <div
            class="fixed bottom-4 p-4 bg-white shadow-lg rounded-xl border border-gray-200 z-50 flex items-center space-x-4">
            <label for="csvTypeSelect" class="text-gray-700 font-semibold">Generar CSV de Prueba:</label>
            <select id="csvTypeSelect"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-gray-700">
                <option value="correctos">Usuarios Correctos</option>
                <option value="duplicados">Usuarios Duplicados</option>
                <option value="invalidos">Datos Inválidos</option>
                <option value="campos_faltantes">Campos Faltantes</option>
                <option value="vacio">Archivo Vacío</option>
            </select>
            <button id="generateCsvButton"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition duration-150 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L10 11.586l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v7a1 1 0 11-2 0V3a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Descargar CSV
            </button>
        </div> 
@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/accesibilidad.js') }}"></script>
@endsection
