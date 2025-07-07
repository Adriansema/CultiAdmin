<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Bienvenido a Cultiva SENA') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            /* Degradado de fondo */
            background: linear-gradient(to bottom, #A8E61D, #4CAF50); /* Verde claro a verde más oscuro */
            background-color: #4CAF50; /* Color de fallback */
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
            max-width: 150px; /* Ajusta el tamaño de tu logo */
            height: auto;
        }
        .header h1 {
            color: #39A900; /* Color principal de tu marca */
            margin-top: 10px;
            font-size: 24px;
        }
        .content {
            padding: 20px 0;
        }
        .password-box {
            background-color: #e6ffed; /* Un verde claro para el fondo de la contraseña */
            border: 2px solid #38a169; /* Borde verde oscuro */
            border-radius: 12px;
            padding: 15px 20px;
            text-align: center;
            font-size: 1.5em;
            font-weight: bold;
            color: #0F6F20; /* Color de texto para la contraseña */
            word-break: break-all; /* Para que la contraseña larga no desborde */
            margin-bottom: 25px; /* Espacio debajo de la caja de contraseña */
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #39A900; /* Color principal del botón */
            color: #ffffff !important; /* !important para asegurar el color de texto */
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
            {{-- Asegúrate de que esta URL sea absoluta para clientes de correo --}}
            <img src="{{ URL::to('images/CultivaAdmin.png') }}" alt="Logo Cultiva Admin" style="max-width: 150px; height: auto;">
            <h1>{{ __('Bienvenido a Cultiva SENA') }}</h1>
        </div>

        <div class="content">
            @php
                $currentHour = now()->hour; // Hora actual en Dosquebradas, Risaralda, Colombia (UTC-5)
                $saludo = '';

                if ($currentHour >= 5 && $currentHour < 12) {
                    $saludo = 'Buenos días';
                } elseif ($currentHour >= 12 && $currentHour < 19) {
                    $saludo = 'Buenas tardes';
                } else {
                    $saludo = 'Buenas noches';
                }
            @endphp

            <p>{{ $saludo }} **{{ $userName }}**,</p>
            <p>{{ __('Nos complace informarte que tu cuenta ha sido creada exitosamente en Cultiva SENA.') }}</p>

            <p>{{ __('Tu contraseña generada para el primer ingreso es:') }}</p>
            <div class="password-box">
                {{ $generatedPassword }}
            </div>

            <p style="font-size: 16px; color: #4a5568; line-height: 1.6;">
                {{ __('Esta contraseña ha sido generada exclusivamente para que realices tu primer ingreso al sistema.') }}
                {{ __('Úsala al iniciar sesión por primera vez en Cultiva SENA. Luego podrás cambiarla por una de tu preferencia desde tu perfil.') }}
            </p>

            <div class="button-container">
                {{-- Si tienes una URL para el login, cámbiala aquí --}}
                <a href="{{ url('/login') }}" class="button">{{ __('Ir al Inicio de Sesión') }}</a>
            </div>

            <p>{{ __('Gracias por unirte a nuestra comunidad.') }}</p>
        </div>

        <div class="footer">
            <p>{{ __('Saludos,') }}<br>{{ __('El equipo de Cultiva SENA') }}</p>
            <p>&copy; {{ date('Y') }} Cultiva SENA. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>