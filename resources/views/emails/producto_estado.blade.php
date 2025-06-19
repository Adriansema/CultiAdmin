<!DOCTYPE html>
<html>
<head>
    <title>Actualización de tu {{ ucfirst($producto->tipo) }}</title>
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
        .status-box {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 1.1em;
            font-weight: bold;
        }
        .status-aprobado {
            background-color: #d4edda; /* Verde claro */
            color: #155724; /* Verde oscuro */
            border: 1px solid #c3e6cb;
        }
        .status-rechazado {
            background-color: #f8d7da; /* Rojo claro */
            color: #721c24; /* Rojo oscuro */
            border: 1px solid #f5c6cb;
        }
        .status-pendiente {
            background-color: #fff3cd; /* Amarillo claro */
            color: #856404; /* Amarillo oscuro */
            border: 1px solid #ffeeba;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white !important;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            margin-top: 25px;
            display: block; /* Para que ocupe todo el ancho disponible si es necesario */
            width: fit-content; /* Se ajusta al contenido */
            margin-left: auto; /* Centrar el botón */
            margin-right: auto;
        }
        .button:hover {
            background-color: #0056b3;
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
        <h1>Hola,</h1>
        <p>Te informamos sobre una actualización en el estado de tu **{{ strtolower(ucfirst($producto->tipo)) }}** con ID **{{ $producto->id }}**.</p>

        <div class="status-box status-{{ strtolower($producto->estado) }}">
            Tu {{ strtolower(ucfirst($producto->tipo)) }} ha sido **{{ ucfirst($producto->estado) }}**.
        </div>

        <p><strong>Detalles:</strong></p>
        <ul>
            <li><strong>Tipo de Elemento:</strong> {{ ucfirst($producto->tipo) }}</li>
            <li><strong>ID de Referencia:</strong> {{ $producto->id }}</li>
            <li><strong>Observaciones del Operador:</strong>
                @if($producto->observaciones_operador) {{-- Asumiendo que el operador deja observaciones en 'observaciones_operador' --}}
                    "{{ $producto->observaciones_operador }}"
                @else
                    No hay observaciones adicionales del operador.
                @endif
            </li>
            <li><strong>Fecha de Actualización:</strong> {{ $producto->updated_at ? $producto->updated_at->format('d/m/Y H:i A') : 'N/A' }}</li>
        </ul>

        @if ($producto->estado === 'rechazado')
            <p>Tu {{ strtolower(ucfirst($producto->tipo)) }} ha sido **rechazada**. Te recomendamos revisar las observaciones del operador y editar el elemento para una nueva validación.</p>
            <p style="text-align: center;">
                <a href="{{ route('productos.edit', $producto->id) }}" class="button">
                    Ir a Editar {{ ucfirst($producto->tipo) }}
                </a>
            </p>
        @elseif ($producto->estado === 'aprobado')
            <p>¡Felicidades! Tu {{ strtolower(ucfirst($producto->tipo)) }} ha sido **aprobada** y ya está disponible.</p>
        @else
            <p>Tu {{ strtolower(ucfirst($producto->tipo)) }} actualmente está **pendiente** de revisión por parte de un operario. Te notificaremos cuando haya una actualización.</p>
        @endif

        <p>Gracias por tu paciencia y colaboración.</p>

        <div class="footer">
            <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
            <p>&copy; {{ date('Y') }} Tu Empresa. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>