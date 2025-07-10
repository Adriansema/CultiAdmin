<div class="mt-4 space-y-4 accordion" id="faqAccordion faq-list">
    <h2 class="mb-4 text-2xl font-bold">Guia de Usuario</h2>

    <p class="mb-6">
        Bienvenido a la guia de usuario. Aqui te explicamos paso a paso como sacar el maximo provecho de cada seccion de
        nuestra plataforma.
        Este documento es una brujula para principiantes y expertos por igual.
    </p>
    
    <div id="faq" class="tab-content">
        <div class="faq-item question-item" data-question="crear-nuevo-usuario">
            <div class="p-4 bg-gray-200 rounded-lg">
                <h3 class="text-xl font-semibold">¿Como <b>crear un nuevo usuario</b> en la plataforma?</h3>
                <p class="text-gray-600">
                    Para <b>crear un nuevo usuario</b>, sigue estos pasos detallados utilizando el asistente de tres
                    fases:
                </p>
                <ol class="pl-5 mt-2 space-y-3 list-decimal">
                    <li>
                        <b>Paso 1: Acceder y Abrir el Asistente</b>
                        <ul class="pl-5 mt-1 space-y-1 list-disc">
                            <li>Dirigete a <b>Usuarios > Lista de usuarios</b>.</li>
                            <li>Encontraras un boton de color <b>verde</b> con el texto <b>"+ Nuevo usuario"</b>. Haz
                                clic en el.</li>
                            <li>Se abrira un <b>modal</b> que te guiara por el proceso.</li>
                        </ul>
                    </li>
                    <li>
                        <b>Paso 2: Completar Datos Basicos del Usuario</b>
                        <p class="mt-1">En esta seccion, deberas ingresar la informacion fundamental del <b>nuevo
                                usuario</b>:</p>
                        <ul class="pl-5 mt-1 space-y-1 list-disc">
                            <li><b>Nombre</b></li>
                            <li><b>Apellido</b></li>
                            <li><b>Correo</b> (sera el identificador de acceso del <b>usuario</b>)</li>
                            <li><b>Telefono</b></li>
                            <li><b>Tipo de documento</b> (ej. Cedula, Pasaporte)</li>
                            <li><b>Documento</b> (numero de identificacion)</li>
                        </ul>
                        <p class="mt-2">Una vez completados todos los campos, presiona el boton <b>"Siguiente >"</b>.
                        </p>
                    </li>
                    <li>
                        <b>Paso 3: Asignar Roles y Permisos</b>
                        <p class="mt-1">Aqui definiras el nivel de acceso y las funcionalidades del <b>nuevo
                                usuario</b>. Puedes elegir entre roles predefinidos o asignar permisos individuales:</p>
                        <p class="mt-2 font-medium">Roles Disponibles:</p>
                        <ul class="pl-5 mt-1 space-y-1 list-disc">
                            <li><b>SuperAdmin:</b> Control total de la plataforma.</li>
                            <li><b>Administrador:</b> Amplios permisos de gestion.</li>
                            <li><b>Operario:</b> Para tareas operativas especificas.</li>
                            <li><b>Funcionario:</b> Acceso limitado y especifico.</li>
                        </ul>
                        <p class="mt-2 font-medium">Permisos Especificos (Cada rol puede tener varios):</p>
                        <ul class="pl-5 mt-1 space-y-1 list-disc">
                            <li><b>Crear:</b> producto, noticia, <b>usuario</b>, boletines</li>
                            <li><b>Editar:</b> producto, noticia, boletines, <b>usuario</b></li>
                            <li><b>Validar:</b> producto, noticia, boletin</li>
                            <li><b>Eliminar:</b> noticia, producto, boletines</li>
                        </ul>
                        <p class="mt-2">Despues de configurar roles y permisos para el <b>usuario</b>, presiona
                            nuevamente el boton <b>"Siguiente >"</b>.</p>
                    </li>
                    <li>
                        <b>Paso 4: Generar Contrasena y Confirmar Creacion</b>
                        <ul class="pl-5 mt-1 space-y-1 list-disc">
                            <li>Haz clic en el boton <b>"Generar contrasena"</b> para que el sistema cree una
                                automaticamente para el <b>usuario</b>.</li>
                            <li>Luego, presiona <b>"Asignar"</b>.</li>
                            <li>Aparecera un <b>modal de confirmacion</b> con un resumen de todos los datos ingresados y
                                configuraciones del <b>usuario</b>. Revisalos.</li>
                            <li>Si todo es correcto, presiona <b>"Confirmar"</b>.</li>
                            <li>Espera un momento hasta que aparezca un ultimo modal indicando <b>"Se ha creado con
                                    exito el usuario"</b>.</li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>

        <hr class="my-6 border-gray-300">

        <div class="faq-item question-item" data-question="roles-usuarios">
            <div class="p-4 bg-gray-200 rounded-lg">
                <h3 class="text-xl font-semibold">¿Como asigno o modifico roles a <b>usuarios existentes</b>?</h3>
                <p class="text-gray-600">
                    Una vez que un <b>usuario</b> ha sido creado, sus roles y permisos pueden ser ajustados en cualquier
                    momento:
                <ul class="pl-5 mt-2 space-y-1 list-disc">
                    <li>Dirigete a <b>Usuarios > Lista de usuarios</b>.</li>
                    <li>Busca al <b>usuario</b> deseado en la lista y haz clic en el boton <b>"Editar"</b> asociado a su
                        perfil.</li>
                    <li>En la seccion de roles (dentro del formulario de edicion del <b>usuario</b>), marca o desmarca
                        el rol o los roles correspondientes: <b>Administrador, Operador o Usuario</b> (o los roles
                        especificos que apliquen en tu configuracion).</li>
                    <li>No olvides hacer clic en <b>"Actualizar"</b> (o "Guardar") para aplicar y guardar los cambios.
                    </li>
                </ul>
                Para una comprension mas profunda sobre la interaccion y la gestion de roles y permisos de los
                <b>usuarios</b>, puedes encontrar mas informacion
                <a href="{{ route('usuarios.index') }}" class="text-blue-600 underline"><b>aqui</b></a>
                </p>
            </div>
        </div>
    </div>
</div>
