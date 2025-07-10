document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('buscar-boletin-input');
    const estadoFilterSelect = document.getElementById('filtro-estado');
    const precioFilterSelect = document.getElementById('filtro-precio');

    const searchIcon = document.getElementById('searchIcon');
    const clearIconContainer = document.getElementById('clearIconContainer');

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
        const estado = estadoFilterSelect ? estadoFilterSelect.value : '';
        const precio = precioFilterSelect ? precioFilterSelect.value : '';

        // Obtenemos la URL base de la pagina actual y manejamos los parametros
        // para asegurarnos de no perder 'page' si ya estamos en una pagina diferente a la primera
        const url = new URL(window.location.href);

        // Si hay una consulta, la establecemos; de lo contrario, la eliminamos.
        if (query) {
            url.searchParams.set('q', query);
        } else {
            url.searchParams.delete('q');
        }

        // Si hay un estado, lo establecemos; de lo contrario, lo eliminamos.
        if (estado) {
            url.searchParams.set('estado', estado);
        } else {
            url.searchParams.delete('estado');
        }

        // Si hay un precio, lo establecemos; de lo contrario, lo eliminamos.
        if (precio) {
            url.searchParams.set('precio', precio);
        } else {
            url.searchParams.delete('precio');
        }

        // Importante: Si se aplica un nuevo filtro de busqueda/estado/precio,
        // usualmente quieres reiniciar la paginacion a la primera pagina.
        // Solo eliminamos 'page' si no es la primera pagina o si quieres forzar un reset.
        // Si ya estas en la pagina 1, eliminar 'page' no tendra efecto.
        url.searchParams.delete('page');

        // Redirige el navegador a la nueva URL
        window.location.href = url.toString();
    }

    // Logica para el INPUT de busqueda
    if (searchInput) {
        // Inicializar el estado de los iconos al cargar la pagina
        if (searchInput.value.trim() !== '') {
            searchIcon.classList.add('hidden');
            clearIconContainer.classList.remove('hidden');
        } else {
            searchIcon.classList.remove('hidden');
            clearIconContainer.classList.add('hidden');
        }

        // Event listener para el input de busqueda con debounce
        searchInput.addEventListener('input', debounce(function () {
            if (searchInput.value.trim() !== '') {
                searchIcon.classList.add('hidden');
                clearIconContainer.classList.remove('hidden');
            } else {
                searchIcon.classList.remove('hidden');
                clearIconContainer.classList.add('hidden');
            }
            applyFiltersAndRedirect(); // Llama a la funcion de redireccion
        }, 300)); // Espera 300ms despues de la ultima pulsacion

        // Event listener para el icono de limpiar busqueda
        if (clearIconContainer) {
            clearIconContainer.addEventListener('click', () => {
                if (searchInput) searchInput.value = '';
                // No es necesario limpiar los selects aqui, ya que applyFiltersAndRedirect
                // se encargara de eliminarlos de la URL si estan vacios.
                applyFiltersAndRedirect(); // Llama a la funcion de redireccion para limpiar
            });
        }
    }

    // Logica para el SELECT de filtro por estado
    if (estadoFilterSelect) {
        estadoFilterSelect.addEventListener('change', function () {
            applyFiltersAndRedirect(); // Llama a la funcion de redireccion
        });
    }

    // Logica para el SELECT de filtro por precio
    if (precioFilterSelect) {
        precioFilterSelect.addEventListener('change', function () {
            applyFiltersAndRedirect(); // Llama a la funcion de redireccion
        });
    }

    // Logica para el boton de exportar CSV
    const exportCsvButton = document.getElementById('exportCsvButton');
    if (exportCsvButton) {
        exportCsvButton.addEventListener('click', function (e) {
            e.preventDefault(); // Previene el comportamiento por defecto del enlace/boton

            const query = searchInput ? searchInput.value : '';
            const estado = estadoFilterSelect ? estadoFilterSelect.value : '';
            const precio = precioFilterSelect ? precioFilterSelect.value : '';

            // Construye la URL para la exportacion con los filtros actuales
             const url = new URL(exportCsvBoletinesRoute, window.location.origin);

            if (query) { url.searchParams.append('q', query); }
            if (estado) { url.searchParams.append('estado', estado); }
            if (precio) { url.searchParams.append('precio', precio); }

            window.location.href = url.toString(); // Esto iniciara la descarga
        });
    }

    // Al cargar la pagina, se inicializan los valores de los filtros desde la URL
    // para que coincidan con lo que Laravel ya ha aplicado.
    const urlParams = new URLSearchParams(window.location.search);
    if (searchInput) {
        searchInput.value = urlParams.get('q') || '';
        // Ajustar la visibilidad de los iconos al cargar la pagina
        if (searchInput.value.trim() !== '') {
            searchIcon.classList.add('hidden');
            clearIconContainer.classList.remove('hidden');
        } else {
            searchIcon.classList.remove('hidden');
            clearIconContainer.classList.add('hidden');
        }
    }
    if (estadoFilterSelect) {
        estadoFilterSelect.value = urlParams.get('estado') || '';
    }
    if (precioFilterSelect) {
        precioFilterSelect.value = urlParams.get('precio') || '';
    }
});