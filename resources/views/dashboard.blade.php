@extends('layouts.app')

@section('content')

  <section id="banner" class="w-full bg-white shadow rounded-lg overflow-hidden">
        <!-- Aquí va la imagen de fondo, título, logo y campesino -->
        <div class="relative w-full h-80 md:h-100 bg-banner"> <!-- Usa la clase personalizada aquí -->
            
        </div>
    </section>

<main>
    
    {{-- Gráfica de usuarios conectados --}}
    <section id="usuarios-conectados" class="bg-white shadow rounded-lg p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Panel de Administración</h2>
            <div class="space-x-2 mt-4 md:mt-0" id="filter-buttons">
                <button onclick="setFilter('hoy')" class="filter-btn">Hoy</button>
                <button onclick="setFilter('semana')" class="filter-btn active">Semana</button>
                <button onclick="setFilter('mes')" class="filter-btn">Mes</button>
                <button onclick="setFilter('año')" class="filter-btn">Año</button>
            </div>
        </div>  
        <div id="chart" class="my-6"></div>
    </section>

    {{-- Métricas generales --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
        <div class="bg-gray-50 rounded-md p-4 shadow-sm">
            <h3 class="text-gray-600 text-sm">Usuarios</h3>
            <p id="users-count" class="text-2xl font-bold text-gray-800">0</p>
            <p id="users-change" class="text-sm text-green-600">+0% más que ayer</p>
        </div>

        <div class="bg-gray-50 rounded-md p-4 shadow-sm">
            <h3 class="text-gray-600 text-sm">Registrados</h3>
            <p id="registered-count" class="text-2xl font-bold text-gray-800">0</p>
            <p id="registered-change" class="text-sm text-green-600">+0% más que ayer</p>
            <p id="registered-percent" class="text-sm text-gray-500">0% de los usuarios</p>
        </div>

        <div class="bg-gray-50 rounded-md p-4 shadow-sm">
            <h3 class="text-gray-600 text-sm">Activos</h3>
            <p id="active-count" class="text-2xl font-bold text-gray-800">0</p>
            <p id="active-change" class="text-sm text-green-600">+0% más que ayer</p>
            <p id="active-percent" class="text-sm text-gray-500">0% de los usuarios</p>
        </div>

        <div class="bg-gray-50 rounded-md p-4 shadow-sm">
            <h3 class="text-gray-600 text-sm">Conectados</h3>
            <p id="connected-count" class="text-2xl font-bold text-gray-800">0</p>
            <p id="connected-change" class="text-sm text-green-600">+0% más que ayer</p>
            <p id="connected-percent" class="text-sm text-gray-500">0% de los usuarios</p>
        </div>
    </div>

    {{-- Novedades y Boletines --}}
    <section id="novedades-boletines" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
        <div id="novedades" class="bg-white shadow rounded-lg p-6 lg:col-span-2">
            <!-- Aquí van las novedades -->
            <p class="text-gray-500">No hay novedades por ahora.</p>
        </div>
        <div id="boletines" class="bg-white shadow rounded-lg p-6">
            <!-- Aquí van los boletines -->
            <p class="text-gray-500">No hay boletines disponibles.</p>
        </div>
    </section>

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
@endsection