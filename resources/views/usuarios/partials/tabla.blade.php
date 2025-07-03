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

         <tbody>
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
                     <tr class="bg-white hover:bg-gray-100">

                         <td class="px-6 py-4 flex items-center relative">
                             <span>{{ $usuario->roles->pluck('name')->join(', ') }}</span>

                             @php
                                 $canEditTargetUser = true; // Por defecto, se asume que se puede editar
                                 $loggedInUser = Auth::user();
                                 $targetUser = $usuario; // Usuario en la fila actual

                                 // Regla 1: Si el usuario logueado es Operario o Funcionario, NO puede editar NINGÚN usuario.
                                 if ($loggedInUser->hasAnyRole(['Operario', 'Funcionario'])) {
                                     $canEditTargetUser = false;
                                 }
                                 // Regla 2: Si el usuario logueado es un Administrador
                                 elseif ($loggedInUser->hasRole('Administrador')) {
                                     // Regla 2.1: Administrador no puede auto-editarse.
                                     if ($targetUser->id === $loggedInUser->id) {
                                         $canEditTargetUser = false;
                                     }
                                     // Regla 2.2: Administrador no puede editar SuperAdmin o a otro Administrador.
                                     // Esto aplica para otros Admins, no para sí mismo (ya cubierto por 2.1).
                                     elseif (
                                         $targetUser->hasRole('SuperAdmin') ||
                                         $targetUser->hasRole('Administrador')
                                     ) {
                                         $canEditTargetUser = false;
                                     }
                                     // Si el Administrador está viendo un Operario o Funcionario, $canEditTargetUser permanece true.
                                 }
                                 // Regla 3: Si el usuario logueado es SuperAdmin, puede editar a cualquiera
                                 // No se necesita un 'else' explícito aquí, ya que $canEditTargetUser permanece true por defecto
                                 // y solo las reglas anteriores lo cambian a false para roles específicos.
                                 // El SuperAdmin no entra en los 'if'/'elseif' de restricciones de rol.
                             @endphp

                             @can('editar usuario')
                                 @if ($canEditTargetUser && $loggedInUser)
                                     {{-- Muestra el botón interactivo si el usuario tiene permiso y se cumplen las condiciones --}}
                                     <button type="button" class="ml-2 edit-user-button"
                                         data-user-id="{{ $usuario->id }}">
                                         <img src="{{ asset('images/lapiz.svg') }}" class="w-4 h-4" alt="editar">
                                     </button>
                                 @else
                                     {{-- Muestra el icono no interactivo y opaco si el usuario tiene permiso, pero las condiciones internas no se cumplen --}}
                                     <span class="ml-2 cursor-not-allowed">
                                         <img src="{{ asset('images/lapiz.svg') }}" class="w-4 h-4 opacity-50"
                                             alt="editar">
                                     </span>
                                 @endif
                             @endcan
                         </td>

                         <td class="px-6 py-4">
                             {{ $usuario->name }}
                         </td>

                         <td class="px-6 py-4">
                             {{ $usuario->email }}
                         </td>

                         <td class="py-4">
                             {{-- Condicional principal: Solo mostrar el botón de toggle si NO es el usuario autenticado --}}
                             @if ($usuario->id !== Auth::id())
                                 {{-- Condición adicional: Calcular si el botón de toggle debe mostrarse --}}
                                 @php
                                     $loggedInUser = Auth::user();
                                     $canShowToggleButton = true; // Por defecto, se asume que se puede mostrar el botón

                                     // Regla 1 (UI): Si el usuario logueado es Operario o Funcionario, NO puede ver el botón para NINGÚN otro usuario.
                                     if ($loggedInUser->hasAnyRole(['Operario', 'Funcionario'])) {
                                         $canShowToggleButton = false;
                                     }
                                     // Regla 2 (UI): Si el usuario logueado es un Administrador (y no Operario/Funcionario por la condición anterior)
                                     elseif ($loggedInUser->hasRole('Administrador')) {
                                         // Un Administrador no puede ver el botón para un SuperAdmin o para otro Administrador.
                                         // (Ya sabemos que no es el propio usuario por el if principal: $usuario->id !== Auth::id())
                                         if ($usuario->hasRole('SuperAdmin') || $usuario->hasRole('Administrador')) {
                                             $canShowToggleButton = false;
                                         }
                                     }
                                     // Regla 3: Si el usuario logueado es SuperAdmin, $canShowToggleButton permanece true por defecto,
                                     // ya que no entra en las condiciones de restricciones de los roles inferiores.
                                 @endphp

                                 @if ($canShowToggleButton)
                                     <form action="{{ route('usuarios.toggle', $usuario) }}" method="POST"
                                         class="inline-block">
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

                                                 {{-- ? Icono para el estado "activo" o "inactivo" --}}
                                                 <img src="{{ asset('images/' . ($usuario->estado === 'activo' ? 'activo-flecha.svg' : 'inact-flecha.svg')) }}"
                                                     alt="Icono {{ $usuario->estado }}" class="w-4 h-4">
                                             </span>

                                             {{-- !Al pasar el ratón por encima, el botón cambia para indicar la acción que se realizará (Desactivar/Activar), mostrando el color correspondiente y un icono distinto para la acción. --}}
                                             {{-- ? Contenido para el estado al hacer hover (Desactivar/Activar) --}}
                                             <span
                                                 class="absolute inset-0 flex items-center justify-center space-x-2
                                            opacity-0 group-hover:opacity-100 transition-opacity duration-300
                                            {{ $usuario->estado === 'activo' ? 'text-[var(--color-textDesact)]' : 'text-[var(--color-textActivar)]' }}
                                            pointer-events-none">
                                                 <span>{{ $usuario->estado === 'activo' ? 'Desactivar' : 'Activar' }}</span>

                                                 {{-- ? Icono para la acción al hacer hover ("Desactivar" o "Activar") --}}
                                                 <img src="{{ asset('images/' . ($usuario->estado === 'activo' ? 'desact-flecha.svg' : 'activar-flecha.svg')) }}"
                                                     alt="Icono Hover" class="w-4 h-4">
                                             </span>
                                         </button>
                                     </form>
                                 @else
                                     {{-- Muestra solo el estado actual (Activo/Inactivo) si es el propio usuario,
                                        o un Administrador intentando cambiar a un SuperAdmin/otro Administrador. Solo cumple la funcion de desactivar o activar
                                        a roles inferiores como Operario y Funcionarios --}}
                                     <span
                                         class="px-4 py-2 text-sm rounded-lg
                                            {{ $usuario->id === Auth::id() ? 'bg-blue-200 text-blue-800' : '' }} {{-- Si es tu propio usuario --}}
                                            {{ $usuario->id !== Auth::id() && $usuario->estado === 'activo' ? 'bg-[var(--color-activo)] text-[var(--color-textAct)]' : '' }} {{-- Si es otro usuario activo --}}
                                            {{ $usuario->id !== Auth::id() && $usuario->estado === 'inactivo' ? 'bg-[var(--color-inactivo)] text-[var(--color-textInact)]' : '' }} {{-- Si es otro usuario inactivo --}}
                                            inline-flex items-center justify-center space-x-1 cursor-not-allowed">
                                         <span>
                                             @if ($usuario->id === Auth::id())
                                                 {{ $usuario->estado === 'activo' ? 'Estas Activo' : 'Estas Inactivo' }}
                                             @else
                                                 {{ ucfirst($usuario->estado) }}
                                             @endif
                                         </span>

                                         {{-- Icono para el estado actual --}}
                                         <span>
                                             <img src="{{ asset('images/' . ($usuario->estado === 'activo' ? 'activo-flecha.svg' : 'inact-flecha.svg')) }}"
                                                 alt="Icono {{ $usuario->estado }}" class="w-4 h-4">
                                         </span>
                                     </span>
                                 @endif
                             @else
                                 {{-- Este bloque es para la fila del propio usuario logueado (sin botón de toggle) de que no se puede auto-desactivarse --}}
                                 <span
                                     class="px-4 py-2 text-sm rounded-lg
                                            bg-blue-200 text-blue-800
                                            inline-flex items-center justify-center space-x-1 cursor-not-allowed">
                                     <span>{{ $usuario->estado === 'activo' ? 'Estas Activo' : 'Estas Inactivo' }}</span>
                                     <span
                                         class="w-3 h-3 rounded-full {{ $usuario->estado === 'activo' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                 </span>
                             @endif
                         </td>
                     </tr>
                 @endforeach
             @endif
         </tbody>
     </table>
 </div>
