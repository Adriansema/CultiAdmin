document.addEventListener('DOMContentLoaded', function () {
    const buscadorTabla = document.getElementById('buscadorTabla');
    const filtrosBotones = document.getElementById('filtrosBotones');
    const buscame = document.getElementById('buscame');
    const searchIcon = document.getElementById('searchIcon'); // Icono de lupa
    const clearIconContainer = document.getElementById('clearIconContainer'); // Contenedor de la 'X'

    // 1. Lógica para el botón "Filtrar" (que envía el formulario)
    if (filtrosBotones && buscadorTabla) {
        filtrosBotones.addEventListener('click', function () {
            console.log('Botón Filtrar clicado. Enviando formulario...');
            buscadorTabla.submit(); // Envía el formulario
        });
    }

    // 2. Lógica para la tecla Enter en el input de búsqueda
    // AHORA USA 'buscame' (la variable correcta)
    if (buscame && buscadorTabla) { // Aseguramos que el input y el formulario existan
        buscame.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Evita el comportamiento predeterminado del Enter (ej. si estás en un textarea, evita un salto de línea)
                console.log('Enter presionado en el input. Enviando formulario...');
                buscadorTabla.submit(); // Envía el formulario
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
    if (buscame) {
        // Al cargar la página, inicializa la visibilidad de los iconos
        if (buscame.value.length > 0) {
            showClearIcon();
        } else {
            showSearchIcon();
        }

        // Evento 'input': Se dispara cuando el valor del input cambia (al escribir, pegar, etc.)
        buscame.addEventListener('input', () => {
            if (buscame.value.length > 0) {
                showClearIcon(); // Si hay texto, muestra la 'X'
            } else {
                showSearchIcon(); // Si no hay texto, muestra la lupa
            }
        });

        // Lógica para la "Equis" de limpiar
        if (clearIconContainer) {
            clearIconContainer.addEventListener('click', () => {
                buscame.value = ''; // Borra el texto
                showSearchIcon(); // Vuelve a mostrar la lupa
                buscame.focus(); // Opcional: vuelve a poner el foco en el input

                // Si limpiar el campo debe DISPARAR una nueva búsqueda para mostrar todo:
                if (buscadorTabla) { // Asegúrate de que el formulario exista
                    buscadorTabla.submit(); // Envía el formulario vacío
                }
                console.log('Campo de búsqueda limpiado y formulario enviado.');
            });
        }
    }
});