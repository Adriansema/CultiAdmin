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
                <!-- Botón "Últimos 3 días" - Sin borde inicial. El JS aplicará el borde y color al activarse. -->
                <button onclick="window.setFilter('ultimos3dias')" data-filtro="ultimos3dias"
                    class="px-4 py-2 text-green-600 transition-all duration-300 ease-in-out rounded-full filter-btn focus:outline-none hover:border hover:border-green-600">
                    Últimos 3 días
                </button>

                <!-- Botón "Semana" - Sin borde inicial. El JS aplicará las clases de activo (incluyendo el borde). -->
                <button onclick="window.setFilter('semana')" data-filtro="semana"
                    class="px-4 py-2 text-green-600 transition-all duration-300 ease-in-out rounded-full filter-btn focus:outline-none hover:border hover:border-green-600">
                    Semana
                </button>

                <!-- Botón "Mes" - Sin borde inicial. El JS aplicará el borde y color al activarse. -->
                <button onclick="window.setFilter('mes')" data-filtro="mes"
                    class="px-4 py-2 text-green-600 transition-all duration-300 ease-in-out rounded-full filter-btn focus:outline-none hover:border hover:border-green-600">
                    Mes
                </button>

                {{-- Botón "Año" con el selector de año de Flatpickr integrado (Borde punteado azul siempre) --}}
                <div class="relative inline-block">
                    <button onclick="window.setFilter('año')" data-filtro="año"
                        class="flex items-center px-4 py-2 text-blue-600 transition-all duration-300 ease-in-out border-2 border-blue-400 border-dashed rounded-full filter-btn focus:outline-none hover:text-blue-800">
                        Año
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ml-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                    <input type="text" id="yearPicker" class="absolute inset-0 opacity-0 cursor-pointer"
                        placeholder="Selecciona Año">
                </div>

                {{-- Contenedor de sub-filtros de año --}}
                <div id="yearChartFiltersContainer" class="flex flex-wrap items-center mt-4 space-x-2 md:mt-0"
                    style="display: none;">
                    <label for="chartSubFilter" class="sr-only">Filtrar por:</label>
                    <select id="chartSubFilter"
                        class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-300">
                        <option value="month">Por Mes</option>
                        <option value="week">Por Semana</option>
                        <option value="day">Por Día</option>
                        <option value="hour">Por Hora</option>
                    </select>
                </div>
            </div>

    </section>

    {{-- Sección de Gráfica de Usuarios Conectados y Métricas --}}
    {{-- QUITAMOS el div intermedio flex-1 p-6 overflow-y-auto que estaba causando el colapso --}}
    <section id="usuarios-conectados" class="bg-[var(--color-gris1)] shadow rounded-3xl p-6 mb-6 flex flex-col"> {{--
        Añadimos flex flex-col para que el contenido se apile --}}

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

    {{-- Sección de Novedades y Boletines --}}
    {{-- Asegúrate de que esta sección tiene margen superior para no pegarse a la anterior --}}
    <section id="novedades-boletines" class="relative grid items-stretch mt-1 gap-7 md:grid-cols-2 rounded-xl">

        <section id="mensajes" class="bg-[var(--color-gris1)] shadow p-4 flex flex-col rounded-3xl ">

            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <div
                        class="flex items-center justify-center w-9 h-9 rounded-full bg-[var(--color-iconos)] text-white">
                        <img src="{{ asset('images/campanita.svg') }}" alt="boletines" class="w-5 h-5">
                    </div>
                    <h2 class="text-lg font-semibold text-[var(--color-iconos)]">Mensajes</h2>
                </div>
                <x-responsive-nav-link href="{{ route('productos.index') }}"
                    :active="request()->routeIs('accesibilidad')"
                    class="block px-3 py-2 text-sm text-gray-800 rounded-xl hover:bg-gray-300">
                    <div class="relative flex w-full justify-evenly">
                        <span class="text-ms">{{ __(' ver todo ↗') }}</span>
                    </div>
                </x-responsive-nav-link>
            </div>

            <div id="mensajes-novedades" class="p-4 bg-white shadow rounded-2xl">
                <p class="text-gray-500">No hay novedades por ahora.</p>
            </div>
        </section>

        <section id="boletines" class="bg-[var(--color-gris1)] shadow rounded-3xl p-6">

            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2">
                    <div
                        class="flex items-center justify-center w-9 h-9 rounded-full bg-[var(--color-iconos)] text-white">
                        <img src="{{ asset('images/boletin.svg') }}" alt="boletines" class="w-5 h-5">
                    </div>

                    <h2 class="text-lg font-semibold text-[var(--color-iconos)]">Boletines</h2>
                </div>


                <x-responsive-nav-link href="{{ route('boletines.index') }}"
                    :active="request()->routeIs('accesibilidad')"
                    class="block px-3 py-2 text-sm text-gray-800 rounded-xl hover:bg-gray-300">
                    <div class="relative flex w-full justify-evenly">
                        <span class="text-ms">{{ __(' ver todo ↗') }}</span>
                    </div>
                </x-responsive-nav-link>
            </div>

            <div id="mensajes-novedades" class="p-4 bg-white shadow rounded-2xl">
                <p class="text-gray-500">No hay novedades por ahora.</p>
            </div>

            <style>
                .boletin-content {
                    max-height: 0;
                    overflow: hidden;
                    transition: max-height 0.3s ease-in-out;
                }
            </style>

            {{-- Comentado: Contenido de boletines --}}

            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.querySelectorAll('.boletin-header').forEach(header => {
                        header.addEventListener('click', e => {
                            if (e.target.closest('a')) return;

                            const li = header.parentElement;
                            const content = li.querySelector('.boletin-content');
                            const icon = header.querySelector('svg');

                            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                                content.style.maxHeight = '0px';
                                icon.style.transform = 'rotate(0deg)';
                            } else {
                                content.style.maxHeight = content.scrollHeight + 'px';
                                icon.style.transform = 'rotate(180deg)';
                            }
                        });
                    });
                });
            </script>
            @endpush
        </section>
    </section> {{-- ¡ATENCIÓN! ESTA SECCIÓN CIERRA EL PADRE DE LAS NOVEDADES Y BOLETINES, NO EL CONTENEDOR PRINCIPAL
    --}}
</main>
@endsection
