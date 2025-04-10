
document.addEventListener("DOMContentLoaded", function () {
    let filtroActual = 'hoy'; // Variable global
    let porcentajes = [];     // Variable global para los porcentajes

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
                type: 'line', height: 300, toolbar: { show: false }
            },
            series: [{ name: 'Visitas', data: visitas }],
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
    }

    function actualizarPorcentajes(actual, anterior, elementoID) {
        const cambio = actual - anterior;
        let porcentaje = 0;
        if (anterior > 0) {
            porcentaje = ((cambio / anterior) * 100).toFixed(1);
        }

        const textoCambio = cambio === 0
            ? "Sin cambios"
            : `${cambio > 0 ? "+" : ""}${porcentaje}% ${cambio > 0 ? "más" : "menos"} que ${periodoAnteriorTexto(filtroActual)}`;

        const elemento = document.getElementById(elementoID);
        if (elemento) {
            elemento.textContent = textoCambio;
            elemento.className = `text-sm ${cambio >= 0 ? 'text-green-600' : 'text-red-600'}`;
        }
    }

    function actualizarRelacionProporcional(parcial, total, elementoID, tipo = 'usuarios') {
        const elemento = document.getElementById(elementoID);
        if (!elemento || total === 0) return;

        const porcentaje = ((parcial / total) * 100).toFixed(1);
        let texto = "";

        switch (tipo) {
            case 'registrados':
                texto = `${porcentaje}% de los usuarios están registrados`;
                break;
            case 'activos':
                texto = `${porcentaje}% de los usuarios están activos`;
                break;
            case 'conectados':
                texto = `${porcentaje}% de los usuarios están conectados`;
                break;
            default:
                texto = `${porcentaje}% del total`;
        }

        elemento.textContent = texto;
        elemento.className = 'text-sm text-blue-600';
    }
    <script src="{{ mix('js/app.js') }}" defer></script>


    function periodoAnteriorTexto(filtro) {
        switch (filtro) {
            case 'hoy': return 'ayer';
            case 'semana': return 'la semana pasada';
            case 'mes': return 'el mes pasado';
            case 'año': return 'el año anterior';
            default: return 'el periodo anterior';
        }
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
                    localStorage.setItem("offline_stats", JSON.stringify(data));
                    renderChart(data);

                    document.getElementById("users-count").textContent = data.usuarios ?? 0;
                    document.getElementById("registered-count").textContent = data.registrados ?? 0;
                    document.getElementById("active-count").textContent = data.activos ?? 0;
                    document.getElementById("connected-count").textContent = data.conectados ?? 0;

                    actualizarPorcentajes(data.usuarios ?? 0, data.usuarios_anteriores ?? 0, "users-change");
                    actualizarPorcentajes(data.registrados ?? 0, data.registrados_anteriores ?? 0, "registered-change");
                    actualizarPorcentajes(data.activos ?? 0, data.activos_anteriores ?? 0, "active-change");
                    actualizarPorcentajes(data.conectados ?? 0, data.conectados_anteriores ?? 0, "connected-change");

                    actualizarRelacionProporcional(data.registrados ?? 0, data.usuarios ?? 1, "registered-percent", "registrados");
                    actualizarRelacionProporcional(data.activos ?? 0, data.usuarios ?? 1, "active-percent", "activos");
                    actualizarRelacionProporcional(data.conectados ?? 0, data.usuarios ?? 1, "connected-percent", "conectados");
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
    loadData();
});
