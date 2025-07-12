document.addEventListener('DOMContentLoaded', function () {
    const buscame = document.getElementById('buscar-noticia-input'); // El input de búsqueda
    const searchIcon = document.getElementById('searchIcon'); // Icono de lupa
    const clearIconContainer = document.getElementById('clearIconContainer'); // Contenedor de la 'X'
    const exportCsvButton = document.getElementById('exportCsvButton');
    const resetFiltersButton = document.getElementById('resetFiltersButton'); // Botón de restablecer filtros
    const sortButtons = document.querySelectorAll('.sort-icon-btn'); // Botones de ordenación en los encabezados

    // --- Función para aplicar los filtros de búsqueda y navegar ---
    function applySearchFilter() {
        const currentUrl = new URL(window.location.href);
        const searchQuery = buscame ? buscame.value : '';

        currentUrl.searchParams.delete('q'); // Limpiar parámetro existente de 'q'
        currentUrl.searchParams.delete('page'); // Reiniciar paginación al cambiar búsqueda

        if (searchQuery) {
            currentUrl.searchParams.set('q', searchQuery); // Añadir nuevo parámetro si tiene valor
        }

        window.location.href = currentUrl.toString(); // Redireccionar
    }

    // --- Lógica para mostrar/ocultar la "Equis" y la lupa ---
    function toggleSearchIcons() {
        if (buscame && searchIcon && clearIconContainer) {
            if (buscame.value.length > 0) {
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
    if (buscame) {
        buscame.addEventListener('input', toggleSearchIcons); // Actualiza iconos al escribir

        buscame.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Evita el comportamiento predeterminado del Enter
                applySearchFilter(); // Aplica el filtro de búsqueda
            }
        });
    }

    // Lógica para el clic en la "Equis" de limpiar búsqueda
    if (clearIconContainer && buscame) {
        clearIconContainer.addEventListener('click', () => {
            buscame.value = ''; // Borra el texto del input
            toggleSearchIcons(); // Actualiza la visibilidad (mostrar lupa)
            buscame.focus(); // Opcional: vuelve a poner el foco en el input
            applySearchFilter(); // Aplica el filtro (con el campo de búsqueda vacío)
        });
    }

    // --- Lógica para los botones de ordenación (flechas) ---
    if (sortButtons.length > 0) {
        sortButtons.forEach(button => {
            button.addEventListener('click', function () {
                const sortField = this.dataset.sortField; // e.g., 'tipo', 'titulo', 'creador', 'estado', 'created_at'
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
                window.location.href = url.toString(); // Redirecciona
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

            // Elimina todos los parámetros de filtro y ordenación conocidos de la URL
            url.searchParams.delete('q');
            url.searchParams.delete('estado'); // Mantener si el filtro de estado se sigue usando en el servicio para filtrar
            url.searchParams.delete('sort_by');
            url.searchParams.delete('sort_direction');
            url.searchParams.delete('page');

            console.log('Restableciendo filtros. La página se recargará en 3 segundos.');

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
                console.error('Error: No se encontró el formulario para el botón de exportar CSV.');
                return;
            }

            const url = new URL(exportForm.action); // Obtiene la URL de acción del formulario

            const searchQuery = buscame ? buscame.value : '';
            const urlParams = new URLSearchParams(window.location.search);
            const sortBy = urlParams.get('sort_by') || '';
            const sortDirection = urlParams.get('sort_direction') || '';
            const estadoFilter = urlParams.get('estado') || ''; // Si aún se usa el filtro de estado

            // Limpia los parámetros de la URL de exportación antes de añadir los nuevos
            url.search = '';

            if (searchQuery) { url.searchParams.append('q', searchQuery); }
            if (estadoFilter) { url.searchParams.append('estado', estadoFilter); }
            if (sortBy) { url.searchParams.append('sort_by', sortBy); }
            if (sortDirection) { url.searchParams.append('sort_direction', sortDirection); }

            window.location.href = url.toString(); // Esto iniciará la descarga
        });
    }


    // Al cargar la pagina, se inicializan los valores de los filtros desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    if (buscame) {
        buscame.value = urlParams.get('q') || '';
        toggleSearchIcons(); // Asegura que los iconos se muestren correctamente al cargar
    }

    // Lógica para resaltar el icono de ordenación activo al cargar la página
    const currentSortBy = urlParams.get('sort_by');
    const currentSortDirection = urlParams.get('sort_direction');

    console.log('Parámetros de ordenación actuales al cargar:', 'sort_by:', currentSortBy, 'sort_direction:', currentSortDirection);

    if (currentSortBy && currentSortDirection) {
        document.querySelectorAll('.sort-icon-btn').forEach(button => {
            button.classList.remove('is-active');
        });

        const activeSortButton = document.querySelector(`.sort-icon-btn[data-sort-field="${currentSortBy}"][data-sort-direction="${currentSortDirection}"]`);

        if (activeSortButton) {
            console.log('Botón de ordenación activo encontrado para resaltar:', activeSortButton);
            activeSortButton.classList.add('is-active');
        } else {
            console.log('No se encontró el botón de ordenación activo para resaltar:', currentSortBy, currentSortDirection);
        }
    }
});
