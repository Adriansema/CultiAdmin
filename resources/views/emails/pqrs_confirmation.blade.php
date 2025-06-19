<!DOCTYPE html>
<html>
<head>
    <title>Confirmación de Recepción de tu PQR</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 25px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        h1 {
            color: #0056b3;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        p {
            margin-bottom: 15px;
        }
        strong {
            color: #007bff;
        }
        ul {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        ul li {
            margin-bottom: 10px;
            padding-left: 15px;
            position: relative;
        }
        ul li:before {
            content: '•';
            color: #007bff;
            position: absolute;
            left: 0;
            top: 2px;
        }
        .highlight-box {
            background-color: #e6f7ff; /* Azul muy claro */
            border: 1px solid #b3e0ff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.1em;
            color: #0056b3;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.85em;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Hola {{ $pqrs->nombre ?? 'Estimado Usuario' }}!</h1>

        <p>Agradecemos tu comunicación. Hemos recibido exitosamente tu PQR y le hemos asignado la siguiente referencia:</p>

        <div class="highlight-box">
            <strong>Referencia de tu PQR: {{ $pqrs->id }}</strong>
        </div>

        <p>A continuación, puedes encontrar un resumen de los detalles de tu solicitud:</p>

        <ul>
            <li><strong>Tipo de Solicitud:</strong> {{ ucfirst($pqrs->tipo) }}</li>
            <li><strong>Asunto:</strong> "{{ $pqrs->asunto }}"</li>
            <li><strong>Mensaje:</strong>
                <p style="white-space: pre-wrap; background-color: #f0f0f0; padding: 10px; border-radius: 5px; border: 1px solid #e0e0e0; font-style: italic;">
                    {{ $pqrs->mensaje }}
                </p>
            </li>
            <li><strong>Fecha y Hora de Envío:</strong> {{ $pqrs->created_at->format('d/m/Y H:i A') }}</li>
            {{-- Si tienes el email del remitente en el PQR --}}
            @if (!empty($pqrs->email))
                <li><strong>Email de Contacto:</strong> {{ $pqrs->email }}</li>
            @endif
            {{-- Si tienes un campo para el número de teléfono --}}
            @if (!empty($pqrs->telefono))
                <li><strong>Teléfono de Contacto:</strong> {{ $pqrs->telefono }}</li>
            @endif
            {{-- Puedes añadir otros campos relevantes aquí --}}
        </ul>

        <p>Nuestro equipo de soporte revisará tu solicitud con la referencia **{{ $pqrs->id }}** y te contactará a la brevedad posible, o a más tardar dentro de nuestros plazos de respuesta establecidos.</p>

        <p>Agradecemos tu paciencia mientras procesamos tu PQR.</p>

        <div class="footer">
            <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
            <p>Atentamente,<br>El equipo de Tu Empresa</p>
            <p>&copy; {{ date('Y') }} Tu Empresa. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>