@extends('layouts.app')

@section('content')


<<<<<<< HEAD
=======
<main>
    
    {{-- Gráfica de usuarios conectados --}}
    <div class="flex-1 p-6 overflow-y-auto">
    <section id="usuarios-conectados" class="bg-white shadow rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Panel de Administración</h2>
            <div class="space-x-2 mt-4 md:mt-0" id="filter-buttons">
            <div class="space-x-2 mt-4 md:mt-0" id="filter-buttons">
              <button onclick="setFilter('hoy')" class="filter-btn px-4 py-2 rounded-lg text-green-600  hover:bg-green-700 transition-colors">Hoy</button>
              <button onclick="setFilter('semana')" class="filter-btn px-4 py-2 rounded-lg text-green-600  hover:bg-green-700 transition-colors active">Semana</button>
              <button onclick="setFilter('mes')" class="filter-btn px-4 py-2 rounded-lg text-green-600  hover:bg-green-700 transition-colors">Mes</button>
              <button onclick="setFilter('año')" class="filter-btn px-4 py-2 rounded-lg text-green-600  hover:bg-green-700 transition-colors">Año</button>
      </div>
>>>>>>> 5df863f (actualizando el dashboard)

    <section class="flex-1 p-6 overflow-y-auto">
           <div class="flex flex-col mb-6 md:flex-row md:items-center md:justify-between">
           <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Panel de Administración</h1>
              <p class="mt-2 text-gray-600">Visualiza estadística de vistas al sitio, notificación y boletines.</p>
             </div>

                <div class="mt-4 space-x-2 md:mt-0" id="filter-buttons">
                    <button onclick="setFilter('hoy')" class="px-4 py-2 text-green-600 transition-colors rounded-lg filter-btn hover:bg-green-700">Hoy</button>
                    <button onclick="setFilter('semana')" class="px-4 py-2 text-green-600 transition-colors rounded-lg filter-btn hover:bg-green-700 active">Semana</button>
                    <button onclick="setFilter('mes')" class="px-4 py-2 text-green-600 transition-colors rounded-lg filter-btn hover:bg-green-700">Mes</button>
                    <button onclick="setFilter('año')" class="px-4 py-2 text-green-600 transition-colors rounded-lg filter-btn hover:bg-green-700">Año</button>

                </div>
            </div>
     </section>

<main>
    {{-- Gráfica de usuarios conectados + Métricas --}}
    <div class="flex-1 p-6 overflow-y-auto">
        <section id="usuarios-conectados" class="bg-[var(--color-gris1)] shadow rounded-lg p-6">
           <h2 class="text-xl font-bold text-[var(--color-usucone)]">Usuario Conectados</h2>
           <div class="p-5 bg-white shadow-sm rounded-2xl">
           {{-- Gráfica --}}
            <div id="chart" class="my-6"></div>
            </div>
            {{-- Métricas generales --}}
            <div class="grid grid-cols-1 gap-6 mt-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="p-4 rounded-md shadow-sm bg-gray-50">
                    <h3 class="text-sm text-gray-600">Usuarios</h3>
                    <p id="users-count" class="text-2xl font-bold text-gray-800">0</p>
                    <p id="users-change" class="text-sm text-green-600">+0% más que ayer</p>
                </div>

                <div class="p-4 rounded-md shadow-sm bg-gray-50">
                    <h3 class="text-sm text-gray-600">Registrados</h3>
                    <p id="registered-count" class="text-2xl font-bold text-gray-800">0</p>
                    <p id="registered-change" class="text-sm text-green-600">+0% más que ayer</p>
                    <p id="registered-percent" class="text-sm text-gray-500">0% de los usuarios</p>
                </div>

                <div class="p-4 rounded-md shadow-sm bg-gray-50">
                    <h3 class="text-sm text-gray-600">Activos</h3>
                    <p id="active-count" class="text-2xl font-bold text-gray-800">0</p>
                    <p id="active-change" class="text-sm text-green-600">+0% más que ayer</p>
                    <p id="active-percent" class="text-sm text-gray-500">0% de los usuarios</p>
                </div>

                <div class="p-4 rounded-md shadow-sm bg-gray-50">
                    <h3 class="text-sm text-gray-600">Conectados</h3>
                    <p id="connected-count" class="text-2xl font-bold text-gray-800">0</p>
                    <p id="connected-change" class="text-sm text-green-600">+0% más que ayer</p>
                    <p id="connected-percent" class="text-sm text-gray-500">0% de los usuarios</p>
                </div>
            </div>
        </section>

        {{-- Novedades y Boletines --}}
        <section id="novedades-boletines" class="grid gap-6 mt-8 md:grid-cols-2">
            <div id="novedades" class="p-6 bg-white rounded-lg shadow ">
                <!-- Aquí van las novedades -->
                <p class="text-gray-500">No hay novedades por ahora.</p>
            </div>
            <div id="boletines" class="p-6 bg-white rounded-lg shadow">
                <!-- Aquí van los boletines -->
                <p class="text-gray-500">No hay boletines disponibles.</p>
            </div>
        </section>
    </div>
</main>
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
