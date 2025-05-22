 <div class="overflow-x-auto rounded-2xl">
    <table class="min-w-full divide-y divide-gray-100">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                <th class="px-6 py-3 font-medium text-left text-gray-600">Rol</th>
                <th class="px-6 py-3 font-medium text-left text-gray-600">Nombre</th>
                <th class="px-6 py-3 font-medium text-left text-gray-600">Email</th>
                <th class="px-6 py-3 font-medium text-left text-gray-600">Estado</th>
            </tr>
        </thead>

        <tbody id="usersTableBody">
            @if ($usuarios->total() === 0)
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        @if (request()->has('q') && !empty(request()->get('q')))
                            No se encontraron usuarios que coincidan con
                            "{{ htmlspecialchars(request()->get('q')) }}".
                        @else
                            No hay usuarios registrados.
                        @endif
                    </td>
                </tr>
            @else
                @foreach ($usuarios as $usuario)
                    <tr class="bg-white hover:bg-gray-300">
                        <td class="px-6 py-4 flex items-center group relative">
                            <span>{{ $usuario->roles->pluck('name')->join(', ') }}</span>
                            <img src="{{ asset('images/lapiz.svg') }}"
                                class="w-4 h-4 absolute left-[calc(60%+4px)] top-1/2 -translate-y-1/2 
                                        opacity-0 group-hover:opacity-100 
                                        transition-opacity duration-300 pointer-events-none group-hover:pointer-events-auto"
                                alt="editar">
                        </td>
                        <td class="px-6 py-4">{{ $usuario->name }}
                        </td>
                        <td class="px-6 py-4">{{ $usuario->email }}
                        </td>
                        <td class=" py-4">
                            {{-- !Cuando se hace clic en el botón, se envía una solicitud PATCH a la aplicación para actualizar el estado del usuario en la base de datos, garantizando la seguridad con el token CSRF de Laravel. --}}
                            <form action="{{ route('usuarios.toggle', $usuario) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')

                                {{-- ! botón dinámico que permite cambiar el estado de un usuario (de "activo" a "inactivo" y viceversa) --}}
                                <button type="submit"
                                    class="group relative px-4 py-2 text-sm rounded-lg text-[var(--color-textAct)] transition-colors duration-300
                                            {{ $usuario->estado === 'activo' ? 'bg-[var(--color-activo)] hover:bg-[var(--color-desactivar)]' : 'bg-[var(--color-inactivo)] hover:bg-[var(--color-activar)]' }}
                                            inline-flex items-center justify-center">
                                            {{-- !El botón se adapta visualmente: --}}
                                    {{-- Usamos inline-flex para el botón --}}

                                    {{-- !Muestra el estado actual del usuario (Activo/Inactivo) con su propio color y un icono específico para ese estado. --}}
                                    {{-- ? Contenido para el estado normal (Activo/Inactivo) --}}
                                    <span
                                        class="flex items-center space-x-2 transition-opacity duration-300
                                                        {{ $usuario->estado === 'activo' ? '' : 'text-[var(--color-textInact)]' }}
                                                        group-hover:opacity-0 pointer-events-none">
                                        {{-- Añadir pointer-events-none aquí también --}}
                                        <span>{{ ucfirst($usuario->estado) }}</span>

                                        {{-- ? Icono para el estado "activo" o "inactivo" activo-flecha.svg: Representa visualmente el estado "activo".
                                         ? inact-flecha.svg: Representa visualmente el estado "inactivo".--}}
                                        <img src="{{ asset('images/' . ($usuario->estado === 'activo' ? 'activo-flecha.svg' : 'inact-flecha.svg')) }}" 
                                            alt="Icono {{ $usuario->estado }}" 
                                            class="w-4 h-4">
                                    </span>

                                    {{-- !Al pasar el ratón por encima, el botón cambia para indicar la acción que se realizará (Desactivar/Activar), mostrando el color correspondiente y un icono distinto para la acción. --}}
                                    {{-- ? Contenido para el estado al hacer hover (Desactivar/Activar) --}}
                                    <span
                                        class="absolute inset-0 flex items-center justify-center space-x-2
                                                        opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                                        {{ $usuario->estado === 'activo' ? 'text-[var(--color-textDesact)]' : 'text-[var(--color-textActivar)]' }}
                                                        pointer-events-none">
                                        <span>{{ $usuario->estado === 'activo' ? 'Desactivar' : 'Activar' }}</span>

                                        {{-- ? Icono para la acción al hacer hover ("Desactivar" o "Activar") desactivar-desact.svg: Representa visualmente la acción de "desactivar" (cuando el usuario está activo y haces hover).
                                          ? activar-flecha.svg: Representa visualmente la acción de "activar" (cuando el usuario está inactivo y haces hover).--}}
                                        <img src="{{ asset('images/' . ($usuario->estado === 'activo' ? 'desact-flecha.svg' : 'activar-flecha.svg')) }}" 
                                            alt="Icono Hover" 
                                            class="w-4 h-4">
                                    </span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>


 {{-- resources/views/usuarios/partials/tabla.blade.php --}}
{{-- <div class="overflow-x-auto rounded-2xl">
    {{-- Añadimos id="usuariosTable" a la tabla --}
    <table id="usuariosTable" class="min-w-full divide-y divide-gray-100">
        <thead class="bg-[var(--color-tabla)]">
            <tr>
                {{-- Columna "Rol" --}}
                {{-- data-column="roles_name" porque queremos ordenar por el nombre del rol --}
                <th class="px-6 py-3 font-medium text-left text-gray-600 cursor-pointer" data-column="roles_name">
                    <div class="flex items-center"> {{-- Añadimos un flex container para el texto y el icono --}
                        Rol
                        <img src="{{ asset('images/asce.svg') }}" alt="Ordenar Rol" class="sort-icon inline-block w-4 h-4 ml-1"
                             data-sort-asc-src="{{ asset('images/asce.svg') }}"
                             data-sort-desc-src="{{ asset('images/desce.svg') }}">
                    </div>
                </th>

                {{-- Columna "Nombre" --}}
                {{-- data-column="name" para el nombre del usuario --}
                <th class="px-6 py-3 font-medium text-left text-gray-600 cursor-pointer" data-column="name">
                    <div class="flex items-center">
                        Nombre
                        <img src="{{ asset('images/asce.svg') }}" alt="Ordenar Nombre" class="sort-icon inline-block w-4 h-4 ml-1"
                             data-sort-asc-src="{{ asset('images/asce.svg') }}"
                             data-sort-desc-src="{{ asset('images/desce.svg') }}">
                    </div>
                </th>

                {{-- Columna "Email" --}}
                {{-- data-column="email" para el email del usuario --}
                <th class="px-6 py-3 font-medium text-left text-gray-600 cursor-pointer" data-column="email">
                    <div class="flex items-center">
                        Email
                        <img src="{{ asset('images/asce.svg') }}" alt="Ordenar Email" class="sort-icon inline-block w-4 h-4 ml-1"
                             data-sort-asc-src="{{ asset('images/asce.svg') }}"
                             data-sort-desc-src="{{ asset('images/desce.svg') }}">
                    </div>
                </th>

                {{-- Columna "Estado" --}}
                {{-- data-column="estado" para el estado del usuario --}
                <th class="px-6 py-3 font-medium text-left text-gray-600 cursor-pointer" data-column="estado">
                    <div class="flex items-center">
                        Estado
                        <img src="{{ asset('images/asce.svg') }}" alt="Ordenar Estado" class="sort-icon inline-block w-4 h-4 ml-1"
                             data-sort-asc-src="{{ asset('images/asce.svg') }}"
                             data-sort-desc-src="{{ asset('images/desce.svg') }}">
                    </div>
                </th>
            </tr>
        </thead>

        <tbody id="usersTableBody"> {{-- Asegúrate de que el ID sea 'usersTableBody' como lo tienes --}
            @if ($usuarios->total() === 0)
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        @if (request()->has('q') && !empty(request()->get('q')))
                            No se encontraron usuarios que coincidan con
                            "{{ htmlspecialchars(request()->get('q')) }}".
                        @else
                            No hay usuarios registrados.
                        @endif
                    </td>
                </tr>
            @else
                @foreach ($usuarios as $usuario)
                    <tr class="bg-white hover:bg-gray-300" data-user-id="{{ $usuario->id }}" data-user-name="{{ $usuario->name }}" data-user-email="{{ $usuario->email }}" data-user-estado="{{ $usuario->estado }}" data-user-roles="{{ $usuario->roles->pluck('name')->join(', ') }}">
                        {{-- Añadimos data-attributes a la fila para facilitar la lectura en JS --}

                        <td class="px-6 py-4 flex items-center group relative">
                            <span>{{ $usuario->roles->pluck('name')->join(', ') }}</span>
                            <img src="{{ asset('images/lapiz.svg') }}"
                                class="w-4 h-4 absolute left-[calc(60%+4px)] top-1/2 -translate-y-1/2
                                        opacity-0 group-hover:opacity-100
                                        transition-opacity duration-300 pointer-events-none group-hover:pointer-events-auto"
                                alt="editar">
                        </td>
                        <td class="px-6 py-4">{{ $usuario->name }}
                        </td>
                        <td class="px-6 py-4">{{ $usuario->email }}
                        </td>
                        <td class=" py-4">
                            {{-- !Mantenemos tu formulario y botón tal cual, ya que el JS lo reconstruirá --}
                            <form action="{{ route('usuarios.toggle', $usuario) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="group relative px-4 py-2 text-sm rounded-lg text-[var(--color-textAct)] transition-colors duration-300
                                            {{ $usuario->estado === 'activo' ? 'bg-[var(--color-activo)] hover:bg-[var(--color-desactivar)]' : 'bg-[var(--color-inactivo)] hover:bg-[var(--color-activar)]' }}
                                            inline-flex items-center justify-center">

                                    <span
                                        class="flex items-center space-x-2 transition-opacity duration-300
                                                        {{ $usuario->estado === 'activo' ? '' : 'text-[var(--color-textInact)]' }}
                                                        group-hover:opacity-0 pointer-events-none">
                                        <span>{{ ucfirst($usuario->estado) }}</span>

                                        <img src="{{ asset('images/' . ($usuario->estado === 'activo' ? 'activo-flecha.svg' : 'inact-flecha.svg')) }}"
                                            alt="Icono {{ $usuario->estado }}"
                                            class="w-4 h-4">
                                    </span>

                                    <span
                                        class="absolute inset-0 flex items-center justify-center space-x-2
                                                        opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                                        {{ $usuario->estado === 'activo' ? 'text-[var(--color-textDesact)]' : 'text-[var(--color-textActivar)]' }}
                                                        pointer-events-none">
                                        <span>{{ $usuario->estado === 'activo' ? 'Desactivar' : 'Activar' }}</span>

                                        <img src="{{ asset('images/' . ($usuario->estado === 'activo' ? 'desact-flecha.svg' : 'activar-flecha.svg')) }}"
                                            alt="Icono Hover"
                                            class="w-4 h-4">
                                    </span>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div> --}}