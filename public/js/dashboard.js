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


        // Verificar si existe una instancia anterior de ApexCharts y destruirla
        if (window.chart && typeof window.chart.destroy === "function") {
            window.chart.destroy();
        }

        const options = {
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                  show: false
                }
              },

              colors: ['#9'],


            series: [{
              name: 'Visitas',
              data: seriesData
            }],
            stroke: {
                curve: 'smooth',
                width: 3,

            },
            fill: {
                type: 'gradient',
                    gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0,
                    gradientToColors: ['#bbf7d0'], // Verde claro abajo
                    inverseColors: false,
                    opacityFrom: 0.8, // Inicio del degradado (más fuerte)
                    opacityTo: 0.3,     // Final del degradado (transparente)
                    stops: [0, 90]
                    }
              },
              markers: {
                size: 6,
                colors: ['#ffffff'],
                strokeColors: '#22c55e',
                strokeWidth: 3,
                hover: {
                  size: 8
                }
              },
              dataLabels: {
                enabled: true,
                style: {
                  colors: ['#ffffff'],
                  fontWeight: 'bold'
                },
                background: {
                  enabled: true,
                  foreColor: '#ffffff',
                  borderRadius: 4,
                  padding: 6,
                  backgroundColor: '#22c55e' // Fondo del número encima del punto
                }
              },

            xaxis: {
              type: 'category',
              labels: {
                style: {
                    color:'#000'
                }
              }
            },
            tooltip: {
              custom: function({ series, seriesIndex, dataPointIndex, w }) {
                const valorActual = series[seriesIndex][dataPointIndex];
                let porcentaje = 0;
                if (dataPointIndex > 0) {
                  const anterior = series[seriesIndex][dataPointIndex - 1];
                  porcentaje = anterior === 0 ? 0 : ((valorActual - anterior) / anterior * 100).toFixed(1);
                }
                return `<div class="p-2">
                  <strong>${w.globals.labels[dataPointIndex]}</strong><br>
                  Visitas: <strong>${valorActual}</strong><br>
                  Cambio: <strong>${porcentaje}%</strong>
                </div>`;
              }
            }
          };


          if (!Array.isArray(seriesData) || seriesData.length === 0 || seriesData.some(v => isNaN(v.y))) {
            document.querySelector("#chart").innerHTML = `<div class="text-center text-gray-500 p-4">No hay datos numéricos válidos para graficar.</div>`;
            return;
        }



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

