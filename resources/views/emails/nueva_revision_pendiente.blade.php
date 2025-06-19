<!DOCTYPE html>
<html>

<head>
    <title>Nueva Revisión Pendiente - {{ ucfirst($itemTipo) }}</title>
    <style>
        /* Estilos generales para el cuerpo del correo */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            /* Fondo blanco para TODO el correo */
            background-color: #FFFFFF;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            /* Asegurarse de que el color de fondo blanco se muestre en algunos clientes de correo */
            /* Puede que necesites un wrapper adicional o una tabla si el cliente de correo lo ignora */
        }

        /* Contenedor principal del correo, que ahora tendrá su propio fondo blanco explícito */
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px 30px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #FFFFFF; /* Fondo blanco explícito para el contenido */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        /* Estilo para el encabezado principal */
        h1 {
            color: #0056b3;
            font-size: 28px;
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        /* Párrafos de texto */
        p {
            margin-bottom: 15px;
            font-size: 15px;
        }

        /* Resaltar texto importante */
        strong {
            color: #007bff;
        }

        /* Lista de detalles */
        ul {
            list-style: none;
            padding: 0;
            margin: 25px 0;
            border-top: 1px solid #f0f0f0;
            padding-top: 20px;
        }

        ul li {
            margin-bottom: 10px;
            padding-left: 15px;
            position: relative;
            font-size: 14px;
        }

        /* Icono de lista (bullet) */
        ul li:before {
            content: '•';
            color: #007bff;
            position: absolute;
            left: 0;
            top: 2px;
        }

        /* Estilo para el mensaje intuitivo como el de Twitch */
        .welcome-section {
            background-color: #E6F0FF; /* Fondo azul claro, similar al ejemplo */
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome-section h2 {
            color: #004085;
            font-size: 24px;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .welcome-section p {
            font-size: 16px;
            color: #004085;
            margin-bottom: 20px;
        }

        /* Contenedor del icono, centrado y con espacio */
        .icon-container {
            text-align: center; /* Asegura que la imagen esté centrada */
            margin-bottom: 15px;
        }

        /* Estilo para el logo */
        .logo-img {
            max-width: 150px; /* Tamaño máximo del logo */
            height: auto;    /* Mantener proporciones */
            display: inline-block; /* Asegura que se centre con text-align: center */
            /* Eliminar opacity-90 que podría hacerla menos visible */
        }

        /* Estilo para el botón de acción */
        .button {
            display: inline-block;
            background-color: #6C5CE7;
            color: white !important;
            padding: 14px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-size: 17px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: #5849C9;
        }

        /* Pie de página */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 0.85em;
            color: #888;
            border-top: 1px solid #f0f0f0;
            padding-top: 20px;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #FFFFFF;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <div class="container">
                    <div class="welcome-section">
                        <div class="icon-container">
                            <img src="{{ url('/images/cultivasena.svg') }}" alt="Logo Cultiva Sena" class="logo-img" style="max-width: 150px; height: auto; display: block; margin: 0 auto;">
                        </div>
                        <h2>¡Nueva Revisión Pendiente!</h2>
                        <p>Un nuevo elemento ha sido registrado y está esperando tu atención.</p>
                    </div>

                    <p>Tienes un nuevo elemento de tipo **{{ ucfirst($itemTipo) }}** pendiente de revisión.</p>

                    <p><strong>Detalles Clave del {{ strtolower($itemTipo) }}:</strong></p>
                    <ul>
                        <li><strong>ID:</strong> {{ $item->id }}</li>
                        <li><strong>Tipo:</strong> {{ ucfirst($item->tipo) }}</li>
                        <li><strong>Estado:</strong> {{ ucfirst($item->estado) }}</li>
                        <li><strong>Creado por:</strong> {{ $item->user->name ?? 'Usuario Desconocido' }}</li>
                        <li><strong>Fecha de Registro:</strong> {{ $item->created_at->format('d/m/Y H:i A') }}</li>

                        @if ($item->imagen)
                            <li><strong>Adjunto:</strong> <a href="{{ asset('storage/' . $item->imagen) }}" target="_blank">Ver
                                    Imagen Asociada</a></li>
                        @endif
                    </ul>

                    <p>Por favor, ingresa al sistema para revisar a fondo este elemento y gestionar su estado.</p>
                    <p style="text-align: center;">
                        {{-- Condicionalmente dirige al usuario según el tipo de ítem --}}
                        @if ($itemTipo === 'Boletín')
                            <a href="{{ route('pendientes.boletines.index') }}" class="button">
                                Ir a Boletines Pendientes
                            </a>
                        @elseif ($itemTipo === 'Producto')
                            <a href="{{ route('pendientes.productos.index') }}" class="button">
                                Ir a Productos Pendientes
                            </a>
                        @else
                            {{-- Fallback si no es ni boletín ni producto --}}
                            <a href="{{ route('dashboard') }}" class="button">
                                Ir a la Plataforma
                            </a>
                        @endif
                    </p>

                    <div class="footer">
                        <p>&copy; {{ date('Y') }} Cultiva Sena. Todos los derechos reservados.</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>