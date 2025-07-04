{{-- Este div contiene solo el HTML del sidebar --}}
<div :class="sidebarOpen ? 'w-64' : 'w-24'"
    class="h-full flex flex-col transition-all duration-1000 bg-[#00304D] text-white flex-shrink-0 overflow-y-auto overflow-x-hidden">

    <div class="flex items-center justify-between px-4 py-3">
        {{-- Logo + botón de colapsar --}}
        <div class="flex items-center shrink-0" :class="!sidebarOpen && 'justify-center w-full'">
            <a href="{{ route('dashboard') }}">
                <x-application-mark class="block w-auto h-9" />
            </a>
        </div>
        <button @click="sidebarOpen = !sidebarOpen"
            class="-ml-2 text-[var(--color-text)] rounded hover:bg-[var(--color-sidebarhoverbtn)] transition-transform duration-700 ease-in-out hover:translate-x-1"
            :class="!sidebarOpen && 'rotate-180 mx-auto'">
            <svg class="w-4 h-5 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 px-6 pt-4 space-y-2">
        <div class="px-2 space-y-2">

            {{-- Inicio (Generalmente visible para todos) --}}
            <div
                :class="sidebarOpen
                    ?
                    'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                    'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">
                <x-responsive-nav-link href="{{ route('dashboard') }}"
                    class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('dashboard')">

                    <img src="{{ asset('images/casa.svg') }}" class="w-4 h-4" alt="Inicio">

                    <span x-show="sidebarOpen" x-transition
                        class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                        {{ __('Inicio') }}
                    </span>
                </x-responsive-nav-link>
            </div>

            {{-- Módulo de cultivos --}}
            @canany(['crear producto'])
                <div
                    :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                    <x-responsive-nav-link href="{{ route('productos.index') }}"
                        class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('boletines')">

                        <img src="{{ asset('images/plant.svg') }}" class="w-4 h-4" alt="cultivos">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Cultivos') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            @endcanany

            {{-- Módulo de Noticias --}}
            @canany(['crear noticia'])
                <div
                    :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                    <x-responsive-nav-link href="{{ route('noticias.index') }}"
                        class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('boletines')">

                        <img src="{{ asset('images/noticias.svg') }}" class="w-4 h-4" alt="cultivos">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Noticias') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            @endcanany

            {{-- Módulo de Boletines --}}
            @canany(['crear boletin'])
                <div
                    :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                    <x-responsive-nav-link href="{{ route('boletines.index') }}"
                        class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('boletines')">

                        <img src="{{ asset('images/form.svg') }}" class="w-4 h-4" alt="Boletines">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Boletines') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            @endcanany
        </div>
    </nav>

    <nav class="flex-1 px-6 pt-4 space-y-2">
        <div class="px-2 space-y-2">
            <div x-show="sidebarOpen" x-transition class="px-6 py-2 text-xs text-[var(--color-ajustes)]">
                {{ __('Ajustes') }}
            </div>

            {{-- Módulo de Gestión de Usuarios --}}
            @canany(['crear usuario'])
                <div
                    :class="sidebarOpen
                        ?
                        'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                    <x-responsive-nav-link href="{{ route('usuarios.index') }}"
                        class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('usuarios')">

                        <img src="{{ asset('images/Icon.svg') }}" class="w-4 h-4" alt="usuarios">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Gestion de Usuarios') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            @endcanany

            {{-- Accesibilidad (Generalmente visible para todos) --}}
            <div
                :class="sidebarOpen
                    ?
                    'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                    'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                <x-responsive-nav-link href="{{ route('accesibilidad.index') }}"
                    class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('accesibilidad')">

                    <img src="{{ asset('images/accesi.svg') }}" class="w-4 h-4" alt="Accesibilidad">

                    <span x-show="sidebarOpen" x-transition
                        class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                        {{ __('Accesibilidad') }}
                    </span>
                </x-responsive-nav-link>
            </div>

            {{-- Centro de Ayuda (Generalmente visible para todos) --}}
            <div
                :class="sidebarOpen
                    ?
                    'flex pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                    'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                <x-responsive-nav-link href="{{ route('centroAyuda.index') }}"
                    class="flex items-center transition-all duration-300 ease-in-out" :active="request()->routeIs('centroAyuda')">

                    <img src="{{ asset('images/preg.svg') }}" class="w-4 h-4" alt="Centro de Ayuda">

                    <span x-show="sidebarOpen" x-transition
                        class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                        {{ __('Centro de Ayuda') }}
                    </span>
                </x-responsive-nav-link>
            </div>

            {{-- Cerrar Sesión (Generalmente visible para todos) --}}
            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <div
                    :class="sidebarOpen
                        ?
                        'flex items-center pl-2 py-2 ml-[20px] transition rounded-xl  hover:bg-[var(--color-sidebarhoverbtn)]' :
                        'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)]'">

                    <x-responsive-nav-link href="{{ route('logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center">

                        <img src="{{ asset('images/off.svg') }}" class="w-4 h-4" alt="Cerrar Sesión">

                        <span x-show="sidebarOpen" x-transition
                            class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Cerrar Sesión') }}
                        </span>
                    </x-responsive-nav-link>
                </div>
            </form>
        </div>
    </nav>

    <div class="px-6 py-16">
        {{-- Perfil (Generalmente visible para todos) --}}
        <x-responsive-nav-link href="{{ route('profile.show') }}" class="rounded-3xl"
            x-bind:class="sidebarOpen ? 'px-3 py-6' : 'flex justify-center p-0'">

            <div class="flex items-center w-full rounded-lg"
                x-bind:class="sidebarOpen
                    ?
                    'bg-[var(--color-profile)]  hover:bg-[var(--color-sidebarhoverbtn)] px-3 py-2' :
                    'justify-center px-0'">

                <img class="object-cover rounded-md size-10" src="{{ Auth::user()->profile_photo_url }}"
                    alt="{{ Auth::user()->name }}" />

                <div class="flex flex-col ml-3" x-show="sidebarOpen">
                    <span class="text-base font-bold text-gray-800">
                        {{ Auth::user()->name }}
                    </span>
                    <span class="text-sm text-gray-600">
                        {{-- Muestra el primer rol del usuario o 'Usuario' por defecto --}}
                        {{ Auth::user()->getRoleNames()->first() ?? 'Usuario' }}
                    </span>
                </div>
            </div>
        </x-responsive-nav-link>
    </div>
</div>
