document.addEventListener('DOMContentLoaded', function () {
    // Referencias a los elementos del DOM
    const SearchUser = document.getElementById('SearchUser'); // Input de búsqueda de usuario
    const searchIcon = document.getElementById('searchIcon'); // Icono de lupa
    const clearIconContainer = document.getElementById('clearIconContainer'); // Contenedor de la 'X'
    // const filtrarEstadoSelect = document.getElementById('filtrarEstado'); // ELIMINADO
    // const filtrarRolSelect = document.getElementById('filtrarRol');     // ELIMINADO
    const resetFiltersButton = document.getElementById('resetFiltersButton'); // Nuevo: Botón de restablecer filtros

    // Seleccionar todos los botones de ordenación
    const sortButtons = document.querySelectorAll('.sort-icon-btn');

    // --- Función central para aplicar todos los filtros y navegar ---
    function applyAllFilters() {
        const currentUrl = new URL(window.location.href);
        const searchQuery = SearchUser ? SearchUser.value : '';

        // Limpiar parámetros existentes de 'q'
        currentUrl.searchParams.delete('q');
        // Los parámetros 'estado' y 'rol' ya no se manejan por selects para filtrar
        // Si se usaban para filtrar la tabla, ahora se manejarán via sort_by/sort_direction si se ordenan por ellos.
        // Si aún necesitas filtrar por estado/rol SIN ordenar, la lógica debería ser diferente (e.g., checkboxes).
        // Por ahora, asumimos que el filtro de estado/rol se ha movido a la ordenación.

        // Añadir nuevo parámetro 'q' si tiene valor
        if (searchQuery) {
            currentUrl.searchParams.set('q', searchQuery);
        }

        // Eliminar el parámetro 'page' para reiniciar la paginación
        currentUrl.searchParams.delete('page');

        // Redireccionar a la nueva URL con los filtros aplicados
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
                const sortField = this.dataset.sortField; // e.g., 'name', 'email', 'estado', 'roles.name'
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

    // LÓGICA: Botón de Restablecer Filtros con spinner y retraso
    if (resetFiltersButton) {
        resetFiltersButton.addEventListener('click', function() {
            const button = this; // Referencia al botón clicado
            button.disabled = true; // Deshabilita el botón para evitar múltiples clics
            button.innerHTML = `
                <span class="flex items-center justify-center text-black w-full">
                    <span>Restableciendo</span>
                    <img src="./images/restablecer.svg" alt="Cargando..." class="w-5 h-5 ml-2 animate-spin">
                </span>
            `; // Cambia el contenido a "Restableciendo" con el spinner

            const url = new URL(window.location.href);

            // Elimina todos los parámetros de filtro conocidos de la URL
            url.searchParams.delete('q');
            url.searchParams.delete('estado'); // Aunque el select ya no está, es buena práctica limpiarlo
            url.searchParams.delete('rol');    // Aunque el select ya no está, es buena práctica limpiarlo
            url.searchParams.delete('sort_by');
            url.searchParams.delete('sort_direction');
            url.searchParams.delete('page'); // Siempre restablecer la paginación a la primera página

            console.log('Restableciendo filtros. La página se recargará en 3 segundos.');

            // Retrasa la recarga de la página por 3 segundos (3000 milisegundos)
            setTimeout(() => {
                window.location.href = url.origin + url.pathname; // Redirige a la URL base sin parámetros
            }, 3000); // 3 segundos de retraso
        });
    }

    // Al cargar la pagina, se inicializan los valores de los filtros desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    if (SearchUser) {
        SearchUser.value = urlParams.get('q') || '';
        toggleSearchIcons(); // Asegura que los iconos se muestren correctamente al cargar
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
