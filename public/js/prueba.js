document.addEventListener("DOMContentLoaded", function () {
    let filtroActual = 'hoy';
    let porcentajes = [];

    function setFilter(filtro) {
        filtroActual = filtro;

        const buttons = document.querySelectorAll('#filter-buttons .filter-btn');
        buttons.forEach(btn => btn.classList.remove('active'));

        const activeBtn = Array.from(buttons).find(btn =>
            btn.textContent.trim().toLowerCase() === filtro.toLowerCase()
        );

        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        loadData(filtro);
    }


    window.setFilter = setFilter;

    const chartElement = document.querySelector("#chart");
    if (!chartElement) {
        console.error("No se encontró el elemento #chart");
        return;
    }

    function renderChart(data) {
        // ... tu código de ordenación y cálculo de porcentajes …
        let datosOrdenados = [...data.vistas];

        switch (filtroActual) {
            case 'hoy':
                datosOrdenados.sort((a, b) => parseInt(a.grupo) - parseInt(b.grupo));
                break;
            case 'semana':
                const diasSemana = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
                datosOrdenados.sort((a, b) => diasSemana.indexOf(a.grupo.toLowerCase()) - diasSemana.indexOf(b.grupo.toLowerCase()));
                break;
            case 'mes':
                datosOrdenados.sort((a, b) => parseInt(a.grupo) - parseInt(b.grupo));
                break;
            case 'año':
                const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                datosOrdenados.sort((a, b) => meses.indexOf(a.grupo.toLowerCase()) - meses.indexOf(b.grupo.toLowerCase()));
                break;
        }

        let visitas = datosOrdenados.map(stat => stat.total);
        let categorias = datosOrdenados.map(stat => {
            let valor = stat.grupo;
            switch (filtroActual) {
                case 'hoy':
                    let hora = parseInt(valor);
                    let ampm = hora >= 12 ? 'PM' : 'AM';
                    let hora12 = hora % 12 || 12;
                    return `${hora12}:00 ${ampm}`;
                case 'semana':
                    return valor;
                case 'mes':
                    return `Semana ${valor}`;
                case 'año':
                    return valor.charAt(0).toUpperCase() + valor.slice(1);
                default:
                    return valor;
            }
        });

        porcentajes = visitas.map((valor, i, arr) => {
            if (i === 0) return 0;
            const anterior = arr[i - 1];
            return anterior === 0 ? 0 : ((valor - anterior) / anterior * 100).toFixed(1);
        });

        if (window.chart && typeof window.chart.destroy === "function") {
            window.chart.destroy();
        }

        const options = {
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                }
            },

            colors: ['#9'],


            series: [{
                name: 'Visitas',
                data: seriesData
            }],
            stroke: {
                curve: 'smooth',
                width: 3,

            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0,
                    gradientToColors: ['#bbf7d0'], // Verde claro abajo
                    inverseColors: false,
                    opacityFrom: 0.8, // Inicio del degradado (más fuerte)
                    opacityTo: 0.3,     // Final del degradado (transparente)
                    stops: [0, 90]
                }
            },
            markers: {
                size: 6,
                colors: ['#ffffff'],
                strokeColors: '#22c55e',
                strokeWidth: 3,
                hover: {
                    size: 8
                }
            },
            dataLabels: {
                enabled: true,
                style: {
                    colors: ['#ffffff'],
                    fontWeight: 'bold'
                },
                background: {
                    enabled: true,
                    foreColor: '#ffffff',
                    borderRadius: 4,
                    padding: 6,
                    backgroundColor: '#22c55e' // Fondo del número encima del punto
                }
            },

            xaxis: {
                type: 'category',
                labels: {
                    style: {
                        color: '#000'
                    }
                }
            },
            tooltip: {
                custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                    const valorActual = series[seriesIndex][dataPointIndex];
                    let porcentaje = 0;
                    if (dataPointIndex > 0) {
                        const anterior = series[seriesIndex][dataPointIndex - 1];
                        porcentaje = anterior === 0 ? 0 : ((valorActual - anterior) / anterior * 100).toFixed(1);
                    }
                    return `<div class="p-2">
                  <strong>${w.globals.labels[dataPointIndex]}</strong><br>
                  Visitas: <strong>${valorActual}</strong><br>
                  Cambio: <strong>${porcentaje}%</strong>
                </div>`;
                }
            }
        };

        window.chart = new ApexCharts(chartElement, options);
        window.chart.render();

        // Actualizar métricas
        const usersCountEl = document.getElementById("users-count");
        const registeredCountEl = document.getElementById("registered-count");
        const activeCountEl = document.getElementById("active-count");
        const connectedCountEl = document.getElementById("connected-count");

        const registeredPercentEl = document.getElementById("registered-percent");
        const activePercentEl = document.getElementById("active-percent");
        const connectedPercentEl = document.getElementById("connected-percent");

        if (usersCountEl) usersCountEl.textContent = data.usuarios;
        if (registeredCountEl) registeredCountEl.textContent = data.registrados;
        if (activeCountEl) activeCountEl.textContent = data.activos;
        if (connectedCountEl) connectedCountEl.textContent = data.conectados;

        if (registeredPercentEl) registeredPercentEl.textContent = `${((data.registrados / data.usuarios) * 100).toFixed(1)}% de los usuarios`;
        if (activePercentEl) activePercentEl.textContent = `${((data.activos / data.usuarios) * 100).toFixed(1)}% de los usuarios`;
        if (connectedPercentEl) connectedPercentEl.textContent = `${((data.conectados / data.usuarios) * 100).toFixed(1)}% de los usuarios`;
    }

    function loadData(filtro = null) {
        let url = STATISTICS_ROUTE;
        if (filtro) {
            url += `?filtro=${filtro}`;
        }

        // Mostrar cargando
        chartElement.innerHTML = `<div class="text-center text-gray-500 p-4 animate-pulse">Cargando estadísticas...</div>`;

        const storageKey = `offline_stats_${filtro}`;

        if (navigator.onLine) {
            fetch(url)
                .then(response => {
                    // Verifica si la respuesta es exitosa (200)
                    if (!response.ok) {
                        throw new Error('Error al cargar las estadísticas');
                    }
                    return response.json();
                })
                .then(data => {
                    localStorage.setItem(storageKey, JSON.stringify(data));
                    renderChart(data);
                })
                .catch(error => {
                    console.error("Error al obtener las estadísticas:", error);
                    const saved = localStorage.getItem(storageKey);
                     // Si hay datos guardados en localStorage, los usa
                    if (saved) {
                        renderChart(JSON.parse(saved));
                    } else {
                        // --- DATOS DE PRUEBA ---
                        const mockData = {

                            hoy: {
                                vistas: [
                                    { grupo: '0', total: 5 },  // 12 AM
                                    { grupo: '6', total: 20 },
                                    { grupo: '9', total: 30 },
                                    { grupo: '12', total: 50 },
                                    { grupo: '15', total: 40 },
                                    { grupo: '18', total: 35 },
                                    { grupo: '21', total: 25 },
                                ],
                                usuarios: 100,
                                registrados: 80,
                                activos: 60,
                                conectados: 20
                            },
                            semana: {
                                vistas: [
                                    { grupo: 'lunes', total: 120 },
                                    { grupo: 'martes', total: 200 },
                                    { grupo: 'miércoles', total: 150 },
                                    { grupo: 'jueves', total: 300 },
                                    { grupo: 'viernes', total: 400 },
                                    { grupo: 'sábado', total: 250 },
                                    { grupo: 'domingo', total: 180 },
                                ],
                                    usuarios: 500,
                                    registrados: 400,
                                    activos: 300,
                                    conectados: 90
                            },
                            mes: {
                                vistas: [
                                    { grupo: '1', total: 300 },
                                    { grupo: '2', total: 400 },
                                    { grupo: '3', total: 350 },
                                    { grupo: '4', total: 500 },

                                ],
                                usuarios: 800,
                                registrados: 600,
                                activos: 500,
                                conectados: 100
                            },
                            año: {
                                vistas: [
                                    { grupo: 'enero', total: 1200 },
                                    { grupo: 'febrero', total: 1100 },
                                    { grupo: 'marzo', total: 1500 },
                                    { grupo: 'abril', total: 1400 },
                                    { grupo: 'mayo', total: 1600 },
                                    { grupo: 'junio', total: 1300 },
                                    { grupo: 'julio', total: 1700 },
                                    { grupo: 'agosto', total: 1200 },
                                    { grupo: 'septiembre', total: 1500 },
                                    { grupo: 'octubre', total: 1600 },
                                    { grupo: 'noviembre', total: 1400 },
                                    { grupo: 'diciembre', total: 1800 },
                                ],
                                usuarios: 1000,
                                registrados: 750,
                                activos: 600,
                                conectados: 150
                            },

                        };
                         // Mostramos mensaje visual de que se están usando datos de prueba
                        chartElement.innerHTML = "<div class='text-center text-yellow-500 p-4'>Mostrando datos de prueba.</div>";
                        // Renderiza la gráfica con los datos simulados según el filtro actual
                        const selectedMock = mockData[filtroActual] || {};

                      renderChart({
                           vistas: selectedMock.vistas || [],
                           usuarios: selectedMock.usuarios || 0,
                           registrados: selectedMock.registrados || 0,
                           activos: selectedMock.activos || 0,
                           conectados: selectedMock.conectados || 0
});

                    }
                });
            }

    }


    window.loadData = loadData;
    loadData();
});
