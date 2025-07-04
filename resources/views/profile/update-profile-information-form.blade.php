<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        <h1 class="text-2xl whitespace-nowrap font-bold">Información de perfil</h1>
    </x-slot>

    <x-slot name="description">
        {{ __('Actualice la información del perfil y la dirección de correo electrónico de su cuenta.') }}
    </x-slot>

    <x-slot name="form">
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
                <input type="file" id="photo" class="hidden" wire:model.live="photo" x-ref="photo"
                    x-on:change="
                                photoName = $refs.photo.files[0].name;
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    photoPreview = e.target.result;
                                };
                                reader.readAsDataURL($refs.photo.files[0]);
                        " />

                <x-label for="photo" value="{{ __('Foto') }}" />

                <div class="mt-2">
                    <div class="relative w-32 h-32 overflow-hidden rounded-full shadow-md cursor-pointer group hover:shadow-lg transition-all duration-300 border-2 border-gray-300"
                        x-on:click.prevent="$refs.photo.click()">

                        <div x-show="! photoPreview">
                            <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}"
                                class="object-cover w-full h-full transition-transform duration-300 ease-in-out group-hover:scale-110">

                            <div
                                class="absolute inset-0 flex items-center justify-center transition-opacity duration-300 bg-black opacity-0 bg-opacity-40 group-hover:opacity-100">
                                <span class="text-xs font-semibold text-white">Selecciona una foto</span>
                            </div>
                        </div>

                        <div x-show="photoPreview" style="display: none;">
                            <span class="block bg-center bg-no-repeat bg-cover w-full h-full"
                                x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                            </span>
                        </div>
                    </div>
                </div>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button" class="mt-2" wire:click="deleteProfilePhoto">
                        {{ __('Remover Foto') }}
                    </x-secondary-button>
                @endif

                <x-input-error for="photo" class="mt-2" />
            </div>
        @endif

        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="block w-full rounded-xl mt-1" wire:model="state.name" required
                autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" type="email" class="block rounded-xl w-full mt-1" wire:model="state.email"
                required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) &&
                    !$this->user->hasVerifiedEmail())
                <p class="mt-4 text-sm">
                    {{ __('Su dirección de correo electrónico no está verificada.') }}

                    <button type="button"
                        class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        wire:click.prevent="sendEmailVerification">
                        {{ __('Haga clic aquí para volver a enviar el correo electrónico de verificación.') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 text-sm font-medium text-green-600">
                        {{ __('Se ha enviado un nuevo enlace de verificación a su dirección de correo electrónico.') }}
                    </p>
                @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Guardado.') }}
        </x-action-message>

        <x-button wire:loading.attr="disabled" wire:target="photo">
            {{ __('Guardar') }}
        </x-button>
    </x-slot>
</x-form-section>
