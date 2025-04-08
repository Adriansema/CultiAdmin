document.addEventListener("DOMContentLoaded", function () {
    let filtroActual = 'hoy'; // Variable global

    function setFilter(filtro) {
        filtroActual = filtro; // Actualizamos la variable global

        // 🔄 Cambiar botón activo visualmente
        const buttons = document.querySelectorAll('#filter-buttons .filter-btn');
        buttons.forEach(btn => btn.classList.remove('active'));

        const activeBtn = Array.from(buttons).find(btn =>
            btn.textContent.trim().toLowerCase() === filtro.toLowerCase()
        );

        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        // Cargar los datos del filtro seleccionado
        loadData(filtro);
    }

    window.setFilter = setFilter;

    console.log("📌 DOM completamente cargado");

    const chartElement = document.querySelector("#chart");

    if (!chartElement) {
        console.error("No se encontró el elemento #chart");
        return;
    }

    function getWeekNumber(date) {
        const firstDayOfYear = new Date(date.getFullYear(), 0, 1);
        const pastDaysOfYear = Math.floor((date - firstDayOfYear) / 86400000);
        return Math.ceil((pastDaysOfYear + firstDayOfYear.getDay() + 1) / 7);
    }

    function renderChart(data) {
        let visitas = data.vistas.map(stat => stat.total);

        // Calcular porcentaje de cambio respecto al anterior
        let porcentajes = visitas.map((valor, i, arr) => {
            if (i === 0) return 0;
            let anterior = arr[i - 1] || 1;
            return Math.round(((valor - anterior) / anterior) * 100);
        });
        
        let categorias = data.vistas.map(stat => {
            let valor = stat.grupo;
    
            switch (filtroActual) {
                case 'hoy':
                    // valor es la hora en formato 24h (ej: '15'), lo convertimos a 3:00 PM
                    let hora = parseInt(valor);
                    let ampm = hora >= 12 ? 'PM' : 'AM';
                    let hora12 = hora % 12 || 12;
                    return `${hora12}:00 ${ampm}`;
    
                case 'semana':
                    // valor es la fecha en formato 'YYYY-MM-DD', lo convertimos a día de la semana
                    let fechaSemana = new Date(valor);
                    return fechaSemana.toLocaleDateString('es-ES', { weekday: 'long' });
    
                case 'mes':
                    // valor es el número de semana (ej: '14'), mostramos como Semana 14
                    return `Semana ${valor}`;
    
                case 'año':
                    // valor es el nombre del mes (ej: 'April'), lo pasamos a español
                    return valor.charAt(0).toUpperCase() + valor.slice(1).toLowerCase();
    
                default:
                    return valor;
            }
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
            series: [{
                name: 'Visitas',
                data: visitas
            }],
            xaxis: {
                categories: categorias,
                labels: { style: { colors: '#666', fontSize: '12px' } }
            },
            yaxis: {
                labels: { style: { colors: '#666', fontSize: '12px' } }
            },
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
            
                    return `
                        <div style="padding: 8px; text-align: center">
                            <strong>${w.globals.labels[dataPointIndex]}</strong><br>
                            ${valor} visitas<br>
                            ${color} ${signo}${porcentaje}%
                        </div>
                    `;
                }
            }
            
        };

        window.chart = new ApexCharts(chartElement, options);
        window.chart.render();
    }

    function loadData(filtro = null) {
        let url = STATISTICS_ROUTE;
        if (filtro) {
            url += `?filtro=${filtro}`;
        }

        if (navigator.onLine) {
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    console.log('Datos recibidos:', data);
                    localStorage.setItem("offline_stats", JSON.stringify(data));
                    renderChart(data);

                    // Actualizar métricas
                    document.getElementById("users-count").textContent = data.usuarios ?? 0;
                    document.getElementById("registered-count").textContent = data.registrados ?? 0;
                    document.getElementById("active-count").textContent = data.activos ?? 0;
                    document.getElementById("connected-count").textContent = data.conectados ?? 0;
                })
                .catch(() => {
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
    }

    window.loadData = loadData;

    loadData(); // Carga inicial
});

