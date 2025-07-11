window.mostrarModal = function (type, id) {
    console.log(`--- Funcion mostrarModal llamada: tipo=${type}, id=${id} ---`);

    // Cierra todos los modales del mismo tipo antes de abrir el nuevo
    document.querySelectorAll(`[id^="modal-${type}-"]`).forEach(m => {
        if (m.id !== `modal-${type}-${id}`) {
            m.classList.add('hidden');
            m.classList.remove('flex');
            console.log(`Ocultando modal anterior: ${m.id}`);
        }
    });

    const modal = document.getElementById(`modal-${type}-${id}`);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('modal-open'); // Anadir para bloquear scroll
        console.log(`Mostrando modal: modal-${type}-${id}`);
    } else {
        console.warn(`Advertencia: Modal con ID modal-${type}-${id} no encontrado.`);
    }
};

window.cerrarModal = function (type, id) {
    console.log(`--- Funcion cerrarModal llamada: tipo=${type}, id=${id} ---`);
    const modal = document.getElementById(`modal-${type}-${id}`);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        // Comprobar si hay otros modales abiertos antes de quitar modal-open
        const openModals = document.querySelectorAll('.modal-overlay.flex:not(.hidden)');
        if (openModals.length === 0) {
            document.body.classList.remove('modal-open'); // Remover para restaurar scroll
        }
        console.log(`Ocultando modal: modal-${type}-${id}`);
        if (type === 'editar') {
            window.clearValidationErrors(id); // Limpia errores de validacion al cerrar modal de edicion
        }
    } else {
        console.warn(`Advertencia: Modal con ID modal-${type}-${id} no encontrado para cerrar.`);
    }
};

// --- Funciones de Validacion (Se mantienen) ---

/**
 * Limpia los mensajes de error de validacion y los bordes rojos de los campos de un formulario.
 * @param {string|number} boletinId - El ID del boletin cuyo formulario se va a limpiar.
 */
window.clearValidationErrors = function (boletinId) {
    console.log(`--- Funcion clearValidationErrors llamada para boletin ID: ${boletinId} ---`);
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
    window.clearValidationErrors(boletinId); // Limpia errores antes de mostrar los nuevos
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

// --- Funcion de Mensaje Global (Se mantiene, es tu nuevo enfoque para mensajes de exito/error) ---

/**
 * Muestra un mensaje global de exito o error utilizando un modal vanilla JS.
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

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('modal-open'); // Usa la clase para bloquear el scroll

    // Cierra el modal al hacer clic en el boton OK
    const closeHandler = () => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.classList.remove('modal-open'); // Restaura el scroll
        closeButton.removeEventListener('click', closeHandler);
        clearTimeout(autoHideTimer);
    };
    closeButton.addEventListener('click', closeHandler);

    // Cierra el modal automaticamente despues de 3 segundos
    const autoHideTimer = setTimeout(() => {
        if (!modal.classList.contains('hidden')) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.classList.remove('modal-open');
            closeButton.removeEventListener('click', closeHandler);
        }
    }, 3000);
};


document.addEventListener('DOMContentLoaded', function () {
    // Delegacion de eventos para los botones de accion en la tabla (se mantiene)
    const tableBody = document.querySelector('#boletines-table-body');
    if (tableBody) {
        tableBody.addEventListener('click', function (event) {
            const targetButton = event.target.closest('button[onclick^="mostrarModal"]');
            if (targetButton) {
                const onclickAttr = targetButton.getAttribute('onclick');
                const match = onclickAttr.match(/mostrarModal\('([^']+)', '([^']+)'\)/);
                if (match && match.length === 3) {
                    const type = match[1];
                    const id = match[2];
                    // Si el tipo es 'boletin', es para el modal 'ver', lo dejamos pasar.
                    // Si es 'editar', tambien lo dejamos pasar.
                    // Cualquier otro tipo que deba ser manejado por mostrarModal()
                    window.mostrarModal(type, id);
                }
            }
        });
    }

    // Listener para los formularios de edicion
    // Se delega a un contenedor si hay multiples formularios o se anaden dinamicamente.
    // En este caso, asumimos que estan presentes en el DOM al cargar.
    document.querySelectorAll('[id^="editBoletinForm-"]').forEach(form => {
        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const formId = this.id;
            const boletinId = formId.split('-')[1];
            const formData = new FormData(this);

            // Anadir metodo PUT para Laravel
            formData.append('_method', 'PUT');

            const updateButton = this.querySelector('button[type="submit"]');
            if (updateButton) {
                updateButton.disabled = true;
                updateButton.innerHTML = `
                    <span class="flex items-center justify-center w-full">
                        <span>Actualizando...</span>
                        <img src="./images/cargando_.svg" alt="Cargando..." class="w-5 h-5 ml-2 animate-spin">
                    </span>
                `;
            }

            try {
                const response = await fetch(this.action, {
                    method: 'POST', // Siempre POST para Laravel con _method PUT
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    window.cerrarModal('editar', boletinId); // Cierra el modal de edicion
                    window.showGlobalMessage('success', result.message || 'Boletin actualizado con exito.');

                    // *** CAMBIO CLAVE AQUI: Recargar la pagina completa despues de la actualizacion ***
                    setTimeout(() => {
                        window.location.reload(); // Recarga la pagina para refrescar todos los datos y la tabla
                    }, 1500); // Pequeno delay para que el usuario vea el mensaje de exito

                } else if (response.status === 422) {
                    window.displayValidationErrors(boletinId, result.errors);
                    window.showGlobalMessage('error', result.message || 'Por favor, corrige los errores en el formulario.');
                } else {
                    window.showGlobalMessage('error', result.message || 'Ocurrio un error inesperado al actualizar el boletin.');
                }
            } catch (error) {
                window.showGlobalMessage('error', 'Error de red o conexion al servidor. Intentalo de nuevo.');
                console.error('Fetch error:', error);
            } finally {
                if (updateButton) {
                    updateButton.disabled = false;
                    updateButton.textContent = 'Guardar Cambios'; // Restaura el texto original
                }
            }
        });
    });

    // Se elimina la funcion reindexTableRows y su llamada,
    // ya que la recarga de pagina la hace innecesaria.
    // function reindexTableRows() { /* ... */ }
    // reindexTableRows(); // Ya no se llama aqui.

    // Listener para cerrar modales por click externo/tecla Escape
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('bg-opacity-50') && event.target.closest('[id^="modal-"]')) {
            const modalWrapper = event.target.closest('[id^="modal-"]');
            // Excluimos los modales que tienen su propia logica de cierre (ej. globalMessageModalVanilla, createBoletinModal)
            if (modalWrapper &&
                modalWrapper.id !== 'globalMessageModalVanilla' &&
                modalWrapper.id !== 'createBoletinModal' &&
                modalWrapper.id !== 'custom-confirm-modal') // Asumiendo que 'custom-confirm-modal' tambien tiene su propia logica
            {
                const idParts = modalWrapper.id.split('-');
                const tipo = idParts[1];
                const id = idParts[2];
                window.cerrarModal(tipo, id);
            }
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal-overlay.flex:not(.hidden)');
            // Iterar en orden inverso para cerrar el modal mas "superior" primero
            for (let i = openModals.length - 1; i >= 0; i--) {
                const modal = openModals[i];
                // Excluimos los modales que tienen su propia logica de cierre o no deben cerrarse con Escape
                if (modal.id !== 'globalMessageModalVanilla' &&
                    modal.id !== 'createBoletinModal' &&
                    modal.id !== 'custom-confirm-modal')
                {
                    const idParts = modal.id.split('-');
                    const tipo = idParts[1];
                    const id = idParts[2];
                    window.cerrarModal(tipo, id);
                    break; // Cierra solo el modal mas alto
                }
            }
        }
    });
});

// Asegurarse de que la funcion global para abrir el modal este disponible
window.openCreateBoletinModal = window.openCreateBoletinModalVanilla;