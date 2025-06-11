document.addEventListener('DOMContentLoaded', function () {

    window.mostrarModal = function (tipo, id) {
        const modal = document.getElementById(`modal-${tipo}-${id}`);
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    window.ocultarModal = function (tipo, id) {
        const modal = document.getElementById(`modal-${tipo}-${id}`);
        if (modal) {
            modal.classList.add('hidden');
        }
    }
    
    document.querySelectorAll('[id^="form-boletin-"]').forEach(form => {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const formId = this.id;
            const boletinId = formId.split('-')[2];
            const formData = new FormData(this);

            const updateButton = this.querySelector('button[type="submit"]');
            if (updateButton) {
                updateButton.disabled = true;
                updateButton.textContent = 'Actualizando...';
            }

            fetch(this.action, {
                method: 'POST', // Esto seguirá siendo POST debido a FormData y _method
                body: formData,
                headers: {
                    'Accept': 'application/json',
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Boletín actualizado con éxito:', data);

                    // *******************************************************************
                    // * LÓGICA DE ACTUALIZACIÓN DE LA FILA - MUCHO MÁS LIMPIA AHORA *
                    // *******************************************************************
                    if (data.html_row) { // Si el servidor nos envió el HTML de la fila
                        const oldRow = document.getElementById(`boletin-row-${boletinId}`);
                        if (oldRow) {
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.html_row.trim(); // Parsear el HTML
                            const newRow = tempDiv.firstChild; // Obtener el nuevo <tr>

                            oldRow.parentNode.replaceChild(newRow, oldRow); // Reemplazar en el DOM

                            // Opcional: Re-numerar las filas si la primera columna es un índice
                            reindexTableRows(); // Llama a una nueva función para re-numerar
                        }
                    }
                    // *******************************************************************

                    ocultarModal('editar', boletinId); // Ocultar el modal

                    if (updateButton) {
                        updateButton.disabled = false;
                        updateButton.textContent = 'Actualizar';
                    }

                    // Opcional: limpiar errores de validación anteriores si los había
                    form.querySelectorAll('.error-message').forEach(el => el.remove());
                })
                .catch(error => {
                    console.error('Error al actualizar el boletín:', error);

                    // ... (tu lógica de manejo de errores de validación en el modal) ...

                    if (updateButton) {
                        updateButton.disabled = false;
                        updateButton.textContent = 'Actualizar';
                    }
                });
        });
    });

    // *******************************************************************
    // * NUEVA FUNCIÓN AUXILIAR PARA RE-NUMERAR LAS FILAS (SI ES NECESARIO) *
    // *******************************************************************
    function reindexTableRows() {
        const tableBody = document.querySelector('tbody'); // Asumiendo que es el tbody principal
        if (tableBody) {
            const rows = tableBody.querySelectorAll('tr[id^="boletin-row-"]'); // Solo las filas de boletines
            rows.forEach((row, index) => {
                const orderNumberCell = row.querySelector('.boletin-order-number');
                if (orderNumberCell) {
                    orderNumberCell.textContent = index + 1;
                }
            });
        }
    }

    // Llama a reindexTableRows al cargar la página para asegurarte de que los números estén bien desde el inicio
    reindexTableRows();

});