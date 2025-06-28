// resources/js/Boletin-create.js

// Registramos la función 'uploadForm' como un componente de datos de Alpine
Alpine.data('uploadForm', () => ({
    file: null,
    progress: 0,
    producto: 'cafe', // valor por defecto
    nombreBoletin: '', // Para el nombre del boletín
    descripcionBoletin: '', // Para la descripción
    open: false, // Controla la visibilidad general del modal (x-show)
    currentStep: 1, // 1 para selección de archivo, 2 para formulario de detalles
    isDragging: false, // Para el estilo de arrastrar y soltar

    // Inicialización del componente
    init() {
        this.$watch('open', value => {
            if (value) {
                this.resetForm();
            }
        });
    },

    // Función para mostrar mensajes globales
    showGlobalMessage(message, isError = false) {
        const globalElement = document.querySelector('[x-data*="showSuccessModal"]');
        if (globalElement && window.Alpine && Alpine.get) {
            try {
                const globalModalState = Alpine.get(globalElement);
                if (globalModalState) {
                    globalModalState.modalMessage = message;
                    globalModalState.showSuccessModal = !isError;
                    globalModalState.showErrorModal = isError;
                } else {
                    Swal.fire(isError ? 'Error' : 'Éxito', message, isError ? 'error' : 'success');
                }
            } catch (e) {
                console.error("Error al acceder a la instancia global de Alpine para mensajes:", e);
                Swal.fire(isError ? 'Error' : 'Éxito', message, isError ? 'error' : 'success');
            }
        } else {
            Swal.fire(isError ? 'Error' : 'Éxito', message, isError ? 'error' : 'success');
        }
    },

    // Manejo de arrastre de archivos
    handleDrop(event) {
        this.isDragging = false;
        if (event.dataTransfer.files.length > 0) {
            this.handleFileChange({ target: { files: event.dataTransfer.files } });
        }
    },

    // Manejo del cambio de archivo (selección o arrastre)
    handleFileChange(event) {
        this.file = event.target.files[0];
        if (this.file) {
            this.progress = 0;
            let simulatedProgress = 0;
            const interval = setInterval(() => {
                simulatedProgress += 10;
                if (simulatedProgress <= 100) {
                    this.progress = simulatedProgress;
                } else {
                    clearInterval(interval);
                    this.currentStep = 2;
                }
            }, 100);
        } else {
            this.resetForm();
        }
    },

    // Resetea el formulario a su estado inicial
    resetForm() {
        this.file = null;
        this.progress = 0;
        this.producto = 'cafe';
        this.nombreBoletin = '';
        this.descripcionBoletin = '';
        this.currentStep = 1;
        this.isDragging = false;
        const pdfFileInput = document.getElementById('pdfFileInput');
        if (pdfFileInput) {
            pdfFileInput.value = '';
        }
    },

    // Método interno para abrir el modal
    _openModalInternal() {
        this.open = true;
        this.resetForm();
    },

    // Método para cerrar el modal
    closeModal() {
        this.open = false;
        this.resetForm();
    },

    // Función para manejar el envío del archivo y datos del boletín
    async uploadFile() {
        console.log('DEBUG: uploadFile iniciado.'); // DEBUG
        if (!this.file) {
            this.showGlobalMessage('Por favor, selecciona un archivo PDF.', true);
            return;
        }
        if (!this.nombreBoletin.trim()) {
            this.showGlobalMessage('Por favor, ingresa el nombre del boletín.', true);
            return;
        }
        if (!this.producto.trim()) {
            this.showGlobalMessage('Por favor, selecciona un producto.', true);
            return;
        }
        if (!this.descripcionBoletin.trim()) {
            this.showGlobalMessage('Por favor, ingresa la descripción del boletín.', true);
            return;
        }

        const form = document.getElementById('createBoletinForm');
        if (!form) {
            console.error("ERROR: Formulario 'createBoletinForm' no encontrado."); // DEBUG
            this.showGlobalMessage('Error interno: El formulario no se pudo encontrar.', true);
            return;
        }
        console.log('DEBUG: Formulario encontrado:', form); // DEBUG

        const formData = new FormData();
        formData.append('archivo', this.file);
        formData.append('nombre_boletin', this.nombreBoletin);
        formData.append('producto', this.producto);
        formData.append('contenido', this.descripcionBoletin);

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            formData.append('_token', csrfToken.getAttribute('content'));
        } else {
            console.error('CSRF token not found!');
            this.showGlobalMessage('Error de seguridad: CSRF token no encontrado.', true);
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", form.action, true);

        xhr.upload.addEventListener("progress", (e) => {
            if (e.lengthComputable) {
                this.progress = Math.round((e.loaded / e.total) * 100);
            }
        });

        xhr.onload = async () => {
            console.log('DEBUG: xhr.onload disparado. Status:', xhr.status); // DEBUG
            if (xhr.status === 200 || xhr.status === 201) {
                this.closeModal(); // Cierra el modal al éxito

                try {
                    const responseData = JSON.parse(xhr.responseText);
                    const boletinId = responseData.boletin_id;
                    console.log('DEBUG: Boletín creado en backend. ID recibido:', boletinId); // DEBUG

                    const boletinesTableBody = document.getElementById('boletines-table-body');
                    console.log('DEBUG: Elemento boletinesTableBody:', boletinesTableBody); // DEBUG

                    if (boletinId && boletinesTableBody) {
                        console.log(`DEBUG: Intentando obtener HTML de fila para boletín ID: ${boletinId}`); // DEBUG
                        const rowResponse = await fetch(`/boletines/${boletinId}/row-html`);
                        console.log('DEBUG: Respuesta de fetch row-html. Status:', rowResponse.status, 'OK:', rowResponse.ok); // DEBUG

                        if (!rowResponse.ok) {
                            throw new Error(`HTTP error al obtener fila: ${rowResponse.status} - ${rowResponse.statusText}`); // Más detalle en el error
                        }
                        const newRowHtml = await rowResponse.text();
                        console.log('DEBUG: HTML de nueva fila recibido:', newRowHtml); // DEBUG

                        const noBoletinesRow = document.getElementById('no-boletines-row');
                        if (noBoletinesRow) {
                            console.log('DEBUG: Removiendo fila "no-boletines-row".'); // DEBUG
                            noBoletinesRow.remove();
                        }

                        boletinesTableBody.insertAdjacentHTML('afterbegin', newRowHtml);
                        this.showGlobalMessage('Boletín creado y tabla actualizada.', false);
                        console.log('DEBUG: Nueva fila de boletín añadida a la tabla. Todo ok.'); // DEBUG
                        // Ya no recargamos la página aquí
                    } else {
                        console.warn('ADVERTENCIA: ID de boletín no recibido o tbody de la tabla no encontrado. La tabla NO se actualizó dinámicamente.'); // DEBUG
                        this.showGlobalMessage('Boletín creado, pero la tabla no se pudo actualizar. Por favor, recargue la página.', false);
                        setTimeout(() => window.location.reload(), 2000);
                    }

                } catch (e) {
                    console.error('ERROR EN LA FASE DE ACTUALIZACIÓN DE TABLA DINÁMICA:', e); // DEBUG
                    this.showGlobalMessage('Boletín creado, pero hubo un problema al actualizar la tabla en vivo. Por favor, recargue la página.', true);
                    setTimeout(() => window.location.reload(), 2000);
                }

            } else {
                // Manejo de errores de la subida del formulario (HTTP status no 200/201)
                let errorMessage = 'Error al subir el archivo.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    } else if (response.errors) {
                        errorMessage = Object.values(response.errors).flat().join('\n');
                    }
                } catch (e) {
                    console.error("ERROR: parsing XHR error response:", e); // DEBUG
                }
                this.closeModal();
                this.showGlobalMessage(errorMessage, true);
            }
        };

        xhr.onerror = () => {
            console.error('ERROR: xhr.onerror disparado (error de red).'); // DEBUG
            this.closeModal();
            this.showGlobalMessage('Error de red o conexión al servidor. Inténtalo de nuevo.', true);
        };

        xhr.send(formData);
    }
}));

// FUNCIÓN GLOBAL PARA ABRIR EL MODAL (usada desde el botón)
window.openCreateBoletinModal = function() {
    const modalElement = document.getElementById('createBoletinModal');
    if (modalElement && modalElement.__x) {
        modalElement.__x.$data._openModalInternal();
    } else {
        console.warn("ADVERTENCIA: No se pudo encontrar el modal con ID 'createBoletinModal' o su instancia Alpine.js."); // DEBUG
        if (modalElement) {
            modalElement.style.display = 'flex';
        }
    }
};
