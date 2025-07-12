<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Restablecer Contraseña - Cultiva sena') }}</title>
    <style>
        /* Estilos basicos para compatibilidad de email. Tailwind no funciona directamente aqui sin post-procesamiento. */
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            /* Degradado para el fondo del body */
            background: linear-gradient(to bottom, #A8E61D, #4CAF50); /* Verde claro a verde mas oscuro */
            background-color: #4CAF50; /* Color de fallback para clientes que no soportan degradados */
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
            max-width: 150px; /* Ajusta el tamano de tu logo */
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
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #39A900; /* Color principal del boton */
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
            {{-- Puedes usar un logo aqui --}}
            <img src="{{ asset('images/logo-cultiva-sena.png') }}" alt="Cultiva SENA Logo">
            <h1>{{ __('Restablecimiento de Contraseña') }}</h1>
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

            <p>{{ $saludo }} <b>{{ $user->name ?? 'Usuario' }}</b>,</p>
            <p>{{ __('Estas recibiendo este correo porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta.') }}</p>

            <div class="button-container">
                <a href="{{ $url }}" class="button">{{ __('Restablecer Contrasena') }}</a>
            </div>

            <p>{{ __('Este enlace de restablecimiento de contraseña expirara en :count minutos.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]) }}</p>

            <p>{{ __('Si no solicitaste un restablecimiento de contraseña, no se requiere ninguna acción adicional.') }}</p>
        </div>

        <div class="footer">
            <p>{{ __('Saludos,') }}<br>{{ __('El equipo de Cultiva sena') }}</p>
            <p>&copy; {{ date('Y') }} Cultiva sena. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>