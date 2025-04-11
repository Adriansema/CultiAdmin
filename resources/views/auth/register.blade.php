<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="Role" value="Selecciona tu rol" />
                <select id="role" name="role" required class="block w-full mt-1">
                    <option value="administrador">Administrador</option>
                    <option value="operador">Operador</option>
                </select>
            </div>

            <!-- actualizacion 09/04/2025 -->

                {{-- Campo de Contraseña --}}
                <div class="relative mt-4">
                    <x-label for="password" value="Contraseña" />
                    <x-input id="password" class="block w-full pr-10 mt-1" type="password" name="password" required autocomplete="new-password" />
                    <button type="button" onclick="togglePassword('password', this)"
                        class="absolute text-gray-500 transform -translate-y-1/2 right-3 top-1/2 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path class="eye-show" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path class="eye-show" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            <path class="hidden eye-hide" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3l18 18M10.477 10.477A3 3 0 0113.5 13.5m2.57 2.57A9.964 9.964 0 0112 19c-4.478 0-8.27-2.944-9.544-7a9.961 9.961 0 012.384-3.69M6.867 6.867A9.958 9.958 0 0112 5c4.478 0 8.27 2.944 9.544 7a9.962 9.962 0 01-4.143 5.233" />
                        </svg>
                    </button>
                </div>

                {{-- Confirmación --}}
                <div class="relative mt-4">
                    <x-label for="password_confirmation" value="Confirmar Contraseña" />
                    <x-input id="password_confirmation" class="block w-full pr-10 mt-1" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <button type="button" onclick="togglePassword('password_confirmation', this)"
                        class="absolute text-gray-500 transform -translate-y-1/2 right-3 top-1/2 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path class="eye-show" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path class="eye-show" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            <path class="hidden eye-hide" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3l18 18M10.477 10.477A3 3 0 0113.5 13.5m2.57 2.57A9.964 9.964 0 0112 19c-4.478 0-8.27-2.944-9.544-7a9.961 9.961 0 012.384-3.69M6.867 6.867A9.958 9.958 0 0112 5c4.478 0 8.27 2.944 9.544 7a9.962 9.962 0 01-4.143 5.233" />
                        </svg>
                    </button>
                </div>

                <script>
                    function togglePassword(inputId, button) {
                        const input = document.getElementById(inputId);
                        const svg = button.querySelector('svg');
                        const show = svg.querySelectorAll('.eye-show');
                        const hide = svg.querySelector('.eye-hide');

                        const isPassword = input.type === 'password';
                        input.type = isPassword ? 'text' : 'password';

                        show.forEach(path => path.classList.toggle('hidden', !isPassword));
                        hide.classList.toggle('hidden', isPassword);
                    }
                </script>



            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
