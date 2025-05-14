@extends('layouts.app')

@section('content')
<main>
<!-- Contenedor del Buscador -->

    <div class="flex justify-end">
        <div class="relative w-full max-w-sm"> <!-- Cambiado a max-w-sm -->
            <input
                type="text"
                placeholder="Buscar"
                class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <div class="absolute text-gray-400 transform -translate-y-1/2 left-3 top-1/2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.35z" />
                </svg>
            </div>
        </div>
    </div>

  <hr class="my-4 border-gray-200">

    <section class="flex-1 p-1 overflow-y-auto">
           <div class="flex flex-col mb-6 md:flex-row md:items-center md:justify-between">
           <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Panel de Administración</h1>
              <p class="mt-2 text-gray-600">Visualiza estadística de vistas al sitio, notificación y boletines.</p>
             </div>

                <div class="mt-4 space-x-2 md:mt-0" id="filter-buttons">
                    <button onclick="setFilter('ultimos3dias')" class="px-4 py-2 text-green-600 transition-colors rounded-lg filter-btn hover:bg-green-700">Ultimos 3 dias</button>
                    <button onclick="setFilter('semana')" class="px-4 py-2 text-green-600 transition-colors rounded-lg filter-btn hover:bg-green-700 active">Semana</button>
                    <button onclick="setFilter('mes')" class="px-4 py-2 text-green-600 transition-colors rounded-lg filter-btn hover:bg-green-700">Mes</button>
                    <button onclick="setFilter('año')" class="px-4 py-2 text-green-600 transition-colors rounded-lg filter-btn hover:bg-green-700">Año</button>
                </div>
            </div>
     </section>




    {{-- Gráfica de usuarios conectados + Métricas --}}
    <div class="flex-1 p-1 overflow-y-auto">
    <section id="usuarios-conectados" class="bg-[var(--color-gris1)] shadow rounded-lg p-6">

        <!-- Agrupamos el icono y el texto en un div flex -->
        <div class="flex items-center mb-3 space-x-2">
    <!-- Fondo circular para el icono -->
    <div class="bg-[var(--color-ICONOESTA)]  p-0 rounded-full relative -top-1">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 16l5-5 3 3 7-7M5 h12" />
        </svg>
    </div>

            <!-- Título -->
            <h2 class="text-xl font-bold text-[var(--color-usucone)] -mt-3" >Usuarios Conectados</h2>
        </div>

        <div class="p-5 bg-white shadow-sm rounded-2xl">
            {{-- Gráfica --}}
            <div id="chart" class="my-6"></div>
        </div>
            {{-- Métricas generales --}}
            <div class="grid grid-cols-1 gap-6 mt-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative p-4 rounded-md shadow-sm bg-gray-50">
                        <!-- Ícono en la esquina superior derecha -->
                        <div class="absolute top-3 right-3 bg-[var(--color-iconos)] p-2 rounded-full">
                          <img src="{{asset('images/Icon.svg') }}" alt="usuario">
                      </div>

                    <h3 class="text-2xl text-[var(--color-iconos)]">Usuarios</h3>
                    <p id="users-count" class="text-3xl font-bold text-gray-800">0</p>
                    <p id="registered-percent" class="text-sm text-gray-800">0% de los usuarios</p>
                </div>

                {{-- Registrados --}}
                <div class="relative p-4 rounded-md shadow-sm bg-gray-50">

    <!-- Ícono en la esquina superior derecha -->
    <div class="absolute top-3 right-3 bg-[var(--color-iconos3)] p-2 rounded-full">
        <img src="{{asset('images/regis.svg') }}" alt="registro">
    </div>

    <!-- Contenido interno, sin fondo ni sombra duplicados -->
    <div>
        <h3 class="text-2xl text-[var(--color-iconos)]">{{ __('message.Register') }}</h3>
        <p id="registered-count" class="text-3xl font-bold text-gray-800">0</p>
        <p id="registered-percent" class="text-sm text-gray-800">0% de los usuarios</p>
    </div>

</div>

                 <div class="relative p-4 rounded-md shadow-sm bg-gray-50">
                 <!-- Ícono -->
                      <div class="absolute top-3 right-3 bg-[var(--color-iconos2)] p-2 rounded-full">
                      <img src="{{asset('images/activos.svg') }}" alt="activos">
                     </div>

                           <h3 class="text-2xl text-[var(--color-iconos)]">Activos</h3>
                           <p id="active-count" class="text-3xl font-bold text-gray-800">0</p>
                           <p id="active-percent" class="text-sm text-gray-800">0% de los usuarios</p>
                     </div>

                          <div class="relative p-4 rounded-md shadow-sm bg-gray-50">
                             <!-- Ícono -->
                             <div class="absolute top-3 right-3 bg-[var(--color-iconos4)] p-2 rounded-full">
                                   <img src="{{asset('images/conectados.svg') }}" alt="conectados">
                             </div>

                                 <h3 class="text-2xl text-[var(--color-iconos)]">Conectados</h3>
                                  <p id="connected-count" class="text-3xl font-bold text-gray-800">0</p>
                                  <p id="connected-percent" class="text-sm text-gray-800">0% de los usuarios</p>
                         </div>
        </section>
</main>
<div class="flex-1 p-1 overflow-y-auto">
        {{-- Novedades y Boletines --}}
        <section id="novedades-boletines" class="relative grid gap-6 mt-8 md:grid-cols-2">

        <section id="boletines" class="bg-[var(--color-gris1)] shadow rounded-lg p-6">
             <!-- Encabezado con ícono y título -->
                    <div class="flex items-center justify-between mb-4">
                         <div class="flex items-center space-x-2">
                    <div class="flex items-center justify-center w-9 h-9 rounded-full bg-[var(--color-iconos)] text-white">
                          <img src="{{ asset('images/campanita.svg') }}" alt="boletines" class="w-5 h-5">
                     </div>
                          <h2 class="text-lg font-semibold text-[var(--color-iconos)]">Mensajes</h2>
                      </div>
                         <x-responsive-nav-link href="{{ route('accesibilidad.index') }}"
                                 :active="request()->routeIs('accesibilidad')"
                                 class="block px-3 py-2 text-sm text-gray-800 rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]">
                                <!-- Ícono de accesibilidad (persona con brazos extendidos) -->
                                   <div class="relative flex w-full justify-evenly">

                                     <span class=" text-ms">
                                        {{ __(' ver todo ↗') }}
                                   </span>
                                  </div>
                                </x-responsive-nav-link>
                             </div>

                             <!-- Lista de boletines o mensaje -->
                         <div id="novedades" class="p-4 bg-white rounded-lg shadow">
                              <p class="text-gray-500">No hay novedades por ahora.</p>
                           </div>
                </section>


                <section id="boletines" class="bg-[var(--color-gris1)] shadow rounded-lg p-6">
                            <!-- Encabezado con ícono y título -->
                         <div class="flex items-center justify-between mb-4">
                               <div class="flex items-center space-x-2">
                                    <div class="flex items-center justify-center w-9 h-9 rounded-full bg-[var(--color-iconos)] text-white">
                                       <img src="{{ asset('images/boletin.svg') }}" alt="boletines" class="w-5 h-5">
                                  </div>
                                <h2 class="text-lg font-semibold text-[var(--color-iconos)]">Boletines</h2>
                              </div>
                      <x-responsive-nav-link href="{{ route('accesibilidad.index') }}"
                                 :active="request()->routeIs('accesibilidad')"
                                   class="block px-3 py-2 text-sm text-gray-800 rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]">
                                  <!-- Ícono de accesibilidad (persona con brazos extendidos) -->
                                 <div class="relative flex w-full justify-evenly">

                                           <span class=" text-ms">
                                                 {{ __(' ver todo ↗') }}
                                          </span>
                                  </div>
             </x-responsive-nav-link>
       </div>

                      <!-- Lista de boletines o mensaje -->
                 <div id="novedades" class="p-4 bg-white rounded-lg shadow">
                      <p class="text-gray-500">No hay novedades por ahora.</p>
                    </div>
         </section>



                </section>
        </section>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const STATISTICS_ROUTE = "{{ route('statistics.index') }}";
</script>
<script src="{{ asset('js/dashboard.js') }}"></script>

<style>
    .filter-btn {
        @apply px-4 py-2 rounded bg-green-200 hover:bg-green-400 transition-all;
    }
    .filter-btn.active {
        @apply bg-green-600 text-white;
    }
</style>
<style>

.filter-btn {
    @apply px-4 py-2 rounded-lg text-white hover:bg-green-300 transition-colors;
}

.filter-btn.active {
    @apply bg-green-600 hover:bg-green-500;
}
</style>



    <style>

        .filter-btn:hover {
            color: white;
        }

        .filter-btn.active {
            background-color: #15803d;
            color: white;
        }
    </style>

    <section id="usuarios-conectados" class="...">
        <!-- Tus botones aquí -->
    </section>

 </div>

@endsection
