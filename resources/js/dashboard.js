// Importaciones necesarias
import * as echarts from 'echarts'; // Para las gráficas de datos
// Flatpickr ya NO se importará para el selector de año, ya que usaremos una UI personalizada.


// =========================================================================
// VARIABLES GLOBALES
// =========================================================================
let myChart = null; // Instancia global de ECharts para poder actualizarla
let filtroActual = 'semana'; // Define el filtro por defecto al cargar la página
let currentSelectedYear = new Date().getFullYear(); // Variable global para el año actualmente seleccionado


// =========================================================================
// FUNCIONES GLOBALES (accesibles desde el HTML - window.setFilter)
// =========================================================================

/**
 * @function window.setFilter
 * @description Función global llamada por los eventos `onclick` de los botones de filtro.
 * Controla el filtro activo, la visibilidad del selector de año y la carga de datos.
 * @param {string} filterType - El tipo de filtro a activar (ej. 'semana', 'año').
 * @param {number} [yearToLoadValue=null] - El año específico si el filtro es 'año' (opcional, para uso interno).
 */
window.setFilter = function(filterType, yearToLoadValue = null) {
    console.log(`DEBUG: setFilter llamado. Tipo de filtro: ${filterType}, Año Seleccionado (directo): ${yearToLoadValue}`);

    filtroActual = filterType; // Actualiza la variable global del filtro actual

    // 1. Actualiza los estilos de los botones de filtro (ESTO AHORA MANEJA LA VISIBILIDAD DEL customYearSelector)
    setActiveFilterButton(filterType);

    // 2. Solo actualiza el valor del año mostrado si el filtro es 'año'
    if (filterType === 'año') {
        // Si se pasa un año específico (ej. desde los botones de navegación), actualiza currentSelectedYear
        if (yearToLoadValue !== null) {
            currentSelectedYear = yearToLoadValue;
        }
        // Actualiza el texto visible del año
        const currentYearDisplay = document.getElementById('currentYearDisplay');
        if (currentYearDisplay) {
            currentYearDisplay.textContent = currentSelectedYear;
        }
        console.log(`DEBUG: Año seleccionado en UI personalizada: ${currentSelectedYear}`);
    }
    // NOTA: La visibilidad de customYearSelector ahora se controla COMPLETAMENTE desde setActiveFilterButton.

    // 3. Llama a la función para cargar los datos desde la API
    if (filterType === 'año') {
        loadData('año', currentSelectedYear); // Usa el año global actualizado
    } else {
        loadData(filterType);
    }
};


// =========================================================================
// INICIALIZACIÓN DE DOMContentLoaded (Para tareas que requieren el DOM completo)
// =========================================================================
document.addEventListener("DOMContentLoaded", function () {
    console.log("DEBUG: DOMContentLoaded - El DOM está completamente cargado. Iniciando componentes.");

    // --- Configuración inicial del año y sus botones ---
    const currentYearDisplay = document.getElementById('currentYearDisplay');
    const prevYearBtn = document.getElementById('prevYearBtn');
    const nextYearBtn = document.getElementById('nextYearBtn');

    if (currentYearDisplay) {
        currentYearDisplay.textContent = currentSelectedYear; // Establece el año inicial al cargar
    }

    if (prevYearBtn) {
        prevYearBtn.addEventListener('click', () => {
            currentSelectedYear--;
            // Llama a setFilter para actualizar la UI (el texto del año) y cargar los nuevos datos.
            window.setFilter('año', currentSelectedYear);
        });
    }

    if (nextYearBtn) {
        nextYearBtn.addEventListener('click', () => {
            currentSelectedYear++;
            // Llama a setFilter para actualizar la UI (el texto del año) y cargar los nuevos datos.
            window.setFilter('año', currentSelectedYear);
        });
    }

    // --- Carga inicial de datos al cargar la página ---
    window.setFilter('semana');
    console.log("DEBUG: Carga inicial de dashboard con filtro 'semana'.");
});


// =========================================================================
// OTRAS FUNCIONES (no necesitan ser globales, llamadas internamente)
// =========================================================================

/**
 * @function setActiveFilterButton
 * @description Gestiona las clases de Tailwind para los botones de filtro, incluyendo el grupo "Año".
 * Controla la visibilidad del selector de año personalizado.
 * @param {string} filterType - El tipo de filtro que debe estar activo.
 */
function setActiveFilterButton(filterType) {
    // 1. Restablecer estilos para todos los botones estándar
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.classList.remove('bg-green-600', 'text-white', 'shadow-md', 'border', 'border-green-600');
        button.classList.add('text-green-600', 'border', 'border-transparent', 'hover:border-green-500', 'hover:bg-green-50');
    });

    // 2. Restablecer estilos para el grupo de filtro "Año"
    const yearFilterGroup = document.getElementById('yearFilterGroup');
    const customYearSelector = document.getElementById('customYearSelector'); // Obtener referencia al selector personalizado

    if (yearFilterGroup) {
        yearFilterGroup.classList.remove('bg-green-600', 'text-white', 'shadow-md', 'border', 'border-green-600');
        yearFilterGroup.classList.add('text-blue-600', 'border', 'border-transparent', 'hover:border-blue-400', 'hover:bg-blue-50');

        // Restablecer colores de texto para elementos dentro de yearFilterGroup (incluyendo el span "Año")
        const yearTextSpan = yearFilterGroup.querySelector('span'); // El primer span es el texto "Año"
        const yearIconSvg = yearFilterGroup.querySelector('span svg'); // El SVG del icono del año
        const currentYearSpan = yearFilterGroup.querySelector('#currentYearDisplay');
        const prevYearSvg = yearFilterGroup.querySelector('#prevYearBtn svg');
        const nextYearSvg = yearFilterGroup.querySelector('#nextYearBtn svg');

        if (yearTextSpan) {
             yearTextSpan.classList.remove('text-white'); // Asegurar que el texto "Año" no esté blanco
             yearTextSpan.classList.add('text-blue-600'); // O el color original inactivo si es diferente
        }
        if (yearIconSvg) {
            yearIconSvg.classList.remove('text-white');
            yearIconSvg.classList.add('text-blue-600'); // Color original del icono
        }
        if (currentYearSpan) {
            currentYearSpan.classList.remove('text-white');
            currentYearSpan.classList.add('text-gray-800'); // Asegurar default text color para el año numérico
        }
        if (prevYearSvg) {
            prevYearSvg.classList.remove('text-white');
            prevYearSvg.classList.add('text-gray-600'); // Asegurar default icon color
        }
        if (nextYearSvg) {
            nextYearSvg.classList.remove('text-white');
            nextYearSvg.classList.add('text-gray-600'); // Asegurar default icon color
        }

        // Ocultar customYearSelector por defecto (se mostrará solo si 'año' está activo)
        if (customYearSelector) {
            customYearSelector.style.display = 'none';
        }
    }


    // 3. Aplicar estilos al elemento activo
    if (filterType === 'año') {
        if (yearFilterGroup) {
            yearFilterGroup.classList.remove('text-blue-600', 'border', 'border-transparent', 'hover:border-blue-400', 'hover:bg-blue-50');
            yearFilterGroup.classList.add('bg-green-600', 'text-white', 'shadow-md', 'border', 'border-green-600');

            // Mostrar customYearSelector
            if (customYearSelector) {
                customYearSelector.style.display = 'flex';
            }

            // Cambiar colores de texto/iconos a blanco para los elementos dentro de yearFilterGroup (incluyendo el span "Año")
            const yearTextSpan = yearFilterGroup.querySelector('span'); // El primer span es el texto "Año"
            const yearIconSvg = yearFilterGroup.querySelector('span svg'); // El SVG del icono del año
            const currentYearSpan = yearFilterGroup.querySelector('#currentYearDisplay');
            const prevYearSvg = yearFilterGroup.querySelector('#prevYearBtn svg');
            const nextYearSvg = yearFilterGroup.querySelector('#nextYearBtn svg');

            if (yearTextSpan) yearTextSpan.classList.add('text-white');
            if (yearIconSvg) yearIconSvg.classList.add('text-white');
            if (currentYearSpan) currentYearSpan.classList.add('text-white');
            if (prevYearSvg) prevYearSvg.classList.add('text-white');
            if (nextYearSvg) nextYearSvg.classList.add('text-white');
        }
    } else {
        const activeButton = document.querySelector(`.filter-btn[data-filtro="${filterType}"]`);
        if (activeButton) {
            activeButton.classList.remove('text-green-600', 'border', 'border-transparent', 'hover:border-green-500', 'hover:bg-green-50');
            activeButton.classList.add('bg-green-600', 'text-white', 'shadow-md', 'border', 'border-green-600');
        }
    }
    console.log(`DEBUG: Botón activo establecido a: ${filterType}`);
}


/**
 * @function loadData
 * @description Realiza una solicitud fetch a la API para obtener datos de estadísticas.
 * @param {string} filterType - El tipo de filtro solicitado (ej. 'semana', 'año').
 * @param {string|number} [value=null] - Valor adicional del filtro (ej. el año para 'año').
 */
function loadData(filterType, value = null) {
    let url = `/api/estadisticas?`;

    if (filterType === 'año' && value) {
        url += `filter=año&year=${value}`;
    } else if (['ultimos3dias', 'semana', 'mes', 'todo'].includes(filterType)) {
        url += `filter=${filterType}`;
    } else {
        url += `filter=semana`; // Fallback
        filtroActual = 'semana';
    }

    console.log("DEBUG: Realizando fetch a la URL:", url);

    fetch(url)
        .then(response => {
            console.log("DEBUG: Respuesta HTTP recibida del fetch:", response);
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(`Error HTTP! Estado: ${response.status}, Detalle: ${errorData.detalle || response.statusText}`);
                }).catch(() => {
                    throw new Error(`Error HTTP! Estado: ${response.status}, Texto: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log("DEBUG: Datos JSON recibidos de la API (loadData):", data);
            localStorage.setItem('dashboardData', JSON.stringify(data));
            renderChart(data);
            updateMetrics(data);
        })
        .catch(error => {
            console.error('ERROR: Error al obtener estadísticas en loadData:', error);
            const chartDom = document.getElementById('chart');
            if (chartDom) {
                chartDom.innerHTML = `<div class="flex justify-center items-center h-full text-red-500 p-4">Error al cargar la gráfica: ${error.message}.</div>`;
                if (myChart) { myChart.dispose(); myChart = null; }
            }
            updateMetrics({ usuarios: 0, registrados: 0, activos: 0, conectados: 0 });
        });
}

/**
 * @function updateMetrics
 * @description Actualiza los valores y porcentajes en las tarjetas de métricas.
 * @param {object} data - Objeto con los datos de usuarios, activos, conectados, registrados.
 */
function updateMetrics(data) {
    const usersCountElement = document.getElementById('users-count');
    const registeredCountElement = document.getElementById('registered-count');
    const activeCountElement = document.getElementById('active-count');
    const connectedCountElement = document.getElementById('connected-count');

    const usersPercentElement = document.getElementById('users-percent');
    const registeredPercentElement = document.getElementById('registered-percent');
    const activePercentElement = document.getElementById('active-percent');
    const connectedPercentElement = document.getElementById('connected-percent');

    if (usersCountElement) usersCountElement.textContent = data.usuarios || 0;
    if (registeredCountElement) registeredCountElement.textContent = data.registrados || 0;
    if (activeCountElement) activeCountElement.textContent = data.activos || 0;
    if (connectedCountElement) connectedCountElement.textContent = data.conectados || 0;

    const totalUsers = data.usuarios || 0;

    if (usersPercentElement) {
        usersPercentElement.textContent = `${(totalUsers > 0 ? (data.usuarios / totalUsers * 100) : 0).toFixed(0)}% de los usuarios`;
    }
    if (registeredPercentElement) {
        registeredPercentElement.textContent = `${(totalUsers > 0 ? (data.registrados / totalUsers * 100) : 0).toFixed(0)}% de los usuarios`;
    }
    if (activePercentElement) {
        activePercentElement.textContent = `${(totalUsers > 0 ? (data.activos / totalUsers * 100) : 0).toFixed(0)}% de los usuarios`;
    }
    if (connectedPercentElement) {
        connectedPercentElement.textContent = `${(totalUsers > 0 ? (data.conectados / totalUsers * 100) : 0).toFixed(0)}% de los usuarios`;
    }
}

/**
 * @function renderChart
 * @description Renderiza o actualiza la gráfica ECharts.
 * @param {object} data - Objeto con los datos de 'vistas' y 'selectedFilter' de la API.
 */
function renderChart(data) {
    const chartDom = document.getElementById('chart');

    if (!chartDom) {
        console.error("ECharts ERROR: Contenedor de la gráfica con id 'chart' no encontrado.");
        return;
    }

    if (chartDom.offsetWidth === 0 || chartDom.offsetHeight === 0) {
        console.warn(`ECharts ADVERTENCIA: Contenedor de la gráfica tiene dimensiones cero (${chartDom.offsetWidth}x${chartDom.offsetHeight}). Reintentando renderizar en 200ms...`);
        chartDom.innerHTML = `<div class="flex justify-center items-center h-full text-gray-500">Ajustando la gráfica...</div>`;
        setTimeout(() => renderChart(data), 200);
        return;
    }

    if (myChart === null || myChart.isDisposed()) {
        console.log("ECharts DEBUG: Inicializando ECharts por primera vez o re-inicializando.");
        myChart = echarts.init(chartDom);
        window.addEventListener('resize', function() {
            if (myChart && !myChart.isDisposed()) {
                 myChart.resize();
                 console.log("ECharts DEBUG: Gráfica redimensionada.");
            }
        });
    } else {
        console.log("ECharts DEBUG: Actualizando ECharts con nuevos datos.");
    }

    let datosOrdenados = [...(data.vistas || [])];

    if (!Array.isArray(datosOrdenados) || datosOrdenados.length === 0) {
        chartDom.innerHTML = `<div class="flex justify-center items-center h-full text-gray-500">No hay datos válidos para mostrar para este período.</div>`;
        updateMetrics({ usuarios: 0, registrados: 0, activos: 0, conectados: 0 });
        if (myChart) { myChart.clear(); myChart.dispose(); myChart = null; }
        return;
    }

    // Lógica de ordenamiento para el eje X
    switch (data.selectedFilter) {
        case 'ultimos3dias':
            const dayNamesOrderUltimos3Días = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
            datosOrdenados.sort((a, b) => {
                const indexA = dayNamesOrderUltimos3Días.indexOf(a.grupo.toLowerCase());
                const indexB = dayNamesOrderUltimos3Días.indexOf(b.grupo.toLowerCase());
                if (indexA === -1 && indexB === -1) return 0;
                if (indexA === -1) return 1;
                if (indexB === -1) return -1;
                return indexA - indexB;
            });
            break;
        case 'semana':
            const monthNamesForSorting = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
            datosOrdenados.sort((a, b) => {
                const parseDate = (label) => {
                    const parts = label.split(' ');
                    if (parts.length < 3) return new Date(0);
                    const day = parseInt(parts[1]);
                    const month = monthNamesForSorting.indexOf(parts[2].toLowerCase());
                    const year = new Date().getFullYear();
                    return new Date(year, month, day);
                };
                return parseDate(a.grupo) - parseDate(b.grupo);
            });
            break;
        case 'mes':
            datosOrdenados.sort((a, b) => parseInt(a.grupo.replace('Semana ', '')) - parseInt(b.grupo.replace('Semana ', '')));
            break;
        case 'año':
            const mesesOrden = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            datosOrdenados.sort((a, b) => mesesOrden.indexOf(a.grupo.toLowerCase()) - mesesOrden.indexOf(b.grupo.toLowerCase()));
            break;
        case 'todo':
            datosOrdenados.sort((a, b) => parseInt(a.grupo) - parseInt(b.grupo));
            break;
        default:
            break;
    }

    const labels = datosOrdenados.map(item => item.grupo);
    const values = datosOrdenados.map(item => parseInt(item.total));

    let seriesChartName = 'Visitas';

    if (data.selectedFilter === 'año') {
        seriesChartName = 'Registros';
    }

    const options = {
        animation: true, animationDuration: 1000, animationEasing: 'cubicOut', animationDurationUpdate: 1000, animationEasingUpdate: 'cubicOut',
        tooltip: { trigger: 'axis', formatter: function (params) {
            const dataPoint = params[0]; const valorActual = dataPoint.value; const seriesName = seriesChartName;
            let changeInfo = ''; const index = dataPoint.dataIndex;
            if (index > 0 && values[index - 1] !== undefined) {
                const previousValue = values[index - 1];
                if (previousValue === 0) { changeInfo = `Cambio: N/A (desde 0)`; }
                else { const percentageChange = ((valorActual - previousValue) / previousValue * 100).toFixed(1); changeInfo = `Cambio: ${percentageChange}%`; }
            }
            return `<div class="p-2"><strong>${dataPoint.name}</strong><br>${seriesName}: <strong>${valorActual}</strong><br>${changeInfo}</div>`;
        }},
        grid: { left: '3%', right: '4%', top: '10%', bottom: '3%', containLabel: true },
        xAxis: { type: 'category', data: labels, axisLabel: { color: '#000', fontSize: 12, interval: 0, rotate: 30 } },
        yAxis: { type: 'value', minInterval: 1, axisLabel: { formatter: function (value) { return value % 1 === 0 ? value : ''; } } },
        series: [{
            name: seriesChartName, data: values, type: 'line', smooth: true,
            areaStyle: { color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{ offset: 0, color: '#22c55e' }, { offset: 1, color: '#ffffff' }]) },
            itemStyle: { color: '#22c55e' }, lineStyle: { width: 1.5 }, symbolSize: 8
        }],
        title: {
            text: '',
            left: 'center', top: '2%', textStyle: { color: '#333', fontSize: 18, fontWeight: 'bold' }
        }
    };

    myChart.setOption(options);
    myChart.resize();
    console.log("ECharts DEBUG: Gráfica renderizada/actualizada con datos:", data.vistas);
}
