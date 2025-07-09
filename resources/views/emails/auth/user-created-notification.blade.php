<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Tu acceso ha sido creado – ¡Bienvenido(a) a Cultiva Sena!') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #A8E61D, #4CAF50);
            background-color: #4CAF50;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }

        .header img {
            max-width: 150px;
            height: auto;
        }

        .header h1 {
            color: #39A900;
            margin-top: 10px;
            font-size: 24px;
        }

        .content {
            padding: 20px 0;
        }

        .password-box {
            background-color: #e6ffed;
            border: 2px solid #38a169;
            border-radius: 12px;
            padding: 15px 20px;
            text-align: center;
            font-size: 1.5em;
            font-weight: bold;
            color: #0F6F20;
            word-break: break-all;
            margin-bottom: 25px;
        }

        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            background-color: #39A900;
            color: #ffffff !important;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            font-size: 12px;
            color: #777777;
        }

        a {
            color: #39A900;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ URL::to('images/CultivaAdmin.png') }}" alt="Logo Cultiva Admin">
            <h1>{{ __('Tu acceso ha sido creado') }}</h1>
        </div>

        <div class="content">
            @php
                $currentHour = now()->hour;
                $saludo = '';

                if ($currentHour >= 5 && $currentHour < 12) {
                    $saludo = 'Buenos días';
                } elseif ($currentHour >= 12 && $currentHour < 19) {
                    $saludo = 'Buenas tardes';
                } else {
                    $saludo = 'Buenas noches';
                }
            @endphp

            <p>{{ $saludo }} {{ $userName }},</p>

            <p>
                Te damos la bienvenida a <strong>Cultiva Sena</strong>, la plataforma para gestionar productos agrícolas
                con eficiencia y confianza.
            </p>

            <p>Hemos creado tu cuenta, y estos son tus datos de acceso:</p>

            <p><strong> Usuario:</strong> {{ $userEmail }}</p>
            <p><strong> Contraseña temporal:</strong></p>
            <div class="password-box">
                {{ $generatedPassword }}
            </div>

            <p style="font-size: 16px; color: #4a5568;">
                Por tu seguridad, esta contraseña es <strong>temporal</strong>.
                Te recomendamos cambiarla apenas inicies sesión.
            </p>

            <div class="button-container">
                <a href="{{ url('/login') }}" class="button">{{ __('Iniciar sesión') }}</a>
            </div>

            <p>
                Si no solicitaste esta cuenta o tienes alguna duda, por favor escríbenos a <a
                    href="mailto:soporteayuda2025@gmail.com">soporteayuda2025@gmail.com</a>.
                Estamos para ayudarte.
            </p>

            <p>Gracias por ser parte de este cultivo digital 🌿</p>
        </div>

        <div class="footer">
            <p>— El equipo de Cultiva Sena</p>
            <p><em>🛡️ Este mensaje es confidencial. No compartas tu contraseña con nadie. Si sospechas de un acceso no
                    autorizado, contáctanos de inmediato.</em></p>
            <p>&copy; {{ date('Y') }} Cultiva Sena. Todos los derechos reservados.</p>
        </div>
    </div>
</body>

</html>
