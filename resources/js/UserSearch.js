document.addEventListener('DOMContentLoaded', function () {
    // Referencias a los elementos del DOM
    const SearchUser = document.getElementById('SearchUser'); // Input de búsqueda de usuario
    const searchIcon = document.getElementById('searchIcon'); // Icono de lupa
    const clearIconContainer = document.getElementById('clearIconContainer'); // Contenedor de la 'X'
    const resetFiltersButton = document.getElementById('resetFiltersButton'); // Botón de restablecer filtros
    const sortButtons = document.querySelectorAll('.sort-icon-btn'); // Seleccionar todos los botones de ordenación
    const exportCsvButton = document.getElementById('exportCsvButton'); // Botón de exportar CSV

    // --- Función central para aplicar todos los filtros y navegar ---
    function applyAllFilters() {
        const currentUrl = new URL(window.location.href);

        const searchQuery = SearchUser ? SearchUser.value : '';
        // Los filtros de estado y rol ya no se manejan por selects en el HTML,
        // pero pueden seguir siendo parte de la URL si se aplican de otra forma
        // o si queremos que se restablezcan con el botón.
        const urlParams = new URLSearchParams(window.location.search);
        const estadoFilter = urlParams.get('estado') || '';
        const rolFilter = urlParams.get('rol') || '';
        const sortBy = urlParams.get('sort_by') || '';
        const sortDirection = urlParams.get('sort_direction') || '';


        // Limpiar parámetros existentes de 'q', 'estado', 'rol', 'sort_by', 'sort_direction'
        currentUrl.searchParams.delete('q');
        currentUrl.searchParams.delete('estado');
        currentUrl.searchParams.delete('rol');
        currentUrl.searchParams.delete('sort_by');
        currentUrl.searchParams.delete('sort_direction');
        currentUrl.searchParams.delete('page');

        // Añadir nuevos parámetros si tienen valor
        if (searchQuery) {
            currentUrl.searchParams.set('q', searchQuery);
        }
        if (estadoFilter) {
            currentUrl.searchParams.set('estado', estadoFilter);
        }
        if (rolFilter) {
            currentUrl.searchParams.set('rol', rolFilter);
        }
        if (sortBy) {
            currentUrl.searchParams.set('sort_by', sortBy);
        }
        if (sortDirection) {
            currentUrl.searchParams.set('sort_direction', sortDirection);
        }

        // Redireccionar a la nueva URL con todos los filtros aplicados
        window.location.href = currentUrl.toString();
    }

    // --- Lógica para mostrar/ocultar la "Equis" y la lupa ---
    function toggleSearchIcons() {
        if (SearchUser && searchIcon && clearIconContainer) {
            if (SearchUser.value.length > 0) {
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
    if (SearchUser) {
        SearchUser.addEventListener('input', toggleSearchIcons);

        // Event listener para la tecla Enter en el input de búsqueda
        SearchUser.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Evita el comportamiento predeterminado del Enter
                applyAllFilters();
            }
        });
    }

    // Lógica para el clic en la "Equis" de limpiar
    if (clearIconContainer && SearchUser) {
        clearIconContainer.addEventListener('click', () => {
            SearchUser.value = ''; // Borra el texto del input
            toggleSearchIcons(); // Actualiza la visibilidad (mostrar lupa)
            SearchUser.focus(); // Opcional: vuelve a poner el foco en el input

            // Aplica los filtros (con el campo de búsqueda vacío)
            applyAllFilters();
        });
    }

    // --- Lógica para los botones de ordenación (flechas) ---
    if (sortButtons.length > 0) {
        sortButtons.forEach(button => {
            button.addEventListener('click', function () {
                const sortField = this.dataset.sortField;
                const sortDirection = this.dataset.sortDirection;

                const url = new URL(window.location.href);

                url.searchParams.delete('sort_by');
                url.searchParams.delete('sort_direction');

                url.searchParams.set('sort_by', sortField);
                url.searchParams.set('sort_direction', sortDirection);

                url.searchParams.delete('page');
                window.location.href = url.toString();
            });
        });
    }

    // LÓGICA: Botón de Restablecer Filtros con spinner y retraso
    if (resetFiltersButton) {
        resetFiltersButton.addEventListener('click', function() {
            const button = this;
            button.disabled = true;
            button.innerHTML = `
                <span class="flex items-center justify-center text-black w-full">
                    <span>Restableciendo</span>
                    <img src="./images/restablecer.svg" alt="Cargando..." class="w-5 h-5 ml-2 animate-spin">
                </span>
            `;

            const url = new URL(window.location.href);

            // Elimina todos los parámetros de filtro conocidos de la URL
            url.searchParams.delete('q');
            url.searchParams.delete('estado');
            url.searchParams.delete('rol');
            url.searchParams.delete('sort_by');
            url.searchParams.delete('sort_direction');
            url.searchParams.delete('page');

            setTimeout(() => {
                window.location.href = url.origin + url.pathname;
            }, 3000);
        });
    }

    // Lógica para el botón de Exportar CSV (NUEVA LÓGICA)
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
            const currentRol = urlParams.get('rol') || '';
            const currentSortBy = urlParams.get('sort_by') || '';
            const currentSortDirection = urlParams.get('sort_direction') || '';

            // Limpiar inputs ocultos existentes para evitar duplicados
            exportForm.querySelectorAll('input[type="hidden"][name="q"], input[type="hidden"][name="estado"], input[type="hidden"][name="rol"], input[type="hidden"][name="sort_by"], input[type="hidden"][name="sort_direction"]').forEach(input => input.remove());

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
            if (currentRol) {
                const inputRol = document.createElement('input');
                inputRol.type = 'hidden';
                inputRol.name = 'rol';
                inputRol.value = currentRol;
                exportForm.appendChild(inputRol);
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
    if (SearchUser) {
        SearchUser.value = urlParams.get('q') || '';
        toggleSearchIcons(); // Asegura que los iconos se muestren correctamente al cargar
    }

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
