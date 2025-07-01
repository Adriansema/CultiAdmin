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

    // *** PROPIEDADES PARA LOS INDICADORES ***
    precioMasAlto: '',
    lugarPrecioMasAlto: '',
    precioMasBajo: '',
    lugarPrecioMasBajo: '',
    // ***************************************

    // Inicialización del componente
    init() {
        this.$watch('open', value => {
            console.log(`DEBUG: Alpine 'open' property changed to: ${value}`);
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
        // *** RESETEAR PROPIEDADES DE INDICADORES ***
        this.precioMasAlto = '';
        this.lugarPrecioMasAlto = '';
        this.precioMasBajo = '';
        this.lugarPrecioMasBajo = '';
        // *******************************************
    },

    // Método interno para abrir el modal
    _openModalInternal() {
        console.log('DEBUG: _openModalInternal llamado. Estableciendo open = true.');
        this.open = true;
        this.resetForm();
    },

    // Método para cerrar el modal
    closeModal() {
        console.log('DEBUG: closeModal llamado. Estableciendo open = false.');
        this.open = false;
        this.resetForm();
    },

    // Función para manejar el envío del archivo y datos del boletín
    async uploadFile() {
        console.log('DEBUG: uploadFile iniciado.');
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

        // *** VALIDACIÓN DE INDICADORES: Asegurar que si uno se llena, el otro también ***
        const hasPrecioAlto = this.precioMasAlto !== null && this.precioMasAlto !== '';
        const hasLugarAlto = this.lugarPrecioMasAlto.trim() !== '';
        const hasPrecioBajo = this.precioMasBajo !== null && this.precioMasBajo !== '';
        const hasLugarBajo = this.lugarPrecioMasBajo.trim() !== '';

        if ((hasPrecioAlto && !hasLugarAlto) || (!hasPrecioAlto && hasLugarAlto)) {
            this.showGlobalMessage('Para el precio más alto, por favor ingresa tanto el precio como el lugar, o déjalos ambos vacíos.', true);
            return;
        }
        if ((hasPrecioBajo && !hasLugarBajo) || (!hasPrecioBajo && hasLugarBajo)) {
            this.showGlobalMessage('Para el precio más bajo, por favor ingresa tanto el precio como el lugar, o déjalos ambos vacíos.', true);
            return;
        }
        // ********************************************************************************

        const form = document.getElementById('createBoletinForm');
        if (!form) {
            console.error("ERROR: Formulario 'createBoletinForm' no encontrado.");
            this.showGlobalMessage('Error interno: El formulario no se pudo encontrar.', true);
            return;
        }
        console.log('DEBUG: Formulario encontrado:', form);

        const formData = new FormData();
        formData.append('archivo', this.file);
        formData.append('nombre_boletin', this.nombreBoletin);
        formData.append('producto', this.producto);
        formData.append('contenido', this.descripcionBoletin); // 'contenido' es la descripción

        // *** AÑADIR NUEVOS CAMPOS DE INDICADORES AL FORM DATA ***
        // Solo añadir si tienen valor para evitar enviar cadenas vacías o null
        if (hasPrecioAlto) formData.append('precio_mas_alto', this.precioMasAlto);
        if (hasLugarAlto) formData.append('lugar_precio_mas_alto', this.lugarPrecioMasAlto);
        if (hasPrecioBajo) formData.append('precio_mas_bajo', this.precioMasBajo);
        if (hasLugarBajo) formData.append('lugar_precio_mas_bajo', this.lugarPrecioMasBajo);

        // *** LOG DE LOS DATOS ENVIADOS ***
        console.log('DEBUG: Datos de formData a enviar:');
        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }
        // **********************************

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
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener("progress", (e) => {
            if (e.lengthComputable) {
                this.progress = Math.round((e.loaded / e.total) * 100);
            }
        });

        xhr.onload = async () => {
            console.log('DEBUG: xhr.onload disparado. Status:', xhr.status);
            if (xhr.status === 200 || xhr.status === 201) {
                this.closeModal(); // Cierra el modal de subida inmediatamente al éxito

                try {
                    const responseData = JSON.parse(xhr.responseText);
                    const boletinId = responseData.boletin_id;
                    console.log('DEBUG: Boletín creado en backend. ID recibido:', boletinId);

                    const boletinesTableBody = document.getElementById('boletines-table-body');
                    console.log('DEBUG: Elemento boletinesTableBody:', boletinesTableBody);

                    if (boletinId && boletinesTableBody) {
                        console.log(`DEBUG: Intentando obtener HTML de fila para boletín ID: ${boletinId}`);

                        const rowResponse = await fetch(`/boletines/${boletinId}/row-html`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            },
                            credentials: 'include'
                        });

                        console.log('DEBUG: Respuesta de fetch row-html. Status:', rowResponse.status, 'OK:', rowResponse.ok);

                        if (!rowResponse.ok) {
                            const errorText = await rowResponse.text();
                            console.error('DEBUG: Raw response text for row-html failure:', errorText);
                            throw new Error(`HTTP error al obtener fila: ${rowResponse.status} - ${rowResponse.statusText || 'Error desconocido'}. Mensaje del servidor: ${errorText.substring(0, 200)}...`);
                        }
                        let newRowHtml = await rowResponse.text();
                        console.log('DEBUG: HTML de nueva fila recibido (crudo):', newRowHtml);

                        newRowHtml = newRowHtml.trim();
                        console.log('DEBUG: HTML de nueva fila recibido (trim):', newRowHtml);

                        if (!newRowHtml.startsWith('<tr')) {
                            console.error('ERROR: El HTML recibido no comienza con <tr>:', newRowHtml);
                            this.showGlobalMessage('Boletín creado, pero el HTML de la fila es inesperado. Recargue la página.', true);
                            setTimeout(() => window.location.reload(), 2000);
                            return;
                        }

                        boletinesTableBody.insertAdjacentHTML('afterbegin', newRowHtml);
                        this.showGlobalMessage('Boletín creado y tabla actualizada.', false);
                        console.log('DEBUG: Nueva fila de boletín añadida a la tabla. Todo ok.');

                    } else {
                        const msg = boletinId ? 'El cuerpo de la tabla no se encontró.' : 'ID de boletín no recibido.';
                        console.warn(`ADVERTENCIA: La tabla NO se actualizó dinámicamente. ${msg}`);
                        this.showGlobalMessage(`Boletín creado, pero la tabla no se pudo actualizar. ${msg} Por favor, recargue la página.`, false);
                        setTimeout(() => window.location.reload(), 2000);
                    }

                } catch (e) {
                    console.error('ERROR EN LA FASE DE ACTUALIZACIÓN DE TABLA DINÁMICA:', e);
                    this.showGlobalMessage(`Boletín creado, pero hubo un problema al actualizar la tabla en vivo. Detalle: ${e.message}. Por favor, recargue la página.`, true);
                    setTimeout(() => window.location.reload(), 2000);
                }

            } else {
                let errorMessage = 'Error al subir el archivo.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    } else if (response.errors) {
                        errorMessage = Object.values(response.errors).flat().join('\n');
                    }
                } catch (e) {
                    console.error("ERROR: parsing XHR error response:", e);
                }
                this.closeModal();
                this.showGlobalMessage(errorMessage, true);
            }
        };

        xhr.onerror = () => {
            console.error('ERROR: xhr.onerror disparado (error de red).');
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
        console.warn("ADVERTENCIA: No se pudo encontrar el modal con ID 'createBoletinModal' o su instancia Alpine.js.");
        if (modalElement) {
            modalElement.style.display = 'flex';
        }
    }
};
