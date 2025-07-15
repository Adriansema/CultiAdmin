/**
 * Abre un modal especifico.
 * @param {string} type - El tipo de modal (ej. 'validar-noticia', 'editar', 'boletin').
 * @param {string|number} id - El ID de la entidad asociada al modal.
 */
window.mostrarModal = function (type, id) {
    // Cierra todos los modales del mismo tipo antes de abrir el nuevo
    document.querySelectorAll(`[id^="modal-${type}-"]`).forEach(m => {
        if (m.id !== `modal-${type}-${id}`) {
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
    });

    const modal = document.getElementById(`modal-${type}-${id}`);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('modal-open');
    } else {

    }
};

/**
 * Cierra un modal especifico.
 * @param {string} type - El tipo de modal (ej. 'validar-noticia', 'editar', 'boletin').
 * @param {string|number} id - El ID de la entidad asociada al modal.
 */
window.cerrarModal = function (type, id) {
    const targetModalId = `modal-${type}-${id}`;
    const modal = document.getElementById(targetModalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');

        const openModals = document.querySelectorAll('.fixed.inset-0.z-50.flex:not(.hidden)');
        const actualOpenModals = Array.from(openModals).filter(m =>
            m.id !== 'globalMessageModalVanilla' &&
            m.id !== 'createBoletinModal' &&
            m.id !== 'custom-confirm-modal'
        );

        if (actualOpenModals.length === 0) {
            document.body.classList.remove('modal-open');
        }
    } else {

    }
};

document.addEventListener('DOMContentLoaded', function () {
    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';

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
                    window.mostrarModal(type, id);
                }
            }
        });
    }

    document.addEventListener('submit', async function (event) {
        const form = event.target.closest('form');
        if (!form) return;

        /* console.log('Form Action URL:', form.action); */

        let isHandledForm = false;
        let typeAction = '';
        let entityType = '';
        let entityId = null;
        let modalIdPrefix = '';

        const validationRejectRegex = /\/pendiente\/(boletines|noticias|productos|noticia|producto)\/(\d+)\/(validar|rechazar)$/;
        const validationRejectMatch = form.action.match(validationRejectRegex);

        const deleteRegex = /\/(boletines|noticias|productos|noticia|producto)\/(\d+)$/;
        const deleteMatch = form.action.match(deleteRegex);

        if (validationRejectMatch) {
            isHandledForm = true;
            const rawEntityType = validationRejectMatch[1];
            entityType = rawEntityType; // Mantener como 'noticias', 'productos', 'boletines'
            entityId = validationRejectMatch[2];
            typeAction = validationRejectMatch[3];
            modalIdPrefix = `${typeAction}-${entityType}`; // Esto resultará en 'validar-noticias'

        } else if (deleteMatch && form.method.toLowerCase() === 'post' && form.querySelector('input[name="_method"][value="DELETE"]')) {
            isHandledForm = true;
            const rawEntityType = deleteMatch[1];
            entityType = rawEntityType; // Mantener como 'noticias', 'productos', 'boletines'
            typeAction = 'eliminar';
            entityId = deleteMatch[2];
            modalIdPrefix = entityType; // Esto resultará en 'noticias' (si es de noticias)
        }

        if (!isHandledForm) {
            return;
        }

        event.preventDefault();

        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            let originalButtonContent = submitButton.innerHTML;
            let loadingText = '';

            switch (typeAction) {
                case 'validar':
                    loadingText = 'Validando...';
                    break;
                case 'rechazar':
                    loadingText = 'Rechazando...';
                    break;
                case 'eliminar':
                    loadingText = 'Eliminando...';
                    break;
                default:
                    loadingText = 'Procesando...';
            }

            submitButton.innerHTML = `
                <span class="flex items-center justify-center w-full">
                    <span>${loadingText}</span>
                    <img src="./images/cargando_.svg" alt="Cargando..." class="w-5 h-5 ml-2 animate-spin">
                </span>
            `;
            submitButton.dataset.originalContent = originalButtonContent;
        }

        try {
            const formData = new FormData(form);
            const requestMethod = 'POST';

            if (typeAction === 'eliminar') {
                formData.append('_method', 'DELETE');
            }

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
                window.cerrarModal(modalIdPrefix, entityId); // Llama a la funcion de este mismo script

                // Muestra el mensaje global de exito
                let successMessage = result.message || '';
                if (!successMessage) {
                    const capitalizedEntityType = entityType.charAt(0).toUpperCase() + entityType.slice(1);
                    if (typeAction === 'validar') successMessage = `${capitalizedEntityType} aprobado con exito.`;
                    else if (typeAction === 'rechazar') successMessage = `${capitalizedEntityType} rechazado con exito.`;
                    else if (typeAction === 'eliminar') successMessage = `${capitalizedEntityType} eliminado con exito.`;
                }
                window.showGlobalMessage('success', successMessage);

                setTimeout(() => {
                    window.location.reload();
                }, 1500);

            } else if (response.status === 422) {
                window.showGlobalMessage('error', result.message || 'Por favor, corrige los errores en el formulario.');
            } else {
                const capitalizedEntityType = entityType.charAt(0).toUpperCase() + entityType.slice(1);
                window.showGlobalMessage('error', result.message || `Ocurrio un error inesperado al ${typeAction} el ${capitalizedEntityType}.`);
            }
        } catch (error) {
            window.showGlobalMessage('error', 'Error de red o conexion al servidor. Intentalo de nuevo.');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                if (submitButton.dataset.originalContent) {
                    submitButton.innerHTML = submitButton.dataset.originalContent;
                } else {
                    let defaultText = 'Enviar';
                    let imgSrc = ``;

                    if (typeAction === 'validar') {
                        defaultText = 'Validar';
                        imgSrc = `src="./images/siguiente.svg" alt="siguiente" class="w-5 h-6 ml-2"`;
                    } else if (typeAction === 'rechazar') {
                        defaultText = 'Rechazar';
                        imgSrc = `src="./images/siguiente.svg" alt="siguiente" class="w-5 h-6 ml-2"`;
                    } else if (typeAction === 'eliminar') {
                        defaultText = 'Eliminar';
                        imgSrc = `src="./images/siguiente.svg" alt="siguiente" class="w-5 h-6 ml-2"`;
                    }

                    submitButton.innerHTML = `<span class="whitespace-nowrap text-inherit">${defaultText}</span>
                                            ${imgSrc ? `<img ${imgSrc}>` : ''}`;
                }
            }
        }
    });

    // Función auxiliar para obtener el tipo e ID del modal a cerrar
    function getModalTypeAndIdFromElement(modalElement) {
        const idParts = modalElement.id.split('-');
        let typeToClose = '';
        let idToClose = '';

        // Excluir modales globales y otros que no maneja este script
        if (modalElement.id === 'globalMessageModalVanilla' ||
            modalElement.id === 'createBoletinModal' ||
            modalElement.id === 'custom-confirm-modal') {
            return null;
        }

        // Determinar el tipo y ID basado en la estructura del ID
        if (idParts.length === 4 && (idParts[1] === 'validar' || idParts[1] === 'rechazar')) {
            typeToClose = `${idParts[1]}-${idParts[2]}`; // Mantener como 'validar-noticias'
            idToClose = idParts[3];
        } else if (idParts.length === 3 && idParts[0] === 'modal' && (idParts[1] === 'boletines' || idParts[1] === 'noticias' || idParts[1] === 'productos')) {
            typeToClose = idParts[1]; // Mantener como 'noticias'
            idToClose = idParts[2];
        } else if (idParts.length >= 3 && idParts[1] === 'editar') {
            typeToClose = idParts[1]; // Mantener como 'editar'
            idToClose = idParts[2];
        } else {
            return null;
        }
        return { type: typeToClose, id: idToClose };
    }

    // Listener para cerrar modales especificos por click externo
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('bg-black/50') && event.target.closest('[id^="modal-"]')) {
            const modal = event.target.closest('[id^="modal-"]');
            const modalInfo = getModalTypeAndIdFromElement(modal);

            if (modalInfo) {
                window.cerrarModal(modalInfo.type, modalInfo.id);
            }
        }
    });

    // Listener para cerrar modales especificos con la tecla Escape
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            const openModals = document.querySelectorAll('.fixed.inset-0.z-50.flex:not(.hidden)');
            for (let i = openModals.length - 1; i >= 0; i--) {
                const modal = openModals[i];
                const modalInfo = getModalTypeAndIdFromElement(modal);

                if (modalInfo) {
                    window.cerrarModal(modalInfo.type, modalInfo.id);
                    break;
                }
            }
        }
    });
});
