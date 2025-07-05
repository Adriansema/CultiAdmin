document.addEventListener('DOMContentLoaded', function () {
    const SearchLive = document.getElementById('SearchLive'); // Input de búsqueda
    const searchIcon = document.getElementById('searchIcon'); // Icono de lupa
    const clearIconContainer = document.getElementById('clearIconContainer'); // Contenedor de la 'X'
    const filterButton = document.getElementById('filterButton'); // Botón "Filtrar"
    const estadoFilterSelect = document.getElementById('filtro-estado'); // Select de estado

    // --- Función central para aplicar los filtros y navegar ---
    function applyFilters() {
        const currentUrl = new URL(window.location.href);
        const searchQuery = SearchLive ? SearchLive.value : '';
        const estadoFilter = estadoFilterSelect ? estadoFilterSelect.value : '';

        // Limpiar parámetros existentes de 'q' y 'estado'
        currentUrl.searchParams.delete('q');
        currentUrl.searchParams.delete('estado');

        // Añadir nuevos parámetros si tienen valor
        if (searchQuery) {
            currentUrl.searchParams.set('q', searchQuery);
        }
        if (estadoFilter) {
            currentUrl.searchParams.set('estado', estadoFilter);
        }

        // Redireccionar a la nueva URL
        window.location.href = currentUrl.toString();
    }

    // --- Lógica para mostrar/ocultar la "Equis" y la lupa ---
    function toggleSearchIcons() {
        if (SearchLive && searchIcon && clearIconContainer) {
            if (SearchLive.value.length > 0) {
                searchIcon.classList.add('hidden');
                clearIconContainer.classList.remove('hidden');
            } else {
                searchIcon.classList.remove('hidden');
                clearIconContainer.classList.add('hidden');
            }
        }
    }

    // Inicializar la visibilidad de los iconos al cargar la página
    toggleSearchIcons();

    // Event listener para el input de búsqueda: actualiza iconos al escribir
    if (SearchLive) {
        SearchLive.addEventListener('input', toggleSearchIcons);

        // Event listener para la tecla Enter en el input de búsqueda
        SearchLive.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Previene el envío por defecto del formulario (si existe)
                applyFilters(); // Aplica los filtros (incluyendo el estado)
            }
        });
    }

    // Lógica para la "Equis" de limpiar
    if (clearIconContainer && SearchLive) {
        clearIconContainer.addEventListener('click', () => {
            SearchLive.value = ''; // Borra el texto del input
            toggleSearchIcons(); // Actualiza la visibilidad (mostrar lupa)
            SearchLive.focus(); // Opcional: vuelve a poner el foco en el input
            applyFilters(); // Aplica los filtros (con búsqueda vacía)
        });
    }

    // Lógica para el botón "Filtrar"
    if (filterButton) {
        filterButton.addEventListener('click', function () {
            applyFilters(); // Aplica los filtros
        });
    }

    // Lógica para el select de filtro por estado
    if (estadoFilterSelect) {
        estadoFilterSelect.addEventListener('change', function () {
            applyFilters(); // Aplica los filtros
        });
    }
});