// resources/js/sorting.js

/* let currentSortColumn = null;
let sortDirection = 'asc';
let usersDataTable = []; // Este será el array de datos que se ordenará
let tableBodyElement = null; // Referencia al tbody
let renderFunction = null;   // Referencia a la función renderTableRows de Inputsearch.js

// Funciones a exportar para que Inputsearch.js pueda llamarlas
export function initializeSorting(initialData, tbodyElement, renderer) {
    usersDataTable = initialData; // Recibe los datos iniciales
    tableBodyElement = tbodyElement; // Recibe la referencia al tbody
    renderFunction = renderer;       // Recibe la función de renderizado

    // Adjuntar event listeners a las cabeceras solo una vez
    const theadThs = document.querySelectorAll('#usuariosTable thead th[data-column]');
    theadThs.forEach(th => {
        th.addEventListener('click', () => {
            const column = th.dataset.column;
            if (column) {
                sortAndRender(usersDataTable, column); // Pasar la columna clicada
            }
        });
    });

    // Opcional: Establecer el icono inicial en la primera columna si no hay orden previo
    if (theadThs.length > 0 && currentSortColumn === null) {
        currentSortColumn = theadThs[0].dataset.column;
        const initialIcon = theadThs[0].querySelector('.sort-icon');
        if (initialIcon) {
            initialIcon.src = initialIcon.dataset.sortAscSrc;
        }
    }
}

// Esta función es llamada por el sorting y por Inputsearch.js después de fetch
export function sortAndRender(data, columnToSort = currentSortColumn) {
    if (!renderFunction) {
        console.error("Render function not initialized in sorting.js");
        return;
    }

    // Actualizar la columna y dirección de ordenamiento si se especificó una nueva columna
    if (columnToSort && columnToSort !== currentSortColumn) {
        currentSortColumn = columnToSort;
        sortDirection = 'asc'; // Reset a ascendente para nueva columna
    } else if (columnToSort && columnToSort === currentSortColumn) {
        sortDirection = (sortDirection === 'asc') ? 'desc' : 'asc'; // Alternar dirección
    }
    // Si no se especifica columnToSort (ej. llamado desde fetchUsers), usa el currentSortColumn

    // Actualizar iconos de las cabeceras
    const theadThs = document.querySelectorAll('#usuariosTable thead th[data-column]');
    theadThs.forEach(th => {
        const iconImg = th.querySelector('.sort-icon');
        if (iconImg) {
            if (th.dataset.column === currentSortColumn) {
                iconImg.src = iconImg.dataset[`sort${sortDirection === 'asc' ? 'Asc' : 'Desc'}Src`];
            } else {
                // Si la columna no es la activa, puedes resetear su icono a un estado neutro
                // o dejarlo como está. Para este ejemplo, lo dejamos como está,
                // ya que no hay un icono 'sort-none'.
                // Opcional: iconImg.src = iconImg.dataset.sortAscSrc; // Si quieres que todas las inactivas muestren asc
            }
        }
    });

    // Ordenar los datos
    data.sort((a, b) => {
        const valueA = a[currentSortColumn];
        const valueB = b[currentSortColumn];

        // Manejo específico para roles_name (puede ser string vacío o con comas)
        if (currentSortColumn === 'roles_name') {
            const roleA = valueA || ''; // Tratar null/undefined como string vacío
            const roleB = valueB || '';
            return sortDirection === 'asc'
                ? roleA.localeCompare(roleB)
                : roleB.localeCompare(roleA);
        }
        // Manejo específico para 'estado'
        else if (currentSortColumn === 'estado') {
            const order = { 'activo': 1, 'inactivo': 2 };
            const orderA = order[valueA] || 99;
            const orderB = order[valueB] || 99;
            return sortDirection === 'asc'
                ? orderA - orderB
                : orderB - orderA;
        }
        // Comparación general para strings y numbers
        else if (typeof valueA === 'string' && typeof valueB === 'string') {
            return sortDirection === 'asc'
                ? valueA.localeCompare(valueB)
                : valueB.localeCompare(valueA);
        } else if (typeof valueA === 'number' && typeof valueB === 'number') {
            return sortDirection === 'asc'
                ? valueA - valueB
                : valueB - valueA;
        }
        // Fallback
        else {
            const stringA = String(valueA || '');
            const stringB = String(valueB || '');
            return sortDirection === 'asc'
                ? stringA.localeCompare(stringB)
                : stringB.localeCompare(stringA);
        }
    });

    // Renderizar los datos ordenados
    renderFunction(data);
}

// NOTE: No se usa DOMContentLoaded aquí porque initializeSorting
// es llamada por Inputsearch.js después de DOMContentLoaded. */