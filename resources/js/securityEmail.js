// securityForm.js

// --- 1. Expresiones Regulares ---
const emailFilterRegex = /[^a-zA-Z0-9.@_-\u00F1\u00D1]/g;
const emailStructureRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

// Reglas de la contraseña (una letra minúscula, una mayúscula, un número, un símbolo, al menos 8 caracteres)
const passwordRules = {
    minLength: 8,
    hasLowercase: /[a-z]/,
    hasUppercase: /[A-Z]/,
    hasNumber: /[0-9]/,
    hasSymbol: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/ // Puedes ajustar los símbolos permitidos
};

// --- 2. Elementos del DOM y Estado del Formulario ---
// Guardaremos las referencias a los elementos una vez se cargue el DOM
let emailInput;
let emailErrorDiv;
let emailIconContainer;
let emailSuccessIcon;
let emailErrorIcon;

let passwordInput;
let passwordToggleIcon;
let passwordErrorDiv; // Para errores de validación JS

// Estado del formulario (simulando x-data de Alpine)
const formState = {
    email: '',
    emailExists: null,
    debounceTimeout: null,
    errors: {
        email: '',
        password: ''
    },
    showPassword: false
};

// --- 3. Funciones de Ayuda ---

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
 * Actualiza el icono de existencia de email.
 * @param {boolean|null} exists Si el email existe (true/false) o null para no mostrar icono.
 */
function updateEmailExistenceIcon(exists) {
    if (!emailIconContainer) return;

    // Limpiar iconos previos
    if (emailSuccessIcon) emailSuccessIcon.remove();
    if (emailErrorIcon) emailErrorIcon.remove();

    if (exists !== null) {
        let iconSrc = exists ? './images/bien.svg' : './images/mal.svg';
        let iconAlt = exists ? 'Correo existe' : 'Correo no existe';
        let iconClass = exists ? 'text-green-500' : 'text-red-500';

        const img = document.createElement('img');
        img.src = iconSrc;
        img.alt = iconAlt;
        img.className = `w-5 h-5 ${iconClass}`;

        if (exists) {
            emailSuccessIcon = img;
            emailIconContainer.appendChild(emailSuccessIcon);
        } else {
            emailErrorIcon = img;
            emailIconContainer.appendChild(emailErrorIcon);
        }
    }
}


// --- 4. Lógica de Validación de Email ---

/**
 * Valida y filtra el correo electrónico en tiempo real.
 */
function validateEmail() {
    let value = emailInput.value;
    const filteredValue = value.replace(emailFilterRegex, '');

    // Si hubo filtrado, actualiza el input (sin que afecte la posición del cursor si es posible)
    if (value !== filteredValue) {
        const start = emailInput.selectionStart;
        const end = emailInput.selectionEnd;
        emailInput.value = filteredValue;
        emailInput.setSelectionRange(start, end);
    }

    formState.email = filteredValue; // Actualiza el estado

    // Limpiar errores previos del email
    formState.errors.email = '';

    // Validar para mostrar error
    if (!formState.email.trim()) {
        formState.errors.email = 'El correo es obligatorio.';
    } else if (!emailStructureRegex.test(formState.email)) {
        formState.errors.email = 'El correo no es válido. Debe tener un formato como usuario@dominio.com.';
    }

    displayError(emailErrorDiv, formState.errors.email);

    // Si hay un error de JS, ocultar el icono de existencia del email
    if (formState.errors.email) {
        updateEmailExistenceIcon(null);
    }
}

/**
 * Comprueba si el correo electrónico existe en el backend con debounce.
 */
function checkEmailExistence() {
    clearTimeout(formState.debounceTimeout);
    formState.debounceTimeout = setTimeout(async () => {
        // Solo si el email es válido por JS y no está vacío
        if (!formState.errors.email && formState.email.length > 0) {
            try {
                const response = await fetch('./check-email', { // Llama a tu endpoint de Laravel
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email: formState.email })
                });
                const data = await response.json();
                formState.emailExists = data.exists;
                updateEmailExistenceIcon(formState.emailExists);
            } catch (error) {
                console.error('Error checking email:', error);
                formState.emailExists = null; // O manejar el error como prefieras
                updateEmailExistenceIcon(null);
            }
        } else {
            formState.emailExists = null; // Resetea si el campo está vacío o tiene error de validación
            updateEmailExistenceIcon(null);
        }
    }, 500); // Debounce de 500ms
}

// --- 5. Lógica de Validación de Contraseña ---

/**
 * Valida la contraseña según las reglas definidas.
 */
function validatePassword() {
    const password = passwordInput.value;
    formState.errors.password = ''; // Limpiar error previo

    if (!password.trim()) {
        formState.errors.password = 'La contraseña es obligatoria.';
    } else if (password.length < passwordRules.minLength) {
        formState.errors.password = `La contraseña debe tener al menos ${passwordRules.minLength} caracteres.`;
    } else if (!passwordRules.hasLowercase.test(password)) {
        formState.errors.password = 'La contraseña debe contener al menos una letra minúscula.';
    } else if (!passwordRules.hasUppercase.test(password)) {
        formState.errors.password = 'La contraseña debe contener al menos una letra mayúscula.';
    } else if (!passwordRules.hasNumber.test(password)) {
        formState.errors.password = 'La contraseña debe contener al menos un número.';
    } else if (!passwordRules.hasSymbol.test(password)) {
        formState.errors.password = 'La contraseña debe contener al menos un carácter especial.';
    }

    displayError(passwordErrorDiv, formState.errors.password);
}

/**
 * Alterna la visibilidad de la contraseña.
 */
function togglePasswordVisibility() {
    formState.showPassword = !formState.showPassword;
    passwordInput.type = formState.showPassword ? 'text' : 'password';

    if (passwordToggleIcon) {
        const iconSrc = formState.showPassword ? './images/ojo-open.svg' : './images/ojo-close.svg';
        const iconAlt = formState.showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña';
        passwordToggleIcon.src = iconSrc;
        passwordToggleIcon.alt = iconAlt;
    }
}

// --- 6. Inicialización del Formulario ---

document.addEventListener('DOMContentLoaded', () => {
    // Referencias a los elementos del DOM del email
    emailInput = document.getElementById('email');
    emailErrorDiv = document.getElementById('email-error-message'); // Nuevo div para errores JS
    emailIconContainer = document.getElementById('email-icon-container'); // Contenedor para iconos de bien/mal

    // Referencias a los elementos del DOM de la contraseña
    passwordInput = document.getElementById('password');
    passwordToggleIcon = document.getElementById('password-toggle-icon');
    passwordErrorDiv = document.getElementById('password-error-message'); // Nuevo div para errores JS

    // Inicializar el valor del email si viene de old() de Laravel
    if (emailInput && emailInput.value) {
        formState.email = emailInput.value;
        validateEmail(); // Validar al cargar si ya hay un email
        checkEmailExistence(); // Comprobar existencia al cargar si hay un email
    }

    // --- 7. Event Listeners ---

    // Email
    if (emailInput) {
        emailInput.addEventListener('input', () => {
            validateEmail();
            checkEmailExistence(); // Disparar la comprobación de existencia con debounce
        });
        emailInput.addEventListener('blur', validateEmail); // Revalidar al salir del campo
    }

    // Contraseña
    if (passwordInput) {
        passwordInput.addEventListener('input', validatePassword);
        passwordInput.addEventListener('blur', validatePassword); // Revalidar al salir del campo
    }
    if (passwordToggleIcon) {
        passwordToggleIcon.addEventListener('click', togglePasswordVisibility);
    }

    // Manejo de errores iniciales de Laravel (si los hay)
    // Para el email
    const laravelEmailError = document.getElementById('laravel-email-error');
    if (laravelEmailError && laravelEmailError.textContent.trim()) {
        displayError(emailErrorDiv, laravelEmailError.textContent.trim());
    }
    // Para la contraseña
    const laravelPasswordError = document.getElementById('laravel-password-error');
    if (laravelPasswordError && laravelPasswordError.textContent.trim()) {
        displayError(passwordErrorDiv, laravelPasswordError.textContent.trim());
    }

    // Mostrar mensaje de estado de sesión (ej. después de restablecer contraseña)
    const sessionStatusMessage = document.getElementById('session-status-message');
    if (sessionStatusMessage && sessionStatusMessage.textContent.trim()) {
        sessionStatusMessage.style.display = 'block';
    }
});