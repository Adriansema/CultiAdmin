<!DOCTYPE html>
<html>
<head>
    <title>Actualización de Estado del Boletin</title>
</head>
<body>
    <h1>Hola,</h1>
    <p>Te informamos sobre una actualización en el estado de tu boletin.</p>
    <p><strong>Tipo:</strong> Boletin</p>
    <p><strong>Estado:</strong> {{ ucfirst($boletin->estado) }}</p>
    <p><strong>Última Observación del Operador:</strong> 
        @if($boletin->observaciones)
            {{ $boletin->observaciones }}
        @else
            No hay observaciones adicionales.
        @endif
    </p>

    @if ($boletin->estado === 'rechazado')
        <p>Tu noticia ha sido rechazada. Te recomendamos revisar la observación y editarla para una nueva validación.</p>
        <p>
            <a href="{{ route('boletines.edit', $boletin->id) }}" style="background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
                Ir a Editar boletin
            </a>
        </p>
    @elseif ($boletin->estado === 'aprobado')
        <p>¡Felicidades! Tu boletin ha sido aprobada y ya está disponible.</p>
    @else
        <p>Tu boletin está pendiente de revisión por parte del operador.</p>
    @endif

    <p>Gracias.</p>
</body>
</html>