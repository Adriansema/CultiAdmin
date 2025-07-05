document.addEventListener('DOMContentLoaded', function () {
    // Referencias a los elementos del DOM
    const buscadorTabla = document.getElementById('buscadorTabla'); // Tu formulario de búsqueda principal
    const buscame = document.getElementById('buscar-noticia-input'); // El input de búsqueda, con su ID correcto
    const searchIcon = document.getElementById('searchIcon'); // Icono de lupa
    const clearIconContainer = document.getElementById('clearIconContainer'); // Contenedor de la 'X'
    const filtrosBotones = document.getElementById('filtrosBotones'); // El botón "Filtrar" que mencionaste
    const filtroEstadoSelect = document.getElementById('filtro-estado'); // El select para el estado

    // --- Función central para aplicar los filtros y navegar ---
    // Esta función leerá los valores actuales de búsqueda y estado,
    // construirá la URL y recargará la página.
    function applyFilters() {
        const currentUrl = new URL(window.location.href);
        const searchQuery = buscame ? buscame.value : '';
        const estadoFilter = filtroEstadoSelect ? filtroEstadoSelect.value : '';

        // Limpiar parámetros existentes para evitar duplicados
        currentUrl.searchParams.delete('q');
        currentUrl.searchParams.delete('estado');

        // Añadir nuevos parámetros si tienen valor
        if (searchQuery) {
            currentUrl.searchParams.set('q', searchQuery);
        }
        if (estadoFilter) {
            currentUrl.searchParams.set('estado', estadoFilter);
        }

        // Redireccionar a la nueva URL con los filtros aplicados
        window.location.href = currentUrl.toString();
    }

    // --- Lógica para el botón "Filtrar" ---
    // Este botón enviará todos los filtros actuales.
    if (filtrosBotones) {
        filtrosBotones.addEventListener('click', function () {
            console.log('Botón Filtrar clicado. Aplicando filtros...');
            applyFilters();
        });
    }

    // --- Lógica para la tecla Enter en el input de búsqueda ---
    if (buscame) {
        buscame.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Evita que el formulario se envíe de forma predeterminada
                console.log('Enter presionado en el input. Aplicando filtros...');
                applyFilters();
            }
        });
    }

    // --- Funciones auxiliares para mostrar/ocultar iconos (lupa y equis) ---
    const showClearIcon = () => {
        if (searchIcon) searchIcon.classList.add('hidden');
        if (clearIconContainer) clearIconContainer.classList.remove('hidden');
    };

    const showSearchIcon = () => {
        if (searchIcon) searchIcon.classList.remove('hidden');
        if (clearIconContainer) clearIconContainer.classList.add('hidden');
    };

    // --- Lógica para mostrar/ocultar la "Equis" y la lupa al escribir ---
    if (buscame) {
        // Inicializar la visibilidad de los iconos al cargar la página
        if (buscame.value.length > 0) {
            showClearIcon();
        } else {
            showSearchIcon();
        }

        // Evento 'input': Se dispara cuando el valor del input cambia
        buscame.addEventListener('input', () => {
            if (buscame.value.length > 0) {
                showClearIcon(); // Si hay texto, muestra la 'X'
            } else {
                showSearchIcon(); // Si no hay texto, muestra la lupa
            }
        });

        // Lógica para el clic en la "Equis" de limpiar
        if (clearIconContainer) {
            clearIconContainer.addEventListener('click', () => {
                buscame.value = ''; // Borra el texto
                showSearchIcon(); // Vuelve a mostrar la lupa
                buscame.focus(); // Opcional: vuelve a poner el foco en el input

                // Aplica los filtros (con el campo de búsqueda vacío)
                console.log('Campo de búsqueda limpiado. Aplicando filtros...');
                applyFilters();
            });
        }
    }

    // --- Lógica para el filtro por estado (cuando cambia el select) ---
    if (filtroEstadoSelect) {
        filtroEstadoSelect.addEventListener('change', function() {
            console.log('Filtro de estado cambiado. Aplicando filtros...');
            applyFilters();
        });
    }
});