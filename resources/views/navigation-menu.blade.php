<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->

<<<<<<< Updated upstream
    <div class="fixed flex flex-col w-64 h-screen text-white shadow-lg" style="background-color: #00304D;">
=======
    
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block w-auto h-9" />
                    </a>
                </div>

                <!-- Navigation Links -->
                    <!-- actualizacion 09/04/2025 -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">

                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="relative ms-3">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50">
                                        {{ Auth::user()->currentTeam->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Team Switcher -->
                                    @if (Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif


                <!-- Settings Dropdown -->
                <div class="relative ms-3">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm transition border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300">
                                    <img class="object-cover rounded-full size-8" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50">
                                        {{ Auth::user()->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>
>>>>>>> Stashed changes

        <!-- Logo -->
        <div class="flex items-center justify-center p-4">
            <a href="{{ route('dashboard') }}">
                <x-application-mark class="w-auto h-9" />
            </a>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-4 overflow-y-auto">

                <!-- Nueva entrada (simulación de submenú abierto) -->
            <div class="space-y-1">
                <!-- Botón para abrir el submenú -->
                <button type="button"
                    class="flex items-center w-full px-3 py-2 font-medium text-green-600 transition bg-green-100 rounded-md hover:bg-green-200"
                    data-toggle="submenu"
                    data-target="#submenu-nueva-entrada">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Entrada
                    <svg class="w-4 h-4 ml-auto transition-transform transform" data-icon>
                        <path fill="currentColor" d="M5 8l4 4 4-4" />
                    </svg>
                </button>

                <!-- Submenú que contiene las opciones Cultivo y Boletín -->
                <div id="submenu-nueva-entrada" class="hidden pl-6 mt-1 space-y-1">
                    <a href="/nueva-entrada/cultivo" class="block px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-200">
                        Cultivo
                    </a>
                    <a href="/nueva-entrada/boletin" class="block px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-200">
                        Boletín
                    </a>
                </div>
            </div>

                <!-- Panel -->
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    <svg class="inline w-5 h-5 mr-2 text-orange-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h4v11H3zM10 3h4v18h-4zM17 14h4v7h-4z"/>
                    </svg>
                    {{ __('Inicio') }}
                </x-responsive-nav-link>

                <!-- Productos Agrícolas con submenú -->
                <div class="space-y-1">
                    <button type="button"
                        class="flex items-center w-full px-3 py-2 font-medium text-green-600 transition bg-green-100 rounded-md hover:bg-green-200"
                        data-toggle="submenu"
                        data-target="#submenu-productos">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 6h18M3 14h9m-3 4h6" />
                        </svg>
                        {{ __('Productos') }}
                        <svg class="w-4 h-4 ml-auto transition-transform transform" data-icon>
                            <path fill="currentColor" d="M5 8l4 4 4-4" />
                        </svg>
                    </button>

                    <div id="submenu-productos" class="hidden mt-1 ml-6 space-y-1">
                        <x-responsive-nav-link href="{{ route('productos.cafe') }}">
                            {{ __('Café') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('productos.mora') }}">
                            {{ __('Mora') }}
                        </x-responsive-nav-link>
                    </div>
                </div>

                <!-- Boletines con submenú -->
                <div class="space-y-1">
                    <button type="button"
                        class="flex items-center w-full px-3 py-2 font-medium text-blue-600 transition bg-blue-100 rounded-md hover:bg-blue-200"
                        data-toggle="submenu"
                        data-target="#submenu-boletines">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18" />
                        </svg>
                        {{ __('Boletines') }}
                        <svg class="w-4 h-4 ml-auto transition-transform transform" data-icon>
                            <path fill="currentColor" d="M5 8l4 4 4-4" />
                        </svg>
                    </button>

                    <div id="submenu-boletines" class="hidden mt-1 ml-6 space-y-1">
                        <x-responsive-nav-link href="{{ route('boletines.cafe') }}">
                            {{ __('Boletines de Café') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link href="{{ route('boletines.mora') }}">
                            {{ __('Boletines de Mora') }}
                        </x-responsive-nav-link>
                    </div>
                </div>

                <x-responsive-nav-link href="{{ route('usuarios.index') }}" :active="request()->routeIs('usuarios.*')">
                    <svg class="inline w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A4 4 0 009 16h6a4 4 0 013.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ __('Gestión de Usuarios') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('historial.index') }}" :active="request()->routeIs('historial.index')">
                    <svg class="inline w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ __('Historial') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('view-user.index') }}" :active="request()->routeIs('view-user.index')">
                    <svg class="inline w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5.121 17.804A13.937 13.937 0 0112 15c2.21 0 4.29.535 6.121 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    {{  __('Vista de Usuario') }}
                </x-responsive-nav-link>

            <div class="px-4 py-3 border-t border-gray-700">
                    <x-responsive-nav-link href="{{ route('profile.show') }}">
                            <svg class="inline w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.121 17.804A4 4 0 0 1 8.514 16h6.972a4 4 0 0 1 3.393 1.804M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
                            </svg>
                                {{ __('Perfil') }}
                    </x-responsive-nav-link>

                    <!-- Botón de cerrar sesión -->
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        <svg class="inline w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1" />
                        </svg>
                            {{ __('Cerrar Sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </nav>

        <script>
            document.querySelectorAll('[data-toggle="submenu"]').forEach(button => {
                button.addEventListener('click', () => {
                    const submenu = document.querySelector(button.getAttribute('data-target'));
                    const icon = button.querySelector('[data-icon]');

                    submenu.classList.toggle('hidden');
                    icon?.classList.toggle('rotate-180'); // Para girar la flechita
                });
            });
        </script>
    </div>


    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div class="shrink-0 me-3">
                        <img class="object-cover rounded-full size-10" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}" :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan

                    <!-- Team Switcher -->
                    @if (Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-200"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>

                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team" component="responsive-nav-link" />
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</nav>
