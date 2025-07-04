<x-mail::message>
    {{-- Banner de la imagen --}}
    {{-- Para imágenes en correos, es mejor usar la URL pública directamente
         y aplicar estilos esenciales en línea.
         Nota: El uso de `localhost:8000` en la URL de la imagen en correos electrónicos
         solo funcionará si el cliente de correo puede acceder a esa URL.
         En producción, DEBES usar la URL de tu dominio (ej: https://tudominio.com/images/CultivaAdmin.png). --}}
    <div style="text-align: center; margin-bottom: 25px;">
        <img src="{{ asset('images/CultivaAdmin.png') }}"
             alt="Logo Cultiva Admin"
             style="width: 100%; max-width: 600px; height: auto; display: block; margin: 0 auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    </div>

    {{-- Contenido principal del mensaje --}}
    <x-mail::panel> {{-- Este componente añade un fondo y padding predefinidos --}}
        <h1 style="font-size: 26px; color: #1a202c; margin-bottom: 20px; text-align: center; font-weight: bold;">
            ¡Bienvenido a {{ $appName }}, {{ $userName }}!
        </h1>

        <p style="font-size: 16px; color: #4a5568; line-height: 1.6; margin-bottom: 15px;">
            Nos complace informarte que tu cuenta en **{{ $appName }}** ha sido creada exitosamente.
            ¡Prepárate para explorar todas las funcionalidades que tenemos para ti!
        </p>

        <p style="font-size: 16px; color: #4a5568; line-height: 1.6; margin-bottom: 25px;">
            Para tu primer acceso, por favor utiliza la siguiente contraseña temporal. Es crucial que la guardes de
            forma segura:
        </p>

        {{-- Contraseña en un panel específico para destacarla --}}
        <x-mail::panel style="background-color: #e6ffed; border: 2px solid #38a169; border-radius: 12px;">
            <div style="font-family: 'Courier New', Courier, monospace; font-size: 1.8em; font-weight: bold; padding: 15px 20px; text-align: center; color: #2d3748;">
                {{ $generatedPassword }}
            </div>
        </x-mail::panel>

        <p style="font-size: 16px; color: #e53e3e; line-height: 1.6; margin-top: 25px; font-weight: bold;">
            <span style="color: #c53030;">¡Importante!</span> Por tu seguridad, te recomendamos encarecidamente
            cambiar esta contraseña temporal por una personal y segura tan pronto como inicies sesión. Puedes
            hacerlo desde la sección de perfil de tu cuenta.
        </p>

        <p style="font-size: 16px; color: #4a5568; line-height: 1.6; margin-top: 20px;">
            Si tienes alguna pregunta o necesitas ayuda, no dudes en contactar al equipo de soporte.
        </p>

        <p style="font-size: 16px; color: #4a5568; line-height: 1.6; margin-top: 30px; text-align: center;">
            ¡Gracias por unirte a nuestra comunidad!
        </p>

        <p style="font-size: 16px; color: #4a5568; line-height: 1.6; text-align: center;">
            El equipo de {{ $appName }}
        </p>
    </x-mail::panel> {{-- Cierre del panel principal --}}

    {{-- Si necesitas un botón, usa: --}}
    {{-- <x-mail::button :url="url('/login')">
        Iniciar Sesión
    </x-mail::button> --}}

</x-mail::message>