<x-form-section submit="updatePassword">
    <x-slot name="title">
        <h1 class="text-2xl whitespace-nowrap font-bold">Actualizacion de Contraseñas</h1>
    </x-slot>

    <x-slot name="description">
        {{ __('Asegúrese de que su cuenta utilice una contraseña larga y aleatoria para mantener su seguridad. Por ejemplo: puedes usar 
        Mayúsculas, minúsculas, numeros y simbolos.') }}
    </x-slot>

    <x-slot name="form">
    {{-- Campo de Contraseña Actual --}}
    <div class="col-span-6 sm:col-span-4 relative">
        <x-label for="current_password" value="{{ __('Contraseña actual') }}" />
        <x-input id="current_password" type="password" class="mt-1 rounded-xl block w-full pr-10" wire:model="state.current_password" autocomplete="current-password" />
        {{-- Contenedor del icono, posicionado absolutamente --}}
        <div class="absolute right-0 pr-3 flex items-center cursor-pointer" style="top: 32px;" onclick="togglePasswordVisibility('current_password', 'toggle_current_password')">
            <img id="toggle_current_password" src="{{ asset('images/ojo-close.svg') }}" alt="ocultar" class="w-5 h-5 text-gray-500" />
        </div>
        <x-input-error for="current_password" class="mt-2" />
    </div>

    {{-- Campo de Nueva Contraseña --}}
    <div class="col-span-6 sm:col-span-4 relative">
        <x-label for="password" value="{{ __('Nueva contraseña') }}" />
        <x-input id="password" type="password" class="mt-1 rounded-xl block w-full pr-10" wire:model="state.password" autocomplete="new-password" />
        <div class="absolute right-0 pr-3 flex items-center cursor-pointer" style="top: 32px;" onclick="togglePasswordVisibility('password', 'toggle_password')">
            <img id="toggle_password" src="{{ asset('images/ojo-close.svg') }}" alt="ocultar" class="w-5 h-5 text-gray-500" />
        </div>
        <x-input-error for="password" class="mt-2" />
    </div>

    {{-- Campo de Confirmar Contraseña --}}
    <div class="col-span-6 sm:col-span-4 relative">
        <x-label for="password_confirmation" value="{{ __('Confirmar contraseña') }}" />
        <x-input id="password_confirmation" type="password" class="mt-1 rounded-xl block w-full pr-10" wire:model="state.password_confirmation" autocomplete="new-password" />
        <div class="absolute right-0 pr-3 flex items-center cursor-pointer" style="top: 32px;" onclick="togglePasswordVisibility('password_confirmation', 'toggle_password_confirmation')">
            <img id="toggle_password_confirmation" src="{{ asset('images/ojo-close.svg') }}" alt="ocultar" class="w-5 h-5 text-gray-500" />
        </div>
        <x-input-error for="password_confirmation" class="mt-2" />
    </div>
</x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Guardado.') }}
        </x-action-message>

        <x-button>
            {{ __('Guardar') }}
        </x-button>
    </x-slot>
</x-form-section>
<script>
    function togglePasswordVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(iconId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.src = "{{ asset('images/ojo-open.svg') }}";
            toggleIcon.alt = "mostrar";
        } else {
            passwordInput.type = 'password';
            toggleIcon.src = "{{ asset('images/ojo-close.svg') }}";
            toggleIcon.alt = "ocultar";
        }
    }
</script>
