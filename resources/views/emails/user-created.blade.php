<x-mail::message>
# ¡Hola {{ $userName }}!

Tu cuenta en **{{ $appName }}** ha sido creada exitosamente.

Para empezar a utilizarla, por favor **establece tu contraseña** haciendo clic en el siguiente botón:

<x-mail::button :url="$resetUrl">
Establecer Contraseña
</x-mail::button>

Este enlace expirará en {{ config('auth.passwords.users.expire') }} minutos.

Si no has solicitado esto o crees que se trata de un error, por favor ignora este correo electrónico.

Gracias,
El equipo de {{ $appName }}
</x-mail::message>