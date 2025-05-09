document.addEventListener("DOMContentLoaded", function () {
    let filtroActual = 'ultimos3dias';
    let porcentajes = [];

    // Cambiar el filtro y cargar datos
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

    // Función para renderizar la gráfica con los datos
    function renderChart(data) {
        let datosOrdenados = [...data.vistas];

        // Filtrar y ordenar los datos según el filtro seleccionado
        switch (filtroActual) {
            case 'ultimos3dias':
                datosOrdenados.sort((a, b) => parseInt(a.grupo) - parseInt(b.grupo));
                break;
            case 'semana':
                const diasSemana = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];
                datosOrdenados.sort((a, b) =>
                    diasSemana.indexOf(a.grupo.toLowerCase()) - diasSemana.indexOf(b.grupo.toLowerCase())
                );
                break;
            case 'mes':
                datosOrdenados.sort((a, b) => parseInt(a.grupo) - parseInt(b.grupo));
                break;
            case 'año':
                const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                datosOrdenados.sort((a, b) =>
                    meses.indexOf(a.grupo.toLowerCase()) - meses.indexOf(b.grupo.toLowerCase())
                );
                break;
        }
        console.log("DEBUG data recibida:", data);

// ✅ Validar si hay datos antes de mapear
if (!data.vistas || data.vistas.length === 0) {
    const chartElement = document.querySelector("#chart");
    chartElement.innerHTML = `<div class="text-center text-gray-500 p-4">No hay datos suficientes para mostrar la gráfica.</div>`;
    return;
}

        let visitas = datosOrdenados.map(stat => parseInt(stat.total) || 0);
        console.log("DEBUG data recibida:", data);

        let categorias = datosOrdenados.map(stat => stat.grupo);
        console.log("Datos para la gráfica:", visitas, categorias);

        // Calcular los porcentajes de cambio entre los valores
        porcentajes = visitas.map((valor, i, arr) => {
            if (i === 0) return 0;
            const anterior = arr[i - 1];
            return anterior === 0 ? 0 : ((valor - anterior) / anterior * 100).toFixed(1);
        });

        // Verificar si existe una instancia anterior de ApexCharts y destruirla
        if (window.chart && typeof window.chart.destroy === "function") {
            window.chart.destroy();
        }

        const options = {
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            series: [{ name: 'Visitas', data: visitas }],
            xaxis: { categories: categorias },
            colors: ['#4CAF50'],
            markers: {
                size: 4,
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

        // Inicializar y renderizar el gráfico
        window.chart = new ApexCharts(document.querySelector("#chart"), options);
        window.chart.render();

        // Actualizar las métricas de usuarios, registros, etc.
        updateMetrics(data);
    }

    // Función para actualizar las métricas
    function updateMetrics(data) {
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

    // Cargar datos de la API o desde localStorage si no hay conexión
    function loadData(filtro = null) {
        let url = '/api/estadisticas'; // Cambia esta URL a la de tu API
        if (filtro) {
            url += `?filtro=${filtro}`;
        }

        const chartElement = document.querySelector("#chart");
        chartElement.innerHTML = `<div class="text-center text-gray-500 p-4 animate-pulse">Cargando estadísticas...</div>`;

        const storageKey = `offline_stats_${filtro || 'default'}`;

        if (navigator.onLine) {
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    localStorage.setItem(storageKey, JSON.stringify(data));
                    renderChart(data);
                })
                .catch(error => {
                    console.error("Error al obtener las estadísticas:", error);
                    loadOfflineData(storageKey);
                });
        } else {
            loadOfflineData(storageKey);
        }
    }

    // Cargar datos desde localStorage si no hay conexión
    function loadOfflineData(storageKey) {
        const savedData = localStorage.getItem(storageKey);
        if (savedData) {
            renderChart(JSON.parse(savedData));
            const chartElement = document.querySelector("#chart");
            chartElement.innerHTML = "<div class='text-center text-yellow-500 p-4'>Mostrando datos de prueba.</div>";
        }
    }

    // Cargar los datos inicialmente
    loadData();

    // Exponer la función de cambiar el filtro globalmente
    window.setFilter = setFilter;
});

