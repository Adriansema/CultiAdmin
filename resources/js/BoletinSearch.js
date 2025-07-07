document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('buscar-boletin-input');
    const estadoFilterSelect = document.getElementById('filtro-estado');
    const precioFilterSelect = document.getElementById('filtro-precio');
    const tableBody = document.getElementById('boletines-table-body');
    const noBoletinesMessageRow = document.getElementById('no-boletines-message-row');
    const loadingSpinnerRow = document.getElementById('loading-spinner-row');
    const exportCsvButton = document.getElementById('exportCsvButton');

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
    if (typeof window.mostrarModal !== 'function') {
        window.mostrarModal = function (tipo, id) {
            if (tipo === 'ver') {
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
                        document.getElementById('modal-precio-alto').textContent = boletin.precio_mas_alto_formatted;
                        document.getElementById('modal-lugar-precio-alto').textContent = boletin.lugar_precio_mas_alto || 'N/A';
                        document.getElementById('modal-precio-bajo').textContent = boletin.precio_mas_bajo_formatted;
                        document.getElementById('modal-lugar-precio-bajo').textContent = boletin.lugar_precio_mas_bajo || 'N/A';
                        document.getElementById('modal-observaciones').textContent = boletin.observaciones || 'Sin observaciones.';

                        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
                            $('#verBoletinModal').modal('show');
                        }
                    })
            } else if (tipo === 'editar') {
                const editModal = document.getElementById(`editBoletinModal-${id}`);
                if (editModal) {
                    editModal.classList.remove('hidden');
                    editModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                }
            } else if (tipo === 'boletin') { // Asumo que 'boletin' es para eliminar
                const deleteModal = document.getElementById(`deleteBoletinModal-${id}`);
                if (deleteModal) {
                    deleteModal.classList.remove('hidden');
                    deleteModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                }
            } else if (tipo === 'validar-boletin') {
                const validateModal = document.getElementById(`validarBoletinModal-${id}`);
                if (validateModal) {
                    validateModal.classList.remove('hidden');
                    validateModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                }
            } else if (tipo === 'rechazar-boletin') {
                const rejectModal = document.getElementById(`rechazarBoletinModal-${id}`);
                if (rejectModal) {
                    rejectModal.classList.remove('hidden');
                    rejectModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                }
            }
        };
    }

    function createBoletinRowHtml(boletin) {
        const dateObj = new Date(boletin.created_at);
        const options = { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
        let formattedDate = dateObj.toLocaleString('es-CO', options);
        formattedDate = formattedDate.replace('a. m.', 'a.m.').replace('p. m.', 'p.m.');

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

        let estadoBadgeClass = '';
        if (boletin.estado === 'aprobado') {
            estadoBadgeClass = 'bg-green-600';
        } else if (boletin.estado === 'pendiente') {
            estadoBadgeClass = 'bg-yellow-500';
        } else {
            estadoBadgeClass = 'bg-red-600';
        }

        const precioAltoDisplay = boletin.precio_mas_alto_formatted;
        const precioBajoDisplay = boletin.precio_mas_bajo_formatted;

        return `
            <tr id="boletin-row-${boletin.id}" class="bg-white hover:bg-gray-200">
                <td class="px-6 py-4 text-gray-800 break-words whitespace-normal align-top boletin-nombre-cell">
                    ${boletin.nombre ? boletin.nombre.substring(0, 40) + (boletin.nombre.length > 40 ? '...' : '') : ''}
                </td>
                <td class="px-6 py-4 text-gray-600 break-words whitespace-normal align-top boletin-descripcion-cell">
                    ${boletin.descripcion ? boletin.descripcion.substring(0, 60) + (boletin.descripcion.length > 60 ? '...' : '') : ''}
                </td>
                <td class="px-6 py-4 text-gray-600 break-words whitespace-normal align-top">
                    ${formattedDate}
                    <span class="block text-xs text-gray-500">
                        (${diffForHumans})
                    </span>
                </td>
                <td class="px-6 py-4 text-gray-700 align-top whitespace-nowrap boletin-precio-bajo-cell">
                    ${boletin.precio_mas_alto ? `
                        <p class="flex items-center text-green-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            ${precioAltoDisplay}
                        </p>
                        ${boletin.lugar_precio_mas_alto ? `<span class="block text-xs text-gray-500">(${boletin.lugar_precio_mas_alto})</span>` : ''}
                    ` : 'N/A'}
                </td>
                <td class="px-6 py-4 text-gray-700 align-top whitespace-nowrap boletin-precio-bajo-cell" >
                    ${boletin.precio_mas_bajo ? `
                        <p class="flex items-center text-red-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                            </svg>
                            ${precioBajoDisplay}
                        </p>
                        ${boletin.lugar_precio_mas_bajo ? `<span class="block text-xs text-gray-500">(${boletin.lugar_precio_mas_bajo})</span>` : ''}
                    ` : 'N/A'}
                </td>
                <td class="px-6 py-4 align-top boletin-estado-cell">
                    <span
                        class="inline-block px-3 py-2 text-md font-semibold text-white rounded-xl ${estadoBadgeClass}">
                        ${boletin.estado ? boletin.estado.charAt(0).toUpperCase() + boletin.estado.slice(1) : ''}
                    </span>
                </td>
                <td class="flex flex-col px-6 py-4 space-y-1 align-top md:space-y-0 md:space-x-2 md:flex-row boletin-acciones-cell">
                    ${boletin.can_crear ? `
                        <button type="button" onclick="mostrarModal('ver', '${boletin.id}')"
                            class="px-3 py-2 text-center text-white bg-green-600 rounded-xl hover:bg-green-700">
                            Ver
                        </button>
                    ` : ''}
                    ${boletin.can_editar ? `
                        <button type="button" onclick="mostrarModal('editar', '${boletin.id}')"
                            class="px-3 py-2 text-center text-white bg-yellow-600 rounded-xl hover:bg-yellow-700">
                            Editar
                        </button>
                    ` : ''}
                    ${boletin.can_eliminar ? `
                        <button type="button" onclick="mostrarModal('boletin', '${boletin.id}')"
                            class="px-3 py-2 text-center text-white bg-red-600 rounded-xl hover:bg-red-700">
                            Eliminar
                        </button>
                    ` : ''}

                    ${boletin.can_validar ? `
                        <button type="button" onclick="mostrarModal('validar-boletin', '${boletin.id}')"
                            class="px-3 py-2 text-center text-white bg-blue-600 rounded-xl hover:bg-blue-700">
                            Validar
                        </button>
                        ` : ''}

                    ${boletin.can_validar ? `
                        <button type="button" onclick="mostrarModal('rechazar-boletin', '${boletin.id}')"
                            class="px-3 py-2 text-center text-white bg-orange-600 rounded-xl hover:bg-orange-700">
                            Rechazar
                        </button>
                    ` : ''}
                </td>
            </tr>
        `;
    }

    if (exportCsvButton) {
        exportCsvButton.addEventListener('click', function () {
            const query = searchInput ? searchInput.value : '';
            const estado = estadoFilterSelect ? estadoFilterSelect.value : '';
            const precio = precioFilterSelect ? precioFilterSelect.value : ''; // Obtener el valor del filtro de precio

            const url = new URL('/boletines/exportar-csv', window.location.origin);

            if (query) {
                url.searchParams.append('q', query);
            }
            if (estado) {
                url.searchParams.append('estado', estado);
            }
            if (precio) { // Añadir el parámetro de precio si está seleccionado
                url.searchParams.append('precio', precio);
            }

            // Redirigir el navegador para descargar el archivo
            window.location.href = url.toString();
        });
    }

    function fetchBoletines() {
        const query = searchInput ? searchInput.value : '';
        const estado = estadoFilterSelect ? estadoFilterSelect.value : '';
        const precio = precioFilterSelect ? precioFilterSelect.value : '';

        const url = new URL('/boletines/filtrados', window.location.origin);
        if (query) {
            url.searchParams.append('q', query);
        }
        // Clave: estos "if" aseguran que el parámetro NO se añada si el valor es vacío.
        // Esto permite que "Todos los estados" o "Todos los precios" desactiven ese filtro en particular.
        if (estado) {
            url.searchParams.append('estado', estado);
        }
        if (precio) {
            url.searchParams.append('precio', precio);
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
            })
            .catch(error => {
                if (loadingSpinnerRow) loadingSpinnerRow.style.display = 'none';
                if (tableBody) {
                    tableBody.innerHTML = '';
                }
                toggleNoBoletinesMessage(0);
            });
    }

    // Función para restablecer absolutamente TODOS los filtros (búsqueda, estado, precio)
    function resetAllFiltersAndFetch() {
        if (searchInput) searchInput.value = '';
        if (estadoFilterSelect) estadoFilterSelect.value = ''; // Resetea el select de estado a su option con value=""
        if (precioFilterSelect) precioFilterSelect.value = ''; // Resetea el select de precio a su option con value=""

        if (searchIcon) searchIcon.classList.remove('hidden');
        if (clearIconContainer) clearIconContainer.classList.add('hidden');

        fetchBoletines(); // Llama a fetchBoletines para recargar con todo limpio
    }


    // Lógica para el INPUT de búsqueda
    if (searchInput) {
        if (searchInput.value.length > 0) {
            if (searchIcon) searchIcon.classList.add('hidden');
            if (clearIconContainer) clearIconContainer.classList.remove('hidden');
        } else {
            if (searchIcon) searchIcon.classList.remove('hidden');
            if (clearIconContainer) clearIconContainer.classList.add('hidden');
        }

        searchInput.addEventListener('input', debounce(function () {
            fetchBoletines(); // Al escribir en la búsqueda, simplemente se aplica el filtro de búsqueda
            if (searchInput.value.length > 0) {
                if (searchIcon) searchIcon.classList.add('hidden');
                if (clearIconContainer) clearIconContainer.classList.remove('hidden');
            } else {
                if (searchIcon) searchIcon.classList.remove('hidden');
                if (clearIconContainer) clearIconContainer.classList.add('hidden');
            }
        }, 300));

        if (clearIconContainer) {
            // Este botón SIEMPRE debe resetear ABSOLUTAMENTE todo
            clearIconContainer.addEventListener('click', () => {
                resetAllFiltersAndFetch();
                searchInput.focus(); // Opcional: enfocar el input después de limpiar
            });
        }
    }

    // Lógica para el SELECT de filtro por estado
    if (estadoFilterSelect) {
        estadoFilterSelect.addEventListener('change', function () {
            // NO hay "if (this.value === '')" aquí.
            // Simplemente llama a fetchBoletines para que lea el nuevo estado del filtro (vacío o con valor)
            // y lo combine con los otros filtros activos.
            fetchBoletines();
        });
    }

    // Lógica para el SELECT de filtro por precio
    if (precioFilterSelect) {
        precioFilterSelect.addEventListener('change', function () {
            // NO hay "if (this.value === '')" aquí.
            // Simplemente llama a fetchBoletines para que lea el nuevo estado del filtro (vacío o con valor)
            // y lo combine con los otros filtros activos.
            fetchBoletines();
        });
    }

    // Llama a fetchBoletines al cargar la página inicialmente
    fetchBoletines();
});