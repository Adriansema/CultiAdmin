document.addEventListener('DOMContentLoaded', () => { 
    const BuscarUser = document.getElementById('BuscarUser');
    const filtrarBoton = document.getElementById('filtrarBoton');
    const SearchUser = document.getElementById('SearchUser');
    const searchIcon = document.getElementById('searchIcon');
    const clearIconContainer = document.getElementById('clearIconContainer');

    // 1. Lógica para el botón "Filtrar" (que envía el formulario)
    if (filtrarBoton && BuscarUser) {
        filtrarBoton.addEventListener('click', function () {
            console.log('Botón Filtrar clicado. Enviando formulario...');
            BuscarUser.submit(); // Envía el formulario
        });
    }

    // 2. Lógica para la tecla Enter en el input de búsqueda
    // AHORA USA 'SearchUser' (la variable correcta)
    if (SearchUser && BuscarUser) { // Aseguramos que el input y el formulario existan
        SearchUser.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Evita el comportamiento predeterminado del Enter (ej. si estás en un textarea, evita un salto de línea)
                console.log('Enter presionado en el input. Enviando formulario...');
                BuscarUser.submit(); // Envía el formulario
            }
        });
    }

    // Funciones auxiliares para mostrar/ocultar iconos
    const showClearIcon = () => {
        if (searchIcon) searchIcon.classList.add('hidden');
        if (clearIconContainer) clearIconContainer.classList.remove('hidden');
    };

    const showSearchIcon = () => {
        if (searchIcon) searchIcon.classList.remove('hidden');
        if (clearIconContainer) clearIconContainer.classList.add('hidden');
    };

    // 3. Lógica para mostrar/ocultar la "Equis" y la lupa, y debounce para 'fetchUsers'
    if (SearchUser) {
        // Al cargar la página, inicializa la visibilidad de los iconos
        if (SearchUser.value.length > 0) {
            showClearIcon();
        } else {
            showSearchIcon();
        }

        // Evento 'input': Se dispara cuando el valor del input cambia (al escribir, pegar, etc.)
        SearchUser.addEventListener('input', () => {
            if (SearchUser.value.length > 0) {
                showClearIcon(); // Si hay texto, muestra la 'X'
            } else {
                showSearchIcon(); // Si no hay texto, muestra la lupa
            }
        });

        // Lógica para la "Equis" de limpiar
        if (clearIconContainer) {
            clearIconContainer.addEventListener('click', () => {
                SearchUser.value = ''; // Borra el texto
                showSearchIcon(); // Vuelve a mostrar la lupa
                SearchUser.focus(); // Opcional: vuelve a poner el foco en el input

                // Si limpiar el campo debe DISPARAR una nueva búsqueda para mostrar todo:
                if (BuscarUser) { // Asegúrate de que el formulario exista
                    BuscarUser.submit(); // Envía el formulario vacío
                }
                console.log('Campo de búsqueda limpiado y formulario enviado.');
            });
        }
    }
});
