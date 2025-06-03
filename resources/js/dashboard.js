
//para poder utilizar la libreria de apechar
import * as echarts from 'echarts';
import { Container } from 'postcss';

document.addEventListener("DOMContentLoaded", function () {
    let filtroActual = 'ultimos3dias';
    let porcentajes = [];

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


    // Cambiar el filtro y cargar datos
   function setFilter(filtro) {
    filtroActual = filtro;

    const buttons = document.querySelectorAll('#filter-buttons .filter-btn');
    buttons.forEach(btn => btn.classList.remove('active'));

    // Busca el botón con data-filtro igual al filtro actual
    const activeBtn = Array.from(buttons).find(btn => btn.getAttribute('data-filtro') === filtro);

    if (activeBtn) {
        activeBtn.classList.add('active');
    }

    loadData(filtro);
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

