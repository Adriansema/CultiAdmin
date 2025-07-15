/**
 * Limpia los mensajes de error de validacion y los bordes rojos de los campos de un formulario.
 * @param {string|number} boletinId - El ID del boletin cuyo formulario se va a limpiar.
 */
window.clearValidationErrors = function (boletinId) {
    const form = document.getElementById(`editBoletinForm-${boletinId}`);
    if (form) {
        const errorDivs = form.querySelectorAll(`[id$="_error_${boletinId}"]`);
        errorDivs.forEach(div => {
            div.textContent = '';
        });
        const inputFields = form.querySelectorAll('input, textarea, select');
        inputFields.forEach(input => {
            input.classList.remove('border-red-500');
        });
    }
};

/**
 * Muestra los mensajes de error de validacion en el formulario del modal.
 * @param {string|number} boletinId - El ID del boletin cuyo formulario mostrara los errores.
 * @param {object} errors - Un objeto con los errores de validacion, donde la clave es el nombre del campo.
 */
window.displayValidationErrors = function (boletinId, errors) {
    window.clearValidationErrors(boletinId);
    const form = document.getElementById(`editBoletinForm-${boletinId}`);
    if (form) {
        for (const field in errors) {
            const errorDiv = document.getElementById(`edit_${field}_error_${boletinId}`);
            if (errorDiv) {
                errorDiv.textContent = errors[field][0];
                const inputField = form.querySelector(`[name="${field}"]`);
                if (inputField) {
                    inputField.classList.add('border-red-500');
                }
            }
        }
    }
};

// Listener principal para el envio de formularios de edicion de boletines
document.addEventListener('DOMContentLoaded', function () {
    // Obtener el token CSRF una vez al cargar la pagina
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';

    // Delegacion de eventos para formularios de edicion de boletines
    document.addEventListener('submit', async function (event) {
        const form = event.target.closest('form[id^="editBoletinForm-"]'); // Solo captura formularios de edicion de boletines
        if (!form) return;

        event.preventDefault();

        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            let originalButtonContent = submitButton.innerHTML;
            let loadingText = 'Actualizando...'; // Siempre actualizando para este script

            submitButton.innerHTML = `
                <span class="flex items-center justify-center w-full">
                    <span>${loadingText}</span>
                    <img src="./images/cargando_.svg" alt="Cargando..." class="w-5 h-5 ml-2 animate-spin">
                </span>
            `;
            submitButton.dataset.originalContent = originalButtonContent;
        }

        const entityId = form.id.split('-')[1]; // Obtiene el ID del boletin desde el ID del formulario
        const typeAction = 'editar'; // La accion es siempre 'editar' para este script
        const modalIdPrefix = typeAction; // 'editar'

        try {
            const formData = new FormData(form);
            // Para edicion, siempre enviamos como POST y añadimos _method=PUT
            const requestMethod = 'POST';
            formData.append('_method', 'PUT'); // Añade el campo oculto para simular PUT

            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            const response = await fetch(form.action, {
                method: requestMethod,
                body: formData,
                headers: headers
            });

            const result = await response.json();

            if (response.ok) {
                // Cierra el modal de edicion especifico
                window.cerrarModal(modalIdPrefix, entityId); // Llama a la funcion de TypeModals.js

                // Muestra el mensaje global de exito
                const successMessage = result.message || 'Boletín actualizado con éxito.';
                window.showGlobalMessage('success', successMessage); // Llama a la funcion de ModalesGenerales.js

                setTimeout(() => {
                   window.location.reload();
                }, 1500);

            } else if (response.status === 422) {
                // Muestra errores de validacion en el formulario
                if (typeof window.displayValidationErrors === 'function') {
                    window.displayValidationErrors(entityId, result.errors); // Llama a la funcion de este mismo script
                }
                window.showGlobalMessage('error', result.message || 'Por favor, corrige los errores en el formulario.'); // Llama a la funcion de ModalesGenerales.js
            } else {
                // Muestra un error inesperado
                window.showGlobalMessage('error', result.message || `Ocurrio un error inesperado al actualizar el boletin.`); // Llama a la funcion de ModalesGenerales.js
            }
        } catch (error) {
            window.showGlobalMessage('error', 'Error de red o conexion al servidor. Intentalo de nuevo.'); // Llama a la funcion de ModalesGenerales.js
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                if (submitButton.dataset.originalContent) {
                    submitButton.innerHTML = submitButton.dataset.originalContent;
                } else {
                    submitButton.innerHTML = `<span class="whitespace-nowrap text-inherit">Guardar Cambios</span>`;
                }
            }
        }
    });
});
