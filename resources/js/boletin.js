window.mostrarModal = function (type, id) {
    console.log(`--- Función mostrarModal llamada: tipo=${type}, id=${id} ---`);

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
        document.body.classList.add('modal-open'); // Añadir para bloquear scroll
        console.log(`Mostrando modal: modal-${type}-${id}`);
    } else {
        console.warn(`Advertencia: Modal con ID modal-${type}-${id} no encontrado.`);
    }
};

window.cerrarModal = function (type, id) {
    console.log(`--- Función cerrarModal llamada: tipo=${type}, id=${id} ---`);
    const modal = document.getElementById(`modal-${type}-${id}`);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('modal-open'); // Remover para restaurar scroll
        console.log(`Ocultando modal: modal-${type}-${id}`);
        if (type === 'editar') {
            window.clearValidationErrors(id);
        }
        // NO MANIPULAMOS document.body.style.overflow aquí, usamos la clase 'modal-open'
    } else {
        console.warn(`Advertencia: Modal con ID modal-${type}-${id} no encontrado para cerrar.`);
    }
};

// --- Funciones de Validación (Se mantienen, ya que no están relacionadas con el modal de éxito) ---

/**
 * Limpia los mensajes de error de validación y los bordes rojos de los campos de un formulario.
 * @param {string|number} boletinId - El ID del boletín cuyo formulario se va a limpiar.
 */
window.clearValidationErrors = function (boletinId) {
    console.log(`--- Función clearValidationErrors llamada para boletín ID: ${boletinId} ---`);
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
 * Muestra los mensajes de error de validación en el formulario del modal.
 * @param {string|number} boletinId - El ID del boletín cuyo formulario mostrará los errores.
 * @param {object} errors - Un objeto con los errores de validación, donde la clave es el nombre del campo.
 */
window.displayValidationErrors = function (boletinId, errors) {
    console.log(`--- Función displayValidationErrors llamada para boletín ID: ${boletinId} ---`);
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

// --- Función de Mensaje Global (Se mantiene, es tu nuevo enfoque para mensajes de éxito/error) ---

/**
 * Muestra un mensaje global de éxito o error utilizando un modal vanilla JS.
 * @param {string} type - El tipo de mensaje ('success' o 'error').
 * @param {string} message - El mensaje a mostrar.
 */
window.showGlobalMessage = function (type, message) {
    console.log(`--- Función showGlobalMessage llamada (Vanilla JS): Tipo=${type}, Mensaje="${message}" ---`);

    const modal = document.getElementById('globalMessageModalVanilla');
    const messageText = document.getElementById('globalMessageText');
    const successIcon = document.getElementById('globalMessageSuccessIcon');
    const errorIcon = document.getElementById('globalMessageErrorIcon');
    const closeButton = document.getElementById('globalMessageCloseButton');

    if (!modal || !messageText || !successIcon || !errorIcon || !closeButton) {
        console.error('ERROR: Elementos del modal de mensaje global vanilla no encontrados. Mostrando alert de fallback.');
        alert(type === 'error' ? `Error: ${message}` : `Éxito: ${message}`);
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

    // Cierra el modal al hacer clic en el botón OK
    const closeHandler = () => {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.classList.remove('modal-open'); // Restaura el scroll
        closeButton.removeEventListener('click', closeHandler);
        clearTimeout(autoHideTimer);
        console.log('DEBUG: Modal de mensaje global cerrado manualmente.');
    };
    closeButton.addEventListener('click', closeHandler);

    // Cierra el modal automáticamente después de 3 segundos
    const autoHideTimer = setTimeout(() => {
        if (!modal.classList.contains('hidden')) {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.classList.remove('modal-open');
            closeButton.removeEventListener('click', closeHandler);
            console.log('DEBUG: Modal de mensaje global cerrado automáticamente.');
        }
    }, 3000);
};


document.addEventListener('DOMContentLoaded', function () {
    console.log('--- DOMContentLoaded event fired: Script loaded and ready ---');

    // Delegación de eventos para los botones de acción en la tabla (se mantiene)
    const tableBody = document.querySelector('#boletines-table-body');
    if (tableBody) {
        console.log('Event listener añadido a #boletines-table-body para delegación de eventos.');
        tableBody.addEventListener('click', function (event) {
            console.log('Click detectado en la tabla.');
            const targetButton = event.target.closest('button[onclick^="mostrarModal"]');
            if (targetButton) {
                const onclickAttr = targetButton.getAttribute('onclick');
                const match = onclickAttr.match(/mostrarModal\('([^']+)', '([^']+)'\)/);
                if (match && match.length === 3) {
                    const type = match[1];
                    const id = match[2];
                    console.log(`Botón de acción clicado: Tipo=${type}, ID=${id}`);
                    if (type === 'boletin') {
                        console.warn(`ADVERTENCIA: La acción 'boletin' (Eliminar) no está manejada en este script.`);
                        return;
                    } else {
                        // Aquí se llama a mostrarModal. Asegúrate de que los modales que usa (ej. 'editar')
                        // no son el modal de éxito de noticias si quieres que lo maneje el global.
                        window.mostrarModal(type, id);
                    }
                } else {
                    console.warn('No se pudieron extraer los argumentos de mostrarModal del atributo onclick.');
                }
            } else {
                console.log('Click no fue en un botón de acción de tabla.');
            }
        });
    } else {
        console.warn('Advertencia: #boletines-table-body no encontrado. La delegación de eventos de la tabla no funcionará.');
    }

    // Listener para los formularios de edición (se mantiene)
    document.querySelectorAll('[id^="editBoletinForm-"]').forEach(form => {
        console.log(`Añadiendo event listener de submit al formulario: ${form.id}`);
        form.addEventListener('submit', async function (event) {
            console.log('--- Submit de formulario detectado ---');
            event.preventDefault();

            const formId = this.id;
            const boletinId = formId.split('-')[1];
            const formData = new FormData(this);

            console.log(`Boletín ID para actualización: ${boletinId}`);

            const updateButton = this.querySelector('button[type="submit"]');
            if (updateButton) {
                updateButton.disabled = true;
                updateButton.textContent = 'Actualizando...';
                console.log('Botón de actualización deshabilitado.');
            }

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                console.log('--- Fetch Response recibido ---');
                console.log('Response Status:', response.status);

                const result = await response.json();

                if (response.ok) {
                    console.log('--- Fetch Data Procesado (Éxito) ---');
                    console.log('Boletín actualizado con éxito:', result);
                    console.log('HTML recibido para depuración:', result.html_row);

                    if (result.html_row) {
                        const oldRow = document.getElementById(`boletin-row-${boletinId}`);
                        console.log('Buscando oldRow con ID:', `boletin-row-${boletinId}`);
                        console.log('oldRow encontrado:', oldRow);

                        if (oldRow) {
                            oldRow.outerHTML = result.html_row;
                            console.log('Fila reemplazada en el DOM.');
                            reindexTableRows();
                            console.log('reindexTableRows llamado.');
                        } else {
                            console.error(`Error: oldRow con ID boletin-row-${boletinId} no encontrado para reemplazar.`);
                            window.showGlobalMessage('error', 'Boletín actualizado, pero la tabla no se pudo refrescar. Recargue la página.');
                        }
                    } else {
                        console.warn('Advertencia: result.html_row no presente en la respuesta del servidor.');
                        window.showGlobalMessage('success', 'Boletín actualizado, pero no se recibió HTML para refrescar la tabla. Recargue la página.');
                    }

                    window.cerrarModal('editar', boletinId); // Cierra el modal de edición (esto se mantiene)

                    // Después de la actualización exitosa, mostramos directamente el mensaje global de éxito
                    window.showGlobalMessage('success', result.message || 'Boletín actualizado con éxito.');

                } else if (response.status === 422) {
                    console.error('--- Errores de validación (422) ---');
                    console.error('Errores:', result.errors);
                    window.displayValidationErrors(boletinId, result.errors);
                    window.showGlobalMessage('error', result.message || 'Por favor, corrige los errores en el formulario.');
                } else {
                    console.error('--- Error HTTP (no 2xx ni 422) ---');
                    console.error('Error en la respuesta del servidor:', result);
                    window.showGlobalMessage('error', result.message || 'Ocurrió un error inesperado al actualizar el boletín.');
                }
            } catch (error) {
                console.error('--- Fetch Catch (Error de red/parsing) ---');
                console.error('Error al actualizar el boletín:', error);
                window.showGlobalMessage('error', 'Error de red o conexión al servidor. Inténtalo de nuevo.');
            } finally {
                if (updateButton) {
                    updateButton.disabled = false;
                    updateButton.textContent = 'Guardar Cambios';
                    console.log('Botón de actualización re-habilitado.');
                }
            }
        });
    });

    function reindexTableRows() {
        console.log('--- Función reindexTableRows llamada ---');
        const tableBody = document.querySelector('#boletines-table-body');
        if (tableBody) {
            const rows = tableBody.querySelectorAll('tr[id^="boletin-row-"]');
            console.log(`Encontradas ${rows.length} filas para re-indexar.`);
            rows.forEach((row, index) => {
                const orderNumberCell = row.querySelector('.boletin-order-number');
                if (orderNumberCell) {
                    orderNumberCell.textContent = index + 1;
                    console.log(`Fila ${row.id} re-indexada a ${index + 1}.`);
                } else {
                    console.log(`Celda de número de orden no encontrada para fila ${row.id}.`);
                }
            });
        } else {
            console.warn('Advertencia: tbody con ID #boletines-table-body no encontrado para reindexTableRows.');
        }
    }

    reindexTableRows();

    // Listener para cerrar modales por click externo/botón de cierre
    // SE MODIFICA PARA EXCLUIR EL MODAL DE ÉXITO DE NOTICIAS,
    // YA QUE AHORA SE ESPERA QUE showGlobalMessage LO MANEJE
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('bg-opacity-50') && event.target.closest('[id^="modal-"]')) {
            const modalWrapper = event.target.closest('[id^="modal-"]');
            // Excluir el modal de éxito de noticias y el modal global de showGlobalMessage
            // Asumiendo que el modal de éxito de noticias ya no usa "modal-success-"
            // o que showGlobalMessage() ya maneja su cierre.
            if (modalWrapper &&
                modalWrapper.id !== 'custom-confirm-modal' &&
                modalWrapper.id !== 'createBoletinModal' &&
                modalWrapper.id !== 'modal-success-' && // Excluimos explícitamente si aún existe
                modalWrapper.id !== 'globalMessageModalVanilla') { // Excluimos el modal global
                const idParts = modalWrapper.id.split('-');
                const tipo = idParts[1];
                const id = idParts[2];
                window.cerrarModal(tipo, id);
                console.log(`Cerrando modal por click externo/botón de cierre: ${modalWrapper.id}`);
            }
        }
    });
});

window.openCreateBoletinModal = window.openCreateBoletinModalVanilla;