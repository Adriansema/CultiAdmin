 //!{{-- SE IMPORTA ALGUNA FUNCION, PERO SE VE EN EL EJEMPLO 2 --}}

document.addEventListener('DOMContentLoaded', () => { 
    const searchInput = document.getElementById('searchInput');
    const searchIcon = document.getElementById('searchIcon');
    const clearIconContainer = document.getElementById('clearIconContainer');
    const usersTableBody = document.getElementById('usersTableBody'); // El contenedor donde se renderizan los usuarios
    // Asegúrate de que el contenedor de la lista de usuarios tenga un ID.
    // En tu Blade

    // Variables para el debounce
    let debounceTimer;
    const DEBOUNCE_DELAY = 300; // Milisegundos de espera antes de hacer la petición

    //!{{-- SE IMPLEMENTA UNA VARIABLE NUEVA, SE VE EN EL EJEMPLO 2 --}}

    // Función para mostrar la "Equis" y ocultar la lupa
    const showClearIcon = () => {
        searchIcon.classList.add('hidden'); // Oculta la lupa
        clearIconContainer.classList.remove('hidden'); // Muestra el contenedor de la Equis
    };

    // Función para mostrar la lupa y ocultar la "Equis"
    const showSearchIcon = () => {
        searchIcon.classList.remove('hidden'); // Muestra la lupa
        clearIconContainer.classList.add('hidden'); // Oculta el contenedor de la Equis
    };

    // ! Lógica para realizar la petición AJAX {{-- CREO QUE SE QUITA, SE VE EN EL EJEMPLO 2 --}}
    const fetchUsers = async () => {
        const query = searchInput.value;
        const url = `/api/usuarios-filtrados?q=${encodeURIComponent(query)}`; // URL a tu endpoint de Laravel

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json(); // La respuesta es un objeto JSON con los datos de paginación

            // Actualizar la lista de usuarios en el DOM
            renderTableRows(data.data, query); // data.data contendrá el array de usuarios /nuevo
            // Aquí también podrías manejar la paginación si la lista va a cambiar de página
            // renderPagination(data.links, data.meta);

        } catch (error) {
            console.error('Error al obtener usuarios:', error);
            // Aquí podrías mostrar un mensaje de error al usuario
        }
    };

    /// --- Función para renderizar las filas de la tabla --- 
    // Ahora acepta un segundo argumento: el query de búsqueda
    const renderTableRows = (users, query = '') => { // <--- Recibe el query /nuevo
        usersTableBody.innerHTML = ''; // Limpiar la lista actual

        // Función auxiliar para resaltar el texto  /nuevo
        const highlightMatch = (text, query) => {
            if (!query) return text; // Si no hay query, devuelve el texto original
            const regex = new RegExp(`(${query})`, 'gi'); // Expresión regular para buscar el query (global, insensible a mayúsculas/minúsculas)
            return text.replace(regex, '<span class="highlight">$1</span>'); // Envuelve la coincidencia con <strong>
        };

        if (users.length === 0) {
            // Mostrar mensaje de no resultados en una fila
            const noResultsRow = document.createElement('tr');
            // 'colspan="4"' debe coincidir con el número de columnas de tu tabla
            noResultsRow.innerHTML = `<td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron usuarios que coincidan.
                                      </td>`;
            usersTableBody.appendChild(noResultsRow);
            return;
        }

        // ! SE IMPLEMENTA OTRA VARIABLE, SE VE EN EL EJEMPLO 2

        users.forEach(user => {
            const tableRow = document.createElement('tr');
            tableRow.className = 'bg-white hover:bg-gray-300'; // Clases de tu fila

            // ! SE IMPLEMENTA UNAS FUNCIONES PARA LOS DATA-ATTRIBUTES

            // Aplica la función highlightMatch a cada campo de texto que quieres resaltar
            const highlightedRol = highlightMatch(user.roles && user.roles.length > 0 ? user.roles.map(r => r.name).join(', ') : 'Sin Rol', query);
            const highlightedName = highlightMatch(user.name, query);
            const highlightedEmail = highlightMatch(user.email, query);
            const highlightedEstado = highlightMatch(user.estado.charAt(0).toUpperCase() + user.estado.slice(1), query); // Resaltar estado también

            // ! REPLICAN LA LOGICA DEL BOTON DE ESTADO, SE VE EN EL EJEMPLO 2

            //con negrilla en las letras seleccionadas
            let rowHtml = `
                <td class="px-6 py-4 flex items-center group relative">
                    <span>${highlightedRol}</span>
                    <img src="/images/lapiz.svg"
                        class="w-4 h-4 absolute left-[calc(60%+4px)] top-1/2 -translate-y-1/2
                                opacity-0 group-hover:opacity-100
                                transition-opacity duration-300 pointer-events-none group-hover:pointer-events-auto"
                        alt="editar">
                </td>
                <td class="px-6 py-4">${highlightedName}</td>
                <td class="px-6 py-4">${highlightedEmail}</td>
                <td class="px-6 py-4">
                    <button type="button"
                        class="group relative px-4 py-2 text-sm rounded text-white transition-colors duration-300
                                ${user.estado === 'activo' ? 'bg-green-600 hover:bg-red-600' : 'bg-gray-400 hover:bg-yellow-300 hover:text-black'}
                                inline-flex items-center justify-center">
                        <span class="flex items-center space-x-2 transition-opacity duration-300
                                    ${user.estado === 'activo' ? '' : 'text-black'}
                                    group-hover:opacity-0 pointer-events-none">
                            <span>${highlightedEstado}</span> <img src="/images/RL.svg" alt="Icono" class="w-4 h-4">
                        </span>
                        <span class="absolute inset-0 flex items-center justify-center space-x-2
                                    opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                    ${user.estado === 'activo' ? 'text-white' : 'text-black'}
                                    pointer-events-none">
                            <span>${user.estado === 'activo' ? 'Desactivar' : 'Activar'}</span>
                            <img src="/images/RL.svg" alt="Icono Hover" class="w-4 h-4">
                        </span>
                    </button>
                </td>
            `;
            tableRow.innerHTML = rowHtml;
            usersTableBody.appendChild(tableRow);
        });
    };

    // --- Event listeners para el input de búsqueda (ya los tienes) ---
    // 1. Cuando el input GANA FOCO o tiene texto
    searchInput.addEventListener('focus', () => {
        // Siempre mostrar la Equis al enfocar, incluso si está vacío
        showClearIcon();
    });

    // 2. Cuando el input PIERDE FOCO
    searchInput.addEventListener('blur', () => {
        // Si el input está vacío al perder el foco, volvemos a la lupa
        if (searchInput.value.length === 0) {
            showSearchIcon();
        }
        // Si tiene texto, la Equis se queda (ya que el usuario podría querer borrarlo)
    });

    // 3. Cuando el usuario escribe/borra texto en el input
    searchInput.addEventListener('input', () => {
        if (searchInput.value.length > 0) {
            // Si hay texto, aseguramos que la Equis esté visible (en caso de que se haya enfocado y luego borrado, y se vuelva a escribir)
            showClearIcon();
        } else {
            // Si no hay texto, y el input no está enfocado, volvemos a la lupa
            // Si está enfocado, la 'focus' event listener ya se encargará
            if (document.activeElement !== searchInput) { // Sólo si no está actualmente en foco
                showSearchIcon();
            }
        }

        // Limpiar el timer anterior
        clearTimeout(debounceTimer);
        // Configurar un nuevo timer
        debounceTimer = setTimeout(() => {
            fetchUsers(); // Llama a la función para obtener usuarios después de la espera
        }, DEBOUNCE_DELAY);
    });


    // 4. Lógica para borrar el texto al hacer clic en la "Equis"
    clearIconContainer.addEventListener('click', () => {
        searchInput.value = ''; // Borra el texto del input
        searchInput.focus(); // Opcional: vuelve a poner el foco en el input
        showSearchIcon(); // Vuelve a mostrar la lupa (porque el input ahora está vacío)
        fetchUsers(); // Vuelve a cargar todos los usuarios (o los iniciales)
        console.log('Campo de búsqueda limpiado.'); //TODO: se quita despues de hacer pruebas
    });

    // Estado inicial: Asegúrate de que la lupa esté visible al cargar la página si el input está vacío
    // O la Equis si ya tiene contenido (ej. autocompletado del navegador)
    if (searchInput.value.length > 0) {
        showClearIcon();
        // Opcional: Si quieres que la búsqueda se realice con el valor inicial al cargar la página
        // fetchUsers();
    } else {
        showSearchIcon();
    }

    // Para la primera carga de la página, si no estás haciendo una búsqueda inicial,
    // asegúrate de que la lista de usuarios se muestre correctamente.
    // Esto es manejado por Laravel en el `index` del controlador.
}); 



// resources/js/Inputsearch.js

// Importar la función de inicialización del sorting.
// Esta función recibirá los datos y el renderizador.
/* import { initializeSorting, sortAndRender } from './sorting.js';

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const searchIcon = document.getElementById('searchIcon');
    const clearIconContainer = document.getElementById('clearIconContainer');
    const usersTableBody = document.getElementById('usersTableBody');

    let debounceTimer;
    const DEBOUNCE_DELAY = 300;

    // Almacenará los datos de los usuarios actualmente cargados/filtrados
    let currentUsersData = [];

    // Función para mostrar la "Equis" y ocultar la lupa
    const showClearIcon = () => {
        if (searchIcon) searchIcon.classList.add('hidden');
        if (clearIconContainer) clearIconContainer.classList.remove('hidden');
    };

    // Función para mostrar la lupa y ocultar la "Equis"
    const showSearchIcon = () => {
        if (searchIcon) searchIcon.classList.remove('hidden');
        if (clearIconContainer) clearIconContainer.classList.add('hidden');
    };

    /// --- Función para renderizar las filas de la tabla (GLOBALIZADA y ajustada) ---
    // Esta función será llamada por search y sorting.
    // Necesitamos que sea accesible desde fuera de este ámbito DOMContentLoaded
    // Para simplificar, la ponemos fuera o la pasamos como callback.
    // Por ahora, la definiremos aquí y la pasaremos a sorting.js
    const renderTableRows = (users, query = '') => {
        usersTableBody.innerHTML = '';

        const highlightMatch = (text, query) => {
            if (!query) return text;
            const regex = new RegExp(`(${query})`, 'gi');
            return text.replace(regex, '<span class="highlight">$1</span>');
        };

        if (users.length === 0) {
            const noResultsRow = document.createElement('tr');
            noResultsRow.innerHTML = `<td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No se encontraron usuarios que coincidan.
                                      </td>`;
            usersTableBody.appendChild(noResultsRow);
            return;
        }

        // Obtener la URL base de assets para las imágenes
        // Asegúrate de definir window.assetUrl en tu app.blade.php
        const assetBaseUrl = window.assetUrl || '/'; // Fallback a la raíz si no está definida

        users.forEach(user => {
            const tableRow = document.createElement('tr');
            tableRow.className = 'bg-white hover:bg-gray-300';
            // Añadir data-attributes a la fila para el sorting.js (aunque lo parseamos, es buena práctica)
            tableRow.dataset.userId = user.id;
            tableRow.dataset.userName = user.name;
            tableRow.dataset.userEmail = user.email;
            tableRow.dataset.userEstado = user.estado;
            tableRow.dataset.userRoles = user.roles && user.roles.length > 0 ? user.roles.map(r => r.name).join(', ') : 'Sin Rol';

            const highlightedRol = highlightMatch(user.roles && user.roles.length > 0 ? user.roles.map(r => r.name).join(', ') : 'Sin Rol', query);
            const highlightedName = highlightMatch(user.name, query);
            const highlightedEmail = highlightMatch(user.email, query);
            const highlightedEstado = highlightMatch(user.estado.charAt(0).toUpperCase() + user.estado.slice(1), query);

            // Replicar la lógica del botón de estado
            const estadoIsActivo = user.estado === 'activo';
            const estadoBgClass = estadoIsActivo ? 'bg-[var(--color-activo)] hover:bg-[var(--color-desactivar)]' : 'bg-[var(--color-inactivo)] hover:bg-[var(--color-activar)]';
            const estadoTextColorClass = estadoIsActivo ? 'text-[var(--color-textAct)]' : 'text-[var(--color-textInact)]';
            const hoverTextColorClass = estadoIsActivo ? 'text-[var(--color-textDesact)]' : 'text-[var(--color-textActivar)]';
            const estadoIcon = estadoIsActivo ? 'activo-flecha.svg' : 'inact-flecha.svg';
            const hoverIcon = estadoIsActivo ? 'desact-flecha.svg' : 'activar-flecha.svg';

            tableRow.innerHTML = `
                <td class="px-6 py-4 flex items-center group relative">
                    <span>${highlightedRol}</span>
                    <img src="${assetBaseUrl}images/lapiz.svg"
                        class="w-4 h-4 absolute left-[calc(60%+4px)] top-1/2 -translate-y-1/2
                                opacity-0 group-hover:opacity-100
                                transition-opacity duration-300 pointer-events-none group-hover:pointer-events-auto"
                        alt="editar">
                </td>
                <td class="px-6 py-4">${highlightedName}</td>
                <td class="px-6 py-4">${highlightedEmail}</td>
                <td class="px-6 py-4">
                    <form action="/usuarios/${user.id}/toggle" method="POST" class="inline-block">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <input type="hidden" name="_method" value="PATCH">
                        <button type="submit"
                            class="group relative px-4 py-2 text-sm rounded-lg text-[var(--color-textAct)] transition-colors duration-300
                                    ${estadoBgClass}
                                    inline-flex items-center justify-center">
                            <span class="flex items-center space-x-2 transition-opacity duration-300
                                        ${estadoTextColorClass}
                                        group-hover:opacity-0 pointer-events-none">
                                <span>${highlightedEstado}</span>
                                <img src="${assetBaseUrl}images/${estadoIcon}"
                                    alt="Icono ${user.estado}"
                                    class="w-4 h-4">
                            </span>
                            <span class="absolute inset-0 flex items-center justify-center space-x-2
                                        opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                        ${hoverTextColorClass}
                                        pointer-events-none">
                                <span>${estadoIsActivo ? 'Desactivar' : 'Activar'}</span>
                                <img src="${assetBaseUrl}images/${hoverIcon}"
                                    alt="Icono Hover"
                                    class="w-4 h-4">
                            </span>
                        </button>
                    </form>
                </td>
            `;
            usersTableBody.appendChild(tableRow);
        });
    };

    // Función para realizar la petición AJAX (modificada para guardar los datos)
    const fetchUsers = async (initialLoad = false) => {
        const query = searchInput.value;
        const url = `/api/usuarios-filtrados?q=${encodeURIComponent(query)}`;

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            // Actualizar currentUsersData con los datos recién obtenidos del servidor
            // Esto es crucial para que el sorting pueda operar sobre ellos
            currentUsersData = data.data; // data.data es el array de usuarios

            // Llamar a la función de ordenamiento para aplicar cualquier orden existente
            // y luego renderizar la tabla con los datos filtrados y ordenados.
            sortAndRender(currentUsersData); // <-- Ahora sorting.js manejará el renderizado

        } catch (error) {
            console.error('Error al obtener usuarios:', error);
            // Aquí podrías mostrar un mensaje de error al usuario
        }
    };

    // Event listeners para el input de búsqueda
    searchInput.addEventListener('focus', () => {
        showClearIcon();
    });

    searchInput.addEventListener('blur', () => {
        if (searchInput.value.length === 0) {
            showSearchIcon();
        }
    });

    searchInput.addEventListener('input', () => {
        if (searchInput.value.length > 0) {
            showClearIcon();
        } else {
            if (document.activeElement !== searchInput) {
                showSearchIcon();
            }
        }
        // Limpiar el timer anterior
        clearTimeout(debounceTimer);
        // Configurar un nuevo timer
        debounceTimer = setTimeout(() => {
            fetchUsers(); // Llama a la función para obtener usuarios después de la espera
        }, DEBOUNCE_DELAY);
    });
    
    clearIconContainer.addEventListener('click', () => {
        searchInput.value = '';
        searchInput.focus();
        showSearchIcon();
        fetchUsers(); // Volver a cargar todos los usuarios
        console.log('Campo de búsqueda limpiado.');
    });

    // Estado inicial y carga inicial de usuarios
    if (searchInput.value.length > 0) {
        showClearIcon();
    } else {
        showSearchIcon();
    }

    // Inicializar el sorting y pasarle la función de renderizado
    // y la referencia al array de datos que manejará.
    // IMPORTANTE: Asegúrate de que usersTableBody esté en el DOM antes de esto.
    initializeSorting(currentUsersData, usersTableBody, renderTableRows);

    // Cargar los usuarios inicialmente desde la API cuando la página se carga.
    // Esto sobrescribirá lo que Laravel ya haya puesto en Blade si el endpoint es la única fuente.
    // Si tu Blade ya carga la primera página y quieres que el JS solo tome el control en la búsqueda,
    // puedes cambiar esto. Pero para la integración, es más fácil que el JS maneje toda la tabla.
    fetchUsers(true); // Llamar para la carga inicial de datos.

}); */