document.addEventListener('DOMContentLoaded', function() {
    // Para 'status_producto'
    if (sessionStatusProducto) { // sessionStatusProducto sera una variable global inyectada por Blade
        if (sessionStatusProducto === 'aprobado') {
            Swal.fire({
                icon: 'success',
                title: '!Noticia Aprobada!',
                text: 'Tu noticia ha sido aprobada por el operador.',
                showConfirmButton: false,
                timer: 3000
            });
        } else if (sessionStatusProducto === 'rechazado') {
            Swal.fire({
                icon: 'error',
                title: '!Noticia Rechazada!',
                text: 'Tu noticia ha sido rechazada por el operador. Revisa tu correo o el detalle para mas informacion.',
                showConfirmButton: true,
                confirmButtonText: 'Ir al detalle',
                timer: 3000
            }).then((result) => {
                if (result.isConfirmed) {
                    // sessionProductoIdForRedirect sera una variable global inyectada por Blade
                    if (sessionProductoIdForRedirect) {
                        window.location.href = `/productos/${sessionProductoIdForRedirect}`;
                    }
                }
            });
        }
    }

    // Para 'status_boletin'
    if (sessionStatusBoletin) { // sessionStatusBoletin sera una variable global inyectada por Blade
        if (sessionStatusBoletin === 'aprobado') {
            Swal.fire({
                icon: 'success',
                title: '!Boletin Aprobado!',
                text: 'Tu boletin ha sido aprobado por el operador.',
                showConfirmButton: false,
                timer: 3000
            });
        } else if (sessionStatusBoletin === 'rechazado') {
            Swal.fire({
                icon: 'error',
                title: '!Boletin Rechazado!',
                text: 'Tu boletin ha sido rechazado por el operador. Revisa tu correo o el detalle para mas informacion.',
                showConfirmButton: true,
                confirmButtonText: 'Ir al detalle',
                timer: 3000
            }).then((result) => {
                if (result.isConfirmed) {
                    // sessionBoletinIdForRedirect sera una variable global inyectada por Blade
                    if (sessionBoletinIdForRedirect) {
                        window.location.href = `/boletines/${sessionBoletinIdForRedirect}`;
                    }
                }
            });
        }
    }

    // SweetAlert general para otros mensajes (success, error)
    if (sessionSuccess) { // sessionSuccess sera una variable global inyectada por Blade
        Swal.fire({
            icon: 'success',
            title: 'Exito',
            text: sessionSuccess,
            showConfirmButton: false,
            timer: 3033
        });
    }

    if (sessionError) { // sessionError sera una variable global inyectada por Blade
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: sessionError,
            showConfirmButton: false,
            timer: 3033
        });
    }
});