<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cultiva Sena') }}</title>
    <!-- SE CAMBIO EL NOMBRE DE LA PESTAÑA DE NAVEGACION POR "Cultiva Sena" -->

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/Favicon.svg') }}">
    <!-- SE AGREGA ESTA LINEA PARA QUE MUESTRE EL ICONO DE LA PESTAÑA DE NAVEGACIÓN -->

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
    {{-- AÑADIR CLASES DE ANCHO Y CENTRADO A ESTE DIV --}}
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> 
        @yield('content')
    </div>

    @livewireScripts
</body>

</html>
