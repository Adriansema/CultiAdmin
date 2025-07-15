document.addEventListener('DOMContentLoaded', function () {
    const SearchLive = document.getElementById('SearchLive'); // Input de búsqueda
    const searchIcon = document.getElementById('searchIcon'); // Icono de lupa
    const clearIconContainer = document.getElementById('clearIconContainer'); // Contenedor de la 'X'
    const resetFiltersButton = document.getElementById('resetFiltersButton'); // Nuevo: Botón de restablecer filtros
    const sortButtons = document.querySelectorAll('.sort-icon-btn'); // Botones de ordenación en los encabezados
    const exportCsvButton = document.getElementById('exportCsvButton'); // Botón de exportar CSV

    // --- Función para aplicar los filtros de búsqueda y navegar ---
    function applySearchFilter() {
        const currentUrl = new URL(window.location.href);
        const searchQuery = SearchLive ? SearchLive.value : '';

        currentUrl.searchParams.delete('q'); // Limpiar parámetro existente de 'q'
        currentUrl.searchParams.delete('page'); // Reiniciar paginación al cambiar búsqueda

        if (searchQuery) {
            currentUrl.searchParams.set('q', searchQuery); // Añadir nuevo parámetro si tiene valor
        }

        window.location.href = currentUrl.toString(); // Redireccionar
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

    // Event listener para el input de búsqueda: actualiza iconos y aplica filtro al presionar Enter
    if (SearchLive) {
        SearchLive.addEventListener('input', toggleSearchIcons); // Actualiza iconos al escribir

        SearchLive.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Evita el comportamiento predeterminado del Enter
                applySearchFilter(); // Aplica el filtro de búsqueda
            }
        });
    }

    // Lógica para el clic en la "Equis" de limpiar búsqueda
    if (clearIconContainer && SearchLive) {
        clearIconContainer.addEventListener('click', () => {
            SearchLive.value = ''; // Borra el texto del input
            toggleSearchIcons(); // Actualiza la visibilidad (mostrar lupa)
            SearchLive.focus(); // Opcional: vuelve a poner el foco en el input
            applySearchFilter(); // Aplica el filtro (con el campo de búsqueda vacío)
        });
    }

    // --- Lógica para los botones de ordenación (flechas) ---
    if (sortButtons.length > 0) {
        sortButtons.forEach(button => {
            button.addEventListener('click', function () {
                const sortField = this.dataset.sortField; // e.g., 'tipo', 'created_at', 'estado'
                const sortDirection = this.dataset.sortDirection; // e.g., 'asc', 'desc'

                const url = new URL(window.location.href);

                // Elimina los parámetros de ordenación existentes para aplicar el nuevo
                url.searchParams.delete('sort_by');
                url.searchParams.delete('sort_direction');

                // Establece los nuevos parámetros de ordenación
                url.searchParams.set('sort_by', sortField);
                url.searchParams.set('sort_direction', sortDirection);

                url.searchParams.delete('page'); // Reinicia la paginación
                window.location.href = url.toString(); // Redirecciona
            });
        });
    }

    // LÓGICA: Botón de Restablecer Filtros con spinner y retraso
    if (resetFiltersButton) {
        resetFiltersButton.addEventListener('click', function () {
            const button = this;
            button.disabled = true;
            button.innerHTML = `
                <span class="flex items-center justify-center text-black w-full">
                    <span>Restableciendo</span>
                    <img src="./images/restablecer.svg" alt="Cargando..." class="w-5 h-5 ml-2 animate-spin">
                </span>
            `;

            const url = new URL(window.location.href);

            // Elimina todos los parámetros de filtro y ordenación conocidos de la URL
            url.searchParams.delete('q');
            url.searchParams.delete('estado'); // Aunque el select ya no está, es buena práctica limpiarlo
            url.searchParams.delete('sort_by');
            url.searchParams.delete('sort_direction');
            url.searchParams.delete('page');

            setTimeout(() => {
                window.location.href = url.origin + url.pathname; // Redirige a la URL base sin parámetros
            }, 3000); // 3 segundos de retraso
        });
    }

    // Lógica para el botón de Exportar CSV
    if (exportCsvButton) {
        exportCsvButton.addEventListener('click', function (e) {
            e.preventDefault(); // Previene el envío por defecto del formulario

            const exportForm = this.closest('form'); // Obtiene el formulario padre
            if (!exportForm) {
                return;
            }

            const urlParams = new URLSearchParams(window.location.search);
            const currentQuery = urlParams.get('q') || '';
            const currentEstado = urlParams.get('estado') || '';
            const currentSortBy = urlParams.get('sort_by') || '';
            const currentSortDirection = urlParams.get('sort_direction') || '';

            // Limpiar inputs ocultos existentes para evitar duplicados
            exportForm.querySelectorAll('input[type="hidden"][name="q"], input[type="hidden"][name="estado"], input[type="hidden"][name="sort_by"], input[type="hidden"][name="sort_direction"]').forEach(input => input.remove());

            // Añadir inputs ocultos al formulario con los parámetros actuales
            if (currentQuery) {
                const inputQ = document.createElement('input');
                inputQ.type = 'hidden';
                inputQ.name = 'q';
                inputQ.value = currentQuery;
                exportForm.appendChild(inputQ);
            }
            if (currentEstado) {
                const inputEstado = document.createElement('input');
                inputEstado.type = 'hidden';
                inputEstado.name = 'estado';
                inputEstado.value = currentEstado;
                exportForm.appendChild(inputEstado);
            }
            if (currentSortBy) {
                const inputSortBy = document.createElement('input');
                inputSortBy.type = 'hidden';
                inputSortBy.name = 'sort_by';
                inputSortBy.value = currentSortBy;
                exportForm.appendChild(inputSortBy);
            }
            if (currentSortDirection) {
                const inputSortDirection = document.createElement('input');
                inputSortDirection.type = 'hidden';
                inputSortDirection.name = 'sort_direction';
                inputSortDirection.value = currentSortDirection;
                exportForm.appendChild(inputSortDirection);
            }

            // Enviar el formulario
            exportForm.submit();
        });
    }

    // Al cargar la pagina, se inicializan los valores de los filtros desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    if (SearchLive) {
        SearchLive.value = urlParams.get('q') || '';
        toggleSearchIcons(); // Asegura que los iconos se muestren correctamente al cargar
    }

    // Lógica para resaltar el icono de ordenación activo al cargar la página
    const currentSortBy = urlParams.get('sort_by');
    const currentSortDirection = urlParams.get('sort_direction');

    if (currentSortBy && currentSortDirection) {
        document.querySelectorAll('.sort-icon-btn').forEach(button => {
            button.classList.remove('is-active');
        });

        const activeSortButton = document.querySelector(`.sort-icon-btn[data-sort-field="${currentSortBy}"][data-sort-direction="${currentSortDirection}"]`);

        if (activeSortButton) {
            activeSortButton.classList.add('is-active');
        }
    }
});
