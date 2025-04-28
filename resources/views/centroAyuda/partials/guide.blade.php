<div class="p-4 bg-white rounded shadow-sm">


    <h2 class="mb-4 text-2xl font-bold">Guía de Usuario</h2>

    <p class="mb-6">
        Bienvenido a la guía de usuario. Aquí te explicamos paso a paso cómo sacar el máximo provecho de cada sección de nuestra plataforma.
        Este documento es una brújula para principiantes y expertos por igual.
    </p>

    <!-- Sección: Inicio de Sesión -->
    <div class="mb-8">
        <h3 class="mb-2 text-xl font-semibold">1. Inicio de Sesión</h3>
        <p>
            Accede a tu cuenta utilizando tu correo electrónico y contraseña registrados.
            Si olvidaste tu contraseña, puedes recuperarla haciendo clic en
            <a href="{{ route('password.request') }}" class="text-blue-600 underline">¿Olvidaste tu contraseña?</a>.
        </p>
    </div>

    <!-- Sección: Registro de Productos -->
    <div class="mb-8">
        <h3 class="mb-2 text-xl font-semibold">2. Registro de Productos</h3>
        <p>
            Desde el menú lateral, ingresa a <strong>Productos</strong> y haz clic en <em>Crear Producto</em>.
            Completa los siguientes campos:
        </p>
        <ul class="mt-2 list-disc list-inside">
            <li><strong>Nombre:</strong> Nombre del producto agrícola.</li>
            <li><strong>Descripción:</strong> Información relevante sobre el producto.</li>
            <li><strong>Imagen:</strong> Una fotografía representativa.</li>
            <li><strong>Estado:</strong> Selecciona "Pendiente", "Aprobado" o "Rechazado".</li>
        </ul>
        <p class="mt-2">
            Una vez guardado, el producto será enviado para validación por un Operador.
            <br>
            Más detalles en:
            <a href="https://tuapp.com/tutoriales/crear-producto" target="_blank" class="text-blue-600 underline">Crear un producto</a>.
        </p>
    </div>

    <!-- Sección: Validación de Productos -->
    <div class="mb-8">
        <h3 class="mb-2 text-xl font-semibold">3. Validación de Productos</h3>
        <p>
            Los operadores tienen la responsabilidad de validar los productos ingresados. Para hacerlo:
        </p>
        <ol class="mt-2 list-decimal list-inside">
            <li>Ve a la sección <strong>Productos Pendientes</strong>.</li>
            <li>Revisa los detalles del producto.</li>
            <li>Aprueba o rechaza según la calidad de la información proporcionada.</li>
            <li>En caso de rechazo, debes escribir una observación clara y precisa.</li>
        </ol>
        <p class="mt-2">
            Consulta el manual de validación:
            <a href="https://tuapp.com/manuales/validacion" target="_blank" class="text-blue-600 underline">Manual de Validación</a>.
        </p>
    </div>

    <!-- Sección: Gestión de Boletines -->
    <div class="mb-8">
        <h3 class="mb-2 text-xl font-semibold">4. Gestión de Boletines</h3>
        <p>
            Para comunicar noticias o novedades:
        </p>
        <ul class="mt-2 list-disc list-inside">
            <li>Selecciona la opción <strong>Boletines</strong>.</li>
            <li>Haz clic en <em>Nuevo Boletín</em>.</li>
            <li>Completa el asunto y el contenido del boletín.</li>
            <li>Publica para que llegue a todos los usuarios registrados.</li>
        </ul>
        <p class="mt-2">
            Aprende más sobre boletines en:
            <a href="https://tuapp.com/ayuda/boletines" target="_blank" class="text-blue-600 underline">Guía de Boletines</a>.
        </p>
    </div>

    <!-- Sección: Gestión de Usuarios -->
    <div class="mb-8">
        <h3 class="mb-2 text-xl font-semibold">5. Gestión de Usuarios</h3>
        <p>
            Como administrador puedes:
        </p>
        <ul class="mt-2 list-disc list-inside">
            <li>Crear nuevos usuarios manualmente.</li>
            <li>Asignar y modificar roles: <em>Administrador</em>, <em>Operador</em> o <em>Usuario</em>.</li>
            <li>Activar o desactivar cuentas según necesidades.</li>
            <li>Enviar invitaciones por correo para registro seguro.</li>
        </ul>
        <p class="mt-2">
            Aprende cómo gestionar usuarios aquí:
            <a href="https://tuapp.com/ayuda/gestion-usuarios" target="_blank" class="text-blue-600 underline">Gestión de Usuarios</a>.
        </p>
    </div>

    <!-- Sección: Historial de Actividades -->
    <div class="mb-8">
        <h3 class="mb-2 text-xl font-semibold">6. Historial de Actividades</h3>
        <p>
            Cada acción realizada queda registrada en el historial:
        </p>
        <ul class="mt-2 list-disc list-inside">
            <li>Acceso a productos creados, modificados o eliminados.</li>
            <li>Seguimiento de validaciones realizadas.</li>
            <li>Registro de publicaciones de boletines.</li>
        </ul>
        <p class="mt-2">
            Más información sobre trazabilidad:
            <a href="https://tuapp.com/ayuda/historial" target="_blank" class="text-blue-600 underline">Historial de Actividades</a>.
        </p>
    </div>
</div>
