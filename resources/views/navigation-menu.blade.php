<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->

    <div class="fixed flex flex-col w-64 h-screen text-white shadow-lg bg-blue-950">
        <!-- Logo -->
        <div class="flex items-center justify-center p-4">
            <a href="{{ route('dashboard') }}">
                <x-application-mark class="w-auto h-9" />
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="flex-grow overflow-y-auto">
            <div class="px-4 space-y-4">
                <!-- Panel de Administracion -->
                {{-- <div class="text-xs text-gray-400">
                    {{ __('Dashboard') }}
                </div> --}}
                <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                    <svg class="inline w-5 h-5 mr-2 text-orange-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h4v11H3zM10 3h4v18h-4zM17 14h4v7h-4z"/>
                    </svg>
                    Panel
                </x-responsive-nav-link>

                <!-- CRUD -->
                {{-- <div class="text-xs text-gray-400">
                    {{ __('CRUD') }}
                </div> --}}

                @role('administrador')
                    {{-- Productos Agricolas--}}
                    <x-responsive-nav-link href="{{ route('productos.index') }}" :active="request()->routeIs('productos.*')">
                        <svg class="inline w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 6h18M3 14h9m-3 4h6"></path>
                        </svg>
                        Productos Agrícolas
                    </x-responsive-nav-link>

                    {{-- Gestion de Usuarios --}}
                    <x-responsive-nav-link href="{{ route('usuarios.index') }}" :active="request()->routeIs('usuarios.*')">
                        <svg class="inline w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A4 4 0 009 16h6a4 4 0 013.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Gestión de Usuarios
                    </x-responsive-nav-link>

                    {{-- Boletines --}}
                    <x-responsive-nav-link href="{{ route('boletines.index') }}" :active="request()->routeIs('boletines.*')">
                        <svg class="inline w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18"></path>
                        </svg>
                        Boletines
                    </x-responsive-nav-link>

                    {{-- Historial --}}
                    <x-responsive-nav-link href="{{ route('historial.index') }}" :active="request()->routeIs('historial.index')">
                        <svg class="inline w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 8v4l3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Historial
                    </x-responsive-nav-link>

                    {{-- Vista de Usuario --}}
                    <x-responsive-nav-link href="{{ route('view-user.index') }}" :active="request()->routeIs('view-user.index')">
                        <svg class="inline w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5.121 17.804A13.937 13.937 0 0112 15c2.21 0 4.29.535 6.121 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Vista de Usuario
                    </x-responsive-nav-link>

                @endrole

                @role('operador')
                <x-responsive-nav-link href="{{ route('operador.pendientes') }}" :active="request()->routeIs('operador.pendientes')">
                    <svg class="inline w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7M5 6h14M5 10h14M5 14h9"/>
                    </svg>
                    Validar Productos
                </x-responsive-nav-link>

                <x-nav-link :href="route('operador.historial.index')" :active="request()->routeIs('historial.index')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Historial
                </x-nav-link>
                @endrole
            </div>
        </div>

        <!-- Configuración -->
        <div class="px-4 py-2 text-xs text-gray-400">
            {{-- {{ __('Configuración') }} --}}
            <x-responsive-nav-link href="{{ route('profile.show') }}">
                <svg class="inline w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A4 4 0 0 1 8.514 16h6.972a4 4 0 0 1 3.393 1.804M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
                </svg>
                Perfil
            </x-responsive-nav-link>

            <!-- Authentication - Cerrar Sesión -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                    <svg class="inline w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1"/>
                    </svg>
                    Cerrar Sesión
                </x-responsive-nav-link>
            </form>
        </div>
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
