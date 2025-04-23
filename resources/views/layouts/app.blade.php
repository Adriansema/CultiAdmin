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
    </head>
    
    <body class="flex flex-col h-screen overflow-hidden font-sans antialiased">

        <!-- Cinta GOV.CO -->
        <div class="p-1 bg-blue-500">
            <img src="https://zajuna.sena.edu.co/img/logos/gov-logo.svg" alt="Logo GOV.CO" width="100px">
        </div>

        <div class="flex flex-1 overflow-hidden">

            <!-- Sidebar con scroll interno -->
            <div class="w-64 bg-[#00304D] text-white flex-shrink-0 overflow-y-auto">
                @livewire('navigation-menu')
            </div>

            <!-- Contenido principal -->
            <div class="flex flex-col flex-1 overflow-hidden bg-gray-100">

                @if (isset($header))
                    <header class="px-4 py-6 bg-white shadow">
                        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main class="flex-1 p-6 overflow-y-auto">
                    @yield('content')
                </main>

            </div>
        </div>

        @stack('modals')
        @livewireScripts
        @yield('scripts')

    </body>
</html>
