<!DOCTYPE html>
<html>
<head>
    <title>Actualización del estado de tu Boletín</title>
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
            display: block;
            width: fit-content;
            margin-left: auto;
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
        <p>Te informamos sobre una actualización en el estado de tu <b>Boletin</b> con ID <b>{{ $boletin->id }}</b>.</p>

        <div class="status-box status-{{ strtolower($boletin->estado) }}">
            Tu Boletin ha sido <b>{{ ucfirst($boletin->estado) }}<b>.
        </div>

        <p><strong>Detalles del boletín:</strong></p>
        <ul>
            <li><strong>ID de referencia:</strong> {{ $boletin->id }}</li>
            {{-- Asumiendo que 'contenido' es el campo principal del boletin --}}
            <li><strong>Contenido (extracto):</strong> "{{ Str::limit($boletin->contenido ?? 'Sin contenido disponible', 250) }}"</li>
            <li><strong>Observaciones</strong>
                @if($boletin->observaciones) {{-- Usando 'observaciones' del modelo Boletin --}}
                    "{{ $boletin->observaciones }}"
                @else
                    No hay observaciones adicionales.
                @endif
            </li>
            <li><strong>Fecha de actualización:</strong> {{ $boletin->updated_at ? $boletin->updated_at->format('d/m/Y H:i A') : 'N/A' }}</li>
        </ul>

        @if ($boletin->estado === 'rechazado')
            <p>Tu boletín ha sido <b>rechazado</b>. Te recomendamos revisar las observaciones y editarlo para una nueva validación.</p>
            <p style="text-align: center;">
            </p>
        @elseif ($boletin->estado === 'aprobado')
            <p>¡Felicidades! Tu boletín ha sido <b>aprobado</b> y ya está disponible.</p>

        @else
            <p>Tu Boletín actualmente esta <b>pendiente</b> de revisión. Te notificaremos cuando haya una actualización.</p>
        @endif

        <p>Gracias por tu paciencia y colaboración.</p>

        <div class="footer">
            <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
            <p>&copy; {{ date('Y') }} Cultiva sena. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>