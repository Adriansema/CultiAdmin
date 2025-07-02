document.addEventListener('DOMContentLoaded', function () {
    console.log('--- DOMContentLoaded event fired: Script loaded and ready ---');

    window.mostrarModal = function (tipo, id) {
        console.log(`--- Función mostrarModal llamada: tipo=${tipo}, id=${id} ---`);

        // Cierra todos los modales del mismo tipo antes de abrir el nuevo
        document.querySelectorAll(`[id^="modal-${tipo}-"]`).forEach(m => {
            if (m.id !== `modal-${tipo}-${id}`) {
                m.classList.add('hidden');
                console.log(`Ocultando modal anterior: ${m.id}`);
            }
        });

        const modal = document.getElementById(`modal-${tipo}-${id}`);
        if (modal) {
            modal.classList.remove('hidden');
            console.log(`Mostrando modal: modal-${tipo}-${id}`);
            // Opcional: Asegúrate de que el body no tenga overflow cuando un modal está abierto
            // document.body.style.overflow = 'hidden';
        } else {
            console.warn(`Advertencia: Modal con ID modal-${tipo}-${id} no encontrado.`);
        }
    }

    window.ocultarModal = function (tipo, id) {
        console.log(`--- Función ocultarModal llamada: tipo=${tipo}, id=${id} ---`);
        const modal = document.getElementById(`modal-${tipo}-${id}`);
        if (modal) {
            modal.classList.add('hidden');
            console.log(`Ocultando modal: modal-${tipo}-${id}`);
            // Opcional: Restaura el overflow del body cuando el modal se cierra
            // document.body.style.overflow = '';
        } else {
            console.warn(`Advertencia: Modal con ID modal-${tipo}-${id} no encontrado para ocultar.`);
        }
    }

    // Delegación de eventos para los botones de acción en la tabla (Asegúrate que tu tbody tenga este ID)
    const tableBody = document.querySelector('#boletines-table-body');
    if (tableBody) {
        console.log('Event listener añadido a #boletines-table-body para delegación de eventos.');
        tableBody.addEventListener('click', function (event) {
            console.log('Click detectado en la tabla.');
            const targetButton = event.target.closest('button[data-type]');
            if (targetButton) {
                const type = targetButton.dataset.type;
                const id = targetButton.dataset.id;
                console.log(`Botón de acción clicado: Tipo=${type}, ID=${id}`);

                if (type === 'ver' || type === 'editar' || type === 'eliminar') {
                    if (type === 'eliminar') {
                        mostrarModal('boletin', id); // Asumo que tu modal de eliminar es 'modal-boletin-ID'
                    } else {
                        mostrarModal(type, id);
                    }
                }
            } else {
                console.log('Click no fue en un botón de acción de tabla.');
            }
        });
    } else {
        console.warn('Advertencia: #boletines-table-body no encontrado. La delegación de eventos de la tabla no funcionará.');
    }


    document.querySelectorAll('[id^="form-boletin-"]').forEach(form => {
        console.log(`Añadiendo event listener de submit al formulario: ${form.id}`);
        form.addEventListener('submit', function (event) {
            console.log('--- Submit de formulario detectado ---');
            event.preventDefault();

            const formId = this.id;
            const boletinId = formId.split('-')[2];
            const formData = new FormData(this);

            console.log(`Boletín ID para actualización: ${boletinId}`);

            const updateButton = this.querySelector('button[type="submit"]');
            if (updateButton) {
                updateButton.disabled = true;
                updateButton.textContent = 'Actualizando...';
                console.log('Botón de actualización deshabilitado.');
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append('_token', csrfToken);
            console.log('CSRF token añadido a FormData.');

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                }
            })
                .then(response => {
                    console.log('--- Fetch Response recibido ---');
                    console.log('Response Status:', response.status);
                    if (!response.ok) {
                        return response.json().then(err => {
                            err.status = response.status;
                            console.error('Error en la respuesta del servidor (HTTP not OK):', err);
                            throw err;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('--- Fetch Data Procesado (Éxito) ---');
                    console.log('Boletín actualizado con éxito:', data);
                    console.log('HTML recibido para depuración:', data.html_row); // Mantén este log para verificar

                    if (data.html_row) {
                        const oldRow = document.getElementById(`boletin-row-${boletinId}`);
                        console.log('Buscando oldRow con ID:', `boletin-row-${boletinId}`);
                        console.log('oldRow encontrado:', oldRow);

                        if (oldRow) {
                            const tempDiv = document.createElement('div');
                            // Usamos trim() para eliminar cualquier espacio exterior (líneas vacías, etc.)
                            tempDiv.innerHTML = data.html_row.trim();
                            console.log('tempDiv.innerHTML establecido.');

                            // ***** NUEVO INTENTO: Verificar la estructura interna de tempDiv *****
                            console.log('Contenido de tempDiv (para inspección):', tempDiv.innerHTML);
                            // Opcional: debugger; aquí para inspeccionar tempDiv en el navegador

                            // Intentamos encontrar el TR directamente. Si el HTML está anidado incorrectamente,
                            // esto nos dará el TR más alto que sea descendiente.
                            const newRow = tempDiv.querySelector('tr');

                            console.log('Valor de newRow después de querySelector("tr"):', newRow);
                            console.log('tagName de newRow:', newRow ? newRow.tagName : 'null/undefined');

                            if (newRow && newRow.tagName === 'TR') {
                                oldRow.parentNode.replaceChild(newRow, oldRow);
                                console.log('Fila reemplazada en el DOM.');
                                reindexTableRows();
                                console.log('reindexTableRows llamado.');
                            } else {
                                console.error('El HTML de la fila devuelto no es un <tr> válido o no se encontró. HTML recibido (data.html_row):', data.html_row);
                                console.error('HTML parseado en tempDiv (tempDiv.innerHTML):', tempDiv.innerHTML); // Esto puede mostrar la diferencia
                                console.error('Primer hijo de tempDiv (tempDiv.children[0]):', tempDiv.children[0]); // Esto mostrará el SPAN
                            }
                        } else {
                            console.error(`Error: oldRow con ID boletin-row-${boletinId} no encontrado para reemplazar.`);
                        }
                    } else {
                        console.warn('Advertencia: data.html_row no presente en la respuesta del servidor.');
                    }

                    ocultarModal('editar', boletinId);
                    mostrarToast('success', data.message || 'Boletín actualizado.');

                    if (updateButton) {
                        updateButton.disabled = false;
                        updateButton.textContent = 'Actualizar';
                        console.log('Botón de actualización re-habilitado.');
                    }

                    form.querySelectorAll('.error-message').forEach(el => el.remove());
                    form.querySelectorAll('.text-red-500').forEach(el => el.remove());
                    console.log('Errores de validación anteriores limpiados.');
                })
                .catch(error => {
                    console.error('--- Fetch Catch (Error) ---');
                    console.error('Error al actualizar el boletín:', error);

                    mostrarModal('editar', boletinId);
                    console.log('Modal de edición reabierto por error.');

                    form.querySelectorAll('.error-message').forEach(el => el.remove());
                    form.querySelectorAll('.text-red-500').forEach(el => el.remove());
                    console.log('Errores de validación anteriores limpiados en Catch.');

                    if (error.errors) {
                        console.log('Manejando errores de validación del servidor...');
                        for (const field in error.errors) {
                            const inputElement = form.querySelector(`[name="${field}"]`);
                            if (inputElement) {
                                const errorMessage = document.createElement('p');
                                errorMessage.classList.add('mt-1', 'text-sm', 'text-red-500', 'error-message');
                                errorMessage.textContent = error.errors[field][0];
                                inputElement.parentNode.insertBefore(errorMessage, inputElement.nextSibling);
                                console.log(`Error para el campo ${field}: ${error.errors[field][0]}`);
                            } else {
                                console.warn(`Input para el campo ${field} no encontrado.`);
                            }
                        }
                    } else {
                        alert('Error: ' + (error.message || 'Ocurrió un error inesperado.'));
                        console.log('Alerta mostrada para error inesperado.');
                    }

                    if (updateButton) {
                        updateButton.disabled = false;
                        updateButton.textContent = 'Actualizar';
                        console.log('Botón de actualización re-habilitado en Catch.');
                    }
                });
        });
    });

    function reindexTableRows() {
        console.log('--- Función reindexTableRows llamada ---');
        const tableBody = document.querySelector('#boletines-table-body');
        if (tableBody) {
            const rows = tableBody.querySelectorAll('tr[id^="boletin-row-"]');
            console.log(`Encontradas ${rows.length} filas para re-indexar.`);
            rows.forEach((row, index) => {
                const orderNumberCell = row.querySelector('.boletin-order-number'); // Asegúrate que este selector sea correcto
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

    // Llama a reindexTableRows al cargar la página para asegurarte de que los números estén bien desde el inicio
    reindexTableRows();

    function mostrarToast(type, message) {
        console.log(`--- Función mostrarToast llamada: Tipo=${type}, Mensaje="${message}" ---`);
        // Implementa tu propia lógica de toast aquí (ej. con SweetAlert2, Toastr, etc.)
        // Para pruebas, puedes usar un alert o console.log
        // alert(message);
    }

    // Opcional: Listener global para cerrar modales al hacer clic fuera del contenido del modal
    document.addEventListener('click', function (event) {
        // console.log('Click global detectado.'); // Demasiado ruidoso, descomentar solo si depuras clics
        if (event.target.classList.contains('bg-black/50') || event.target.closest('[data-modal-close]')) { // Si el clic fue en el fondo oscuro o en un botón con data-modal-close
            const modalWrapper = event.target.closest('[id^="modal-"]');
            if (modalWrapper) {
                const idParts = modalWrapper.id.split('-');
                const tipo = idParts[1];
                const id = idParts[2];
                ocultarModal(tipo, id);
                console.log(`Cerrando modal por click externo/botón de cierre: ${modalWrapper.id}`);
            }
        }
    });
});
