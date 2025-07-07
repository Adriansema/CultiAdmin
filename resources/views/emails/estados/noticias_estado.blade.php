<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de tu Noticia</title>
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
            margin: 20px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .header h1 {
            color: #333;
            font-size: 24px;
        }

        .content {
            padding: 20px 0;
        }

        .content p {
            margin-bottom: 10px;
        }

        .status-box {
            padding: 10px 15px;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
            color: white;
            text-transform: uppercase;
        }

        .status-aprobado {
            background-color: #28a745;
            /* Verde */
        }

        .status-rechazado {
            background-color: #dc3545;
            /* Rojo */
        }

        .status-pendiente {
            background-color: #ffc107;
            /* Amarillo */
            color: #333;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #777;
        }

        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Actualización de Estado de tu Noticia</h1>
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>Queremos informarte sobre el estado de tu noticia titulada: <strong>"{{ $noticia->titulo }}"</strong>.
            </p>

            <p>El estado actual es:
                <span
                    class="status-box
                    @if ($noticia->estado === 'aprobado') status-aprobado
                    @elseif ($noticia->estado === 'rechazado') status-rechazado
                    @else status-pendiente @endif">
                    {{ ucfirst($noticia->estado) }}
                </span>
            </p>

            @if ($noticia->estado === 'rechazado' && $noticia->observaciones)
                <p><strong>Observaciones:</strong></p>
                <p style="background-color: #f8d7da; border-left: 5px solid #dc3545; padding: 10px; border-radius: 5px;">
                    {{ $noticia->observaciones }}
                </p>
            @endif

            <p>Puedes ver los detalles de tu noticia iniciando sesión en nuestra plataforma:</p>
            <p style="text-align: center;">
                <a href="{{ url('/pendientes/noticias/' . $noticia->id_noticias) }}" class="button">Ver Noticia</a>
            </p>
        </div>
        <div class="footer">
            <p>Este es un correo electrónico automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} Tu Aplicación. Todos los derechos reservados.</p>
        </div>
    </div>
</body>

</html>
