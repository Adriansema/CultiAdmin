<!-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-welcome />
            </div>
        </div>
    </div>
</x-app-layout>
 --> 

 @extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-4"> Estadísticas de la Página</h2>

        <!-- Contenedor de la gráfica -->
        <div class="bg-white p-6 shadow rounded-lg">
            <canvas id="statisticsChart"></canvas>
        </div>
    </div>

    <!-- Cargar Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Obtener los datos de la API de estadísticas
        fetch("{{ route('statistics.index') }}")
            .then(response => response.json())
            .then(data => {
                // Extraer los meses y los valores de la base de datos
                const labels = data.map(stat => new Date(stat.date).toLocaleString('es-ES', { month: 'long' }));
                const counts = data.map(stat => stat.count);

                // Crear la gráfica con Chart.js
                new Chart(document.getElementById("statisticsChart"), {
                    type: 'bar', // Tipo de gráfica
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Número de visitas",
                            data: counts,
                            backgroundColor: "blue",
                            borderColor: "black",
                            borderWidth: 1
                        }]
                    }
                });
            });
    </script>
@endsection
