document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('buscar-boletin-input');
    const searchIcon = document.getElementById('searchIcon');
    const clearIconContainer = document.getElementById('clearIconContainer');
    const resetFiltersButton = document.getElementById('resetFiltersButton');

    // Seleccionar todos los botones de ordenación
    const sortButtons = document.querySelectorAll('.sort-icon-btn');

    function debounce(func, delay) {
        let timeout;
        return function (...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    // Funcion principal para construir la URL y redirigir
    function applyFiltersAndRedirect() {
        const query = searchInput ? searchInput.value : '';
        // const estado = estadoFilterSelect ? estadoFilterSelect.value : ''; // ELIMINADO

        const url = new URL(window.location.href);

        if (query) {
            url.searchParams.set('q', query);
        } else {
            url.searchParams.delete('q');
        }

        url.searchParams.delete('page');

        console.log('Redirigiendo a:', url.toString());
        window.location.href = url.toString();
    }

    // Logica para el INPUT de busqueda
    if (searchInput) {
        if (searchInput.value.trim() !== '') {
            searchIcon.classList.add('hidden');
            clearIconContainer.classList.remove('hidden');
        } else {
            searchIcon.classList.remove('hidden');
            clearIconContainer.classList.add('hidden');
        }

        searchInput.addEventListener('input', debounce(function () {
            if (searchInput.value.trim() !== '') {
                searchIcon.classList.add('hidden');
                clearIconContainer.classList.remove('hidden');
            } else {
                searchIcon.classList.remove('hidden');
                clearIconContainer.classList.add('hidden');
            }
            applyFiltersAndRedirect();
        }, 900));

        if (clearIconContainer) {
            clearIconContainer.addEventListener('click', () => {
                if (searchInput) searchInput.value = '';
                applyFiltersAndRedirect();
            });
        }
    }

    // Logica para los botones de ordenación (flechas)
    if (sortButtons.length > 0) {
        sortButtons.forEach(button => {
            button.addEventListener('click', function () {
                const sortField = this.dataset.sortField; // e.g., 'nombre', 'precio_mas_alto'
                const sortDirection = this.dataset.sortDirection; // e.g., 'asc', 'desc'

                const url = new URL(window.location.href);

                // Elimina los parámetros de ordenación existentes para aplicar el nuevo
                url.searchParams.delete('sort_by');
                url.searchParams.delete('sort_direction');

                // Establece los nuevos parámetros de ordenación
                url.searchParams.set('sort_by', sortField);
                url.searchParams.set('sort_direction', sortDirection);

                url.searchParams.delete('page'); // Reinicia la paginación

                console.log('Botón de ordenación clicado. Nuevo sort_by:', sortField, 'sort_direction:', sortDirection);
                console.log('URL de redirección:', url.toString());
                window.location.href = url.toString(); // Redirige
            });
        });
    }

    // Logica para el boton de exportar CSV
    const exportCsvButton = document.getElementById('exportCsvButton');
    if (exportCsvButton) {
        exportCsvButton.addEventListener('click', function (e) {
            e.preventDefault();

            const query = searchInput ? searchInput.value : '';
            // const estado = estadoFilterSelect ? estadoFilterSelect.value : ''; // ELIMINADO
            const urlParams = new URLSearchParams(window.location.search);
            const sortBy = urlParams.get('sort_by') || '';
            const sortDirection = urlParams.get('sort_direction') || '';

            const url = new URL(exportCsvBoletinesRoute, window.location.origin);

            if (query) { url.searchParams.append('q', query); }
            // if (estado) { url.searchParams.append('estado', estado); } // ELIMINADO
            if (sortBy) { url.searchParams.append('sort_by', sortBy); }
            if (sortDirection) { url.searchParams.append('sort_direction', sortDirection); }

            window.location.href = url.toString();
        });
    }

    // Botón de Restablecer Filtros con spinner
    if (resetFiltersButton) {
        // No necesitamos guardar el contenido original aquí porque la página se recargará
        // y el botón volverá a su estado inicial automáticamente.

        resetFiltersButton.addEventListener('click', function () {
            const button = this; // Referencia al botón clicado
            button.disabled = true; // Deshabilita el botón para evitar múltiples clics
            button.innerHTML = `
                <span class="flex items-center text-black justify-center w-full">
                    <span>Restableciendo</span>
                   <img src="./images/restablecer.svg" alt="Cargando..." class="w-5 h-5 animate-spin">
                </span>
            `; // Cambia el contenido a "Restableciendo" con el spinner

            const url = new URL(window.location.href);

            url.searchParams.delete('q');
            url.searchParams.delete('estado');
            url.searchParams.delete('sort_by');
            url.searchParams.delete('sort_direction');
            url.searchParams.delete('page');

             // Retrasa la recarga de la página por 3 segundos (3000 milisegundos)
            setTimeout(() => {
                window.location.href = url.origin + url.pathname; // Redirige a la URL base sin parámetros
            }, 3000); // 3 segundos de retraso
        });
    }

    // Al cargar la pagina, se inicializan los valores de los filtros desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    if (searchInput) {
        searchInput.value = urlParams.get('q') || '';
        if (searchInput.value.trim() !== '') {
            searchIcon.classList.add('hidden');
            clearIconContainer.classList.remove('hidden');
        } else {
            searchIcon.classList.remove('hidden');
            clearIconContainer.classList.add('hidden');
        }
    }

    // Lógica para resaltar el icono de ordenación activo al cargar la página
    const currentSortBy = urlParams.get('sort_by');
    const currentSortDirection = urlParams.get('sort_direction');

    console.log('Parámetros de ordenación actuales al cargar:', 'sort_by:', currentSortBy, 'sort_direction:', currentSortDirection);

    if (currentSortBy && currentSortDirection) {
        // Desactivar todos los botones de ordenación activos previamente
        document.querySelectorAll('.sort-icon-btn').forEach(button => {
            button.classList.remove('is-active');
        });

        // Busca el botón que tenga los data-attributes que coincidan
        const activeSortButton = document.querySelector(`.sort-icon-btn[data-sort-field="${currentSortBy}"][data-sort-direction="${currentSortDirection}"]`);

        if (activeSortButton) {
            console.log('Botón de ordenación activo encontrado para resaltar:', activeSortButton);
            activeSortButton.classList.add('is-active');
        } else {
            console.log('No se encontró el botón de ordenación activo para resaltar:', currentSortBy, currentSortDirection);
        }
    }
});