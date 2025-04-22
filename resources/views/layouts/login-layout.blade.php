<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - Cultiva SENA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background-image: url('{{ asset('images/MORA 1 1.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-card {
            background-color: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 1rem;
            padding: 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
        }

        .login-logo {
            display: block;
            margin: 0 auto 1rem auto;
            max-width: 200px;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        @yield('content')
    </div>
</body>
</html>


{{-- resources/views/layouts/login-layout.blade.php --}}
{{-- Este archivo es el layout para la página de inicio de sesión. --}}
{{-- Puedes personalizarlo según tus necesidades. --}}
{{-- Aquí se utiliza Tailwind CSS para el estilo, pero puedes adaptarlo a tu propio CSS. --}}
{{-- Asegúrate de que los enlaces a los archivos CSS y JS sean correctos. --}}
