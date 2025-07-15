// --- 1. Expresiones Regulares ---
const secEmailFilterRegex = /[^a-zA-Z0-9.@_-\u00F1\u00D1]/g;
const secEmailStructureRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

// Reglas de la contraseña (una letra minúscula, una mayúscula, un número, un símbolo, al menos 8 caracteres)
const secPasswordRules = {
    minLength: 8,
    hasLowercase: /[a-z]/,
    hasUppercase: /[A-Z]/,
    hasNumber: /[0-9]/,
    hasSymbol: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/ // Puedes ajustar los símbolos permitidos
};

// --- 2. Elementos del DOM y Estado del Formulario ---
let secEmailInput;
let secEmailErrorDiv;
let secEmailIconContainer;
let secEmailSuccessIcon;
let secEmailErrorIcon;

let secPasswordInput;
let secPasswordToggleIcon;
let secPasswordErrorDiv; // Para errores de validación JS

let secThrottleCountdownDiv;
let secThrottleInterval;

// Estado del formulario (simulando x-data de Alpine)
const secLoginFormState = {
    email: '',
    emailExists: null,
    debounceTimeout: null,
    errors: {
        email: '',
        password: ''
    },
    showPassword: false,
    loginAttempts: 0, // Llevar la cuenta de intentos JS
    isThrottled: false, // Estado de bloqueo
    throttleEndTime: 0, // Tiempo en que termina el bloqueo
};

// --- Funciones de Ayuda ---
/**
 * Muestra u oculta un mensaje de error en un div específico.
 * @param {HTMLElement} errorElement El div donde se mostrará el error.
 * @param {string} message El mensaje de error. Si es vacío, oculta el div.
 */
function secDisplayError(errorElement, message) {
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = message ? 'block' : 'none';
    }
}

/**
 * Actualiza el icono de existencia de Email.
 * @param {boolean|null} exists Si el Email existe (true/false) o null para no mostrar icono.
 */
function secUpdateEmailExistenceIcon(exists) {
    if (!secEmailIconContainer) return;

    // Limpiar iconos previos
    if (secEmailSuccessIcon) secEmailSuccessIcon.remove();
    if (secEmailErrorIcon) secEmailErrorIcon.remove();

    if (exists !== null) {
        let iconSrc = exists ? './images/bien.svg' : './images/mal.svg';
        let iconAlt = exists ? 'Correo existe' : 'Correo no existe';
        let iconClass = exists ? 'text-green-500' : 'text-red-500';

        const img = document.createElement('img');
        img.src = iconSrc;
        img.alt = iconAlt;
        img.className = `w-5 h-5 ${iconClass}`;

        if (exists) {
            secEmailSuccessIcon = img;
            secEmailIconContainer.appendChild(secEmailSuccessIcon);
        } else {
            secEmailErrorIcon = img;
            secEmailIconContainer.appendChild(secEmailErrorIcon);
        }
    }
}

// --- Lógica de Validación de Email ---
function secValidateEmail() {
    // Protección adicional en cada función si se llama directamente
    if (!secEmailInput || !secEmailErrorDiv) return;

    let value = secEmailInput.value;
    const filteredValuelogin = value.replace(secEmailFilterRegex, '');

    if (value !== filteredValuelogin) {
        const start = secEmailInput.selectionStart;
        const end = secEmailInput.selectionEnd;
        secEmailInput.value = filteredValuelogin;
        secEmailInput.setSelectionRange(start, end);
    }

    secLoginFormState.email = filteredValuelogin;

    // Solo limpiar errores de validación, NO los de autenticación persistentes
    // Si el error actual es uno de los errores de autenticación específicos, no lo borramos.
    if (secLoginFormState.errors.email !== __('auth.email_not_found') && secLoginFormState.errors.email !== __('auth.password_mismatch')) {
        secLoginFormState.errors.email = '';
    }

    if (!secLoginFormState.email.trim()) {
        secLoginFormState.errors.email = 'El correo es obligatorio.';
    } else if (!secEmailStructureRegex.test(secLoginFormState.email)) {
        secLoginFormState.errors.email = 'El correo no es válido. Debe tener un formato como usuario@dominio.com.';
    }

    secDisplayError(secEmailErrorDiv, secLoginFormState.errors.email);

    if (secLoginFormState.errors.email) {
        secUpdateEmailExistenceIcon(null);
    }
}

/**
 * Comprueba si el correo electrónico existe en el backend con debounce.
 */
function secCheckEmailExistence() {
    if (!secEmailInput) return;

    clearTimeout(secLoginFormState.debounceTimeout);
    secLoginFormState.debounceTimeout = setTimeout(async () => {
        // Solo si el Email es válido por JS y no está vacío
        if (!secLoginFormState.errors.email && secLoginFormState.email.length > 0) {
            try {
                const response = await fetch('./check-email', { // Llama a tu endpoint de Laravel
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email: secLoginFormState.email })
                });
                const data = await response.json();
                secLoginFormState.emailExists = data.exists;
                secUpdateEmailExistenceIcon(secLoginFormState.emailExists);
            } catch (error) {
                console.error('Error checking Email:', error);
                secLoginFormState.emailExists = null; // O manejar el error como prefieras
                secUpdateEmailExistenceIcon(null);
            }
        } else {
            secLoginFormState.emailExists = null; // Resetea si el campo está vacío o tiene error de validación
            secUpdateEmailExistenceIcon(null);
        }
    }, 500); // Debounce de 500ms
}

// --- Lógica de Validación de Contraseña (para el formulario de LOGIN) ---
function secValidatePassword() {
    if (!secPasswordInput || !secPasswordErrorDiv) return;

    const password = secPasswordInput.value;
    // Aquí es donde usas la función global __()
    if (secLoginFormState.errors.password !== __('auth.password_mismatch')) {
        secLoginFormState.errors.password = '';
    }

    if (!password.trim()) {
        secLoginFormState.errors.password = 'La contraseña es obligatoria.';
    } else if (password.length < secPasswordRules.minLength) {
        secLoginFormState.errors.password = `La contraseña debe tener al menos ${secPasswordRules.minLength} caracteres.`;
    } else if (!secPasswordRules.hasLowercase.test(password)) {
        secLoginFormState.errors.password = 'La contraseña debe contener al menos una letra minúscula.';
    } else if (!secPasswordRules.hasUppercase.test(password)) {
        secLoginFormState.errors.password = 'La contraseña debe contener al menos una letra mayúscula.';
    } else if (!secPasswordRules.hasNumber.test(password)) {
        secLoginFormState.errors.password = 'La contraseña debe contener al menos un número.';
    } else if (!secPasswordRules.hasSymbol.test(password)) {
        secLoginFormState.errors.password = 'La contraseña debe contener al menos un carácter especial.';
    }

    secDisplayError(secPasswordErrorDiv, secLoginFormState.errors.password);
}

function secTogglePasswordVisibility() {
    if (!secPasswordInput || !secPasswordToggleIcon) return;

    secLoginFormState.showPassword = !secLoginFormState.showPassword;
    secPasswordInput.type = secLoginFormState.showPassword ? 'text' : 'password';

    const iconSrc = secLoginFormState.showPassword ? './images/ojo-open.svg' : './images/ojo-close.svg';
    const iconAlt = secLoginFormState.showPassword ? 'Ocultar contraseña' : 'Mostrar contraseña';
    secPasswordToggleIcon.src = iconSrc;
    secPasswordToggleIcon.alt = iconAlt;
}

// --- Funciones para el contador de intentos (para el formulario de LOGIN) ---
function secStartThrottleCountdown(seconds) {
    secLoginFormState.isThrottled = true;
    secLoginFormState.throttleEndTime = Date.now() + (seconds * 1000);
    secUpdateThrottleCountdownDisplay();

    secThrottleInterval = setInterval(() => {
        secUpdateThrottleCountdownDisplay();
    }, 1000);

    // Asegurarse de que los elementos existen antes de deshabilitar
    const submitButton = document.querySelector('form button[type="submit"]');
    if (submitButton) submitButton.disabled = true;
    if (secEmailInput) secEmailInput.disabled = true;
    if (secPasswordInput) secPasswordInput.disabled = true;
}

function secUpdateThrottleCountdownDisplay() {
    const remainingTime = Math.max(0, Math.ceil((secLoginFormState.throttleEndTime - Date.now()) / 1000));

    if (remainingTime > 0) {
        secDisplayError(secEmailErrorDiv, `Demasiados intentos de inicio de sesión. Por favor, espere ${remainingTime} segundos.`);
        secDisplayError(secPasswordErrorDiv, '');
        if (secThrottleCountdownDiv) {
            secThrottleCountdownDiv.textContent = `Puede intentar de nuevo en ${remainingTime} segundos.`;
            secThrottleCountdownDiv.style.display = 'block';
        }
    } else {
        clearInterval(secThrottleInterval);
        secLoginFormState.isThrottled = false;
        secDisplayError(secEmailErrorDiv, '');
        if (secThrottleCountdownDiv) secThrottleCountdownDiv.style.display = 'none';

        const submitButton = document.querySelector('form button[type="submit"]');
        if (submitButton) submitButton.disabled = false;
        if (secEmailInput) secEmailInput.disabled = false;
        if (secPasswordInput) secPasswordInput.disabled = false;

        const formElement = document.querySelector('form');
        if (formElement) formElement.reset();
        secLoginFormState.email = '';
        secLoginFormState.errors.email = '';
        secLoginFormState.errors.password = '';
        secUpdateEmailExistenceIcon(null);
    }
}

// --- Inicialización del Formulario de Login ---
document.addEventListener('DOMContentLoaded', () => {
    // === VERIFICACIÓN CLAVE: SOLO INICIALIZAR SI LOS ELEMENTOS DEL FORMULARIO DE LOGIN EXISTEN ===
    // Intentamos encontrar un elemento clave del formulario de login. Si no existe, no hacemos nada.
    const securityCheckElement = document.getElementById('sec-email');
    if (!securityCheckElement) {
        // console.log("securityForm.js: No se encontró el formulario de login. Abortando inicialización.");
        return; // Si el elemento principal no existe, significa que no estamos en la página de login.
    }

    secEmailInput = document.getElementById('sec-email');
    secEmailErrorDiv = document.getElementById('sec-email-error-message');
    secEmailIconContainer = document.getElementById('sec-email-icon-container');

    secPasswordInput = document.getElementById('sec-password');
    secPasswordToggleIcon = document.getElementById('sec-password-toggle-icon');
    secPasswordErrorDiv = document.getElementById('sec-password-error-message');

    secThrottleCountdownDiv = document.getElementById('sec-throttle-countdown');

    // =====================================================================
    // === LÓGICA PARA MANEJAR ERRORES DE LARAVEL AL CARGAR ===
    // =====================================================================
    const secLaravelAuthErrorCatcher = document.getElementById('sec-laravel-auth-error-catcher');
    const secLaravelPasswordErrorCatcher = document.getElementById('sec-laravel-password-error-catcher');
    const secThrottleMessageDiv = document.getElementById('sec-throttle-message');

    // Estas traducciones se espera que estén disponibles globalmente a través de window.__
    // Añadimos un fallback si __ no está definida (aunque debería estarlo por layouts.guest.blade.php)
    const AUTH_EMAIL_NOT_FOUND = typeof __ === 'function' ? __('auth.email_not_found') : 'El correo electrónico no coincide.';
    const AUTH_PASSWORD_MISMATCH = typeof __ === 'function' ? __('auth.password_mismatch') : 'La contraseña no coincide.';
    const AUTH_THROTTLE_MESSAGE_PATTERN = /Demasiados intentos de inicio de sesión\. Por favor, inténtelo de nuevo en (\d+) segundos\./;


    let secLaravelAuthMessage = secLaravelAuthErrorCatcher ? secLaravelAuthErrorCatcher.textContent.trim() : '';
    let secLaravelPasswordMessage = secLaravelPasswordErrorCatcher ? secLaravelPasswordErrorCatcher.textContent.trim() : '';
    let secLaravelThrottleMessage = secThrottleMessageDiv ? secThrottleMessageDiv.textContent.trim() : '';

    if (secLaravelThrottleMessage) {
        const match = secLaravelThrottleMessage.match(AUTH_THROTTLE_MESSAGE_PATTERN);
        if (match && match[1]) {
            const seconds = parseInt(match[1], 10);
            secStartThrottleCountdown(seconds);
            secDisplayError(secThrottleMessageDiv, '');
        }
        if (secLaravelAuthErrorCatcher) secDisplayError(secLaravelAuthErrorCatcher, '');
        if (secLaravelPasswordErrorCatcher) secDisplayError(secLaravelPasswordErrorCatcher, '');
    }
    else if (secLaravelAuthMessage) {
        if (secLaravelAuthMessage === AUTH_EMAIL_NOT_FOUND) {
            secLoginFormState.errors.email = AUTH_EMAIL_NOT_FOUND;
            secDisplayError(secEmailErrorDiv, secLoginFormState.errors.email);
            secUpdateEmailExistenceIcon(false);
            if (secPasswordInput) secPasswordInput.value = '';
        } else if (secLaravelAuthMessage === AUTH_PASSWORD_MISMATCH) {
            secLoginFormState.errors.password = AUTH_PASSWORD_MISMATCH;
            secDisplayError(secPasswordErrorDiv, secLoginFormState.errors.password);
            secUpdateEmailExistenceIcon(true);
        } else {
            secLoginFormState.errors.email = secLaravelAuthMessage;
            secDisplayError(secEmailErrorDiv, secLoginFormState.errors.email);
            secUpdateEmailExistenceIcon(null);
            if (secPasswordInput) secPasswordInput.value = '';
        }
        if (secLaravelAuthErrorCatcher) secDisplayError(secLaravelAuthErrorCatcher, '');
    }
    else if (secLaravelPasswordMessage) {
        secLoginFormState.errors.password = secLaravelPasswordMessage;
        secDisplayError(secPasswordErrorDiv, secLoginFormState.errors.password);
        if (secLaravelPasswordErrorCatcher) secDisplayError(secLaravelPasswordErrorCatcher, '');
    }

    // Inicializar el valor del email si viene de old() de Laravel
    if (secEmailInput && secEmailInput.value) {
        secLoginFormState.email = secEmailInput.value;
        secValidateEmail();
    }

    // --- Event Listeners ---
    if (secEmailInput) {
        secEmailInput.addEventListener('input', () => {
            // Protección adicional con typeof __ === 'function' para el caso de que __ no esté definida
            const emailNotFound = typeof __ === 'function' ? __('auth.email_not_found') : 'El correo electrónico no coincide.';
            const passwordMismatch = typeof __ === 'function' ? __('auth.password_mismatch') : 'La contraseña no coincide.';

            if (secEmailErrorDiv && (secEmailErrorDiv.textContent === emailNotFound || secEmailErrorDiv.textContent === passwordMismatch)) {
                secDisplayError(secEmailErrorDiv, '');
            }
            secValidateEmail();
            secCheckEmailExistence();
        });
        secEmailInput.addEventListener('blur', secValidateEmail);
    }

    if (secPasswordInput) {
        secPasswordInput.addEventListener('input', () => {
            const passwordMismatch = typeof __ === 'function' ? __('auth.password_mismatch') : 'La contraseña no coincide.';
            const emailNotFound = typeof __ === 'function' ? __('auth.email_not_found') : 'El correo electrónico no coincide.';

            if (secPasswordErrorDiv && secPasswordErrorDiv.textContent === passwordMismatch) {
                secDisplayError(secPasswordErrorDiv, '');
            }
            if (secEmailErrorDiv && secEmailErrorDiv.textContent === emailNotFound) {
                secDisplayError(secEmailErrorDiv, '');
            }

            secValidatePassword();
        });
        secPasswordInput.addEventListener('blur', secValidatePassword);
    }
    if (secPasswordToggleIcon) {
        secPasswordToggleIcon.addEventListener('click', secTogglePasswordVisibility);
    }

    const secSessionStatusMessage = document.getElementById('sec-session-status-message');
    if (secSessionStatusMessage && secSessionStatusMessage.textContent.trim()) {
        secSessionStatusMessage.style.display = 'block';
    }
});