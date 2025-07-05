<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Bienvenida</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f7fafc; padding: 20px; color: #1a202c;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px;">
        <div style="text-align: center; margin-bottom: 25px;">
            <img src="{{ asset('images/CultivaAdmin.png') }}" alt="Logo Cultiva Admin"
                style="max-width: 100%; height: auto;">
        </div>

        <h1 style="text-align: center;">¡Bienvenido a {{ $appName }}, {{ $userName }}!</h1>

        <p>Nos complace informarte que tu cuenta ha sido creada exitosamente...</p>

        <div
            style="background-color: #e6ffed; border: 2px solid #38a169; border-radius: 12px; padding: 15px 20px; text-align: center; font-size: 1.5em;">
            {{ $generatedPassword }}
        </div>

        <p style="font-size: 16px; color: #4a5568; line-height: 1.6; margin-bottom: 25px;">
            Esta contraseña ha sido generada exclusivamente para que realices tu primer ingreso al sistema.
            Úsala al iniciar sesión por primera vez en {{ $appName }}. Luego podrás cambiarla por una de tu
            preferencia desde tu perfil.
        </p>


        <p>Gracias por unirte.<br>El equipo de {{ $appName }}</p>
    </div>
</body>

</html>
