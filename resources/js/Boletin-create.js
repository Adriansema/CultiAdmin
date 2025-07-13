// --- 0. Variables y Referencias Globales ---
let createBoletinModal;
let createBoletinModalContent;
let closeCreateModalXButton;
let cancelCreateModalButton;
let pdfFileInput; // Input de tipo file
let createBoletinForm;
let createBoletinStep1; // Contenedor del Paso 1 (Nombre, Descripcion, Carga PDF)
let createBoletinStep2; // Contenedor del Paso 2 (Indicadores de Precio)
let fileDropArea; // El div que actua como area de drop y click para el archivo

// Referencias para la barra de progreso y previsualizacion (AJUSTADAS)
let fileUploadPreview; // Contenedor de la barra de progreso y nombre de archivo (el que se muestra/oculta)
let previewFileName; // El span para el nombre del archivo (antes selectedFileNameDisplay)
let previewFileSizeDisplay; // Texto para el tamano del archivo en la previsualizacion.
let progressBar; // La barra de progreso de HTML.
let progressText; // El texto de porcentaje de la barra de progreso.
let removeSelectedFileButton; // El boton para quitar el archivo
let intervalIdForSimulation = null; // variable para guardar el ID del intervalo

// Referencias de campos del Paso 1 para validación y conteo
let bulletinNameInput;
let bulletinNameCharCount;
let bulletinDescriptionInput;
let bulletinDescriptionCharCount;

// Referencias de campos del Paso 2 para validación
let productRadios; // NodeList de los radios de producto
let precioMasAltoInput;
let lugarPrecioMasAltoInput;
let precioMasBajoInput;
let lugarPrecioMasBajoInput;

let submitCreateBoletinButton;

let currentFile = null;
let currentStep = 1; // Controla el paso actual del formulario (1 o 2)
let isDragging = false;

// Estado para almacenar los mensajes de error del formulario de boletín
const bulletinFormErrors = {
    nombre: '',
    producto: '',
    descripcion: '',
    precio_mas_alto: '',
    lugar_precio_mas_alto: '',
    precio_mas_bajo: '',
    lugar_precio_mas_bajo: ''
};

// --- 1. Expresiones Regulares (para validación de precios si se requiere) ---
// No se necesitan regex específicas para precios aquí, la normalización ya limpia.

// --- 2. Funciones para manejar el modal ---

/**
 * Abre el modal de creacion de boletines.
 * Siempre inicia en el Paso 1.
 */
window.openCreateBoletinModalVanilla = function () {
    if (createBoletinModal) {
        createBoletinModal.classList.remove('hidden');
        createBoletinModal.classList.add('flex'); // Asegura que el modal se centre
        document.body.style.overflow = 'hidden'; // Bloquea el scroll del body
        resetCreateBoletinForm(); // Resetea el formulario al abrir, llevandolo al Paso 1
    }
};

/**
 * Cierra el modal de creacion de boletines por completo.
 * No resetea el formulario, solo lo oculta.
 */
window.closeCreateBoletinModalVanilla = function () {
    if (createBoletinModal) {
        createBoletinModal.classList.remove('flex');
        createBoletinModal.classList.add('hidden'); // Oculta el modal
        document.body.style.overflow = ''; // Restaura el scroll del body
    }
};

/**
 * Muestra u oculta un mensaje de error en un div específico.
 * @param {HTMLElement} errorElement El div donde se mostrará el error.
 * @param {string} message El mensaje de error. Si es vacío, oculta el div.
 */
function displayError(errorElement, message) {
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = message ? 'block' : 'none';
    }
}

/**
 * Actualiza el contador de caracteres para un input/textarea.
 * @param {HTMLInputElement|HTMLTextAreaElement} inputElement El elemento input/textarea.
 * @param {HTMLElement} charCountElement El span donde se muestra el conteo.
 * @param {number} maxLength La longitud máxima permitida.
 */
function updateCharCount(inputElement, charCountElement, maxLength) {
    if (inputElement && charCountElement) {
        const currentLength = inputElement.value.length;
        charCountElement.textContent = `${currentLength}/${maxLength}`;
    }
}

/**
 * Resetea el formulario del modal a su estado inicial (Paso 1).
 * Mantiene el modal abierto.
 */
function resetCreateBoletinForm() {
    // Detener cualquier simulacion de carga en progreso
    if (intervalIdForSimulation) {
        clearInterval(intervalIdForSimulation);
        intervalIdForSimulation = null;
    }

    currentFile = null;
    currentStep = 1; // Asegura que el paso es el 1

    // Resetear campos del formulario
    if (createBoletinForm) {
        createBoletinForm.reset();
    }

    if (pdfFileInput) {
        pdfFileInput.value = ''; // Limpia el input de archivo
    }

    // Asegura que el area de drop este visible y en su estado normal
    if (fileDropArea) {
        fileDropArea.classList.remove('hidden');
        fileDropArea.classList.remove('border-green-500', 'border-2', 'bg-green-50/50'); // Limpia estilos de drag
        fileDropArea.classList.add('border-gray-300'); // Restaura el borde normal
    }

    // Oculta la seccion de previsualizacion/progreso
    if (fileUploadPreview) {
        fileUploadPreview.classList.add('hidden');
    }

    // Limpia los textos y la barra de progreso
    if (previewFileName) { // Usar previewFileName para el nombre del archivo
        previewFileName.textContent = ''; // Limpia el nombre del archivo
    }

    if (progressBar) {
        progressBar.style.width = '0%';
    }

    if (progressText) {
        progressText.textContent = '0%';
    }

    if (previewFileSizeDisplay) {
        previewFileSizeDisplay.textContent = ''; // Limpia el tamano
    }

    // Resetear contadores de caracteres y valores de input
    if (bulletinNameInput) bulletinNameInput.value = '';
    if (bulletinNameCharCount) bulletinNameCharCount.textContent = '0/100';
    if (bulletinDescriptionInput) bulletinDescriptionInput.value = '';
    if (bulletinDescriptionCharCount) bulletinDescriptionCharCount.textContent = '0/500';

    // Resetear radios de producto a la opción predeterminada (si existe)
    if (productRadios && productRadios.length > 0) {
        // Asumiendo que 'cafe' es la opcion por defecto
        const defaultRadio = document.getElementById('productoCafeRadio');
        if (defaultRadio) {
            defaultRadio.checked = true;
        }
    }
    // Resetear campos de precio y lugar
    if (precioMasAltoInput) precioMasAltoInput.value = '';
    if (lugarPrecioMasAltoInput) lugarPrecioMasAltoInput.value = '';
    if (precioMasBajoInput) precioMasBajoInput.value = '';
    if (lugarPrecioMasBajoInput) lugarPrecioMasBajoInput.value = '';

    // Mostrar Paso 1 y ocultar Paso 2
    if (createBoletinStep1) {
        createBoletinStep1.classList.remove('hidden');
    }

    if (createBoletinStep2) {
        createBoletinStep2.classList.add('hidden');
    }

    if (submitCreateBoletinButton) {
        submitCreateBoletinButton.classList.add('hidden'); // Oculta el boton de subir
        submitCreateBoletinButton.disabled = false; // Asegura que el boton no este deshabilitado
        submitCreateBoletinButton.innerHTML = 'Subir Boletín'; // Restaura el texto
    }

    // Limpiar clases de validacion y mensajes de error
    document.querySelectorAll('#createBoletinForm .border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
    });

    document.querySelectorAll('#createBoletinForm .validation-error-message').forEach(el => {
        el.textContent = ''; // Limpia mensajes de error
        el.style.display = 'none'; // Oculta los divs de error
    });
    // Resetear el objeto de errores
    for (const key in bulletinFormErrors) {
        bulletinFormErrors[key] = '';
    }
}

// --- 3. Funciones de manejo de eventos (Carga de archivo) ---

/**
 * Maneja la seleccion de archivos (input o drop).
 * @param {FileList} fileList - La lista de archivos recibida del evento.
 */
function handleFileChange(fileList) {
    if (fileList && fileList.length > 0) {
        currentFile = fileList[0];

        // Validar tipo de archivo (solo PDF)
        if (currentFile.type !== 'application/pdf') {
            window.showGlobalMessage('error', 'Solo se permiten archivos PDF.');
            resetCreateBoletinForm(); // Limpiar todo si el archivo no es PDF
            return;
        }
        // Validar tamaño del archivo (ej. máximo 5 MB)
        const maxFileSizeMB = 5;
        if (currentFile.size > maxFileSizeMB * 1024 * 1024) {
            window.showGlobalMessage('error', `El archivo PDF no puede exceder los ${maxFileSizeMB} MB.`);
            resetCreateBoletinForm(); // Limpiar todo si el archivo es muy grande
            return;
        }

        // Ocultar el area de drop y mostrar la vista previa del archivo cargado
        if (fileDropArea) fileDropArea.classList.add('hidden');
        if (fileUploadPreview) fileUploadPreview.classList.remove('hidden');

        // Mostrar nombre y tamano del archivo inmediatamente
        if (previewFileName) {
            previewFileName.textContent = currentFile.name;
        }
        if (previewFileSizeDisplay) {
            previewFileSizeDisplay.textContent = `${(currentFile.size / (1024 * 1024)).toFixed(2)} MB`;
        }

        // Resetear la barra de progreso para la simulacion
        if (progressBar) progressBar.style.width = '0%';
        if (progressText) progressText.textContent = '0%';

        // Simular progreso de carga
        let simulatedProgress = 0;
        // Limpiar cualquier intervalo previo para evitar duplicados
        if (intervalIdForSimulation) {
            clearInterval(intervalIdForSimulation);
            intervalIdForSimulation = null;
        }
        intervalIdForSimulation = setInterval(() => {
            simulatedProgress += 10;
            if (simulatedProgress <= 100) {
                if (progressBar) progressBar.style.width = `${simulatedProgress}%`;
                if (progressText) progressText.textContent = `${simulatedProgress}%`;
            } else {
                clearInterval(intervalIdForSimulation);
                intervalIdForSimulation = null;

                if (progressBar) progressBar.style.width = '100%';
                if (progressText) progressText.textContent = '100%';

                // Transicionar al Paso 2 después de la "carga" exitosa
                currentStep = 2;
                if (createBoletinStep1) createBoletinStep1.classList.add('hidden');
                if (createBoletinStep2) createBoletinStep2.classList.remove('hidden');
                if (submitCreateBoletinButton) submitCreateBoletinButton.classList.remove('hidden');
            }
        }, 200);
    } else {
        resetCreateBoletinForm(); // Si no hay archivo, resetear toda la UI
    }
}

/**
 * Maneja el arrastre de archivos sobre el area de carga.
 * @param {Event} event - El evento de arrastre.
 */
function handleDragOver(event) {
    event.preventDefault(); // Previene el comportamiento por defecto (abrir archivo en el navegador)
    isDragging = true;
    if (fileDropArea) {
        fileDropArea.classList.add('border-green-500', 'border-2', 'bg-green-50/50');
        fileDropArea.classList.remove('border-gray-300');
    }
}

/**
 * Maneja cuando un archivo sale del area de arrastre.
 * @param {Event} event - El evento de arrastre.
 */
function handleDragLeave(event) {
    isDragging = false;
    if (fileDropArea) {
        fileDropArea.classList.remove('border-green-500', 'border-2', 'bg-green-50/50');
        fileDropArea.classList.add('border-gray-300');
    }
}

/**
 * Maneja el soltar de archivos en el area de carga.
 * @param {Event} event - El evento de soltar.
 */
function handleDrop(event) {
    event.preventDefault(); // Previene el comportamiento por defecto (abrir archivo en el navegador)
    isDragging = false;
    if (fileDropArea) {
        fileDropArea.classList.remove('border-green-500', 'border-2', 'bg-green-50/50');
        fileDropArea.classList.add('border-gray-300');
    }
    handleFileChange(event.dataTransfer.files);
}

// =========================================================================
// --- INICIO: Funciones para Formateo y Validación de Precios en Tiempo Real ---
// =========================================================================

/**
 * Limpia la cadena de texto de un número, eliminando separadores de miles y el texto " COP".
 * Convierte el separador decimal de coma a punto para parseFloat.
 * @param {string} str La cadena de texto del input.
 * @returns {string} La cadena limpia lista para parseFloat.
 */
function cleanPriceString(str) {
    if (typeof str !== 'string') return '';
    let cleaned = str.replace(/\s*COP/g, '').trim(); // Eliminar " COP" y espacios adicionales
    cleaned = cleaned.replace(/\./g, ''); // Eliminar todos los puntos (separadores de miles)
    cleaned = cleaned.replace(/,/g, '.'); // Reemplazar la coma decimal por un punto decimal
    cleaned = cleaned.replace(/[^\d.]/g, ''); // Eliminar cualquier carácter que no sea dígito o punto decimal
    return cleaned;
}

/**
 * Formatea un número o cadena de número con separadores de miles y el sufijo " COP".
 * Utiliza toLocaleString para manejar la configuración regional colombiana.
 * @param {(number|string)} value El número o cadena a formatear.
 * @returns {string} El número formateado con separadores de miles, decimales y " COP".
 */
function formatPrice(value) {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    const cleanedValue = cleanPriceString(String(value));
    const numberValue = parseFloat(cleanedValue);

    if (isNaN(numberValue)) {
        return ''; // Retorna vacío si no es un número válido
    }

    // Formatear el número sin decimales y añadir " COP"
    let formatted = numberValue.toLocaleString('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });

    return `${formatted} COP`;
}

/**
 * Aplica el formateo en tiempo real a un campo de entrada de precio.
 * @param {HTMLInputElement} inputElement El elemento input HTML.
 */
function applyPriceInputFormatting(inputElement) {
    inputElement.addEventListener('input', (event) => {
        const cursorPosition = inputElement.selectionStart;
        const originalValue = inputElement.value;
        const originalLength = originalValue.length;

        // Limpiar y obtener solo dígitos para formatear
        let rawValue = originalValue.replace(/\s*COP/g, '').replace(/\./g, '');
        let digitsOnly = rawValue.replace(/[^\d]/g, '');

        let formattedDigits = '';
        if (digitsOnly) {
            formattedDigits = Number(digitsOnly).toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // Restaurar el valor formateado y ajustar el cursor
        inputElement.value = formattedDigits ? `${formattedDigits} COP` : '';
        const newLength = inputElement.value.length;
        const lengthDiff = newLength - originalLength;
        inputElement.setSelectionRange(cursorPosition + lengthDiff, cursorPosition + lengthDiff);
    });

    // Si el campo ya tiene un valor al cargar la página (ej. edición), formatearlo
    if (inputElement.value) {
        inputElement.dispatchEvent(new Event('input'));
    }
}


// --- FUNCIONES DE VALIDACIÓN PARA LOS CAMPOS DEL BOLETÍN ---

function validateBulletinName() {
    const value = bulletinNameInput.value.trim();
    bulletinFormErrors.nombre = ''; // Limpiar error previo

    if (!value) {
        bulletinFormErrors.nombre = 'El nombre del boletín es obligatorio.';
    } else if (value.length > 100) {
        bulletinFormErrors.nombre = 'El nombre no puede exceder los 100 caracteres.';
    }
    displayError(document.getElementById('nombre_error'), bulletinFormErrors.nombre);
    return !bulletinFormErrors.nombre; // Retorna true si es válido
}

function validateProduct() {
    let isChecked = false;
    bulletinFormErrors.producto = ''; // Limpiar error previo

    for (const radio of productRadios) {
        if (radio.checked) {
            isChecked = true;
            break;
        }
    }

    if (!isChecked) {
        bulletinFormErrors.producto = 'Debes seleccionar un producto.';
    }
    displayError(document.getElementById('producto_error'), bulletinFormErrors.producto);
    return !bulletinFormErrors.producto;
}

function validateBulletinDescription() {
    const value = bulletinDescriptionInput.value.trim();
    bulletinFormErrors.descripcion = ''; // Limpiar error previo

    if (!value) {
        bulletinFormErrors.descripcion = 'La descripción es obligatoria.';
    } else if (value.length > 500) {
        bulletinFormErrors.descripcion = 'La descripción no puede exceder los 500 caracteres.';
    }
    displayError(document.getElementById('descripcion_error'), bulletinFormErrors.descripcion);
    return !bulletinFormErrors.descripcion;
}

function validatePriceInputFields(inputElement, errorElementId, fieldName) {
    const rawValue = inputElement.value;
    const normalizedValue = cleanPriceString(rawValue); // Usar cleanPriceString para la validación numérica
    
    // Asegurar que el input muestre el valor formateado sin el COP para la validacion si el usuario borra.
    // Esto es más para la validación interna. El applyPriceInputFormatting maneja la UI.

    bulletinFormErrors[fieldName] = ''; // Limpiar error previo

    if (!normalizedValue.trim()) {
        bulletinFormErrors[fieldName] = 'Este campo es obligatorio.';
    } else if (isNaN(parseFloat(normalizedValue))) {
        bulletinFormErrors[fieldName] = 'Debe ser un número válido.';
    } else if (parseFloat(normalizedValue) <= 0) {
        bulletinFormErrors[fieldName] = 'El precio debe ser mayor a cero.';
    }

    displayError(document.getElementById(errorElementId), bulletinFormErrors[fieldName]);
    return !bulletinFormErrors[fieldName];
}

function validateLugarInput(inputElement, errorElementId, fieldName) {
    const value = inputElement.value.trim();
    bulletinFormErrors[fieldName] = ''; // Limpiar error previo

    if (!value) {
        bulletinFormErrors[fieldName] = 'Este campo es obligatorio.';
    } else if (value.length > 255) {
        bulletinFormErrors[fieldName] = 'El lugar no puede exceder los 255 caracteres.';
    }
    displayError(document.getElementById(errorElementId), bulletinFormErrors[fieldName]);
    return !bulletinFormErrors[fieldName];
}


/**
 * Valida todos los campos del formulario de boletín.
 * @returns {boolean} True si todos los campos son válidos, false en caso contrario.
 */
function validateAllBoletinFields() {
    // Validar que se haya subido un archivo PDF
    if (!currentFile) {
        window.showGlobalMessage('error', 'Por favor, selecciona un archivo PDF.');
        // No hay un div de error específico para el archivo, se usa el mensaje global
        return false;
    }

    // Ejecutar todas las validaciones y almacenar sus resultados
    const isBulletinNameValid = validateBulletinName();
    const isProductValid = validateProduct();
    const isDescriptionValid = validateBulletinDescription();

    const isPrecioMasAltoValid = validatePriceInputFields(precioMasAltoInput, 'precio_mas_alto_error', 'precio_mas_alto');
    const isLugarPrecioMasAltoValid = validateLugarInput(lugarPrecioMasAltoInput, 'lugar_precio_mas_alto_error', 'lugar_precio_mas_alto');
    const isPrecioMasBajoValid = validatePriceInputFields(precioMasBajoInput, 'precio_mas_bajo_error', 'precio_mas_bajo');
    const isLugarPrecioMasBajoValid = validateLugarInput(lugarPrecioMasBajoInput, 'lugar_precio_mas_bajo_error', 'lugar_precio_mas_bajo');

    // Retornar true solo si TODAS las validaciones son true
    return isBulletinNameValid && isProductValid && isDescriptionValid &&
           isPrecioMasAltoValid && isLugarPrecioMasAltoValid &&
           isPrecioMasBajoValid && isLugarPrecioMasBajoValid;
}


// --- Manejo del envío del formulario ---

/**
 * Maneja el envio del formulario de creacion de boletines.
 * @param {Event} event - El evento de envio del formulario.
 */
async function handleCreateBoletinSubmit(event) {
    event.preventDefault(); // Prevenir el envío por defecto

    // *** Ejecutar validación del lado del cliente ANTES de enviar ***
    if (!validateAllBoletinFields()) {
        console.log('El formulario tiene errores de validación. No se enviará.');
        window.showGlobalMessage('error', 'Por favor, corrige los errores en el formulario.');
        // Opcional: enfocar el primer campo con error visible
        const firstErrorDiv = document.querySelector('.validation-error-message[style*="display: block"]');
        if (firstErrorDiv) {
            const inputName = firstErrorDiv.dataset.field; // Usar data-field
            let inputToFocus = null;
            if (inputName === 'producto') {
                inputToFocus = productRadios[0]; // Enfocar el primer radio button
            } else {
                inputToFocus = createBoletinForm.querySelector(`[name="${inputName}"]`);
            }
            if (inputToFocus) {
                inputToFocus.focus();
                // Si es un radio, resalta su contenedor visualmente (si hay clases de Tailwind)
                if (inputName === 'producto') {
                    inputToFocus.closest('label').querySelector('div')?.classList.add('ring-2', 'ring-red-500');
                }
            }
        }
        return; // Detener el envío si hay errores de validación
    }

    const formData = new FormData(createBoletinForm);
    formData.append('archivo', currentFile); // Aseguramos que el archivo se anade al FormData

    // --- SANITIZACION DE DATOS DE USUARIO ANTES DE ENVIAR ---
    // Limpiar nombre y descripcion de caracteres no ASCII (ya se hace en el server normalmente)
    // Pero si quieres una capa extra en el cliente:
    formData.set('nombre', bulletinNameInput.value.trim());
    formData.set('descripcion', bulletinDescriptionInput.value.trim());

    // Pre-procesamiento de precios: Limpiar y convertir a punto decimal
    let cleanedPrecioMasAlto = cleanPriceString(precioMasAltoInput.value);
    let cleanedPrecioMasBajo = cleanPriceString(precioMasBajoInput.value);

    // Los "lugares" ya fueron validados, ahora solo los establecemos
    formData.set('precio_mas_alto', parseFloat(cleanedPrecioMasAlto) || null);
    formData.set('lugar_precio_mas_alto', lugarPrecioMasAltoInput.value.trim());
    formData.set('precio_mas_bajo', parseFloat(cleanedPrecioMasBajo) || null);
    formData.set('lugar_precio_mas_bajo', lugarPrecioMasBajoInput.value.trim());

    // Deshabilitar boton y mostrar spinner
    if (submitCreateBoletinButton) {
        submitCreateBoletinButton.disabled = true;
        submitCreateBoletinButton.innerHTML = `
            <span class="flex items-center justify-center w-full">
                <span>Subiendo...</span>
                <img src="./images/cargando_.svg" alt="Cargando..." class="w-5 h-5 ml-2 animate-spin">
            </span>
        `;
    }

    try {
        const response = await fetch(createBoletinForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                // Laravel ya maneja el CSRF token si el formulario está en la plantilla Blade
                // Y si se usa AJAX, se debe incluir como un header:
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const resultStore = await response.json();

        if (response.ok) {
            window.closeCreateBoletinModalVanilla(); // Cierra el modal de creacion
            window.showGlobalMessage('success', resultStore.message || 'Boletín creado con éxito.');
            setTimeout(() => {
                window.location.reload(); // Recarga la pagina
            }, 1500);

        } else if (response.status === 422) {
            displayCreateFormValidationErrors(resultStore.errors);
            window.showGlobalMessage('error', resultStore.message || 'Por favor, corrige los errores en el formulario.');
            // Opcional: enfocar el primer campo con error
            const firstErrorField = document.querySelector('.validation-error-message[style*="display: block"]');
            if (firstErrorField) {
                const inputToFocus = createBoletinForm.querySelector(`[name="${firstErrorField.dataset.field}"]`);
                if (inputToFocus) {
                    inputToFocus.focus();
                }
            }
        } else {
            window.showGlobalMessage('error', resultStore.message || 'Ocurrio un error inesperado al crear el boletín.');
        }
    } catch (error) {
        window.showGlobalMessage('error', 'Error de red o conexión al servidor. Inténtalo de nuevo.');
        console.error('Fetch error:', error);
    } finally {
        if (submitCreateBoletinButton) {
            submitCreateBoletinButton.disabled = false;
            submitCreateBoletinButton.innerHTML = 'Subir Boletín';
        }
    }
}

/**
 * Muestra los errores de validacion en el formulario de creacion.
 * @param {object} errors - Objeto de errores de la respuesta del servidor.
 */
function displayCreateFormValidationErrors(errors) {
    // Limpiar errores previos
    document.querySelectorAll('#createBoletinForm .border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
    });
    document.querySelectorAll('#createBoletinForm .validation-error-message').forEach(el => {
        el.textContent = '';
        el.style.display = 'none';
    });

    for (const field in errors) {
        const inputField = createBoletinForm.querySelector(`[name="${field}"]`);
        if (inputField) {
            inputField.classList.add('border-red-500'); // Resaltar el campo
        }
        // Buscar el div de error por su data-field
        const errorDiv = document.querySelector(`.validation-error-message[data-field="${field}"]`);
        if (errorDiv) {
            errorDiv.textContent = errors[field][0];
            errorDiv.style.display = 'block';
        }
        // Caso especial para radios: si 'producto' tiene error, resaltar los radios
        if (field === 'producto') {
            document.querySelectorAll('input[name="producto"]').forEach(radio => {
                radio.closest('label').querySelector('div')?.classList.add('ring-2', 'ring-red-500');
            });
        }
    }
}


// --- Event Listeners y Inicializacion ---

document.addEventListener('DOMContentLoaded', function () {
    // Asignación de elementos del DOM al inicio
    createBoletinModal = document.getElementById('createBoletinModal');
    createBoletinModalContent = document.getElementById('createBoletinModalContent');
    closeCreateModalXButton = document.getElementById('closeCreateModalXButton');
    cancelCreateModalButton = document.getElementById('cancelCreateModalButton');
    pdfFileInput = document.getElementById('pdfFileInput');
    createBoletinForm = document.getElementById('createBoletinForm');
    createBoletinStep1 = document.getElementById('createBoletinStep1');
    createBoletinStep2 = document.getElementById('createBoletinStep2');

    fileDropArea = document.getElementById('fileDropArea');
    fileUploadPreview = document.getElementById('fileUploadPreview');
    previewFileName = document.getElementById('previewFileName');
    previewFileSizeDisplay = document.getElementById('previewFileSize');
    progressBar = document.getElementById('progressBar');
    progressText = document.getElementById('progressText');
    removeSelectedFileButton = document.getElementById('removeSelectedFileButton');

    bulletinNameInput = document.getElementById('bulletinName');
    bulletinNameCharCount = document.getElementById('bulletinNameCharCount');
    bulletinDescriptionInput = document.getElementById('bulletinDescription');
    bulletinDescriptionCharCount = document.getElementById('bulletinDescriptionCharCount');
    submitCreateBoletinButton = document.getElementById('submitCreateBoletinButton');

    // Referencias para los campos de validación del Paso 2
    productRadios = document.querySelectorAll('input[name="producto"]');
    precioMasAltoInput = document.getElementById('precioMasAlto');
    lugarPrecioMasAltoInput = document.getElementById('lugarPrecioMasAlto');
    precioMasBajoInput = document.getElementById('precioMasBajo');
    lugarPrecioMasBajoInput = document.getElementById('lugarPrecioMasBajo');

    // --- Configuración de Event Listeners ---

    // Botones de cerrar/cancelar
    if (closeCreateModalXButton) {
        closeCreateModalXButton.addEventListener('click', function () {
            // Si estamos en el paso 2, resetear. Si no, solo cerrar.
            if (currentStep === 2) {
                resetCreateBoletinForm();
            } else {
                window.closeCreateBoletinModalVanilla();
            }
        });
    }

    if (cancelCreateModalButton) {
        cancelCreateModalButton.addEventListener('click', function () {
            // Idem al botón X
            if (currentStep === 2) {
                resetCreateBoletinForm();
            } else {
                window.closeCreateBoletinModalVanilla();
            }
        });
    }

    // Input de archivo PDF
    if (pdfFileInput) {
        pdfFileInput.addEventListener('change', (event) => handleFileChange(event.target.files));
    }

    // Área de arrastre de archivo
    if (fileDropArea) {
        fileDropArea.addEventListener('dragover', handleDragOver);
        fileDropArea.addEventListener('dragleave', handleDragLeave);
        fileDropArea.addEventListener('drop', handleDrop);
        // Habilitar click para abrir el selector de archivos
        /* fileDropArea.addEventListener('click', () => pdfFileInput.click()); */
    }

    // Botón para remover el archivo seleccionado
    if (removeSelectedFileButton) {
        removeSelectedFileButton.addEventListener('click', () => {
            resetCreateBoletinForm();
        });
    }

    // Campo Nombre del Boletín
    if (bulletinNameInput) {
        bulletinNameInput.addEventListener('input', () => {
            updateCharCount(bulletinNameInput, bulletinNameCharCount, 100);
            validateBulletinName(); // Validación en tiempo real
        });
        bulletinNameInput.addEventListener('blur', validateBulletinName); // Validación al perder foco
    }

    // Campo Descripción
    if (bulletinDescriptionInput) {
        bulletinDescriptionInput.addEventListener('input', () => {
            updateCharCount(bulletinDescriptionInput, bulletinDescriptionCharCount, 500);
            validateBulletinDescription(); // Validación en tiempo real
        });
        bulletinDescriptionInput.addEventListener('blur', validateBulletinDescription); // Validación al perder foco
    }

    // Radios de Producto
    if (productRadios && productRadios.length > 0) {
        productRadios.forEach(radio => {
            radio.addEventListener('change', validateProduct); // Validación al cambiar
        });
    }

    // Campos de Precio (aplicar formateo y validación en tiempo real)
    if (precioMasAltoInput) {
        applyPriceInputFormatting(precioMasAltoInput);
        precioMasAltoInput.addEventListener('input', () => validatePriceInputFields(precioMasAltoInput, 'precio_mas_alto_error', 'precio_mas_alto'));
        precioMasAltoInput.addEventListener('blur', () => validatePriceInputFields(precioMasAltoInput, 'precio_mas_alto_error', 'precio_mas_alto'));
    }

    if (lugarPrecioMasAltoInput) {
        lugarPrecioMasAltoInput.addEventListener('input', () => validateLugarInput(lugarPrecioMasAltoInput, 'lugar_precio_mas_alto_error', 'lugar_precio_mas_alto'));
        lugarPrecioMasAltoInput.addEventListener('blur', () => validateLugarInput(lugarPrecioMasAltoInput, 'lugar_precio_mas_alto_error', 'lugar_precio_mas_alto'));
    }

    if (precioMasBajoInput) {
        applyPriceInputFormatting(precioMasBajoInput);
        precioMasBajoInput.addEventListener('input', () => validatePriceInputFields(precioMasBajoInput, 'precio_mas_bajo_error', 'precio_mas_bajo'));
        precioMasBajoInput.addEventListener('blur', () => validatePriceInputFields(precioMasBajoInput, 'precio_mas_bajo_error', 'precio_mas_bajo'));
    }

    if (lugarPrecioMasBajoInput) {
        lugarPrecioMasBajoInput.addEventListener('input', () => validateLugarInput(lugarPrecioMasBajoInput, 'lugar_precio_mas_bajo_error', 'lugar_precio_mas_bajo'));
        lugarPrecioMasBajoInput.addEventListener('blur', () => validateLugarInput(lugarPrecioMasBajoInput, 'lugar_precio_mas_bajo_error', 'lugar_precio_mas_bajo'));
    }

    // Envío del formulario (ahora con validación JS previa)
    if (createBoletinForm) {
        createBoletinForm.addEventListener('submit', handleCreateBoletinSubmit);
    }

    // Cerrar modal al hacer clic fuera del contenido o con ESC
    if (createBoletinModal) {
        createBoletinModal.addEventListener('click', function (event) {
            if (event.target === createBoletinModal) {
                if (currentStep === 2) {
                    resetCreateBoletinForm();
                } else {
                    window.closeCreateBoletinModalVanilla();
                }
            }
        });
    }
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && createBoletinModal && !createBoletinModal.classList.contains('hidden')) {
            if (currentStep === 2) {
                resetCreateBoletinForm();
            } else {
                window.closeCreateBoletinModalVanilla();
            }
        }
    });

    // Inicializar el formulario al cargar la página (o cuando se abre el modal por primera vez)
    resetCreateBoletinForm(); // Asegura que el estado inicial sea el correcto
});

// Alias global para abrir el modal, si es necesario desde otros scripts o HTML
window.openCreateBoletinModal = window.openCreateBoletinModalVanilla;