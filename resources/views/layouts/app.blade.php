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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="flex flex-col h-screen overflow-hidden font-sans antialiased" x-data="{ sidebarOpen: true }">

    <div class="p-1 bg-blue-500">
        <img src="https://zajuna.sena.edu.co/img/logos/gov-logo.svg" alt="Logo GOV.CO" width="100px">
    </div>

    <x-banner />

    <div class="relative flex flex-1 overflow-hidden">
        <x-sidebar />

        <div class="flex flex-col flex-1 w-full overflow-hidden" :class="sidebarOpen ? 'ml-8' : 'ml-4'">
            @if (isset($header))
                <header class="px-4 py-6 bg-white shadow">
                    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif
            <main class="flex-1 h-full overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('modals')
    @yield('scripts')
    @livewireScripts

    <script>
        const sessionStatusProducto = @json(session('status_producto', null));
        const sessionProductoIdForRedirect = @json(session('producto_id_for_redirect', null));
        const sessionStatusBoletin = @json(session('status_boletin', null));
        const sessionBoletinIdForRedirect = @json(session('boletin_id_for_redirect', null));
        const sessionSuccess = @json(session('success', null));
        const sessionError = @json(session('error', null));
    </script>
    <script type="module" src="{{ asset('js/accesibilidad.js') }}"></script>
</body>

</html>
