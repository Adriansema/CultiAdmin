document.addEventListener('DOMContentLoaded', function () {
    // Referencias a los elementos del DOM
    const SearchUser = document.getElementById('SearchUser'); // Input de búsqueda de usuario
    const searchIcon = document.getElementById('searchIcon'); // Icono de lupa
    const clearIconContainer = document.getElementById('clearIconContainer'); // Contenedor de la 'X'
    const filtrarEstadoSelect = document.getElementById('filtrarEstado'); // Select de estado
    const filtrarRolSelect = document.getElementById('filtrarRol');     // Select de rol

    // --- Función central para aplicar todos los filtros y navegar ---
    function applyAllFilters() {
        const currentUrl = new URL(window.location.href);

        const searchQuery = SearchUser ? SearchUser.value : '';
        const estadoFilter = filtrarEstadoSelect ? filtrarEstadoSelect.value : '';
        const rolFilter = filtrarRolSelect ? filtrarRolSelect.value : '';

        // Limpiar parámetros existentes de 'q', 'estado' y 'rol'
        currentUrl.searchParams.delete('q');
        currentUrl.searchParams.delete('estado');
        currentUrl.searchParams.delete('rol');

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
                console.log('Enter presionado en el input de búsqueda. Aplicando filtros...');
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
            console.log('Campo de búsqueda limpiado. Aplicando filtros...');
            applyAllFilters();
        });
    }

    // --- Lógica para los selects de filtro (estado y rol) ---
    if (filtrarEstadoSelect) {
        filtrarEstadoSelect.addEventListener('change', function () {
            console.log('Filtro de estado cambiado. Aplicando filtros...');
            applyAllFilters();
        });
    }

    if (filtrarRolSelect) {
        filtrarRolSelect.addEventListener('change', function () {
            console.log('Filtro de rol cambiado. Aplicando filtros...');
            applyAllFilters();
        });
    }
});