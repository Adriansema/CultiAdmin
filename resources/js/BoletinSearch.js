// public/js/boletinsearch.js

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('buscar-boletin-input');
    const estadoFilterSelect = document.getElementById('filtro-estado');
    const tableBody = document.getElementById('boletines-table-body');
    const noBoletinesMessageRow = document.getElementById('no-boletines-message-row');
    const loadingSpinnerRow = document.getElementById('loading-spinner-row');

    const searchIcon = document.getElementById('searchIcon');
    const clearIconContainer = document.getElementById('clearIconContainer');

    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }

    function toggleNoBoletinesMessage(boletinesCount) {
        if (noBoletinesMessageRow) {
            if (boletinesCount === 0) {
                noBoletinesMessageRow.style.display = 'table-row';
            } else {
                noBoletinesMessageRow.style.display = 'none';
            }
        }
    }

    // Asegúrate de que esta función 'mostrarModal' esté definida en algún lugar accesible globalmente
    // (ej. en un script separado que se carga antes, o en el propio global scope si no hay conflictos)
    // Para que los botones 'Ver', 'Editar', 'Eliminar' funcionen.
    if (typeof window.mostrarModal !== 'function') {
        window.mostrarModal = function(tipo, id) {
            console.warn(`Función mostrarModal no definida. Tipo: ${tipo}, ID: ${id}`);
            // Aquí deberías tener tu lógica para abrir los modales correspondientes
            // Por ejemplo:
            if (tipo === 'ver') {
                // Llama al fetch para obtener los detalles del boletín y mostrar el modal de ver
                fetch(`/boletines/${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(boletin => {
                    document.getElementById('modal-creador').textContent = boletin.user ? boletin.user.name : 'N/A';
                    document.getElementById('modal-estado').textContent = boletin.estado.charAt(0).toUpperCase() + boletin.estado.slice(1);
                    document.getElementById('modal-nombre').textContent = boletin.nombre;
                    document.getElementById('modal-descripcion').textContent = boletin.descripcion;
                    document.getElementById('modal-archivo').innerHTML = boletin.archivo ? `<a href="/storage/${boletin.archivo}" target="_blank">Ver PDF</a>` : 'No adjunto';
                    document.getElementById('modal-precio-alto').textContent = '$' + (boletin.precio_mas_alto ? boletin.precio_mas_alto.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00');
                    document.getElementById('modal-lugar-precio-alto').textContent = boletin.lugar_precio_mas_alto || 'N/A';
                    document.getElementById('modal-precio-bajo').textContent = '$' + (boletin.precio_mas_bajo ? boletin.precio_mas_bajo.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '0.00');
                    document.getElementById('modal-lugar-precio-bajo').textContent = boletin.lugar_precio_mas_bajo || 'N/A';
                    document.getElementById('modal-observaciones').textContent = boletin.observaciones || 'Sin observaciones.';

                    // Usar jQuery para mostrar el modal si está disponible
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
                        $('#verBoletinModal').modal('show');
                    } else {
                        console.warn("jQuery or Bootstrap modal function not found. Ensure they are loaded correctly.");
                    }
                })
                .catch(error => console.error('Error al cargar detalles del boletín:', error));
            } else if (tipo === 'editar') {
                window.location.href = `/boletines/${id}/edit`;
            } else if (tipo === 'boletin') { // Asumo que 'boletin' es para eliminar
                if (confirm('¿Estás seguro de que quieres eliminar este boletín?')) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    fetch(`/boletines/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => Promise.reject(errorData));
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(data.message);
                        fetchBoletines(); // Recargar la tabla después de eliminar
                    })
                    .catch(error => {
                        console.error('Error al eliminar el boletín:', error);
                        alert('Error al eliminar el boletín: ' + (error.message || JSON.stringify(error)));
                    });
                }
            }
        };
        console.log
    }


    function createBoletinRowHtml(boletin) {
        // Formateo de fecha y hora para que coincida con Blade
        const dateObj = new Date(boletin.created_at);
        const options = { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
        let formattedDate = dateObj.toLocaleString('es-CO', options);
        // Ajustar el formato a 'd de F del Y h:i a'
        formattedDate = formattedDate.replace('a. m.', 'a.m.').replace('p. m.', 'p.m.');

        // Función para diffForHumans (aproximación, ya que Laravel usa Carbon)
        function timeAgo(date) {
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);
            let interval = seconds / 31536000;
            if (interval > 1) return Math.floor(interval) + " años";
            interval = seconds / 2592000;
            if (interval > 1) return Math.floor(interval) + " meses";
            interval = seconds / 86400;
            if (interval > 1) return Math.floor(interval) + " días";
            interval = seconds / 3600;
            if (interval > 1) return Math.floor(interval) + " horas";
            interval = seconds / 60;
            if (interval > 1) return Math.floor(interval) + " minutos";
            return Math.floor(seconds) + " segundos";
        }
        const diffForHumans = timeAgo(boletin.created_at);

        // Clases para el badge de ESTADO
        let estadoBadgeClass = '';
        if (boletin.estado === 'aprobado') {
            estadoBadgeClass = 'bg-green-600';
        } else if (boletin.estado === 'pendiente') {
            estadoBadgeClass = 'bg-yellow-500';
        } else { // Rechazado o cualquier otro
            estadoBadgeClass = 'bg-red-600';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // ESTE ES EL HTML GENERADO DINÁMICAMENTE - DEBE COINCIDIR CON boletin_row.blade.php
        return `
            <tr id="boletin-row-${boletin.id}" class="bg-white hover:bg-gray-200">
                <td class="max-w-xs px-4 py-2 text-gray-800 break-words whitespace-normal align-top">
                    ${boletin.nombre ? boletin.nombre.substring(0, 40) + (boletin.nombre.length > 40 ? '...' : '') : ''}
                </td>
                <td class="max-w-xs px-4 py-2 text-gray-600 break-words whitespace-normal align-top boletin-contenido-cell">
                    ${boletin.descripcion ? boletin.descripcion.substring(0, 60) + (boletin.descripcion.length > 60 ? '...' : '') : ''}
                </td>
                <td class="max-w-xs px-4 py-2 text-gray-600 break-words whitespace-normal align-top boletin-fecha-cell">
                    ${formattedDate}
                    <span class="block text-xs text-gray-500">
                        (${diffForHumans} ago)
                    </span>
                </td>
                <td class="px-4 py-2 text-gray-700 align-top whitespace-nowrap">
                    ${boletin.precio_mas_alto ? `
                        <p class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            $${boletin.precio_mas_alto.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </p>
                        ${boletin.lugar_precio_mas_alto ? `<span class="block text-xs text-gray-500">(${boletin.lugar_precio_mas_alto})</span>` : ''}
                    ` : 'N/A'}
                </td>
                <td class="px-4 py-2 text-gray-700 align-top whitespace-nowrap">
                    ${boletin.precio_mas_bajo ? `
                        <p class="flex items-center text-red-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            $${boletin.precio_mas_bajo.toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </p>
                        ${boletin.lugar_precio_mas_bajo ? `<span class="block text-xs text-gray-500">(${boletin.lugar_precio_mas_bajo})</span>` : ''}
                    ` : 'N/A'}
                </td>
                <td class="px-4 py-2 align-top boletin-estado-cell">
                    <span
                        class="inline-block px-3 py-1 text-sm font-semibold text-white rounded ${estadoBadgeClass}">
                        ${boletin.estado ? boletin.estado.charAt(0).toUpperCase() + boletin.estado.slice(1) : ''}
                    </span>
                </td>
                <td class="flex flex-col px-4 py-2 space-y-1 align-top md:space-y-0 md:space-x-2 md:flex-row boletin-acciones-cell">
                    <button type="button" onclick="mostrarModal('ver', '${boletin.id}')"
                        class="px-3 py-1 text-sm text-center text-white bg-green-600 rounded hover:bg-green-700">
                        Ver
                    </button>
                    <button type="button" onclick="mostrarModal('editar', '${boletin.id}')"
                        class="px-3 py-1 text-sm text-center text-white bg-yellow-600 rounded hover:bg-yellow-700">
                        Editar
                    </button>
                    <button type="button" onclick="mostrarModal('boletin', '${boletin.id}')"
                        class="w-20 px-1 py-1 text-sm text-center text-white bg-red-600 rounded hover:bg-red-700">
                        Eliminar
                    </button>
                </td>
            </tr>
        `;
    }

    function fetchBoletines() {
        const query = searchInput ? searchInput.value : '';
        const estado = estadoFilterSelect ? estadoFilterSelect.value : 'todos';

        const url = new URL('/boletines/filtrados', window.location.origin);
        if (query) {
            url.searchParams.append('q', query);
        }
        if (estado && estado !== 'todos') {
            url.searchParams.append('estado', estado);
        }
        url.searchParams.append('per_page', 5);

        if (tableBody) tableBody.innerHTML = '';
        if (noBoletinesMessageRow) noBoletinesMessageRow.style.display = 'none';
        if (loadingSpinnerRow) loadingSpinnerRow.style.display = 'table-row';

        fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`Network response was not ok: ${response.status} ${response.statusText}. Response text: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (loadingSpinnerRow) loadingSpinnerRow.style.display = 'none';

            if (tableBody) {
                tableBody.innerHTML = '';
            }

            if (data.data && data.data.length > 0) {
                data.data.forEach(boletin => {
                    const rowHtml = createBoletinRowHtml(boletin);
                    tableBody.insertAdjacentHTML('beforeend', rowHtml);
                });
                toggleNoBoletinesMessage(data.data.length);
            } else {
                toggleNoBoletinesMessage(0);
            }

            // Aquí deberías manejar la actualización de los enlaces de paginación si los quieres con AJAX
            // data.links y data.meta vienen en la respuesta de Laravel cuando usas ->paginate()
        })
        .catch(error => {
            if (loadingSpinnerRow) loadingSpinnerRow.style.display = 'none';
            console.error('Error al obtener boletines filtrados:', error);
            if (tableBody) {
                tableBody.innerHTML = '';
            }
            toggleNoBoletinesMessage(0);
        });
    }

    // Lógica para el INPUT de búsqueda
    if (searchInput) {
        // Inicializar visibilidad de iconos si hay un valor inicial
        if (searchInput.value.length > 0) {
            if (searchIcon) searchIcon.classList.add('hidden');
            if (clearIconContainer) clearIconContainer.classList.remove('hidden');
        } else {
            if (searchIcon) searchIcon.classList.remove('hidden');
            if (clearIconContainer) clearIconContainer.classList.add('hidden');
        }

        searchInput.addEventListener('input', debounce(function() {
            fetchBoletines();
            // Actualizar visibilidad de iconos
            if (searchInput.value.length > 0) {
                if (searchIcon) searchIcon.classList.add('hidden');
                if (clearIconContainer) clearIconContainer.classList.remove('hidden');
            } else {
                if (searchIcon) searchIcon.classList.remove('hidden');
                if (clearIconContainer) clearIconContainer.classList.add('hidden');
            }
        }, 300));

        if (clearIconContainer) {
            clearIconContainer.addEventListener('click', () => {
                searchInput.value = '';
                if (searchIcon) searchIcon.classList.remove('hidden');
                if (clearIconContainer) clearIconContainer.classList.add('hidden');
                searchInput.focus();
                fetchBoletines();
            });
        }
    }

    // Lógica para el SELECT de filtro por estado
    if (estadoFilterSelect) {
        estadoFilterSelect.addEventListener('change', function() {
            fetchBoletines();
        });
    }

    // La delegación de eventos para 'ver-boletin' ahora ya no es necesaria
    // porque los botones llaman a 'mostrarModal' directamente, que manejará el fetch.
    // Solo si tuvieras otros botones que no usen onclick o un manejador global, seguiría siendo útil.
});