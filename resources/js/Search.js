// resources/js/Search.js

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('site-search');
    const searchResultsContainer = document.getElementById('search-results');
    let searchTimeout; // Para el 'debounce'

    // Verifica si los elementos existen en la página para evitar errores
    if (!searchInput || !searchResultsContainer) {
        return;
    }

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout); // Limpia cualquier temporizador anterior

        const query = this.value.trim(); // Obtiene el texto del input

        // Si la consulta está vacía, oculta los resultados
        if (query.length === 0) {
            searchResultsContainer.innerHTML = ''; // Limpia el contenido
            searchResultsContainer.classList.add('hidden'); // Oculta el contenedor
            return;
        }

        // Configura un temporizador para retrasar la búsqueda (debounce)
        searchTimeout = setTimeout(() => {
            // Realiza la solicitud AJAX a tu backend
            // Asegúrate de que la URL '/api/buscar-usuarios' exista en tus rutas Laravel
            fetch(`/api/buscar-usuarios?query=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('La solicitud de búsqueda falló.');
                    }
                    return response.json();
                })
                .then(data => {
                    displayResults(data); // Muestra los resultados
                })
                .catch(error => {
                    console.error('Error al obtener resultados de búsqueda:', error);
                    searchResultsContainer.classList.add('hidden'); // Oculta si hay un error
                });
        }, 300); // Espera 300ms después de la última pulsación
    });

    function displayResults(results) {
        searchResultsContainer.innerHTML = ''; // Limpia resultados anteriores

        if (results.length > 0) {
            const ul = document.createElement('ul');
            ul.className = 'py-1'; // Estilo básico para la lista

            results.forEach(user => {
                // Asegúrate de que 'user.name' coincida con la propiedad real de tu modelo de usuario
                const li = document.createElement('li');
                li.className = 'px-4 py-2 text-sm cursor-pointer hover:bg-gray-100'; // Estilo para cada elemento de la lista
                li.textContent = user.name; // Muestra el nombre del usuario (ajusta a tu campo)

                // Opcional: Permite seleccionar un resultado al hacer clic
                li.addEventListener('click', () => {
                    searchInput.value = user.name; // Rellena el input con el nombre seleccionado
                    searchResultsContainer.classList.add('hidden'); // Oculta los resultados
                });
                ul.appendChild(li);
            });
            searchResultsContainer.appendChild(ul);
            searchResultsContainer.classList.remove('hidden'); // Muestra el contenedor de resultados
        } else {
            searchResultsContainer.classList.add('hidden'); // Oculta si no hay resultados
        }
    }

    // Ocultar resultados cuando se hace clic fuera del input o del contenedor de resultados
    document.addEventListener('click', function(event) {
        if (!searchResultsContainer.contains(event.target) && event.target !== searchInput) {
            searchResultsContainer.classList.add('hidden');
        }
    });
});
