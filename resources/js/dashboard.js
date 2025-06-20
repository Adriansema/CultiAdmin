// Asegúrate de que esta línea esté al principio de tu archivo
import * as echarts from 'echarts';
import flatpickr from 'flatpickr';
import "flatpickr/dist/flatpickr.min.css";
import { Spanish } from 'flatpickr/dist/l10n/es.js';

// Si MonthSelectPlugin es necesario para yearPicker, asegúrate de que se use
// import MonthSelectPlugin from 'flatpickr/dist/plugins/monthSelect/index.js';


// Declarar myChart globalmente para la instancia de ECharts
let myChart = null;

// Variable global para el filtro actual
let filtroActual = 'ultimos3dias';


document.addEventListener("DOMContentLoaded", function () {
    // --- Inicialización y poblar el dropdown de años ---
    const yearDropdown = document.getElementById('yearPicker');
    if (yearDropdown) {
        const currentYear = new Date().getFullYear();
        const startYear = 2025;

        for (let year = currentYear; year >= startYear; year--) {
            let option = document.createElement('option');
            option.value = year.toString();
            option.textContent = year.toString();
            yearDropdown.appendChild(option);
        }
        yearDropdown.value = currentYear.toString();

        yearDropdown.addEventListener('change', function () {
            window.setFilter('año');
        });
    } else {
        console.error("Error: Elemento con ID 'yearPicker' no encontrado. No se pudo inicializar el dropdown de año.");
    }


    // --- Inicialización de Flatpickr para el selector de rango de fechas (#dateRangePicker) ---
    const dateRangePickerInput = document.getElementById("dateRangePicker");
    let dateRangePicker = null;
    if (dateRangePickerInput) {
        dateRangePicker = flatpickr(dateRangePickerInput, {
            mode: "range",
            dateFormat: "Y-m-d",
            altFormat: "F j, Y",
            altInput: true,
            defaultViewDate: new Date(new Date().getFullYear(), 0, 1),
            defaultDate: [new Date(new Date().getFullYear(), 0, 1), "today"],
            maxDate: "today",
            allowInput: false,
            // plugins: [new MonthSelectPlugin({ shorthand: true, dateFormat: "M Y" })], // Ejemplo si usas MonthSelectPlugin
            onChange: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    filtroActual = 'rangoPersonalizado';
                    loadData(filtroActual, dateStr);
                }
            },
        });
    } else {
        console.warn("Advertencia: Elemento con ID 'dateRangePicker' no encontrado. El selector de rango de fechas no se inicializará. Asegúrate de que existe o elimínalo si no lo usas.");
    }


     // --- Función para activar/desactivar los botones de filtro ---
    function setActiveFilterButton(filterType) {
        document.querySelectorAll('.filter-btn').forEach(button => {
            const currentFilter = button.getAttribute('data-filtro');

            // 1. Remover TODAS las clases de estado activo (fondo, texto, sombra, bordes sólidos)
            button.classList.remove('bg-green-600', 'text-white', 'shadow-md', 'border', 'border-green-600');

            // 2. Remover TODAS las clases del botón Año para re-aplicar según estado
            button.classList.remove('border-2', 'border-dashed', 'border-blue-400', 'text-blue-600', 'hover:text-blue-800');


            // 3. Aplicar clases de estado INACTIVO por defecto a todos los botones
            if (currentFilter === 'año') {
                // El botón Año siempre tiene border-2 border-dashed border-blue-400 (definido en HTML en el estado base)
                // Aquí solo gestionamos su color de texto para el estado inactivo
                button.classList.add('border-2', 'border-dashed', 'border-blue-400', 'text-blue-600', 'hover:text-blue-800');
            } else {
                // Botones normales (3 días, Semana, Mes) cuando INACTIVOS:
                // Tienen texto verde y NO deben tener borde sólido visible.
                // El hover:border se gestiona desde el HTML base del botón
                button.classList.add('text-green-600', 'hover:border', 'hover:border-green-600');
            }
        });

        // 4. Aplicar las clases de estado ACTIVO al botón seleccionado
        const activeButton = document.querySelector(`.filter-btn[data-filtro="${filterType}"]`);
        if (activeButton) {
            const activeFilter = activeButton.getAttribute('data-filtro');

            // Remover las clases de inactivo del botón activo antes de añadir las de activo
            if (activeFilter === 'año') {
                activeButton.classList.remove('text-blue-600', 'hover:text-blue-800', 'border-2', 'border-dashed', 'border-blue-400');
            } else {
                activeButton.classList.remove('text-green-600', 'hover:border', 'hover:border-green-600');
            }

            // Añadir las clases de ACTIVO (fondo verde, texto blanco, sombra)
            activeButton.classList.add('bg-green-600', 'text-white', 'shadow-md');

            // Añadir el borde sólido verde si es un botón normal (no Año)
            if (activeFilter !== 'año') {
                activeButton.classList.add('border', 'border-green-600');
            } else {
                // Si es el botón Año, reaplicar su borde punteado azul permanente
                activeButton.classList.add('border-2', 'border-dashed', 'border-blue-400');
            }
        }
        console.log(`DEBUG: Botón activo establecido a: ${filterType}`);
    }



    // --- setFilter global para los onclick de los botones HTML ---
    window.setFilter = function (filterType) {
        filtroActual = filterType;
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active-filter-button');
        });
        const activeBtn = document.querySelector(`.filter-btn[data-filtro="${filterType}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active-filter-button');
        }

        if (dateRangePicker && filterType !== 'rangoPersonalizado') {
            dateRangePicker.clear();
        }

        const yearChartFiltersContainer = document.getElementById('yearChartFiltersContainer');
        if (yearChartFiltersContainer) {
            yearChartFiltersContainer.style.display = (filterType === 'año') ? 'flex' : 'none';
        }

        if (filterType === 'año' && yearDropdown) {
            loadData('año', yearDropdown.value, 'month');
        } else {
            loadData(filterType);
        }
    };


    // --- Adaptar la función loadData ---
    function loadData(filterType, value = null, subFilter = null) {
        let url = `/api/estadisticas?`;

        if (filterType === 'rangoPersonalizado' && value) {
            const [startDate, endDate] = value.split(' to ');
            url += `startDate=${startDate}&endDate=${endDate}`;
        } else if (filterType === 'ultimos3dias' || filterType === 'semana' || filterType === 'mes' || filterType === 'todo') {
            url += `filter=${filterType}`;
        } else if (filterType === 'año' && value) {
            url += `filter=año&year=${value}&subFilter=${subFilter || 'month'}`;
        } else {
            url += `filter=ultimos3dias`;
            filtroActual = 'ultimos3dias';
        }

        console.log("Cargando datos de la URL:", url);
        fetch(url)
            .then(response => {
                console.log("Respuesta HTTP:", response);
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(`Error HTTP! Status: ${response.status}, Detail: ${errorData.detalle || response.statusText}`);
                    }).catch(() => {
                        throw new Error(`Error HTTP! Status: ${response.status}, Text: ${response.statusText}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log("JSON data received from API:", data);
                localStorage.setItem('dashboardData', JSON.stringify(data));
                renderChart(data);
                updateMetrics(data);
            })
            .catch(error => {
                console.error('Error getting statistics:', error);
                const chartDom = document.getElementById('chart');
                if (chartDom) {
                    chartDom.innerHTML = `<div class="text-center text-red-500 p-4">Error loading chart: ${error.message}.</div>`;
                    if (myChart) { myChart.dispose(); myChart = null; }
                }
            });
    }

    // --- Función updateMetrics ---
    function updateMetrics(data) {
        document.getElementById('users-count').textContent = data.usuarios || 0;
        document.getElementById('active-count').textContent = data.activos || 0;
        document.getElementById('connected-count').textContent = data.conectados || 0;

        const totalUsers = data.usuarios || 0;
        document.getElementById('users-percent').textContent = `${(totalUsers > 0 ? (data.usuarios / totalUsers * 100) : 0).toFixed(0)}% of users`;
        document.getElementById('active-percent').textContent = `${(totalUsers > 0 ? (data.activos / totalUsers * 100) : 0).toFixed(0)}% of users`;
        document.getElementById('connected-percent').textContent = `${(totalUsers > 0 ? (data.conectados / totalUsers * 100) : 0).toFixed(0)}% of users`;
    }



    // --- Initial load ---
    window.setFilter('ultimos3dias'); // Load 'ultimos3dias' filter on initial page load
});


// --- renderChart function (¡VERSIÓN FINAL CORREGIDA PARA ANIMACIONES FLUIDAS Y TEXTOS DINÁMICOS!) ---
function renderChart(data) {
    const chartDom = document.getElementById('chart');

    if (!chartDom) {
        console.error("ECharts Error: Contenedor de la gráfica con id 'chart' no encontrado en el DOM.");
        return;
    }

    if (chartDom.offsetWidth === 0 || chartDom.offsetHeight === 0) {
        console.warn(`ECharts Advertencia: El contenedor de la gráfica tiene ancho o alto cero (${chartDom.offsetWidth}x${chartDom.offsetHeight}). Reintentando renderizar en 200ms...`);
        chartDom.innerHTML = `<div class="text-center text-gray-500 p-4">Cargando gráfica...</div>`;
        setTimeout(() => renderChart(data), 200);
        return;
    }

    // Si llegamos aquí, chartDom tiene dimensiones válidas.
    // Solo limpiar el HTML si la gráfica NO ha sido inicializada aún (para el mensaje de carga).
    if (myChart === null) {
        chartDom.innerHTML = '';
        console.log("ECharts DEBUG: Limpiando innerHTML antes de la primera inicialización.");
    }


    let datosOrdenados = [...data.vistas];

    if (!Array.isArray(datosOrdenados) || datosOrdenados.length === 0) {
        chartDom.innerHTML = `<div class="text-center text-gray-500 p-4">No hay datos válidos para mostrar para este período.</div>`;
        updateMetrics({ usuarios: 0, registrados: 0, activos: 0, conectados: 0 }); // Aunque no se use registrados, es buena práctica pasar 0
        if (myChart) {
            myChart.dispose(); // Si no hay datos, dispose de la instancia para liberar recursos
            myChart = null;
            console.log("ECharts DEBUG: Gráfica dispuesta y myChart a null debido a datos vacíos.");
        }
        return;
    }

    // Ordenamiento basado en el filtro actual para asegurar la secuencia correcta en la gráfica
    switch (data.selectedFilter) {
        case 'ultimos3dias':
            const dayNamesOrderUltimos3Dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
            datosOrdenados.sort((a, b) => {
                const indexA = dayNamesOrderUltimos3Dias.indexOf(a.grupo.toLowerCase());
                const indexB = dayNamesOrderUltimos3Dias.indexOf(b.grupo.toLowerCase());
                if (indexA === -1 || indexB === -1) {
                    console.warn(`ECharts Advertencia: Día no reconocido en el ordenamiento: ${a.grupo} o ${b.grupo}`);
                    return 0;
                }
                return indexA - indexB;
            });
            break;

        case 'semana':
            const monthNamesForSorting = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
            datosOrdenados.sort((a, b) => {
                const parseDate = (label) => {
                    const parts = label.split(' ');
                    const day = parseInt(parts[1]);
                    const month = monthNamesForSorting.indexOf(parts[2].replace('.', '').toLowerCase());
                    const year = new Date().getFullYear();
                    return new Date(year, month, day);
                };
                return parseDate(a.grupo) - parseDate(b.grupo);
            });
            break;

        case 'mes':
            datosOrdenados.sort((a, b) => {
                const weekNumA = parseInt(a.grupo.replace('Semana ', ''));
                const weekNumB = parseInt(b.grupo.replace('Semana ', ''));
                return weekNumA - weekNumB;
            });
            break;

        case 'año':
            const mesesOrden = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            datosOrdenados.sort((a, b) =>
                mesesOrden.indexOf(a.grupo.toLowerCase()) - mesesOrden.indexOf(b.grupo.toLowerCase())
            );
            break;

        case 'rangoPersonalizado':
            datosOrdenados.sort((a, b) => new Date(a.grupo) - new Date(b.grupo));
            break;

        case 'todo':
            datosOrdenados.sort((a, b) => parseInt(a.grupo) - parseInt(b.grupo));
            break;
    }

    const labels = datosOrdenados.map(item => item.grupo);
    const values = datosOrdenados.map(item => parseInt(item.total));

    // Inicializar ECharts si no existe.
    if (myChart === null) {
        console.log("ECharts DEBUG: Inicializando ECharts por primera vez.");
        myChart = echarts.init(chartDom);
        // Añadir el listener de redimensionamiento una sola vez
        window.addEventListener('resize', function() {
            if (myChart && !myChart.isDisposed()) {
                 myChart.resize();
                 console.log("ECharts DEBUG: Gráfica redimensionada.");
            }
        });
    } else {
        console.log("ECharts DEBUG: Actualizando ECharts con nuevos datos y opciones.");
        // ¡No se llama a myChart.clear() aquí! ECharts manejará la animación de actualización automáticamente.
    }

    // Determinar el nombre de la serie y el título de la gráfica dinámicamente
    // Utiliza data.selectedFilter y data.chartSubFilter que vienen del backend
    let seriesChartName = 'Visitas';
    let chartTitleText = '';

    if (data.selectedFilter === 'año' && data.chartSubFilter === 'month') {
        seriesChartName = 'Registros';
        chartTitleText = 'Nuevos Registros por Mes';
    } else if (data.selectedFilter === 'año' && data.chartSubFilter === 'week') {
         seriesChartName = 'Registros';
         chartTitleText = 'Nuevos Registros por Semana';
    } else if (data.selectedFilter === 'año' && data.chartSubFilter === 'day') {
         seriesChartName = 'Registros';
         chartTitleText = 'Nuevos Registros por Día';
    } else if (data.selectedFilter === 'año' && data.chartSubFilter === 'hour') {
         seriesChartName = 'Registros';
         chartTitleText = 'Nuevos Registros por Hora';
    }


    const options = {
        // Habilitar animaciones para la inicialización y actualización
        animation: true,
        animationDuration: 1000, // Duración de la animación inicial en ms (1 segundo)
        animationEasing: 'cubicOut', // Tipo de curva de animación
        // Animación al actualizar datos
        animationDurationUpdate: 1000, // Duración de la animación al actualizar datos en ms (1 segundo)
        animationEasingUpdate: 'cubicOut', // Tipo de curva de animación al actualizar

        tooltip: {
            trigger: 'axis',
            formatter: function (params) {
                const dataPoint = params[0];
                const valorActual = dataPoint.value;
                const seriesName = seriesChartName; // <-- CORREGIDO: Usa la variable dinámica

                let changeInfo = '';
                const index = dataPoint.dataIndex;
                if (index > 0 && values[index - 1] !== undefined) {
                    const previousValue = values[index - 1];
                    if (previousValue === 0) {
                        changeInfo = `Cambio: N/A (desde 0)`; // Traducido
                    } else {
                        const percentageChange = ((valorActual - previousValue) / previousValue * 100).toFixed(1);
                        changeInfo = `Cambio: ${percentageChange}%`; // Traducido
                    }
                }

                return `
                    <div class="p-2">
                        <strong>${dataPoint.name}</strong><br>
                        ${seriesName}: <strong>${valorActual}</strong><br>
                        ${changeInfo}
                    </div>`;
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            top: '10%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            data: labels,
            axisLabel: {
                color: '#000',
                fontSize: 12,
                interval: 0,
                rotate: 30
            }
        },
        yAxis: {
            type: 'value',
            minInterval: 1,
            axisLabel: {
                formatter: function (value) {
                    return value % 1 === 0 ? value : '';
                }
            }
        },
        series: [{
            name: seriesChartName, // <-- CORREGIDO: Usa la variable dinámica
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
        }],
        title: {
            text: chartTitleText // <-- CORREGIDO: Usa la variable dinámica
        }
    };

    myChart.setOption(options);
    myChart.resize();

    console.log("ECharts DEBUG: Gráfica renderizada/actualizada con datos:", data.vistas);
}
