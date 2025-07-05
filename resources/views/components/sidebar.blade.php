<div x-cloak
     x-data="{
         sidebarOpen: JSON.parse(localStorage.getItem('sidebarOpen')) ?? true,
         toggleSidebar() {
             this.sidebarOpen = !this.sidebarOpen;
             localStorage.setItem('sidebarOpen', JSON.stringify(this.sidebarOpen));
         }
     }"
     :class="sidebarOpen ? 'w-64' : 'w-24'"
     class="h-full flex flex-col transition-all duration-1000 bg-[#00304D] text-white flex-shrink-0 overflow-y-hidden overflow-x-hidden"
>

    <div class="flex items-center justify-between px-4 py-3">
        {{-- Logo + botón de colapsar --}}
        <div class="flex items-center shrink-0" :class="!sidebarOpen && 'justify-center w-full'">
            <a href="{{ route('dashboard') }}">
                <x-application-mark class="block w-auto h-9" />
            </a>
        </div>
        <button @click="toggleSidebar"
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
            {{-- Inicio --}}
            <a href="{{ route('dashboard') }}"
            :class="sidebarOpen
                ? '{{ request()->routeIs('dashboard') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'
                : '{{ request()->routeIs('dashboard') ? 'bg-white' : '' }} flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                    <img src="{{ asset(request()->routeIs('dashboard') ? 'images/casaColor.svg' : 'images/casa.svg') }}"
                        class="w-4 h-4" alt="Inicio">
                    <span x-show="sidebarOpen" x-transition
                        class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('dashboard') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                        {{ __('Inicio') }}
                    </span>
                </div>
            </a>

            {{-- Cultivos --}}
            @canany(['crear producto'])
            <a href="{{ route('productos.index') }}"
            :class="sidebarOpen
                ? '{{ request()->routeIs('productos.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'
                : '{{ request()->routeIs('productos.index') ? 'bg-white' : '' }} flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                    <img src="{{ asset(request()->routeIs('productos.index') ? 'images/plantColor.svg' : 'images/plant.svg') }}"
                        class="w-4 h-4" alt="Cultivos">
                    <span x-show="sidebarOpen" x-transition
                        class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('productos.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                        {{ __('Cultivos') }}
                    </span>
                </div>
            </a>
            @endcanany

            {{-- Noticias --}}
            @canany(['crear noticia'])
            <a href="{{ route('noticias.index') }}"
            :class="sidebarOpen
                ? '{{ request()->routeIs('noticias.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'
                : '{{ request()->routeIs('noticias.index') ? 'bg-white' : '' }} flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                    <img src="{{ asset(request()->routeIs('noticias.index') ? 'images/noticiasColor.svg' : 'images/noticias.svg') }}"
                        class="w-4 h-4" alt="Noticias">
                    <span x-show="sidebarOpen" x-transition
                        class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('noticias.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                        {{ __('Noticias') }}
                    </span>
                </div>
            </a>
            @endcanany

            {{-- Boletines --}}
            @canany(['crear boletin'])
            <a href="{{ route('boletines.index') }}"
            :class="sidebarOpen
                ? '{{ request()->routeIs('boletines.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'
                : '{{ request()->routeIs('boletines.index') ? 'bg-white' : '' }} flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                    <img src="{{ asset(request()->routeIs('boletines.index') ? 'images/formColor.svg' : 'images/form.svg') }}"
                        class="w-4 h-4" alt="Boletines">
                    <span x-show="sidebarOpen" x-transition
                        class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('boletines.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                        {{ __('Boletines') }}
                    </span>
                </div>
            </a>
            @endcanany
        </div>
    </nav>

    <nav class="flex-1 px-6 pt-4 space-y-2 mt-80">
        <div class="px-2 space-y-2">
            <div x-show="sidebarOpen" x-transition class="px-7 py-2 text-xs text-[var(--color-ajustes)]">
                {{ __('Ajustes') }}
            </div>

            {{-- Gestión de Usuarios --}}
            @canany(['crear usuario'])
            <a href="{{ route('usuarios.index') }}"
            :class="sidebarOpen
                ? '{{ request()->routeIs('usuarios.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'
                : '{{ request()->routeIs('usuarios.index') ? 'bg-white' : '' }} flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                    <img src="{{ asset(request()->routeIs('usuarios.index') ? 'images/IconColor.svg' : 'images/Icon.svg') }}"
                        class="w-4 h-4" alt="Usuarios">
                    <span x-show="sidebarOpen" x-transition
                        class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('usuarios.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                        {{ __('Gestion de Usuarios') }}
                    </span>
                </div>
            </a>
            @endcanany

            {{-- Accesibilidad --}}
            <a href="{{ route('accesibilidad.index') }}"
            :class="sidebarOpen
                ? '{{ request()->routeIs('accesibilidad.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'
                : '{{ request()->routeIs('accesibilidad.index') ? 'bg-white' : '' }} flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                    <img src="{{ asset(request()->routeIs('accesibilidad.index') ? 'images/accesiColor.svg' : 'images/accesi.svg') }}"
                        class="w-4 h-4" alt="Accesibilidad">
                    <span x-show="sidebarOpen" x-transition
                        class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('accesibilidad.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                        {{ __('Accesibilidad') }}
                    </span>
                </div>
            </a>

            {{-- Centro de Ayuda --}}
            <a href="{{ route('centroAyuda.index') }}"
            :class="sidebarOpen
                ? '{{ request()->routeIs('centroAyuda.index') ? 'bg-white' : '' }} flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'
                : '{{ request()->routeIs('centroAyuda.index') ? 'bg-white' : '' }} flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                    <img src="{{ asset(request()->routeIs('centroAyuda.index') ? 'images/pregColors.svg' : 'images/preg.svg') }}"
                        class="w-4 h-4" alt="Centro de Ayuda">
                    <span x-show="sidebarOpen" x-transition
                        class="ml-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('centroAyuda.index') ? 'text-[var(--color-textmarca)]' : 'text-[var(--color-text)]' }}">
                        {{ __('Centro de Ayuda') }}
                    </span>
                </div>
            </a>

            {{-- Cerrar Sesión --}}
            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                :class="sidebarOpen
                    ? 'flex pl-2 py-2 ml-[20px] transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'
                    : 'flex justify-center px-2 py-2 transition rounded-xl hover:bg-[var(--color-sidebarhoverbtn)] cursor-pointer'">
                    <div class="flex items-center w-full transition-all duration-300 ease-in-out">
                        <img src="{{ asset('images/off.svg') }}" class="w-4 h-4" alt="Cerrar Sesión">
                        <span x-show="sidebarOpen" x-transition class="ml-2 text-sm font-medium text-[var(--color-text)] whitespace-nowrap">
                            {{ __('Cerrar Sesión') }}
                        </span>
                    </div>
                </a>
            </form>
        </div>
    </nav>

    <div class="px-6 py-12">
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
