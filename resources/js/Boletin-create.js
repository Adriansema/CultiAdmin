// resources/js/Boletin-create.js
// resources/js/Boletin-create.js

// Registramos la función 'uploadForm' como un componente de datos de Alpine
Alpine.data('uploadForm', () => ({
    file: null,
    progress: 0,
    producto: 'cafe', // valor por defecto
    nombreBoletin: '', // Para el nombre del boletín
    descripcionBoletin: '', // Para la descripción
    open: false, // Controla la visibilidad general del modal
    currentStep: 1, // 1 para selección de archivo, 2 para formulario de detalles
    isDragging: false, // Para el estilo de arrastrar y soltar

    // Acceso a las variables del modal de éxito/error global
    get globalModalState() {
        const globalElement = document.querySelector('[x-data*="showSuccessModal"]');
        if (globalElement && globalElement._x_dataStack && globalElement._x_dataStack.length > 0) {
            return globalElement._x_dataStack[0];
        }
        return { modalMessage: '', showSuccessModal: false, showErrorModal: false };
    },

    // Función para manejar el arrastre de archivos
    handleDrop(event) {
        this.isDragging = false;
        if (event.dataTransfer.files.length > 0) {
            this.handleFileChange({ target: { files: event.dataTransfer.files } });
        }
    },

    // Función para manejar el cambio de archivo (selección o arrastre)
    handleFileChange(event) {
        this.file = event.target.files[0];
        if (this.file) {
            this.progress = 0;
            // Simular una subida con barra de progreso
            let simulatedProgress = 0;
            const interval = setInterval(() => {
                simulatedProgress += 10;
                if (simulatedProgress <= 100) {
                    this.progress = simulatedProgress;
                } else {
                    clearInterval(interval);
                    // Una vez que la "subida" simulada está completa, pasar al Paso 2
                    this.currentStep = 2;
                }
            }, 100);
        } else {
            this.resetForm();
        }
    },

    // Función para resetear el formulario a su estado inicial
    resetForm() {
        this.file = null;
        this.progress = 0;
        this.producto = 'cafe';
        this.nombreBoletin = '';
        this.descripcionBoletin = '';
        this.currentStep = 1; // Volver al primer paso
        this.isDragging = false;
        const pdfFileInput = document.getElementById('pdfFileInput');
        if (pdfFileInput) {
            pdfFileInput.value = '';
        }
    },

    // MÉTODO PARA CERRAR EL MODAL
    closeModal() {
        this.open = false; // Cierra el modal
        this.resetForm();  // Resetea el formulario a su estado inicial (Paso 1, campos vacíos)
    },

    // Función para manejar el envío del archivo y datos del boletín
    uploadFile() {
        if (!this.file) {
            this.globalModalState.modalMessage = 'Por favor, selecciona un archivo PDF.';
            this.globalModalState.showErrorModal = true;
            return;
        }
        if (!this.nombreBoletin.trim()) {
            this.globalModalState.modalMessage = 'Por favor, ingresa el nombre del boletín.';
            this.globalModalState.showErrorModal = true;
            return;
        }
        if (!this.producto.trim()) {
            this.globalModalState.modalMessage = 'Por favor, selecciona un producto.';
            this.globalModalState.showErrorModal = true;
            return;
        }
        if (!this.descripcionBoletin.trim()) {
            this.globalModalState.modalMessage = 'Por favor, ingresa la descripción del boletín.';
            this.globalModalState.showErrorModal = true;
            return;
        }

        const form = this.$el.querySelector('form');
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
            this.globalModalState.modalMessage = 'Error de seguridad: CSRF token no encontrado.';
            this.globalModalState.showErrorModal = true;
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("POST", form.action, true);

        xhr.upload.addEventListener("progress", (e) => {
            if (e.lengthComputable) {
                this.progress = Math.round((e.loaded / e.total) * 100);
            }
        });

        xhr.onload = () => {
            if (xhr.status === 200 || xhr.status === 201) {
                this.open = false;
                this.resetForm();

                this.globalModalState.modalMessage = 'Boletín creado exitosamente.';
                this.globalModalState.showSuccessModal = true;

                setTimeout(() => {
                    window.location.reload();
                }, 2000);
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
                    console.error("Error parsing XHR error response:", e);
                }

                this.open = false;
                this.globalModalState.modalMessage = errorMessage;
                this.globalModalState.showErrorModal = true;
            }
        };

        xhr.onerror = () => {
            this.open = false;
            this.globalModalState.modalMessage = 'Error de red o conexión al servidor.';
            this.globalModalState.showErrorModal = true;
        };

        xhr.send(formData);
    }
}));

// FUNCIÓN GLOBAL PARA ABRIR EL MODAL (ACCESIBLE DESDE CUALQUIER LUGAR)
window.openCreateBoletinModal = function() {
    const modalElement = document.getElementById('createBoletinModal');
    if (modalElement && modalElement.__x && modalElement.__x.$data) {
        const modalAlpineInstance = modalElement.__x.$data;
        modalAlpineInstance.open = true;
        modalAlpineInstance.resetForm(); // Asegura que el modal siempre se abra en el Paso 1 y limpio
    } else {
        console.warn("No se pudo encontrar la instancia de Alpine.js para el modal de boletín. Intentando abrir sin Alpine.");
        if (modalElement) {
            modalElement.style.display = 'flex';
        }
    }
};
