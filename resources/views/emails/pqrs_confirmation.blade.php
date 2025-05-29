{{-- resources/views/emails/pqrs_confirmation.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <title>Confirmación de Recepción de PQR</title>
</head>
<body>
    <h1>¡Hola {{ $pqrs->nombre ?? $pqrs->email }}!</h1> {{-- Usa el nombre si existe, sino el email --}}

    <p>Hemos recibido tu PQR exitosamente. A continuación, los detalles de tu solicitud:</p>

    <ul>
        <li><strong>Referencia:</strong> {{ $pqrs->id }}</li>
        <li><strong>Tipo:</strong> {{ ucfirst($pqrs->tipo) }}</li> {{-- ucfirst para capitalizar la primera letra --}}
        <li><strong>Asunto:</strong> {{ $pqrs->asunto }}</li>
        <li><strong>Mensaje:</strong> <br>{{ $pqrs->mensaje }}</li>
        <li><strong>Fecha de Envío:</strong> {{ $pqrs->created_at->format('d/m/Y H:i') }}</li>
        {{-- Puedes añadir más detalles aquí si los necesitas --}}
    </ul>

    <p>Nuestro equipo de soporte revisará tu solicitud y te contactará a la brevedad posible.</p>

    <p>Gracias por tu paciencia.</p>

    <p>Atentamente,</p>
    <p>El equipo de [Nombre de tu Aplicación/Empresa]</p>
</body>
</html>