@extends('layouts.app')

@section('content')
    <main class="flex flex-col flex-1 p-4 overflow-y-auto">
        {{-- Sección superior: Título y botones de filtro --}}
        <section class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-6 md:mb-0">
                    <h1 class="text-2xl font-semibold text-gray-800">Panel de Administración</h1>
                    <p class="mt-2 text-gray-600">Visualiza estadística de vistas al sitio, notificación y boletines.</p>
                </div>

                <div class="flex flex-wrap items-center mt-4 space-x-2 md:mt-0" id="filter-buttons-container">
                    <!-- Botones de Filtro -->
                    <button onclick="window.setFilter('ultimos3dias')" data-filtro="ultimos3dias"
                        class="px-6 py-3 text-green-600 transition-all duration-300 ease-in-out rounded-full filter-btn hover:border hover:border-green-600 focus:outline-none">
                        Últimos 3 días
                    </button>
                    <button onclick="window.setFilter('semana')" data-filtro="semana"
                        class="px-6 py-3 text-green-600 transition-all duration-300 ease-in-out rounded-full filter-btn hover:border hover:border-green-600 focus:outline-none">
                        Semana
                    </button>
                    <button onclick="window.setFilter('mes')" data-filtro="mes"
                        class="px-6 py-3 text-green-600 transition-all duration-300 ease-in-out rounded-full filter-btn hover:border hover:border-green-600 focus:outline-none">
                        Mes
                    </button>

                    <!-- Contenedor del botón "Año" y el NUEVO selector de año personalizado -->
                    <div id="yearFilterGroup" data-filtro="año" onclick="window.setFilter('año')"
                        class="filter-btn-group flex items-center rounded-full px-6 py-2.5 space-x-2 cursor-pointer
                            transition-all duration-300 ease-in-out group relative
                            text-darkblue border border-transparent hover:border-darkblue hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-400">

                        {{-- Icono de calendario (AHORA AL PRINCIPIO, a la izquierda) --}}
                        <svg id="calendarIcon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-darkblue"
                            viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd" />
                        </svg>

                        <span id="yearLabel" class="text-darkblue">Año</span> {{-- Texto 'Año' --}}

                        {{-- Selector de Año Personalizado (2025 con flechas) - Oculto por defecto --}}
                        {{-- Se mostrará al activar el filtro 'Año', quedando entre el texto 'Año' y el icono --}}
                        <div id="customYearSelector" class="flex items-center space-x-1" style="display: none;">
                            <button id="prevYearBtn"
                                class="p-1.5 rounded-full hover:bg-gray-100 transition-colors duration-200 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <span id="currentYearDisplay"
                                class="px-2 py-1 text-base font-semibold text-gray-800 cursor-pointer">
                                2025
                            </span>
                            <button id="nextYearBtn"
                                class="p-1.5 rounded-full hover:bg-gray-100 transition-colors duration-200 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>
        </section>

        {{-- Sección de Gráfica de Usuarios Conectados y Métricas --}}
        {{-- QUITAMOS el div intermedio flex-1 p-6 overflow-y-auto que estaba causando el colapso --}}
        <section id="usuarios-conectados" class="bg-[var(--color-gris1)] shadow rounded-3xl p-6 mb-6 flex flex-col">
            {{-- Añadimos flex flex-col para que el contenido se apile --}}
            <div class="flex items-center mb-3 space-x-2">
                <div class="bg-[var(--color-ICONOESTA)] p-0 rounded-full relative -top-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M3 17l6-6 4 4 7-7" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-[var(--color-usucone)] -mt-3">Estadísticas de actividad</h2>
            </div>

            {{-- Ajustado 'gap-x-1' para solo espacio horizontal muy pequeño, y 'gap-y-2' para el vertical --}}
            <div class="flex flex-col items-stretch lg:flex-row gap-x-6 gap-y-4">

                {{-- Contenedor de la Gráfica (ocupa 3/4 del ancho en pantallas grandes) --}}
                <div class="flex items-center justify-center flex-grow p-0 bg-white shadow-md rounded-3xl">
                    <div id="chart" class="w-full h-[300px]"></div>
                </div>

                {{-- Contenedor de las Métricas (ocupa 1/4 del ancho en pantallas grandes) --}}
                {{-- CAMBIO CLAVE: Añadido 'flex-grow-0' para que no se estire y solo ocupe el espacio de sus hijos. --}}
                {{-- CAMBIO CLAVE: Añadido 'lg:h-full' o un 'max-h-[XXXpx]' para que su altura se alinee con la gráfica --}}
                <div class="flex flex-col gap-2  lg:w-[200px] flex-shrink-0 flex-grow-0"> {{-- Eliminado lg:h-full --}}
                    {{-- Métrica: Usuarios --}}
                    {{-- Ajustado padding a p-3 para equilibrio y texto a text-lg/text-xl --}}
                    <div class="relative w-full p-3 bg-white border border-gray-200 shadow-md rounded-2xl">
                        <div class="absolute top-3 right-3 bg-[var(--color-iconos)] p-2 rounded-full">
                            <img src="{{ asset('images/Icon.svg') }}" alt="usuario" class="w-4 h-4">
                        </div>
                        <h3 class="text-lg font-mediun text-[var(--color-iconos)]">Usuarios</h3>
                        <p id="users-count" class="mt-1 text-3xl font-bold text-gray-800">0</p>
                        <p id="users-percent" class="text-xs text-gray-600">0% de los usuarios</p>
                    </div>

                    {{-- Métrica: Activos --}}
                    <div class="relative w-full p-3 bg-white border border-gray-200 shadow-md rounded-2xl">
                        <div class="absolute top-3 right-3 bg-[var(--color-iconos2)] p-2 rounded-full">
                            <img src="{{ asset('images/activos.svg') }}" alt="activos" class="w-4 h-4">
                        </div>
                        <h3 class="text-lg font-mediun text-[var(--color-iconos)]">Activos</h3>
                        <p id="active-count" class="mt-1 text-3xl font-bold text-gray-800">0</p>
                        <p id="active-percent" class="text-xs text-gray-600">0% de los usuarios</p>
                    </div>

                    {{-- Métrica: Conectados --}}
                    <div class="relative w-full p-3 bg-white border border-gray-200 shadow-md rounded-2xl">
                        <div class="absolute top-3 right-3 bg-[var(--color-iconos4)] p-2 rounded-full">
                            <img src="{{ asset('images/conectados.svg') }}" alt="conectados" class="w-4 h-4">
                        </div>
                        <h3 class="text-lg font-mediun text-[var(--color-iconos)]">Conectados</h3>
                        <p id="connected-count" class="mt-1 text-3xl font-bold text-gray-800">0</p>
                        <p id="connected-percent" class="text-xs text-gray-600">0% de los usuarios</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Sección de Noticias y boletines --}}
        <section id="novedades-boletines" class="relative grid items-start mt-1 gap-7 md:grid-cols-2 rounded-xl">

            {{-- sesion de noticias --}}
            <section id="mensajes" class="p-4 flex flex-col bg-[var(--color-gris1)] rounded-3xl h-full">
                {{-- Añadido h-full --}}
                <div class="flex items-center justify-between mb-1"> {{-- Ajustado mb-1 para reducir espacio --}}

                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('images/noticias-n.svg') }}" alt="noticias-notificacion"
                            class="w-10 h-8 ml-2">
                        <h2 class="text-lg font-semibold text-[var(--color-iconos)]">Noticias</h2>
                        {{-- ¡Aquí está el nuevo contador! --}}
                        <span id="unread-news-count" class="ml-2 text-lg font-bold text-blue-600">
                            @if ($totalUnreadNoticiasCount >= 10)
                                +9
                            @else
                                {{ $totalUnreadNoticiasCount }}
                            @endif
                        </span>
                    </div>

                    <x-responsive-nav-link href="{{ route('noticias.index') }}" :active="request()->routeIs('boletines.index')"
                        class="hover:text-[var(--color-hovertextver)] group py-2 px-2 rounded-full text-md font-bold text-gray-700 focus:outline-none focus:shadow-outline inline-flex items-center transition duration-150 ease-in-out">
                        <span class="whitespace-nowrap text-inherit">{{ __('Ver Todo') }}</span>
                        <img src="{{ asset('images/verTodo.svg') }}"
                            class="w-10 h-8 relative inset-0 block group-hover:hidden" alt="Icono de Importar">
                        <img src="{{ asset('images/hoverTodo.svg') }}"
                            class="w-10 h-8 relative inset-0 hidden group-hover:block" alt="Icono de importar hover">
                    </x-responsive-nav-link>
                </div>
                <div id="mensajes-noticias" class="p-2 rounded-2xl flex-grow"> {{-- Añadido flex-grow --}}
                    @include('partials.notification-noticia')
                </div>
            </section>

            {{-- sesion de boletines --}}
            <section id="boletines" class="p-4 flex flex-col bg-[var(--color-gris1)] rounded-3xl h-full">
                {{-- Añadido h-full --}}
                <div class="flex items-center justify-between mb-1"> {{-- Ajustado mb-1 para reducir espacio --}}

                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('images/boletin-n.svg') }}" alt="boletines-notificacion"
                            class="w-10 h-8 ml-2">
                        <h2 class="text-lg font-semibold text-[var(--color-iconos)]">Boletines</h2>
                    </div>

                    <x-responsive-nav-link href="{{ route('boletines.index') }}" :active="request()->routeIs('boletines.index')"
                        class="hover:text-[var(--color-hovertextver)] group py-2 px-2 rounded-full text-md font-bold text-gray-700 focus:outline-none focus:shadow-outline inline-flex items-center transition duration-150 ease-in-out">
                        <span class="whitespace-nowrap text-inherit">{{ __('Ver Todos') }}</span>
                        <img src="{{ asset('images/verTodo.svg') }}"
                            class="w-10 h-8 relative inset-0 block group-hover:hidden" alt="Icono de Ver Todo">
                        <img src="{{ asset('images/hoverTodo.svg') }}"
                            class="w-10 h-8 relative inset-0 hidden group-hover:block" alt="Icono de Ver Todo hover">
                    </x-responsive-nav-link>
                </div>

                <div id="mensajes-boletines" class="p-2 rounded-2xl flex-grow"> {{-- Añadido flex-grow --}}
                    @include('partials.notification-boletin')
                </div>
            </section>
        </section>
    </main>
@endsection
