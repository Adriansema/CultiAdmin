<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cultiva Sena') }}</title>
    <link rel="icon" href="{{ asset('images/Favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <div class="fullscreen-image-overlay-purple">
        <img src="{{ asset('images/CultivaAdmin.png') }}" alt="Descripcion de la imagen" class="w-full h-auto">
    </div>
</head>

<body class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> 
        @yield('content')
    </div>

    @livewireScripts
</body>

</html>
