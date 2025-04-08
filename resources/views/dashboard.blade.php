@extends('layouts.app')

@section('content')
<div class="bg-white rounded-md shadow p-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold">Usuarios Conectados</h2>
        <div class="space-x-2" id="filter-buttons">
    <button onclick="setFilter('hoy')" class="filter-btn">Hoy</button>
    <button onclick="setFilter('semana')" class="filter-btn active">Semana</button>
    <button onclick="setFilter('mes')" class="filter-btn">Mes</button>
    <button onclick="setFilter('año')" class="filter-btn">Año</button>
</div>
            
    </div>
 

<style>
    .filter-btn {
        @apply px-4 py-2 rounded bg-green-200 hover:bg-green-400 transition-all;
    }
    .filter-btn.active {
        @apply bg-green-600 text-white;
    }
</style>


    <div id="chart" class="my-6">
        <p class="text-center text-gray-500 pt-10">Cargando gráfico...</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
        <div>
            <p class="text-gray-600">Usuarios</p>
            <p id="users-count" class="text-2xl font-bold">0</p>
        </div>
       

        <div>
            <p class="text-gray-600">Registrados</p>
            <p id="registered-count" class="text-2xl font-bold">0</p>
        </div>
        <div>
            <p class="text-gray-600">Activos</p>
            <p id="active-count" class="text-2xl font-bold">0</p>
        </div>
        <div>
            <p class="text-gray-600">Conectados</p>
            <p id="connected-count" class="text-2xl font-bold">0</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
   
    <!-- Librería ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Definición de la ruta de la API para JS -->
    <script>
        const STATISTICS_ROUTE = "{{ route('statistics.index') }}";
    </script>

    <!-- Carga de tu archivo JS -->
    <script src="{{ asset('js/dashboard.js') }}"></script>




@endsection
