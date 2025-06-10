<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <div class="fullscreen-image-overlay-purple">
        <img src="{{ asset('images/CultivaAdmin.png') }}" alt="Descripción de la imagen" class="w-full h-auto">
    </div>
</head>

<body class="flex items-center justify-center min-h-screen">
    <div>
        @yield('content')
    </div>

    @livewireScripts
</body>

</html>
