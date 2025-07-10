// public/js/ModalesGeneral.js

/**
 * Muestra un mensaje global de exito o error utilizando un modal vanilla JS.
 * Este modal tiene su propio HTML y logica de aparicion/desaparicion.
 * @param {string} type - El tipo de mensaje ('success' o 'error').
 * @param {string} message - El mensaje a mostrar.
 */
window.showGlobalMessage = function (type, message) {
    const modal = document.getElementById('globalMessageModalVanilla');
    const messageText = document.getElementById('globalMessageText');
    const successIcon = document.getElementById('globalMessageSuccessIcon');
    const errorIcon = document.getElementById('globalMessageErrorIcon');
    const closeButton = document.getElementById('globalMessageCloseButton');

    if (!modal || !messageText || !successIcon || !errorIcon || !closeButton) {
        // CAMBIO: 'Éxito' a 'Exito' en el alert
        alert(type === 'error' ? `Error: ${message}` : `Exito: ${message}`);
        return;
    }

    messageText.textContent = message;

    // CAMBIO: 'Configurar icono y colores' a 'Configurar icono y colores' (quitar tilde si la hubiera)
    // En este caso 'icono' no lleva tilde.
    // Aunque el comentario decía 'ícono', la palabra 'icono' es ASCII.
    // No hay cambios necesarios aqui.
    if (type === 'success') {
        successIcon.classList.remove('hidden');
        errorIcon.classList.add('hidden');
        // Opcional: Cambiar colores de boton o fondo si deseas algo dinamico
        // closeButton.classList.remove('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
        // closeButton.classList.add('bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500');
    } else { // type === 'error'
        successIcon.classList.add('hidden');
        errorIcon.classList.remove('hidden');
        // closeButton.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500');
        // closeButton.classList.add('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
    }

    // Mostrar modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('modal-open'); // Bloquea el scroll

    // CAMBIO: 'Lógica para cerrar el modal' a 'Logica para cerrar el modal'
    const closeHandler = () => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.classList.remove('modal-open'); // Restaura el scroll
        closeButton.removeEventListener('click', closeHandler); // Limpia el listener
        // CAMBIO: 'Limpia el temporizador si se cierra manualmente' a 'Limpia el temporizador si se cierra manualmente'
        // 'Limpia el temporizador' no tiene tildes.
        // No hay cambios necesarios aqui.
        clearTimeout(autoHideTimer); // Limpia el temporizador si se cierra manualmente
    };
    closeButton.addEventListener('click', closeHandler);

    // CAMBIO: 'Cierra el modal automáticamente después de 3 segundos' a 'Cierra el modal automaticamente despues de 3 segundos'
    const autoHideTimer = setTimeout(() => {
        if (!modal.classList.contains('hidden')) { // Solo cierra si aun esta visible
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.classList.remove('modal-open');
            closeButton.removeEventListener('click', closeHandler);
        }
    }, 3000); // 3 segundos
};

// CAMBIO: 'Lógica para detectar mensajes flash de Laravel al cargar la página' a 'Logica para detectar mensajes flash de Laravel al cargar la pagina'
document.addEventListener('DOMContentLoaded', function() {
    const successFlashMessageDiv = document.getElementById('flash-success-message-data');
    const errorFlashMessageDiv = document.getElementById('flash-error-message-data');

    if (successFlashMessageDiv) {
        const message = successFlashMessageDiv.dataset.message;
        if (message) {
            window.showGlobalMessage('success', message);
            // CAMBIO: 'Opcional: Eliminar el div después de leerlo para evitar mostrarlo de nuevo en SPA' a 'Opcional: Eliminar el div despues de leerlo para evitar mostrarlo de nuevo en SPA'
            successFlashMessageDiv.remove();
        }
    } else if (errorFlashMessageDiv) {
        const message = errorFlashMessageDiv.dataset.message;
        if (message) {
            window.showGlobalMessage('error', message);
            errorFlashMessageDiv.remove();
        }
    }

    // CAMBIO: 'Opcional: Cerrar modal haciendo clic fuera del contenido' a 'Opcional: Cerrar modal haciendo clic fuera del contenido'
    // 'clic' no tiene tilde.
    // No hay cambios necesarios aqui.
    const globalMessageModalVanilla = document.getElementById('globalMessageModalVanilla');
    if (globalMessageModalVanilla) {
        globalMessageModalVanilla.addEventListener('click', function(event) {
            // CAMBIO: 'Clic en el fondo del modal' a 'Clic en el fondo del modal'
            // 'Clic' no tiene tilde.
            // No hay cambios necesarios aqui.
            if (event.target === globalMessageModalVanilla) { // Clic en el fondo del modal
                // Dispara el clic del boton de cerrar para usar la logica de cierre existente
                document.getElementById('globalMessageCloseButton')?.click();
            }
        });
    }

    // CAMBIO: 'Opcional: Cerrar modal con la tecla ESC' a 'Opcional: Cerrar modal con la tecla ESC'
    // 'tecla' no tiene tilde.
    // No hay cambios necesarios aqui.
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const globalMessageModalVanilla = document.getElementById('globalMessageModalVanilla');
            if (globalMessageModalVanilla && !globalMessageModalVanilla.classList.contains('hidden')) {
                // Dispara el clic del boton de cerrar para usar la logica de cierre existente
                document.getElementById('globalMessageCloseButton')?.click();
            }
        }
    });
});