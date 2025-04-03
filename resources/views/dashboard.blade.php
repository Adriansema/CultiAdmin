
 @extends('layouts.app')

@section('content') <!-- Asegúrate de abrir la sección -->

    <h1>Bienvenido al mi Panel Administrador</h1>

    <!-- Lienzo para la gráfica -->
    <canvas id="statisticsChart"></canvas>
    <!-- Cargar Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Obtener los datos de la API de estadísticas
        fetch("{{ route('statistics.index') }}")
            .then(response => response.json())
            .then(data => {
                // Extraer los meses y los valores de la base de datos
                const labels = data.visits.map(stat => new Date(stat.date).toLocaleString('es-ES', { month: 'long' }));
                const counts = data.visits.map(stat => stat.count);

                // Crear la gráfica con Chart.js
                new Chart(document.getElementById("statisticsChart"), {
                    type: 'bar', // Tipo de gráfica
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Visitas',
                            data: counts,
                            backgroundColor: 'rgba(54, 162, 235, 0.5)'
                        }]
                    }
                });
            });
    </script>

@endsection <!-- Cerrar la sección correctamente -->