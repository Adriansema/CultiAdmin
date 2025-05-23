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

    {{-- Define la variable global para las rutas de assets en JS --}}
    {{-- <script>
        window.assetUrl = "{{ asset('') }}";
    </script> --}}

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <script>
        (function() {
            if (localStorage.getItem('contrastHigh') === 'true') {
                document.documentElement.classList.add('contrast-high');
                document.body.classList.add('contrast-high');
            }
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark-mode');
                document.body.classList.add('dark-mode');
            }
            if (localStorage.getItem('fontSize')) {
                document.documentElement.style.fontSize = localStorage.getItem('fontSize') + 'px';
            }
        })();
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="flex flex-col h-screen overflow-hidden font-sans antialiased">

    <!-- Cinta GOV.CO -->
    <div class="p-1 bg-blue-500">
        <img src="https://zajuna.sena.edu.co/img/logos/gov-logo.svg" alt="Logo GOV.CO" width="100px">
    </div>

    <x-banner />

    <!-- Sidebar dinámico según el rol -->
    @role('administrador')
        <x-sidebar-admin />
        @elserole('operador')
        <x-sidebar-operador />
    @endrole

    <!-- Contenido principal -->
    <div class="flex flex-col flex-1 overflow-hidden bg-gray-2">
        @if (isset($header))
            <header class="px-4 py-6 bg-white shadow">
                <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="flex-1 h-full p-6 overflow-y-auto" :class="sidebarOpen ? 'pl-64' : 'pl-16'">
            @yield('content')
        </main>
    </div>

    {{-- @stack('modals')
    @livewireScripts
    @yield('scripts') --}}

    <script type="module" src="{{ asset('js/accesibilidad.js') }}"></script>
</body>

</html>