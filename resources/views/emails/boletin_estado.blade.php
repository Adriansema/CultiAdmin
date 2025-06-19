<!DOCTYPE html>
<html>
<head>
    <title>Actualización del Estado de tu Boletín</title>
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
        <p>Te informamos sobre una actualización en el estado de tu **Boletín** con ID **{{ $boletin->id }}**.</p>

        <div class="status-box status-{{ strtolower($boletin->estado) }}">
            Tu Boletín ha sido **{{ ucfirst($boletin->estado) }}**.
        </div>

        <p><strong>Detalles del Boletín:</strong></p>
        <ul>
            <li><strong>ID de Referencia:</strong> {{ $boletin->id }}</li>
            {{-- Asumiendo que 'contenido' es el campo principal del boletín --}}
            <li><strong>Contenido (extracto):</strong> "{{ Str::limit($boletin->contenido ?? 'Sin contenido disponible', 250) }}"</li>
            <li><strong>Observaciones del Operador:</strong>
                @if($boletin->observaciones) {{-- Usando 'observaciones' del modelo Boletin --}}
                    "{{ $boletin->observaciones }}"
                @else
                    No hay observaciones adicionales del operador.
                @endif
            </li>
            <li><strong>Fecha de Actualización:</strong> {{ $boletin->updated_at ? $boletin->updated_at->format('d/m/Y H:i A') : 'N/A' }}</li>
            {{-- Puedes añadir más campos específicos del boletín aquí si son relevantes --}}
            {{-- <li><strong>Fecha de Publicación:</strong> {{ $boletin->fecha_publicacion ? $boletin->fecha_publicacion->format('d/m/Y') : 'N/A' }}</li> --}}
            {{-- <li><strong>Autor:</strong> {{ $boletin->user->name ?? 'N/A' }}</li> --}}
        </ul>

        @if ($boletin->estado === 'rechazado')
            <p>Tu Boletín ha sido **rechazado**. Te recomendamos revisar las observaciones del operador y editarlo para una nueva validación.</p>
            <p style="text-align: center;">
                <a href="{{ route('boletines.edit', $boletin->id) }}" class="button">
                    Ir a Editar Boletín
                </a>
            </p>
        @elseif ($boletin->estado === 'aprobado')
            <p>¡Felicidades! Tu Boletín ha sido **aprobado** y ya está disponible.</p>
            {{-- Opcional: Si tienes una ruta para ver el boletín aprobado en el frontend --}}
            {{-- <p style="text-align: center;">
                <a href="{{ route('boletines.show', $boletin->id) }}" class="button">
                    Ver Boletín
                </a>
            </p> --}}
        @else
            <p>Tu Boletín actualmente está **pendiente** de revisión por parte de un operario. Te notificaremos cuando haya una actualización.</p>
        @endif

        <p>Gracias por tu paciencia y colaboración.</p>

        <div class="footer">
            <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
            <p>&copy; {{ date('Y') }} Tu Empresa. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>