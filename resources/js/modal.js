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