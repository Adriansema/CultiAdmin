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
let fileUploadPreview;      // Contenedor de la barra de progreso y nombre de archivo (el que se muestra/oculta)
let previewFileName;        // El span para el nombre del archivo (antes selectedFileNameDisplay)
let previewFileSizeDisplay; // Texto para el tamano del archivo en la previsualizacion.
let progressBar;            // La barra de progreso de HTML.
let progressText;           // El texto de porcentaje de la barra de progreso.
let removeSelectedFileButton; // El boton para quitar el archivo
let intervalIdForSimulation = null; // variable para guardar el ID del intervalo

let bulletinNameInput;
let bulletinNameCharCount;
let bulletinDescriptionInput;
let bulletinDescriptionCharCount;
let submitCreateBoletinButton;

let currentFile = null;
let currentStep = 1; // Controla el paso actual del formulario (1 o 2)
let isDragging = false;


// --- Funciones para manejar el modal ---

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


    if (bulletinNameCharCount) {
        bulletinNameCharCount.textContent = '0/100';
    }
    if (bulletinDescriptionCharCount) {
        bulletinDescriptionCharCount.textContent = '0/500';
    }

    // Mostrar Paso 1 y ocultar Paso 2
    if (createBoletinStep1) {
        createBoletinStep1.classList.remove('hidden');
    }
    if (createBoletinStep2) {
        createBoletinStep2.classList.add('hidden');
    }
    if (submitCreateBoletinButton) {
        submitCreateBoletinButton.classList.add('hidden'); // Oculta el boton de subir
    }

    // Limpiar clases de validacion si las hubiera
    document.querySelectorAll('#createBoletinForm .border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
    });
    document.querySelectorAll('#createBoletinForm .validation-error-message').forEach(el => {
        el.textContent = ''; // Limpia mensajes de error
    });
}

// --- Funciones de manejo de eventos ---

/**
 * Maneja la seleccion de archivos (input o drop).
 * @param {FileList} fileList - La lista de archivos recibida del evento.
 */
function handleFileChange(fileList) {

    if (fileList && fileList.length > 0) {
        currentFile = fileList[0];

        // Ocultar el area de drop y mostrar la vista previa del archivo cargado
        if (fileDropArea) fileDropArea.classList.add('hidden');
        if (fileUploadPreview) fileUploadPreview.classList.remove('hidden');

        // Mostrar nombre y tamano del archivo inmediatamente
        if (previewFileName) { // Usar previewFileName aqui
            previewFileName.textContent = currentFile.name;
        }
        if (previewFileSizeDisplay) {
            previewFileSizeDisplay.textContent = `${(currentFile.size / (1024 * 1024)).toFixed(2)} MB`;
        }

        // Resetear la barra de progreso para la simulacion
        if (progressBar) progressBar.style.width = '0%';
        if (progressText) progressText.textContent = '0%';


        // Simular progreso de carga y avanzar al Paso 2
        let simulatedProgress = 0;
        // Guardar el ID del intervalo
        intervalIdForSimulation = setInterval(() => { // <-- Asigna a la nueva variable
            simulatedProgress += 10;
            if (simulatedProgress <= 100) {
                if (progressBar) progressBar.style.width = `${simulatedProgress}%`;
                if (progressText) progressText.textContent = `${simulatedProgress}%`;
            } else {
                clearInterval(intervalIdForSimulation); // Limpia al finalizar naturalmente
                intervalIdForSimulation = null; // Resetea la variable

                // Asegurar que la barra llegue a 100% al finalizar la simulacion
                if (progressBar) progressBar.style.width = '100%';
                if (progressText) progressText.textContent = '100%';

                currentStep = 2; // Avanza al Paso 2
                // Transicion visual a Paso 2
                if (createBoletinStep1) createBoletinStep1.classList.add('hidden');
                if (createBoletinStep2) createBoletinStep2.classList.remove('hidden');
                if (submitCreateBoletinButton) submitCreateBoletinButton.classList.remove('hidden'); // Muestra el boton de subir
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
        fileDropArea.classList.add('border-green-500', 'border-2', 'bg-green-50/50'); // Ajuste de clases
        fileDropArea.classList.remove('border-gray-300'); // Asegura que el borde gris se quita
    }
}

/**
 * Maneja cuando un archivo sale del area de arrastre.
 * @param {Event} event - El evento de arrastre.
 */
function handleDragLeave(event) {
    isDragging = false;
    if (fileDropArea) {
        fileDropArea.classList.remove('border-green-500', 'border-2', 'bg-green-50/50'); // Ajuste de clases
        fileDropArea.classList.add('border-gray-300'); // Vuelve a anadir el borde gris
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
        fileDropArea.classList.remove('border-green-500', 'border-2', 'bg-green-50/50'); // Ajuste de clases
        fileDropArea.classList.add('border-gray-300'); // Vuelve a anadir el borde gris
    }
    handleFileChange(event.dataTransfer.files);
}

// =========================================================================
// --- INICIO: Funciones para Formateo de Precios en Tiempo Real ---
// =========================================================================

/**
 * Limpia la cadena de texto de un número, eliminando separadores de miles y el texto " COP".
 * Convierte el separador decimal de coma a punto para parseFloat.
 * @param {string} str La cadena de texto del input.
 * @returns {string} La cadena limpia lista para parseFloat.
 */
function cleanPriceString(str) {
    if (typeof str !== 'string') return '';
    // Eliminar " COP" y espacios adicionales
    let cleaned = str.replace(/\s*COP/g, '').trim();
    // Eliminar todos los puntos (separadores de miles)
    cleaned = cleaned.replace(/\./g, '');
    // Reemplazar la coma decimal por un punto decimal
    cleaned = cleaned.replace(/,/g, '.');
    // Eliminar cualquier carácter que no sea dígito o punto decimal
    cleaned = cleaned.replace(/[^\d.]/g, '');
    return cleaned;
}

/**
 * Formatea un número o cadena de número con separadores de miles, dos decimales,
 * símbolo de moneda y el sufijo " COP".
 * Utiliza toLocaleString para manejar la configuración regional colombiana.
 * @param {(number|string)} value El número o cadena a formatear.
 * @returns {string} El número formateado con separadores de miles, decimales y " COP".
 */
function formatPrice(value) {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    // `cleanPriceString` ya maneja la conversión de coma a punto para parseFloat.
    const cleanedValue = cleanPriceString(String(value));
    const numberValue = parseFloat(cleanedValue);

    if (isNaN(numberValue)) {
        return ''; // Retorna vacío si no es un número válido
    }

    // Formatear el número con toLocaleString para la configuración regional de Colombia.
    // 'es-CO' usará punto como separador de miles y coma como decimal.
    // style: 'currency' y currency: 'COP' se encargarán del símbolo y la posición.
    // minimumFractionDigits: 2 y maximumFractionDigits: 2 aseguran dos decimales.
    let formatted = numberValue.toLocaleString('es-CO', {
        style: 'currency',
        currency: 'COP', // Esto añadirá el símbolo de la moneda (ej. "$")
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    // toLocaleString para 'es-CO' con currency 'COP' generalmente produce "$ 15.000,00"
    // o "$15.000,00".
    // Para que aparezca "COP" al final, necesitamos una pequeña manipulación.
    // 1. Quitar el símbolo de moneda inicial que toLocaleString puso.
    // 2. Asegurarse que los espacios son consistentes.
    // 3. Añadir " COP" al final.

    // Expresión regular para quitar el símbolo de moneda (puede ser '$' o '€' u otro) y espacios iniciales/finales
    formatted = formatted.replace(/^[\s\uFEFF\xA0]*\D+/, '').trim(); // Quita el símbolo de moneda y espacios iniciales no numéricos
    
    // Asegurarse de que el formato de los miles y decimales sea el esperado (punto miles, coma decimal)
    // toLocaleString('es-CO') ya lo hace, pero si viniera de otra fuente, podrías asegurar.
    // formatted = formatted.replace(/\./g, 'TEMP_DOT').replace(/,/g, '.').replace(/TEMP_DOT/g, ','); // Esto es si necesitas invertir comas y puntos

    // Finalmente, añadir el " COP" al final
    return `${formatted} COP`;
}

/**
 * Aplica el formateo en tiempo real a un campo de entrada.
 * @param {HTMLInputElement} inputElement El elemento input HTML.
 */
function applyPriceInputFormatting(inputElement) {
    inputElement.addEventListener('input', (event) => {
        const cursorPosition = inputElement.selectionStart;
        const originalValue = inputElement.value;
        const originalLength = originalValue.length;

        // Elimina el sufijo " COP" y los separadores de miles temporales antes de re-formatear
        let rawValue = originalValue.replace(/\s*COP/g, '').replace(/\./g, '');

        // Obtener solo dígitos para el formateo
        let digitsOnly = rawValue.replace(/[^\d]/g, '');

        let formattedDigits = '';
        if (digitsOnly) {
            // Convertir a número y formatear con toLocaleString
            formattedDigits = Number(digitsOnly).toLocaleString('es-CO', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // Si el valor original terminaba en un punto (aunque nuestro formato no lo muestra por los 0 decimales),
        // este condicional es más bien un "seguro" por si se decide en el futuro permitir decimales.
        // Por ahora, con maximumFractionDigits: 0, esta parte tiene menos impacto.
        if (originalValue.endsWith('.') && !formattedDigits.endsWith('.')) {
            formattedDigits += '.'; // Mantener el punto si el usuario lo ingresó, aunque luego no se muestren decimales formateados.
        }


        // Añadir el sufijo COP si hay dígitos formateados
        if (formattedDigits) {
            inputElement.value = `${formattedDigits} COP`;
        } else {
            inputElement.value = ''; // Limpiar si no hay dígitos válidos
        }

        // Reajustar la posición del cursor
        const newLength = inputElement.value.length;
        const lengthDiff = newLength - originalLength;
        // Ajusta la posición del cursor si se han añadido o quitado caracteres de formato
        inputElement.setSelectionRange(cursorPosition + lengthDiff, cursorPosition + lengthDiff);
    });

    // Si el campo ya tiene un valor al cargar la página (ej. edición), formatearlo
    if (inputElement.value) {
        inputElement.dispatchEvent(new Event('input'));
    }
}

// Aplicar la función de formateo a los campos de precio una vez que el DOM esté completamente cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Seleccionamos todos los elementos con la clase 'price-input'
    const priceInputs = document.querySelectorAll('.price-input');
    
    priceInputs.forEach(input => {
        applyPriceInputFormatting(input);
    });
});

// =========================================================================
// --- FIN: Funciones para Formateo de Precios en Tiempo Real ---
// =========================================================================


// =========================================================================
// --- INICIO: Tu función handleCreateBoletinSubmit modificada ---
// =========================================================================

/**
 * Maneja el envio del formulario de creacion de boletines.
 * @param {Event} event - El evento de envio del formulario.
 */
async function handleCreateBoletinSubmit(event) {
    event.preventDefault();

    if (!currentFile) {
        window.showGlobalMessage('error', 'Por favor, selecciona un archivo PDF.');
        return;
    }

    const formData = new FormData(createBoletinForm);
    formData.append('archivo', currentFile); // Aseguramos que el archivo se anade al FormData

    // --- SANITIZACION DE DATOS DE USUARIO ANTES DE ENVIAR ---
    // Limpiar nombre y descripcion de caracteres no ASCII
    let cleanedBulletinName = bulletinNameInput.value.replace(/[^\x00-\x7F]/g, '');
    let cleanedBulletinDescription = bulletinDescriptionInput.value.replace(/[^\x00-\x7F]/g, '');

    formData.set('name', cleanedBulletinName); // Usar la version limpia
    formData.set('description', cleanedBulletinDescription); // Usar la version limpia
    // --- FIN SANITIZACION DE NOMBRE Y DESCRIPCION ---

    // Pre-procesamiento de precios: Limpiar y convertir a punto decimal
    let precioMasAltoVal = document.getElementById('precioMasAlto').value;
    let lugarPrecioMasAltoVal = document.getElementById('lugarPrecioMasAlto').value;
    let precioMasBajoVal = document.getElementById('precioMasBajo').value;
    let lugarPrecioMasBajoVal = document.getElementById('lugarPrecioMasBajo').value;

    // AQUI ES DONDE USAMOS LA NUEVA FUNCION cleanPriceString
    let cleanedPrecioMasAlto = cleanPriceString(precioMasAltoVal);
    let cleanedPrecioMasBajo = cleanPriceString(precioMasBajoVal);

    // --- SANITIZACION DE LUGARES DE PRECIO (texto libre) ---
    // Aplicamos la limpieza a las variables intermedias para los campos de lugar
    let cleanedLugarPrecioMasAlto = lugarPrecioMasAltoVal.replace(/[^\x00-\x7F]/g, '');
    let cleanedLugarPrecioMasBajo = lugarPrecioMasBajoVal.replace(/[^\x00-\x7F]/g, '');
    // --- FIN SANITIZACION DE LUGARES ---


    let processedPrecioMasAlto = parseFloat(cleanedPrecioMasAlto) || null;
    let processedPrecioMasBajo = parseFloat(cleanedPrecioMasBajo) || null;

    // Asegurarse de que si el parsing falla (ej. input vacío o solo "COP"), el valor sea nulo o 0.
    const hasPrecioAlto = processedPrecioMasAlto !== null && !isNaN(processedPrecioMasAlto) && processedPrecioMasAlto > 0; // Añadido check > 0
    // Usar la version limpia y corregir .trim()
    const hasLugarAlto = cleanedLugarPrecioMasAlto.trim() !== '';
    const hasPrecioBajo = processedPrecioMasBajo !== null && !isNaN(processedPrecioMasBajo) && processedPrecioMasBajo > 0; // Añadido check > 0
    // Usar la version limpia y corregir .trim()
    const hasLugarBajo = cleanedLugarPrecioMasBajo.trim() !== '';

    if ((hasPrecioAlto && !hasLugarAlto) || (!hasPrecioAlto && hasLugarAlto)) {
        // Mensaje sin caracteres no ASCII
        window.showGlobalMessage('error', 'Para el precio mas alto, por favor ingresa tanto el precio como el lugar, o dejalos ambos vacios.');
        return;
    }
    if ((hasPrecioBajo && !hasLugarBajo) || (!hasPrecioBajo && hasLugarBajo)) {
        // Mensaje sin caracteres no ASCII
        window.showGlobalMessage('error', 'Para el precio mas bajo, por favor ingresa tanto el precio como el lugar, o dejalos ambos vacios.');
        return;
    }

    // Setear los valores procesados en el formData para que sean enviados correctamente
    if (hasPrecioAlto) {
        formData.set('precio_mas_alto', processedPrecioMasAlto);
    } else {
        formData.delete('precio_mas_alto');
    }
    if (hasLugarAlto) {
        formData.set('lugar_precio_mas_alto', cleanedLugarPrecioMasAlto);
    } else {
        formData.delete('lugar_precio_mas_alto');
    }
    if (hasPrecioBajo) {
        formData.set('precio_mas_bajo', processedPrecioMasBajo);
    } else {
        formData.delete('precio_mas_bajo');
    }
    if (hasLugarBajo) {
        formData.set('lugar_precio_mas_bajo', cleanedLugarPrecioMasBajo);
    } else {
        formData.delete('lugar_precio_mas_bajo');
    }

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
            }
        });

        const resultStore = await response.json();

        if (response.ok) {
            window.closeCreateBoletinModalVanilla(); // Cierra el modal de creacion

            // Mensaje sin caracteres no ASCII
            window.showGlobalMessage('success', resultStore.message || 'Boletín creado con éxito.');

            setTimeout(() => {
                window.location.reload(); // Recarga la pagina
            }, 1500);

        } else if (response.status === 422) {
            displayCreateFormValidationErrors(resultStore.errors);
            // Mensaje sin caracteres no ASCII
            window.showGlobalMessage('error', resultStore.message || 'Por favor, corrige los errores en el formulario.');
        } else {
            // Mensaje sin caracteres no ASCII
            window.showGlobalMessage('error', resultStore.message || 'Ocurrio un error inesperado al crear el boletín.');
        }
    } catch (error) {
        // Mensaje sin caracteres no ASCII
        window.showGlobalMessage('error', 'Error de red o conexión al servidor. Intentalo de nuevo.');
        console.error('Fetch error:', error);
    } finally {
        if (submitCreateBoletinButton) {
            submitCreateBoletinButton.disabled = false;
            // Mensaje sin caracteres no ASCII
            submitCreateBoletinButton.innerHTML = 'Subir Boletín';
        }
    }
}
// =========================================================================
// --- FIN: Tu función handleCreateBoletinSubmit modificada ---
// =========================================================================

/**
 * Muestra los errores de validacion en el formulario de creacion.
 * @param {object} errors - Objeto de errores de la respuesta del servidor.
 */
function displayCreateFormValidationErrors(errors) {
    document.querySelectorAll('#createBoletinForm .border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
    });
    document.querySelectorAll('#createBoletinForm .validation-error-message').forEach(el => {
        el.textContent = '';
    });

    for (const field in errors) {
        const inputField = createBoletinForm.querySelector(`[name="${field}"]`);
        if (inputField) {
            inputField.classList.add('border-red-500');
        }
        const errorDiv = document.querySelector(`.validation-error-message[data-field="${field}"]`);
        if (errorDiv) {
            errorDiv.textContent = errors[field][0];
        }
    }
}


// --- Event Listeners y Inicializacion ---

document.addEventListener('DOMContentLoaded', function () {

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


    if (closeCreateModalXButton) {
        closeCreateModalXButton.addEventListener('click', function () {
            if (currentStep === 2) {
                resetCreateBoletinForm();
            } else {
                window.closeCreateBoletinModalVanilla();
            }
        });
    }
    if (cancelCreateModalButton) {
        cancelCreateModalButton.addEventListener('click', function () {
            if (currentStep === 2) {
                resetCreateBoletinForm();
            } else {
                window.closeCreateBoletinModalVanilla();
            }
        });
    }

    if (pdfFileInput) {
        pdfFileInput.addEventListener('change', (event) => handleFileChange(event.target.files));
    }

    if (createBoletinForm) {
        createBoletinForm.addEventListener('submit', handleCreateBoletinSubmit);
    }

    if (bulletinNameInput) {
        bulletinNameInput.addEventListener('input', () => {
            if (bulletinNameCharCount) {
                bulletinNameCharCount.textContent = `${bulletinNameInput.value.length}/100`;
            }
        });
    }
    if (bulletinDescriptionInput) {
        bulletinDescriptionInput.addEventListener('input', () => {
            if (bulletinDescriptionCharCount) {
                bulletinDescriptionCharCount.textContent = `${bulletinDescriptionInput.value.length}/500`;
            }
        });
    }

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

    if (fileDropArea) {
        fileDropArea.addEventListener('dragover', handleDragOver);
        fileDropArea.addEventListener('dragleave', handleDragLeave);
        fileDropArea.addEventListener('drop', handleDrop);
    }

    if (removeSelectedFileButton) {
        removeSelectedFileButton.addEventListener('click', () => {
            resetCreateBoletinForm();
        });
    }

    resetCreateBoletinForm();
});

window.openCreateBoletinModal = window.openCreateBoletinModalVanilla;