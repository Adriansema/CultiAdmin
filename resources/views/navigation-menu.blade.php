<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->

    <div class="fixed flex flex-col w-64 h-screen text-white shadow-lg" style="background-color: #00304D;">

        <!-- Logo -->
        <div class="flex items-center justify-center p-1 w-72">
            <a href="{{ route('dashboard') }}" class="flex items-center justify-center w-full">
                <x-application-mark class="w-32 h-auto" />
            </a>
        </div>

        @role('administrador')
        <!-- Navigation Links -->
        <nav class="flex flex-col flex-1 px-4 py-0.5 space-y-4 overflow-y-auto">
            <div class="py-1 px-7">
                <!-- "px significa padding horizontal (izquierda y derecha)" -->
                <!-- "py significa padding vertical (arriba y abajo)" -->
                <div class="flex-1 space-y-2">
                    <div class="space-y-1">
                        <!-- Botón con borde superior y laterales -->
                        <button type="button"
                            class="flex items-center w-full px-4 py-2 font-medium transition bg-[#39A900] border-transparent rounded-full border-x-2 hover:bg-[#61BA33]"
                            data-toggle="submenu" data-target="#submenu-nueva-entrada">
                            <img src="{{ asset('images/signo.svg') }}" alt="Signo del (+)" class="w-3 h-3 mr-2" />
                            <span class="text-sm text-white">{{ __('Nueva Entrada') }}</span>
                            <img src="{{ asset('images/flecha.svg') }}" alt="Flecha"
                                class="w-3 h-3 ml-auto transition-transform transform" data-icon />
                        </button>

                        <!-- Submenú con borde inferior y laterales -->
                        <div id="submenu-nueva-entrada"
                            class="hidden pl-0 mt-0 space-y-0 border-b-2 rounded-b-3xl border-x-2"
                            style="border-color: #39A900;">
                            <x-responsive-nav-link href="{{ route('dashboard') }}"
                                :active="request()->routeIs('dashboard')"
                                class="flex items-center px-3 py-2 text-sm text-gray-400 border-2 border-transparent rounded-full hover:bg-[#39A900] hover:text-[#ffffff] transition-all duration-75">
                                <div class="relative flex justify-center w-full">
                                    <img src="{{ asset('images/hoja.svg') }}" alt="Boletines"
                                        class="absolute left-0 w-4 h-4 ml-2 -translate-y-1/2 top-1/2" />
                                    <span class="text-center">
                                        {{ __(' Cultivos ') }}
                                    </span>
                                </div>
                            </x-responsive-nav-link>

                            <x-responsive-nav-link href="{{ route('dashboard') }}"
                                :active="request()->routeIs('dashboard')"
                                class="flex items-center px-3 py-2 text-sm text-gray-400 rounded-full border-2 border-transparent hover:bg-[#39A900] hover:text-[#ffffff] transition-all duration-300">
                                <div class="relative flex justify-center w-full">
                                    <img src="{{ asset('images/form.svg') }}" alt="Boletines"
                                        class="absolute left-0 w-4 h-4 ml-2 -translate-y-1/2 top-1/2" />
                                    <span class="text-center">
                                        {{ __(' Boletines ') }}
                                    </span>
                                </div>
                            </x-responsive-nav-link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="py-8 px-7">
                <div class="flex-1 space-y-3">
                    <div class="space-y-3">
                        <!-- Panel -->
                        <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')"
                            class="flex items-center px-3 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] group">

                            <!-- Ícono de casa -->
                            <img src="{{ asset('images/casa.svg') }}" alt="Inicio" class="w-5 h-5 mr-2" />

                            <!-- Texto -->
                            <span class="text-sm transition
                                {{ request()->routeIs('dashboard')
                                    ? 'text-gray-100'
                                    : 'text-white' }}">
                                {{ __('Inicio') }}
                            </span>
                        </x-responsive-nav-link>

                        <button type="button"
                            class="flex items-center w-full px-3 py-2 font-medium transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]"
                            data-toggle="submenu" data-target="#submenu-productos">
                            <img src="{{ asset('images/plant.svg') }}" alt="Cultivos" class="w-5 h-5 mr-2" />
                            <span class="text-sm text-white">{{ __('Cultivos') }}</span>
                        </button>
                    </div>

                    <!-- Boletines con submenú -->
                    <div class="space-y-1">
                        <button type="button"
                            class="flex items-center w-full px-3 py-2 font-medium transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]"
                            data-toggle="submenu" data-target="#submenu-boletines">

                            <img src="{{ asset('images/files.svg') }}" alt="Boletines" class="w-5 h-5 mr-2" />

                            <span class="text-sm text-white">{{ __('Boletines') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="px-5 py-3">
                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Ajustes') }}
                </div>


                <div class="flex-1 space-y-2.5">
                    <x-responsive-nav-link href="{{ route('usuarios.index') }}"
                        :active="request()->routeIs('usuarios.*')"
                        class="block px-3 py-2 text-sm text-gray-600 rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] group">

                        <!-- Ícono de dos usuarios -->
                        <div class="relative flex justify-center w-full">
                            <img src="{{ asset('images/add.svg') }}" alt="Boletines"
                                class="absolute left-0 w-4 h-4 ml-0 -translate-y-2/3 top-2/3" />
                            <span class="text-center text-white">
                                {{ __(' Gestion de Usuarios ') }}
                            </span>
                        </div>
                    </x-responsive-nav-link>


                    <!-- Botón de Accesibilidad -->
                    <x-responsive-nav-link href="{{ route('accesibilidad.index') }}"
                        :active="request()->routeIs('accesibilidad')"
                        class="block px-3 py-2 text-sm text-gray-600 rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]">
                        <!-- Ícono de accesibilidad (persona con brazos extendidos) -->
                        <div class="relative flex w-full justify-evenly">
                            <img src="{{ asset('images/accesi.svg') }}" alt="Accesibilidad"
                                class="absolute left-0 w-4 h-4 ml-0 -translate-y-2/3 top-2/3" />
                            <span class="text-white ">
                                {{ __(' Accesibilidad ') }}
                            </span>
                        </div>
                    </x-responsive-nav-link>

                    <!-- Boton de Centro de Ayuda -->
                    <x-responsive-nav-link href="{{ route('centroAyuda.index') }}"
                        class="block px-3 py-2 text-sm text-gray-600 rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]">
                        <div class="relative flex justify-center w-full">
                            <img src="{{ asset('images/preg.svg') }}" alt="Ayuda de la aplicación"
                                class="absolute left-0 w-4 h-4 ml-0 -translate-y-2/3 top-2/3" />
                            <span class="text-white ">
                                {{ __(' Centro de Ayuda ') }}
                            </span>
                        </div>
                    </x-responsive-nav-link>

                    <!-- Botón de cerrar sesión -->
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <x-responsive-nav-link href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="block px-3 py-2 text-sm text-gray-800 rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]">
                            <div class="relative flex justify-center w-full">
                                <img src="{{ asset('images/off.svg') }}" alt="Cerrar Sesión"
                                    class="absolute left-0 w-4 h-4 ml-0 -translate-y-2/3 top-2/3" />
                                <span class="text-white ">
                                    {{ __(' Cerrar Sesión ') }}
                                </span>
                            </div>
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>

            {{-- Foto de perfil y nombre --}}
            <div class="py-2">
                <x-responsive-nav-link href="{{ route('profile.show') }}"
                    class="block px-3 py-2 text-sm text-gray-500 hover:bg-[var(--color-sidebarhoverbtn)] rounded-xl">
                    <div
                        class="flex items-center w-full px-3 py-1 space-x-4 bg-gray-100 rounded-2xl min-w-[193px] max-w-sm">
                        {{-- Imagen de perfil --}}
                        <img class="object-cover rounded-lg size-10" src="{{ Auth::user()->profile_photo_url }}"
                            alt="{{ Auth::user()->name }}" />

                        {{-- Nombre y rol apilados verticalmente --}}
                        <div class="flex flex-col">
                            <span class="text-base font-bold text-gray-800">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="text-sm text-gray-600">
                                {{ Auth::user()->getRoleNames()->first() ?? 'Usuario' }}
                            </span>
                        </div>
                    </div>
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
    <div class="px-4 py-3">
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
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v2" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2c-2 0-4 2-4 4 2 0 3.5-1.5 4-4z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2c2 0 4 2 4 4-2 0-3.5-1.5-4-4z" />
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
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm">{{ __('Historial') }}</span>
        </x-responsive-nav-link>
    </div>

    <div class="px-4 py-3">
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <x-responsive-nav-link href="{{ route('logout') }}"
                onclick="event.preventDefault(); this.closest('form').submit();">
                <svg class="inline w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <!-- Palito separado, ajustado para bajar un poco más -->
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v6" />
                    <!-- Semicírculo en forma de U -->
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 11a7 7 0 1 0 13 0" />
                </svg>
                <span class="text-sm">{{ __('Cerrar Sesión') }}</span>
            </x-responsive-nav-link>
        </form>
    </div>

    {{-- Foto de perfil y nombre --}}
    <div class="px-4 py-3">
        <div class="flex items-center space-x-3">
            <div class="text-sm font-medium text-gray-700">
                <x-responsive-nav-link href="{{ route('profile.show') }}">
                    <img class="object-cover rounded-full size-10" src="{{ Auth::user()->profile_photo_url }}"
                        alt="{{ Auth::user()->name }}" />
                    <svg class="inline w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M5.121 17.804A4 4 0 0 1 8.514 16h6.972a4 4 0 0 1 3.393 1.804M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" />
                    </svg>
                    <span class="text-sm">{{ Auth::user()->name }}</span>
                </x-responsive-nav-link>
            </div>
        </div>
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
