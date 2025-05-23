<!DOCTYPE html>
<html>
<head>
    <title>Actualización de Estado de Noticia</title>
</head>
<body>
    <h1>Hola,</h1>
    <p>Te informamos sobre una actualización en el estado de tu noticia.</p>
    <p><strong>Tipo:</strong> Noticia</p>
    <p><strong>Estado:</strong> {{ ucfirst($producto->estado) }}</p>
    <p><strong>Última Observación del Operador:</strong> 
        @if($producto->observaciones)
            {{ $producto->observaciones }}
        @else
            No hay observaciones adicionales.
        @endif
    </p>

    @if ($producto->estado === 'rechazado')
        <p>Tu noticia ha sido rechazada. Te recomendamos revisar la observación y editarla para una nueva validación.</p>
        <p>
            <a href="{{ route('productos.edit', $producto->id) }}" style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
                Ir a Editar Noticia
            </a>
        </p>
    @elseif ($producto->estado === 'aprobado')
        <p>¡Felicidades! Tu noticia ha sido aprobada y ya está disponible.</p>
    @else
        <p>Tu noticia está pendiente de revisión por parte del operador.</p>
    @endif

    <p>Gracias.</p>
</body>
</html>