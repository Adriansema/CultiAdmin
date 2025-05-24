
//para poder utilizar la libreria de apechar
import * as echarts from 'echarts';

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

        const activeBtn = Array.from(buttons).find(btn =>
            btn.textContent.trim().toLowerCase() === filtro.toLowerCase()
        );

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



    // Función para renderizar la gráfica con los datos
    function renderChart(data) {
        if (!Array.isArray(data.vistas)) {
            console.error("Los datos de 'vistas' no son válidos:", data.vistas);
            return;
        }

        // Asignar data.vistas como base
        let datosOrdenados = [...data.vistas];

        // Filtrar y ordenar los datos según el filtro seleccionado
        switch (filtroActual) {
            case 'ultimos3dias':
                datosOrdenados.sort((a, b) => parseInt(a.grupo) - parseInt(b.grupo));
                break;
            case 'semana':
                const diasSemana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                datosOrdenados.sort((a, b) =>
                    diasSemana.indexOf(
                        a.grupo.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase()
                    ) -
                    diasSemana.indexOf(
                        b.grupo.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase()
                    )
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
        // Después (más permisivo)
        if (!Array.isArray(data.vistas) || data.vistas.length === 0) {
            document.querySelector("#chart").innerHTML = `<div class="text-center text-gray-500 p-4">Los datos no son válidos.</div>`;
            return;
        }

        // Validar que cada entrada tenga grupo y total numérico
        datosOrdenados = datosOrdenados.filter(stat => {
            return stat.grupo !== undefined && !isNaN(parseInt(stat.total));
        });

        // Mostrar lo que se recibe en consola
        console.log("DEBUG data.vistas recibida:", JSON.stringify(data.vistas));

        // Verificar si hay datos válidos
        if (datosOrdenados.length === 0) {
            // Mostrar la gráfica con un solo punto "Sin datos"
            datosOrdenados = [{
                grupo: 'Sin datos',
                total: 0
            }];
        }


        // Procesar los datos limpios

        let seriesData = datosOrdenados.map(stat => ({
            x: stat.grupo || "Sin etiqueta",
            y: parseInt(stat.total)
        }));


        console.log("Datos para la gráfica:", seriesData);




        // Calcular los porcentajes de cambio entre los valores
        porcentajes = seriesData.map((p, i, arr) => {
            if (i === 0) return 0;
            const anterior = arr[i - 1].y;
            return anterior === 0 ? 0 : ((p.y - anterior) / anterior * 100).toFixed(1);
        });

        // Validar datos antes de graficar
        if (!Array.isArray(seriesData) || seriesData.length === 0 || seriesData.some(v => isNaN(v.y))) {
            document.querySelector("#chart").innerHTML = `<div class="text-center text-gray-500 p-4">No hay datos numéricos válidos para graficar.</div>`;
            return;
        }

        // Inicializar el contenedor si aún no existe
        if (!window.chartInstance) {
            const chartDom = document.getElementById('chart');
            window.chartInstance = echarts.init(chartDom);
        } else {
            window.chartInstance.dispose();
            const chartDom = document.getElementById('chart');
            window.chartInstance = echarts.init(chartDom);
        }
        const chart = echarts.init(document.getElementById('chart'));

        // Aquí configuras tu gráfico
        chart.setOption({

        });

        // Hacer que se redimensione al cambiar el tamaño de la ventana
        window.addEventListener('resize', () => {
            chart.resize();
        });


        // Convertir los datos a formato compatible con ECharts
        const labels = seriesData.map(item => item.x);
        const values = seriesData.map(item => item.y);

        const options = {
            tooltip: {
                trigger: 'axis',
                formatter: function (params) {
                    const data = params[0];
                    const valorActual = data.value;
                    const index = data.dataIndex;
                    let porcentaje = 0;
                    if (index > 0) {
                        const anterior = params[0].seriesData[index - 1]?.value || 0;
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
                left: '5%',
                right: '5%',
                top: '10%',
                bottom: '20%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: labels,
                axisLabel: {
                    color: '#000',
                    fontSize: 12,
                    interval: 0,
                    rotate: 0// útil si las etiquetas son largas
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

        if (!Array.isArray(seriesData) || seriesData.length === 0 || seriesData.some(v => isNaN(v.y))) {
            document.querySelector("#chart").innerHTML = `<div class="text-center text-gray-500 p-4">No hay datos numéricos válidos para graficar.</div>`;
            return;
        }

        // Renderizar el gráfico
        window.chartInstance.setOption(options);

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

