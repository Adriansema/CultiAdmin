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
        alert(type === 'error' ? `Error: ${message}` : `Exito: ${message}`);
        return;
    }

    messageText.textContent = message;

    if (type === 'success') {
        successIcon.classList.remove('hidden');
        errorIcon.classList.add('hidden');
    } else { // type === 'error'
        successIcon.classList.add('hidden');
        errorIcon.classList.remove('hidden');
    }

    // Mostrar modal
    modal.classList.remove('hidden');
    modal.classList.add('flex'); // Aseguramos que 'flex' se añada para display

    document.body.classList.add('modal-open'); // Bloquea el scroll

    const closeHandler = () => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.classList.remove('modal-open'); // Restaura el scroll
        closeButton.removeEventListener('click', closeHandler); // Limpia el listener
        clearTimeout(autoHideTimer); // Limpia el temporizador si se cierra manualmente
    };
    closeButton.addEventListener('click', closeHandler);

    const autoHideTimer = setTimeout(() => {
        if (!modal.classList.contains('hidden')) { // Solo cierra si aun esta visible
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.classList.remove('modal-open');
            closeButton.removeEventListener('click', closeHandler);
        }
    }, 3000); // 3 segundos
};

// Logica para detectar mensajes flash de Laravel al cargar la pagina
document.addEventListener('DOMContentLoaded', function () {
    const successFlashMessageDiv = document.getElementById('flash-success-message-data');
    const errorFlashMessageDiv = document.getElementById('flash-error-message-data');

    if (successFlashMessageDiv) {
        const message = successFlashMessageDiv.dataset.message;
        if (message) {
            window.showGlobalMessage('success', message);
            successFlashMessageDiv.remove(); // Eliminar el div despues de leerlo
        }
    } else if (errorFlashMessageDiv) {
        const message = errorFlashMessageDiv.dataset.message;
        if (message) {
            window.showGlobalMessage('error', message);
            errorFlashMessageDiv.remove(); // Eliminar el div despues de leerlo
        }
    }

    // Opcional: Cerrar modal global haciendo clic fuera del contenido
    const globalMessageModalVanilla = document.getElementById('globalMessageModalVanilla');
    if (globalMessageModalVanilla) {
        globalMessageModalVanilla.addEventListener('click', function (event) {
            if (event.target === globalMessageModalVanilla) { // Clic en el fondo del modal
                document.getElementById('globalMessageCloseButton')?.click(); // Dispara el clic del boton de cerrar
            }
        });
    }

    // Opcional: Cerrar modal global con la tecla ESC
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            const globalMessageModalVanilla = document.getElementById('globalMessageModalVanilla');
            if (globalMessageModalVanilla && !globalMessageModalVanilla.classList.contains('hidden')) {
                document.getElementById('globalMessageCloseButton')?.click(); // Dispara el clic del boton de cerrar
            }
        }
    });
});
