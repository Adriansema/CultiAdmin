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
</head>

<body class="flex flex-col h-screen overflow-hidden font-sans antialiased">

    <!-- Cinta GOV.CO -->
    <div class="p-1 bg-blue-500">
        <img src="https://zajuna.sena.edu.co/img/logos/gov-logo.svg" alt="Logo GOV.CO" width="100px">

    </div>

    <x-banner />

    <div class="flex flex-1 overflow-hidden">

        <!-- Sidebar con scroll interno -->
        {{-- <div class="w-64 bg-[#00304D] text-white flex-shrink-0 overflow-y-auto">
            @livewire('navigation-menu')
        </div> --}}

        <!-- Contenido principal -->
         <div class="flex flex-col flex-1 overflow-hidden bg-gray-2">
            <x-sidebar>
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
            </x-sidebar>
        </div>
    </div>

    @stack('modals')
    @livewireScripts
    @yield('scripts')

    <script type="module" src="{{ asset('js/accesibilidad.js') }}"></script>
</body>

</html>
