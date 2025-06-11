
import * as echarts from 'echarts';
import flatpickr from 'flatpickr';
import "flatpickr/dist/flatpickr.min.css";
import { Spanish } from 'flatpickr/dist/l10n/es.js';

import  MonthSelectPlugin  from 'flatpickr/dist/plugins/monthSelect/index.js';


// Declarar myChart y chartDom fuera del ámbito del listener
let myChart = null;
let chartDom = null; // Inicialmente null

// CAMBIO CLAVE AQUÍ: Usamos window.onload en lugar de document.addEventListener("DOMContentLoaded")
// Esto asegura que el DOM y todo el CSS se han cargado y renderizado antes de intentar inicializar ECharts.
window.onload = function(){

    // 1. --- DECLARACIÓN DE VARIABLES GLOBALES Y OBTENCIÓN DE ELEMENTOS DEL DOM ---
    let filtroActual = 'ultimos3dias';
    let selectedYearForChart = new Date().getFullYear().toString();
    let selectedChartSubFilter = 'month';
    let porcentajes = [];

    // OBTENEMOS TODOS LOS ELEMENTOS DEL DOM AQUÍ. ¡VERIFICA ESTOS IDs EN TU HTML!
    chartDom = document.getElementById('chart'); //
    const usersCountElement = document.getElementById('users-count'); //
    const registeredCountElement = document.getElementById('registered-count');
    const activeCountElement = document.getElementById('active-count');
    const connectedCountElement = document.getElementById('connected-count');
    const usersPercentElement = document.getElementById('users-percent');
    const registeredPercentElement = document.getElementById('registered-percent');
    const activePercentElement = document.getElementById('active-percent');
    const connectedPercentElement = document.getElementById('connected-percent');

    const yearChartFiltersContainer = document.querySelector('#yearChartFiltersContainer');
    const chartSubFilterSelect = document.getElementById('chartSubFilter');
    const mensajeErrorElement = document.getElementById('mensaje-error'); // Asegúrate de que este ID exista en tu HTML

    // 2. --- INICIALIZACIÓN DE ECHARTS (¡UNA SOLA VEZ Y MÁS ROBUSTA!) ---
    if (chartDom) {
        // Primero, intentar obtener una instancia existente y desecharla para evitar duplicados.
        // Esto resuelve el error "There is a chart instance already initialized".
        if (echarts.getInstanceByDom(chartDom)) {
            myChart = echarts.getInstanceByDom(chartDom);
            myChart.dispose(); // Desechar la instancia anterior
        }

        // Ahora, inicializar una nueva instancia.
        myChart = echarts.init(chartDom);

        // Opciones iniciales para ECharts (vacías o predeterminadas)
        let option = {
            tooltip: { trigger: 'axis' },
            grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
            xAxis: { type: 'category', boundaryGap: false, data: [] },
            yAxis: { type: 'value' },
            series: [{ name: 'Vistas', type: 'line', data: [] }]
        };
        myChart.setOption(option);

        // Añadir el listener de redimensionamiento UNA SOLA VEZ.
        // Esto resuelve el error "Cannot read properties of undefined (reading 'addEventListener')"
        window.addEventListener('resize', () => {
            if (myChart) {
                myChart.resize();
            }
        });
    } else {
        console.error("Elemento del DOM con ID 'chart' no encontrado. La gráfica no se inicializará.");
        if (mensajeErrorElement) { // Si el elemento para mensajes de error existe
            mensajeErrorElement.textContent = "Error grave: El contenedor de la gráfica no se encontró en la página.";
            mensajeErrorElement.style.display = 'block';
        }
    }


    // 3. --- DEFINICIÓN DE LA FUNCIÓN loadData ---
    // Esta función ahora está completa y utiliza las variables globales myChart y chartDom.
    async function loadData(filtro, year = null, subfiltro = null) {
        let url = '/api/estadisticas';
        let params = new URLSearchParams();

        if (filtro) {
            params.append('filtro', filtro);
        }
        if (year) {
            params.append('year', year);
        }
        if (subfiltro && filtro === 'año') { // Usar 'subfiltro' en lugar de 'chartSubFilter' para el parámetro
            params.append('chartFilter', subfiltro);
        }

        url += `?${params.toString()}`;

        // Mostrar loading solo si chartDom existe
        if (chartDom) {
            chartDom.innerHTML = `<div class="text-center text-gray-500 p-4 animate-pulse">Cargando estadísticas...</div>`;
            if (myChart) {
                myChart.showLoading();
            }
        } else {
            console.warn("No se puede mostrar mensaje de carga: Elemento 'chart' no encontrado.");
        }


    // 📌 Agrega aquí las funciones para calcular la semana pasada:
    function getStartOfLastWeek() {
        const date = new Date();
        const dayOfWeek = date.getDay();
        const diff = dayOfWeek + 6; // (0 = domingo, queremos ir al lunes de la semana anterior)
        date.setDate(date.getDate() - diff);
        date.setHours(0, 0, 0, 0); // Inicio del día
        return date;
    }

    function getEndOfLastWeek() {
        const date = getStartOfLastWeek();
        date.setDate(date.getDate() + 6); // Domingo de la semana pasada
        date.setHours(23, 59, 59, 999); // Fin del día
        return date;
    }

    function filterDataForLastWeek(data) {
        const startOfLastWeek = getStartOfLastWeek();
        const endOfLastWeek = getEndOfLastWeek();

        return data.filter(item => {
            const itemDate = new Date(item.fecha);
            return itemDate >= startOfLastWeek && itemDate <= endOfLastWeek;
        });
    }

    function mostrarMensajeError(msg) {
        const mensaje = document.getElementById('mensaje-error');
        if (mensaje) {
            mensaje.textContent = msg;
            mensaje.style.display = 'block';
        }
    }



    function getLast7Days() {
        const days = [];
        const today = new Date();

        for (let i = 6; i >= 0; i--) {
            const d = new Date(today);
            d.setDate(today.getDate() - i);
            const formatted = d.toISOString().slice(0, 10); // YYYY-MM-DD
            days.push(formatted);
        }

        return days;
    }

    function completarDiasFaltantes(vistas) {
        const dias = getLast7Days();
        const mapa = {};

        // Crear mapa base con 0 visitas
        dias.forEach(dia => {
            mapa[dia] = 0;
        });

        // Reemplazar con los datos reales
        vistas.forEach(item => {
            mapa[item.grupo] = item.total;
        });

        // Devolver como array ordenado
        return dias.map(dia => ({
            grupo: dia,
            total: mapa[dia]
        }));
    }

    // --- Modificar la función setFilter para controlar la visibilidad y cargar datos ---
    window.setFilter = function (filtro) {
        filtroActual = filtro;

        const buttons = document.querySelectorAll('#main-filter-buttons .filter-btn');
        buttons.forEach(btn => btn.classList.remove('active'));

        const activeBtn = Array.from(buttons).find(btn => btn.getAttribute('data-filtro') === filtro);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        // Mostrar u ocultar los selectores de año y sub-filtro
        if (filtroActual === 'año') {
            yearChartFiltersContainer.style.display = 'flex'; // Mostrar como flex para mantener el diseño
            // Cuando seleccionamos 'año', cargamos los datos con el año y el sub-filtro actuales
            loadData(filtroActual, selectedYearForChart, selectedChartSubFilter);
        } else {
            yearChartFiltersContainer.style.display = 'none'; // Ocultar
            // Para otros filtros, solo se carga con el filtro principal
            loadData(filtroActual);
        }
    };

    function renderChart(data) {
        let datosOrdenados = [...data.vistas];

        switch (filtroActual) {
            case 'ultimos3dias':
            case 'mes':
                datosOrdenados.sort((a, b) => parseInt(a.grupo) - parseInt(b.grupo));
                break;

            case 'semana':
                const hoy = new Date();
                let diaSemana = hoy.getDay(); // 0 = domingo, ..., 3 = miércoles
                let diferencia = (diaSemana >= 3) ? diaSemana - 3 : 7 - (3 - diaSemana);

                let inicio = new Date(hoy);
                inicio.setDate(hoy.getDate() - diferencia);

                const formatearLabel = (fecha) => {
                    const dias = ['dom.', 'lun.', 'mar.', 'mié.', 'jue.', 'vie.', 'sáb.'];
                    const meses = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];

                    const diaSemana = dias[fecha.getDay()];
                    const dia = fecha.getDate();
                    const mes = meses[fecha.getMonth()];

                    return `${diaSemana} ${dia} ${mes}`;
                };

                const datosSemana = [];

                for (let i = 0; i < 7; i++) {
                    const fecha = new Date(inicio);
                    fecha.setDate(inicio.getDate() + i);

                    const fechaClave = formatearLabel(fecha);
                    const etiqueta = fechaClave;

                    const dato = datosOrdenados.find(d => d.grupo === fechaClave);

                    datosSemana.push({
                        grupo: etiqueta,
                        total: dato ? dato.total : 0
                    });
                }

                datosOrdenados = datosSemana;
                break;

            case 'año':
                const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                datosOrdenados.sort((a, b) =>
                    meses.indexOf(a.grupo.toLowerCase()) - meses.indexOf(b.grupo.toLowerCase())
                );
                break;
        }

        if (!Array.isArray(datosOrdenados) || datosOrdenados.length === 0) {
            document.querySelector("#chart").innerHTML = `<div class="text-center text-gray-500 p-4">No hay datos para mostrar.</div>`;
            // Asegúrate de resetear las métricas también si no hay datos de visitas
            updateMetrics({ usuarios: 0, registrados: 0, activos: 0, conectados: 0 });
            return;
        }

        // Preparar datos
        const labels = datosOrdenados.map(item => item.grupo);
        const values = datosOrdenados.map(item => parseInt(item.total));

        // Crear instancia o reutilizar
        const chartDom = document.getElementById('chart');
        if (!window.chartInstance) {
            window.chartInstance = echarts.init(chartDom);
        } else {
            window.chartInstance.dispose();
            window.chartInstance = echarts.init(chartDom);
        }

        const options = {
            tooltip: {
                trigger: 'axis',
                formatter: function (params) {
                    const data = params[0];
                    const index = data.dataIndex;
                    const valorActual = data.value;
                    let porcentaje = 0;
                    if (index > 0) {
                        const anterior = values[index - 1];
                        porcentaje = anterior === 0 ? 0 : ((valorActual - anterior) / anterior * 100).toFixed(1);
                    }
                    return `
                        <div class="p-2">
                            <strong>${data.name}</strong><br>
                            Visitas: <strong>${valorActual}</strong><br>
                            Cambio: <strong>${porcentaje}%</strong>
                        </div>`;
                }
            },
            grid: {
                left: '0%',
                right: '0%',
                top: '10%',
                bottom: '10',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: labels,
                axisLabel: {
                    color: '#000',
                    fontSize: 12
                }
            },
            yAxis: {
                type: 'value'
            },
            series: [{
                data: values,
                type: 'line',
                smooth: true,
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: '#22c55e' },
                        { offset: 1, color: '#ffffff' }
                    ])
                },
                itemStyle: {
                    color: '#22c55e'
                },
                lineStyle: {
                    width: 1.5
                },
                symbolSize: 8
            }]
        };

        window.chartInstance.setOption(options);

        // Hacer que se redimensione automáticamente
        window.addEventListener('resize', () => {
            window.chartInstance.resize();
        });

        // Actualizar métricas
        updateMetrics(data);
        console.log("Datos recibidos:", data.vistas);

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
    function loadData(filtro = null, year = null, chartSubFilter = null) {
        let url = '/api/estadisticas';
        let params = new URLSearchParams();

        if (filtro) {
            params.append('filtro', filtro);
        }
        if (year) { // Siempre enviar el año si está disponible, incluso si filtro no es 'año'
            params.append('year', year);
        }
        if (chartSubFilter && filtro === 'año') { // Solo enviar el sub-filtro si el filtro principal es 'año'
            params.append('chartFilter', chartSubFilter);
        }

        url += `?${params.toString()}`;

        const chartElement = document.querySelector("#chart");
        chartElement.innerHTML = `<div class="text-center text-gray-500 p-4 animate-pulse">Cargando estadísticas...</div>`;

        const storageKey = `offline_stats_${filtro || 'default'}_${year || ''}_${chartSubFilter || ''}`;

        if (navigator.onLine) {
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(`HTTP error! Status: ${response.status}, Detail: ${errorData.detalle || response.statusText}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    localStorage.setItem(storageKey, JSON.stringify(data));
                    renderChart(data);
                })
                .catch(error => {
                    console.error("Error al obtener las estadísticas:", error);
                    mostrarMensajeError(`No se pudieron cargar los datos: ${error.message}. Mostrando datos offline si están disponibles.`);
                    loadOfflineData(storageKey);
                });
        } else {
            mostrarMensajeError("No hay conexión a Internet. Mostrando datos offline si están disponibles.");
            loadOfflineData(storageKey);
        }
    }

    // Cargar datos desde localStorage si no hay conexión
    function loadOfflineData(storageKey) {
        const savedData = localStorage.getItem(storageKey);
        if (savedData) {
            renderChart(JSON.parse(savedData));
            const chartElement = document.querySelector("#chart");
            // Aquí puedes ajustar el mensaje para datos offline
            chartElement.innerHTML += "<div class='text-center text-yellow-500 p-4'>Mostrando datos almacenados (offline).</div>";
        } else {
            const chartElement = document.querySelector("#chart");
            chartElement.innerHTML = "<div class='text-center text-red-500 p-4'>No hay conexión a Internet y no se encontraron datos almacenados.</div>";
            updateMetrics({ usuarios: 0, registrados: 0, activos: 0, conectados: 0 }); // Resetea métricas si no hay datos
        }
    }
}



    // Cargar los datos inicialmente
    loadData();

    // Exponer la función de cambiar el filtro globalmente
    window.setFilter = setFilter;

};

