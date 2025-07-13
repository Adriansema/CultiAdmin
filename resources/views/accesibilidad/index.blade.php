@extends('layouts.app')

@section('title', 'Accesibilidad de la aplicación') {{-- Añadido el título aquí --}}

@section('content')
    {{-- Contenedor del título y breadcrumbs: ajustado para responsividad --}}
    <div class="w-full px-4 py-6 md:px-8 lg:px-12"> {{-- Ajuste de padding para pantallas pequeñas y grandes --}}
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono">
            {{-- Título responsivo: cambia de tamaño según la pantalla --}}
            <h1 class="text-xl font-bold sm:text-2xl lg:text-3xl whitespace-nowrap">Accesibilidad de la aplicación</h1>
        </div>
        {{-- Breadcrumbs: texto más pequeño en móvil --}}
        <div class="py-2 text-sm text-gray-600">
            {!! Breadcrumbs::render('accesibilidad.index') !!}
        </div>
    </div>

    {{-- Contenedor principal para el contenido de accesibilidad, centrado y con padding --}}
    <div class="w-full p-4 mx-auto mb-8 bg-white shadow-sm max-w-screen-2xl rounded-2xl"> {{-- Añadido fondo blanco, redondeado, padding y sombra --}}
        <div class="flex flex-col items-center justify-center p-4"> {{-- Usa flexbox para centrar y flex-col para apilar en pantallas pequeñas --}}
            <div class="w-full max-w-lg mx-auto text-center"> {{-- Limita el ancho del texto y lo centra --}}
                <h2 class="mb-4 text-xl font-semibold sm:text-2xl lg:text-3xl">Opciones de accesibilidad</h2> {{-- Título responsivo --}}
                <p class="text-base text-gray-700 sm:text-lg">Proporcionamos opciones para mejorar tu experiencia visual y de lectura.</p>
            </div>

            <div class="w-full max-w-sm mt-8 space-y-4"> {{-- Los botones se apilan y tienen un ancho máximo --}}

                <div>
                    <button id="toggle-contrast"
                        class="w-full px-4 py-3 text-lg text-white bg-gray-800 rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"> {{-- w-full para ancho completo, padding y texto más grande --}}
                        Activar/desactivar contraste alto
                    </button>
                </div>

                <div class="flex flex-col items-center justify-center space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4"> {{-- Se apilan en móvil, en fila en sm+ --}}
                    <button id="increase-font"
                        class="w-full px-4 py-3 text-lg text-white bg-blue-600 rounded sm:w-auto hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"> {{-- w-full en móvil, w-auto en sm+ --}}
                        Aumentar tamaño de fuente
                    </button>
                    <button id="decrease-font"
                        class="w-full px-4 py-3 text-lg text-white bg-blue-600 rounded sm:w-auto hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"> {{-- w-full en móvil, w-auto en sm+ --}}
                        Disminuir tamaño de fuente
                    </button>
                </div>

                <div>
                    <button id="toggle-dark-mode"
                        class="w-full px-4 py-3 text-lg text-white bg-black rounded hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"> {{-- w-full para ancho completo, padding y texto más grande --}}
                        Activar/desactivar modo oscuro
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="module" src="{{ asset('js/accesibilidad.js') }}"></script>
@endsection
