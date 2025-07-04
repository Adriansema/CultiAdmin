// public/js/ModalesGeneral.js

/**
 * Muestra un mensaje global de éxito o error utilizando un modal vanilla JS.
 * Este modal tiene su propio HTML y lógica de aparición/desaparición.
 * @param {string} type - El tipo de mensaje ('success' o 'error').
 * @param {string} message - El mensaje a mostrar.
 */
window.showGlobalMessage = function (type, message) {
    console.log(`--- showGlobalMessage llamada: Tipo=${type}, Mensaje="${message}" ---`);

    const modal = document.getElementById('globalMessageModalVanilla');
    const messageText = document.getElementById('globalMessageText');
    const successIcon = document.getElementById('globalMessageSuccessIcon');
    const errorIcon = document.getElementById('globalMessageErrorIcon');
    const closeButton = document.getElementById('globalMessageCloseButton');

    if (!modal || !messageText || !successIcon || !errorIcon || !closeButton) {
        console.error('ERROR: Elementos del modal de mensaje global no encontrados. Mostrando alert de fallback.');
        alert(type === 'error' ? `Error: ${message}` : `Éxito: ${message}`);
        return;
    }

    messageText.textContent = message;

    // Configurar icono y colores
    if (type === 'success') {
        successIcon.classList.remove('hidden');
        errorIcon.classList.add('hidden');
        // Opcional: Cambiar colores de botón o fondo si deseas algo dinámico
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

    // Lógica para cerrar el modal
    const closeHandler = () => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.classList.remove('modal-open'); // Restaura el scroll
        closeButton.removeEventListener('click', closeHandler); // Limpia el listener
        clearTimeout(autoHideTimer); // Limpia el temporizador si se cierra manualmente
        console.log('DEBUG: Modal de mensaje global cerrado manualmente.');
    };
    closeButton.addEventListener('click', closeHandler);

    // Cierra el modal automáticamente después de 3 segundos
    const autoHideTimer = setTimeout(() => {
        if (!modal.classList.contains('hidden')) { // Solo cierra si aún está visible
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.classList.remove('modal-open');
            closeButton.removeEventListener('click', closeHandler);
            console.log('DEBUG: Modal de mensaje global cerrado automáticamente.');
        }
    }, 3000); // 3 segundos
};

// Lógica para detectar mensajes flash de Laravel al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const successFlashMessageDiv = document.getElementById('flash-success-message-data');
    const errorFlashMessageDiv = document.getElementById('flash-error-message-data');

    if (successFlashMessageDiv) {
        const message = successFlashMessageDiv.dataset.message;
        if (message) {
            window.showGlobalMessage('success', message);
            // Opcional: Eliminar el div después de leerlo para evitar mostrarlo de nuevo en SPA
            successFlashMessageDiv.remove();
        }
    } else if (errorFlashMessageDiv) {
        const message = errorFlashMessageDiv.dataset.message;
        if (message) {
            window.showGlobalMessage('error', message);
            errorFlashMessageDiv.remove();
        }
    }

    // Opcional: Cerrar modal haciendo clic fuera del contenido
    const globalMessageModalVanilla = document.getElementById('globalMessageModalVanilla');
    if (globalMessageModalVanilla) {
        globalMessageModalVanilla.addEventListener('click', function(event) {
            if (event.target === globalMessageModalVanilla) { // Clic en el fondo del modal
                // Dispara el clic del botón de cerrar para usar la lógica de cierre existente
                document.getElementById('globalMessageCloseButton')?.click();
            }
        });
    }

    // Opcional: Cerrar modal con la tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const globalMessageModalVanilla = document.getElementById('globalMessageModalVanilla');
            if (globalMessageModalVanilla && !globalMessageModalVanilla.classList.contains('hidden')) {
                // Dispara el clic del botón de cerrar para usar la lógica de cierre existente
                document.getElementById('globalMessageCloseButton')?.click();
            }
        }
    });
});