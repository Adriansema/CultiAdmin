document.addEventListener("DOMContentLoaded", function () {
    let filtroActual = 'hoy';
    let porcentajes = [];

    function setFilter(filtro) {
        filtroActual = filtro;
    
        const buttons = document.querySelectorAll('#filter-buttons .filter-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-green-600', 'hover:bg-green-500');
            btn.classList.add('bg-gray-700', 'hover:bg-green-300');
            btn.classList.remove('active');
        });
    
        const activeBtn = Array.from(buttons).find(btn =>
            btn.textContent.trim().toLowerCase() === filtro.toLowerCase()
        );
    
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.classList.remove('bg-gray-700', 'hover:bg-green-300');
            activeBtn.classList.add('bg-green-600', 'hover:bg-green-500');
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

        let options = {
            chart: {
                type: 'line',
                height: 300,
                toolbar: { show: false }
            },
            series: [{ name: 'Visitas', data: visitas }],
            xaxis: { categories: categorias },
            colors: ['#4CAF50'],
            stroke: { curve: 'smooth', width: 3 },
            markers: {
                size: 6,
                colors: ['#ffffff'],
                strokeColors: ['#4CAF50'],
                strokeWidth: 2,
                hover: { size: 8 }
            },
            tooltip: {
                theme: 'dark',
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const valor = series[seriesIndex][dataPointIndex];
                    const porcentaje = porcentajes[dataPointIndex];
                    const color = porcentaje >= 0 ? '🟢' : '🔴';
                    const signo = porcentaje >= 0 ? '+' : '';
                    return `<div style="padding: 8px; text-align: center">
                        <strong>${w.globals.labels[dataPointIndex]}</strong><br>
                        ${valor} visitas<br>
                        <span style="font-size: 12px; color: ${porcentaje >= 0 ? '#4CAF50' : '#F44336'}">
                            ${color} ${signo}${porcentaje}%
                        </span>
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
                            },
                            mes: {
                                vistas: [
                                    { grupo: '1', total: 300 },
                                    { grupo: '2', total: 400 },
                                    { grupo: '3', total: 350 },
                                    { grupo: '4', total: 500 },
                                    
                                ],
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
                            },
                            usuarios: 1000,
                            registrados: 750,
                            activos: 600,
                            conectados: 150
                        };
                         // Mostramos mensaje visual de que se están usando datos de prueba
                        chartElement.innerHTML = "<div class='text-center text-yellow-500 p-4'>Mostrando datos de prueba.</div>";
                        // Renderiza la gráfica con los datos simulados según el filtro actual
                        renderChart(mockData[filtroActual]);
                    }
                });
            }        
                
    }
    

    window.loadData = loadData;
    loadData();
});
