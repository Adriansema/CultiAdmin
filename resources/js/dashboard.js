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

        yearDropdown.addEventListener('change', function() {
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
    function setActiveFilterButton(filterId) {
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.classList.remove('active-filter-button');
        });
        const activeButton = document.getElementById(filterId);
        if (activeButton) {
            activeButton.classList.add('active-filter-button');
        }
    }


    // --- setFilter global para los onclick de los botones HTML ---
    window.setFilter = function(filterType) {
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
        document.getElementById('registered-count').textContent = data.registrados || 0;
        document.getElementById('active-count').textContent = data.activos || 0;
        document.getElementById('connected-count').textContent = data.conectados || 0;

        const totalUsers = data.usuarios || 0;
        document.getElementById('users-percent').textContent = `${(totalUsers > 0 ? (data.usuarios / totalUsers * 100) : 0).toFixed(0)}% of users`;
        document.getElementById('registered-percent').textContent = `${(totalUsers > 0 ? (data.registrados / totalUsers * 100) : 0).toFixed(0)}% of users`;
        document.getElementById('active-percent').textContent = `${(totalUsers > 0 ? (data.activos / totalUsers * 100) : 0).toFixed(0)}% of users`;
        document.getElementById('connected-percent').textContent = `${(totalUsers > 0 ? (data.conectados / totalUsers * 100) : 0).toFixed(0)}% of users`;
    }

    // --- Initial load ---
    window.setFilter('ultimos3dias'); // Load 'ultimos3dias' filter on initial page load
});


// --- renderChart function (REPLACE YOUR ENTIRE renderChart FUNCTION WITH THIS) ---
function renderChart(data) {
    const chartDom = document.getElementById('chart');

    if (!chartDom) {
        console.error("Error: Chart container with id 'chart' not found in DOM.");
        return;
    }

    // CRITICAL LOGIC FOR HANDLING ZERO DIMENSIONS AND INFINITE LOOP
    // If the container is not visible or has zero dimensions, retry.
    if (chartDom.offsetWidth === 0 || chartDom.offsetHeight === 0) {
        console.warn(`Warning: Chart container has zero width or height (${chartDom.offsetWidth}x${chartDom.offsetHeight}). Retrying render in 200ms...`);
        chartDom.innerHTML = `<div class="text-center text-gray-500 p-4">Loading chart...</div>`;
        setTimeout(() => renderChart(data), 200);
        return;
    }

   

    // Logic for sorting and preparing data
    let datosOrdenados = [...data.vistas];

    switch (filtroActual) {
        case 'ultimos3dias':
            // Custom sorting for day names (assuming Spanish day names)
            const dayNamesOrderUltimos3Dias = ['sábado', 'domingo', 'lunes']; // Adjust based on your expected order
            datosOrdenados.sort((a, b) => {
                const indexA = dayNamesOrderUltimos3Dias.indexOf(a.grupo.toLowerCase());
                const indexB = dayNamesOrderUltimos3Dias.indexOf(b.grupo.toLowerCase());
                if (indexA === -1 || indexB === -1) return 0; // Maintain original order if not found
                return indexA - indexB;
            });
            break;

        case 'semana':
            // Use Date object for JavaScript, not Carbon
            const today = new Date(); // Changed from Carbon.now() to new Date()

            const formatearLabelSemana = (fecha) => {
                const dias = ['dom.', 'lun.', 'mar.', 'mié.', 'jue.', 'vie.', 'sáb.'];
                const meses = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];

                const diaSemana = dias[fecha.getDay()];
                const dia = fecha.getDate();
                const mes = meses[fecha.getMonth()];
                return `${diaSemana} ${dia} ${mes}`;
            };

            // Reconstruct the 7 days of the week and fill with zeros if no data.
            // This assumes the backend returns 'grupo' like 'Día dd Mes' (e.g., 'lun. 17 jun.')
            const tempMapSemana = new Map(datosOrdenados.map(item => [item.grupo, item.total]));
            const labelsSemanaReconstruidos = [];

            // Find the start of the current week (Sunday) based on today's date
            const currentWeekStart = new Date(today);
            currentWeekStart.setDate(today.getDate() - today.getDay()); // Set to Sunday of current week

            for (let i = 0; i < 7; i++) {
                const date = new Date(currentWeekStart);
                date.setDate(currentWeekStart.getDate() + i);
                const formattedLabel = formatearLabelSemana(date);
                labelsSemanaReconstruidos.push({
                    grupo: formattedLabel,
                    total: tempMapSemana.has(formattedLabel) ? tempMapSemana.get(formattedLabel) : 0
                });
            }
            datosOrdenados = labelsSemanaReconstruidos;

            // Sort by actual date for the week
            const monthNamesForSorting = ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'];
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

    // If the data array is truly empty or invalid, show a message.
    if (!Array.isArray(datosOrdenados) || datosOrdenados.length === 0) {
        chartDom.innerHTML = `<div class="text-center text-gray-500 p-4">No valid data to display for this period.</div>`;
        updateMetrics({ usuarios: 0, registrados: 0, activos: 0, conectados: 0 });
        if (myChart) {
            myChart.dispose();
            myChart = null;
        }
        return;
    }

    // Prepare data for ECharts
    const labels = datosOrdenados.map(item => item.grupo);
    const values = datosOrdenados.map(item => parseInt(item.total));

    // Initialize ECharts if it doesn't exist, or just update options
    if (myChart === null) {
        myChart = echarts.init(chartDom);
        window.addEventListener('resize', function() {
            if (myChart) myChart.resize();
        });
    } else {
        myChart.clear();
    }

    const options = {
        tooltip: {
            trigger: 'axis',
            formatter: function (params) {
                const dataPoint = params[0];
                const valorActual = dataPoint.value;
                const seriesName = dataPoint.seriesName || (filtroActual === 'año' ? 'Registros' : 'Visits');

                let changeInfo = '';
                const index = dataPoint.dataIndex;
                if (index > 0 && values[index - 1] !== undefined) {
                    const previousValue = values[index - 1];
                    if (previousValue === 0) {
                        changeInfo = `Change: N/A (from 0)`;
                    } else {
                        const percentageChange = ((valorActual - previousValue) / previousValue * 100).toFixed(1);
                        changeInfo = `Change: ${percentageChange}%`;
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
            name: (filtroActual === 'año' ? 'Registros' : 'Visits'),
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
            text: (filtroActual === 'año' ? 'New Registrations by Month' : 'Visits')
        }
    };

    myChart.setOption(options);
    myChart.resize();

    console.log("Chart rendered with data:", data.vistas);
}
