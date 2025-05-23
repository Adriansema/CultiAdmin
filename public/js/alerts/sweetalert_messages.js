document.addEventListener('DOMContentLoaded', function() {
    // Para 'status_producto'
    if (sessionStatusProducto) { // sessionStatusProducto será una variable global inyectada por Blade
        if (sessionStatusProducto === 'aprobado') {
            Swal.fire({
                icon: 'success',
                title: '¡Noticia Aprobada!',
                text: 'Tu noticia ha sido aprobada por el operador.',
                showConfirmButton: false,
                timer: 3000
            });
        } else if (sessionStatusProducto === 'rechazado') {
            Swal.fire({
                icon: 'error',
                title: '¡Noticia Rechazada!',
                text: 'Tu noticia ha sido rechazada por el operador. Revisa tu correo o el detalle para más información.',
                showConfirmButton: true,
                confirmButtonText: 'Ir al detalle →',
                timer: 3000
            }).then((result) => {
                if (result.isConfirmed) {
                    // sessionProductoIdForRedirect será una variable global inyectada por Blade
                    if (sessionProductoIdForRedirect) {
                        window.location.href = `/productos/${sessionProductoIdForRedirect}`;
                    }
                }
            });
        }
    }

    // Para 'status_boletin'
    if (sessionStatusBoletin) { // sessionStatusBoletin será una variable global inyectada por Blade
        if (sessionStatusBoletin === 'aprobado') {
            Swal.fire({
                icon: 'success',
                title: '¡Boletín Aprobado!',
                text: 'Tu boletín ha sido aprobado por el operador.',
                showConfirmButton: false,
                timer: 3000
            });
        } else if (sessionStatusBoletin === 'rechazado') {
            Swal.fire({
                icon: 'error',
                title: '¡Boletín Rechazado!',
                text: 'Tu boletín ha sido rechazado por el operador. Revisa tu correo o el detalle para más información.',
                showConfirmButton: true,
                confirmButtonText: 'Ir al detalle →',
                timer: 3000
            }).then((result) => {
                if (result.isConfirmed) {
                    // sessionBoletinIdForRedirect será una variable global inyectada por Blade
                    if (sessionBoletinIdForRedirect) {
                        window.location.href = `/boletines/${sessionBoletinIdForRedirect}`;
                    }
                }
            });
        }
    }

    // SweetAlert general para otros mensajes (success, error)
    if (sessionSuccess) { // sessionSuccess será una variable global inyectada por Blade
        Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: sessionSuccess,
            showConfirmButton: false,
            timer: 3033
        });
    }

    if (sessionError) { // sessionError será una variable global inyectada por Blade
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: sessionError,
            showConfirmButton: false,
            timer: 3033
        });
    }
});