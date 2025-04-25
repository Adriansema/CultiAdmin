<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="fixed flex flex-col w-64 h-screen text-white shadow-lg" style="background-color: #00304D;">
        <!-- Logo -->
        <div class="flex items-center justify-center p-4">
            <a href="{{ route('dashboard') }}">
                <x-application-mark class="w-auto h-9" />
            </a>
        </div>

        @role('administrador')
        <!-- Navigation Links -->
        <nav class="flex flex-col flex-1 px-4 py-6 space-y-4 overflow-y-auto">
            <div class="px-6 py-1">  <!-- "px significa padding horizontal (izquierda y derecha)" --> <!-- "py significa padding vertical (arriba y abajo)" -->
                <div class="flex-1 space-y-2">
                    <div class="space-y-1">
                        <!-- Botón con borde superior y laterales -->
                        <button type="button"
                            class="flex items-center w-full px-3 py-2 font-medium text-green-600 transition bg-[var(--color-nuevaentrada)] border-t-2 rounded-t-md border-x-2 hover:bg-green-400"
                            style="border-color: #39A900;"
                            data-toggle="submenu" data-target="#submenu-nueva-entrada">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <span class="text-sm text-white">{{ __('Nueva Entrada') }}</span>
                            <svg class="w-4 h-4 ml-auto transition-transform transform" data-icon>
                                <path fill="currentColor" d="M5 8l4 4 4-4" />
                            </svg>
                        </button>

                        <!-- Submenú con borde inferior y laterales -->
                        <div id="submenu-nueva-entrada" class="hidden pl-6 mt-0 space-y-1 border-b-2 border-x-2 rounded-b-md"
                            style="border-color: #228B22;">
                            <a href="/nueva-entrada/cultivo"
                                class="block px-3 py-2 text-sm text-white bg-green-600 rounded-none hover:bg-green-500">
                                {{ __('Cultivos') }}
                            </a>
                            <a href="/nueva-entrada/boletin"
                                class="block px-3 py-2 text-sm text-white bg-green-600 rounded-none hover:bg-green-500">
                                {{ __('Boletines') }}
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <div class="py-8 px-7">
                <div class="flex-1 space-y-3">
                    <div class="space-y-3">
                        <!-- Panel -->
                        <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                            class="flex items-center px-3 py-2 transition rounded-md hover:bg-orange-300">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5m4 0h5a1 1 0 001-1V10" />
                            </svg>
                            <span class="text-sm text-white">{{ __('Inicio') }}</span>
                        </x-responsive-nav-link>

                        <button type="button"
                            class="flex items-center w-full px-3 py-2 font-medium text-green-600 transition rounded-md hover:bg-green-300"
                            data-toggle="submenu" data-target="#submenu-productos">
                            <svg class="w-5 h-5 mr-2 text-green-700 transition-colors group-hover:text-green-900"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 13c-1.5-3-5-4-9-4 0 6 3 9 6 9s3-3 3-5zm0 0c1.5-3 5-4 9-4 0 6-3 9-6 9s-3-3-3-5z" />
                                <line x1="12" y1="13" x2="12" y2="21" stroke="currentColor" stroke-linecap="round" />
                            </svg>
                            <span class="text-sm text-white">{{ __('Cultivos') }}</span>
                            <svg class="w-4 h-4 ml-auto transition-transform transform" data-icon>
                                <path fill="currentColor" d="M5 8l4 4 4-4" />
                            </svg>
                        </button>

                        <div id="submenu-productos" class="hidden mt-1 ml-6 space-y-1">
                            <x-responsive-nav-link href="{{ route('productos.cafe') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-800">
                                <span class="text-sm text-white">{{ __('Café') }}</span>
                            </x-responsive-nav-link>
                            <x-responsive-nav-link href="{{ route('productos.mora') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-800">
                                <span class="text-sm text-white">{{ __('Mora') }}</span>
                            </x-responsive-nav-link>
                        </div>
                    </div>

                    <!-- Boletines con submenú -->
                    <div class="space-y-1">
                        <button type="button"
                            class="flex items-center w-full px-3 py-2 font-medium text-blue-900 transition rounded-md hover:bg-blue-300"
                            data-toggle="submenu" data-target="#submenu-boletines">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V5a2 2 0 012-2h6a2 2 0 012 2v10a2 2 0 01-2 2H14m-6 0a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v10a2 2 0 01-2 2H8z" />
                            </svg>

                            <span class="text-sm text-white">{{ __('Boletines') }}</span>
                            <svg class="w-4 h-4 ml-auto transition-transform transform" data-icon>
                                <path fill="currentColor" d="M5 8l4 4 4-4" />
                            </svg>
                        </button>

                        <div id="submenu-boletines" class="hidden mt-1 ml-6 space-y-1">
                            <x-responsive-nav-link href="{{ route('boletines.cafe') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-800">
                                <span class="text-sm text-white">{{ __('Café') }}</span>
                            </x-responsive-nav-link>
                            <x-responsive-nav-link href="{{ route('boletines.mora') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-800">
                                <span class="text-sm text-white">{{ __('Mora') }}</span>
                            </x-responsive-nav-link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 py-2">
                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Ajustes') }}
                </div>
                <div class="flex-1 space-y-2.5">
                    <x-responsive-nav-link href="{{ route('usuarios.index') }}"
                        :active="request()->routeIs('usuarios.*')" class="block px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-800">

                        <span class="relative inline-flex items-center">
                            <!-- Ícono "+" ajustado al borde izquierdo -->
                            <svg class="absolute w-3 h-3 text-purple-600 translate-y-1 -left-3" fill="none"
                                stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>

                            <!-- Ícono de dos usuarios -->
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2" />
                                <circle cx="9" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M23 20v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" />
                            </svg>
                        </span>
                        <span class="text-sm text-white">{{ __('Gestión de Usuarios') }}</span>
                    </x-responsive-nav-link>

                    <x-responsive-nav-link href="{{ route('accesibilidad.index') }}"
                        :active="request()->routeIs('accesibilidad')" class="block px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-800">
                        <!-- Ícono de accesibilidad (persona con brazos extendidos) -->
                        <svg class="inline w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor"
                            stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img"
                            aria-labelledby="accesibilidad-icon">
                            <title id="accesibilidad-icon">Accesibilidad de la aplicación</title>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 4a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm-6.364 3.05a1 1 0 0 1 1.414 0l3.536 3.536a1 1 0 0 0 .707.293h1.414a1 1 0 0 0 .707-.293l3.536-3.536a1 1 0 1 1 1.414 1.414l-3.536 3.536a3 3 0 0 1-2.121.879h-1.414a3 3 0 0 1-2.121-.879L4.222 8.464a1 1 0 0 1 0-1.414zM9 20a1 1 0 0 1-1-1v-6h2v6a1 1 0 0 1-1 1zm6 0a1 1 0 0 1-1-1v-6h2v6a1 1 0 0 1-1 1z" />
                        </svg>
                        <span class="text-sm text-white">{{ __('Accesibilidad') }}</span>
                    </x-responsive-nav-link>

                    <x-responsive-nav-link href="{{ route('centroAyuda.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-800">
                        <svg class="inline w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img"
                            aria-label="Centro de Ayuda">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 18h.01M12 10a2 2 0 1 0-2-2m2 2v2m0 10a9 9 0 1 0 0-18 9 9 0 0 0 0 18z" />
                        </svg>
                        <span class="text-sm text-white">{{ __('Centro de Ayuda') }}</span>
                    </x-responsive-nav-link>

                    <!-- Botón de cerrar sesión -->
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <x-responsive-nav-link href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="block px-3 py-2 text-sm text-gray-800 rounded-md hover:bg-gray-800">
                            <svg class="inline w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <!-- Palito separado, ajustado para bajar un poco más -->
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v6" />
                                <!-- Semicírculo en forma de U -->
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 11a7 7 0 1 0 13 0" />
                            </svg>
                            <span class="text-sm text-white">{{ __('Cerrar Sesión') }}</span>
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>

            <div class="px-5">  <!-- "px significa padding horizontal (izquierda y derecha)" --> <!-- "py significa padding vertical (arriba y abajo)" -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-800">
                    <svg class="inline w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A4 4 0 0 1 8.514 16h6.972a4 4 0 0 1 3.393 1.804M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
                    </svg>
                    <span class="text-sm text-white">{{ __('Perfil') }}</span>
                </x-responsive-nav-link>
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
    @endrole

    @role('operador')
        <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
            class="flex items-center px-3 py-2 transition rounded-md hover:bg-orange-300">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5m4 0h5a1 1 0 001-1V10" />
            </svg>
            <span class="text-sm">{{ __('Inicio') }}</span>
        </x-responsive-nav-link>

        <x-responsive-nav-link href="{{ route('operador.pendientes') }}"
            :active="request()->routeIs('operador.pendientes')">
            <svg class="inline w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" stroke-width="1.5"
                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-label="Mano con planta" role="img">
                <!-- Planta -->
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 2c1.2 1.5 1.2 3.5 0 5-1.2-1.5-1.2-3.5 0-5z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 7v2" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 2c-2 0-4 2-4 4 2 0 3.5-1.5 4-4z" />
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 2c2 0 4 2 4 4-2 0-3.5-1.5-4-4z" />
                <!-- Mano estilizada -->
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 15s1-1 2-1h2l2 2h6a2 2 0 0 1 2 2v2H8a5 5 0 0 1-5-5z" />
            </svg>
            <span class="text-sm">{{ __('Validar Cultivos') }}</span>
        </x-responsive-nav-link>



        <x-responsive-nav-link href="{{ route('operador.historial.index') }}"
            :active="request()->routeIs('historial.index')">
            <svg class="inline w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">{{ __('Historial') }}</span>
        </x-responsive-nav-link>

        <div class="px-4 py-3 border-t border-gray-600">
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <x-responsive-nav-link href="{{ route('logout') }}"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    <svg class="inline w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <!-- Palito separado, ajustado para bajar un poco más -->
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v6" />
                        <!-- Semicírculo en forma de U -->
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 11a7 7 0 1 0 13 0" />
                    </svg>
                    <span class="text-sm">{{ __('Cerrar Sesión') }}</span>
                </x-responsive-nav-link>
            </form>

            <x-responsive-nav-link href="{{ route('profile.show') }}">
                <svg class="inline w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                    stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M5.121 17.804A4 4 0 0 1 8.514 16h6.972a4 4 0 0 1 3.393 1.804M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
                </svg>
                <span class="text-sm">{{ __('Perfil') }}</span>
            </x-responsive-nav-link>
        </div>
    @endrole

    <!-- Responsive Navigation Menu -->

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- contenedor principal -->
        <div class="pt-2 pb-3 space-y-1">
            <!-- Sección de Navegación Principal -->
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- SECCION DE USUARIO (responsive) -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                <div class="shrink-0 me-3">
                    <img class="object-cover rounded-full size-10" src="{{ Auth::user()->profile_photo_url }}"
                        alt="{{ Auth::user()->name }}" />
                </div>
                @endif

                <div>
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <!-- OPCIONES DE CUENTA -->
            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                <x-responsive-nav-link href="{{ route('api-tokens.index') }}"
                    :active="request()->routeIs('api-tokens.index')">
                    {{ __('API Tokens') }}
                </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>

                <!-- GESTION DE EQUIPOS (teams) -->
                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                <div class="border-t border-gray-200"></div>

                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Manage Team') }}
                </div>

                <!-- Team Settings -->
                <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
                    :active="request()->routeIs('teams.show')">
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
