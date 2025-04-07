@extends('layouts.app')

@section('content')
    {{-- Título del panel de administración --}} 
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                {{-- Encabezado de la sección de estadísticas --}}
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Estadísticas de Visitas</h2>

                {{-- Contenedor donde se dibujará el gráfico --}}
                <div id="chart" class="w-full h-80 bg-white rounded-md shadow-md border border-gray-300">
                    <p class="text-center text-gray-500 pt-10">Cargando gráfico...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Carga de la librería de ApexCharts desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            console.log("📌 DOM completamente cargado");

              // Obtener el elemento donde se renderizará el gráfico
            const chartElement = document.querySelector("#chart");

           // Si no se encuentra el div con id="chart", mostrar error
            if (!chartElement) {
                console.error("No se encontró el elemento #chart");
                return;
            }
 // Función para obtener el número de semana del año dado una fecha
            function getWeekNumber(date) {
                const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
                const pastDaysOfYear = Math.floor((date - firstDayOfYear) / 86400000);
                return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
            }
   // Función para renderizar el gráfico con los datos recibidos
            function renderChart(data) {
                 // Extraer los valores de visitas
                let visitas = data.visits.map(stat => stat.count);
                let semanas = data.visits.map(stat => {
                    let fecha = new Date(stat.date);
                    return 'Semana ' + getWeekNumber(fecha) + ' (' + fecha.toLocaleDateString('es-ES') + ')';
                });

                console.log("🛠️ Dibujando gráfico con:", { visitas, semanas }); // para verficar si los datos de visitas y semanas esta bien construido  

                 // Opciones de configuración para el gráfico
                let options = {
                    chart: {
                        type: 'line', // Tipo de gráfico
                        height: 300,  // Alturade pixeles 
                        toolbar: { show: false } //olcutar herramienta 
                    },
                    series: [{
                        name: 'Visitas', // Nombre de la serie de datos 
                        data: visitas   // datos a grafcar
                    }],
                    xaxis: {
                        categories: semanas, //Etiquetas del eje x
                        labels: {
                            style: { colors: '#666', fontSize: '12px' }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#666', fontSize: '12px' }
                        }
                    },
                    colors: ['#4CAF50'], //color de la linea 
                    stroke: { curve: 'smooth', width: 3 }, //suavizacion de la lineas
                    markers: {
                        size: 6,
                        colors: ['#ffffff'],
                        strokeColors: ['#4CAF50'],
                        strokeWidth: 2,
                        hover: { size: 8 }
                    },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function (value) {
                                return value + " visitas";
                            }
                        }
                    }
                };

                //crear y renderizar el grafico 
                let chart = new ApexCharts(chartElement, options);
                chart.render();
            }

             
            // Cargar datos online o desde cache
            if (navigator.onLine) {
                //si esta online obtener los datos desde la api
                fetch("{{ route('statistics.index') }}")
                    .then(response => response.json())
                    .then(data => {
                        console.log('📊 Datos recibidos:', data);
                        //guardar una copia local en caso de estar offline despues 
                        localStorage.setItem("offline_stats", JSON.stringify(data));
                        renderChart(data);
                    })
                    .catch(() => {
                        // si ocurre un error, intentar cargar desde localStorage
                        const saved = localStorage.getItem("offline_stats");
                        if (saved) {
                            renderChart(JSON.parse(saved));
                        } else {
                            chartElement.innerHTML = "No se pudo cargar la gráfica.";
                        }
                    });
            } else {
                const saved = localStorage.getItem("offline_stats");
                if (saved) {
                    renderChart(JSON.parse(saved));
                } else {
                    chartElement.innerHTML = "Sin conexión y sin datos guardados.";
                }
            }
        });
    </script>
@endsection
