document.addEventListener('DOMContentLoaded', function () {
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modal = document.getElementById('contactModal');
    const successModal = document.getElementById('successModal');
    const closeSuccessBtn = document.getElementById('closeSuccessBtn');
    const form = document.getElementById('contactForm');

    if (openModalBtn && closeModalBtn && modal) {
        openModalBtn.addEventListener('click', showContact);
        closeModalBtn.addEventListener('click', hideContact);
        window.addEventListener('click', event => {
            if (event.target === modal) hideContact();
        });
    }


    // Funciones para mostrar/ocultar modal de contacto
    function showContact() {
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'items-center', 'justify-center');
    }
    function hideContact() {
        modal.classList.add('hidden');
        modal.classList.remove('flex', 'items-center', 'justify-center');
    }

    // Funciones para mostrar/ocultar modal de éxito
    function showSuccess() {
        successModal.classList.remove('hidden');
        successModal.classList.add('flex', 'items-center', 'justify-center');
    }
    function hideSuccess() {
        successModal.classList.add('hidden');
        successModal.classList.remove('flex', 'items-center', 'justify-center');
    }

    // Evento de cierre manual del modal de éxito
    if (closeSuccessBtn) {
        closeSuccessBtn.addEventListener('click', hideSuccess);
    }

    // Envío del formulario
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(response => {
                if (response.ok) {
                    hideContact();
                    showSuccess();

                    // Cierre automático tras 3s
                    setTimeout(() => {
                        hideSuccess();
                        form.reset();
                    }, 10000);
                }
            });
        });
    }
});

//resources/views/productos, noticias, boletines.blade.php : estos son los script/funciones para utilizar los modales
window.mostrarModal = function(tipo, id) {
    const modalId = `modal-${tipo}-${id}`;
    const modal = document.getElementById(modalId);
    // Selector más genérico para el contenido del modal, asumiendo que tiene estas clases
    const modalContent = modal ? modal.querySelector('.bg-white.rounded-lg.shadow-xl') : null;

    if (modal) {
        modal.classList.remove('hidden'); // Elimina display: none;
        modal.classList.add('flex');    // Añade display: flex;

        // Aplicar animaciones de entrada al contenido del modal
        if (modalContent) {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');

        }

        document.body.classList.add('modal-open'); // Clase para bloquear el scroll del body
    } 
};

window.ocultarModal = function(tipo, id) {
    const modalId = `modal-${tipo}-${id}`;
    const modal = document.getElementById(modalId);
    const modalContent = modal ? modal.querySelector('.bg-white.rounded-lg.shadow-xl') : null;

    if (modal) {

        // Aplicar animaciones de salida antes de ocultar
        if (modalContent) {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
        }

        // Esperar a que la animación termine antes de añadir 'hidden'
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('modal-open');
        }, 300);

    }
};


// Lógica específica para el modal de éxito (reutiliza mostrarModal/ocultarModal)
document.addEventListener('DOMContentLoaded', function() {
    const successMessageDataDiv = document.getElementById('success-message-data');
    const successMessage = successMessageDataDiv?.dataset.message;
    const successModalMessageElement = document.getElementById('success-modal-message');

    if (successMessage && successModalMessageElement) {
        successModalMessageElement.textContent = successMessage;
        window.mostrarModal('success', ''); // Usar la función genérica para mostrar el modal de éxito
    }

    // Opcional: Cerrar modal haciendo clic fuera del contenido
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('fixed') && event.target.classList.contains('inset-0') && event.target.classList.contains('z-50')) {
            const modal = event.target;
            const modalIdParts = modal.id.split('-');
            if (modalIdParts.length === 3 && modalIdParts[0] === 'modal') {
                const tipo = modalIdParts[1];
                const id = modalIdParts[2];
                window.ocultarModal(tipo, id);
            } else if (modal.id === 'modal-success-') { // Para el modal de éxito
                window.ocultarModal('success', '');
            }
        }
    });

    // Opcional: Cerrar modal con la tecla ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const successModal = document.getElementById('modal-success-');
            if (successModal && !successModal.classList.contains('hidden')) {
                window.ocultarModal('success', '');
                return;
            }

            const visibleModal = document.querySelector('.fixed.inset-0.z-50:not(.hidden)');
            if (visibleModal) {
                const modalIdParts = visibleModal.id.split('-');
                if (modalIdParts.length === 3 && modalIdParts[0] === 'modal') {
                    const tipo = modalIdParts[1];
                    const id = modalIdParts[2];
                    window.ocultarModal(tipo, id);
                }
            }
        }
    });
});