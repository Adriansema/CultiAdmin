<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Mail\UserCreatedNotification;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;

class UsuarioController extends Controller
{
    /**
     * Muestra la tabla de usuarios filtrados.
     * Requiere el permiso 'ver tabla de usuarios'.
     */
    public function index(Request $request, UserService $userService)
    {
        Gate::authorize('crear usuario');
        $usuarios = $userService->obtenerUsuariosFiltrados($request);
        // Necesitamos pasar los roles y permisos disponibles para el modal
        $roles = Role::all();
        $permissions = Permission::all();

        return view('usuarios.index', compact('usuarios', 'roles', 'permissions'));
    }

    /**
     * Obtiene usuarios filtrados para peticiones AJAX.
     * Considera si esta ruta también necesita un permiso.
     */
    public function getFilteredUsers(Request $request, UserService $userService)
    {
        $usuarios = $userService->obtenerUsuariosFiltrados($request);
        return response()->json($usuarios);
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     * Requiere el permiso 'crear usuario'.
     */
    public function create()
    {
        // --- RESTRICCIÓN DE ROL: Operario/Funcionario no pueden crear usuarios ---
        if (Auth::user()->hasAnyRole(['Operario', 'Funcionario'])) {
            return redirect()->route('dashboard')->with('error', 'Tu rol no te permite crear usuarios.');
        }

        return redirect()->route('usuarios.index')->with('success', 'Preparado para crear un nuevo usuario.');
    }

    /**
     * Almacena un nuevo usuario.
     * Si la solicitud proviene del Paso 1 del modal, solo creará el usuario inicial.
     * Si la solicitud proviene del Paso 2, actualizará roles y permisos.
     * ¡ESTE MÉTODO AHORA DEVUELVE JSON!
     */
    public function store(Request $request)
    {
        // --- RESTRICCIÓN DE ROL: Operario/Funcionario no pueden crear usuarios ---
        if (Auth::user()->hasAnyRole(['Operario', 'Funcionario'])) {
            // Devuelve un error JSON para que Alpine.js lo maneje
            return redirect()->route('dashboard')->with('error', 'Tu rol no te permite crear usuarios.');
        }

        // Determinar qué parte del formulario se está enviando
        // Podemos usar un campo oculto, por ejemplo 'form_step'
        $formStep = $request->input('form_step');

        if ($formStep === 'step1') {
            // Lógica para el primer paso: Creación inicial del usuario
            $request->validate([
                'name'          => 'required|string|max:255',
                'email'         => 'required|email|unique:users,email',
                'type_document' => 'required|string|max:10',
                'document'      => 'required|string|max:20|unique:users,document',
                'form_step'     => 'required|string|in:step1', // Para confirmar que es el paso 1
            ], [
                'name.required' => 'El nombre es obligatorio.',
                'email.required' => 'El correo es obligatorio.',
                'email.email' => 'El correo debe ser una dirección de email válida.',
                'email.unique' => 'Este correo ya está registrado.',
                'type_document.required' => 'El tipo de documento es obligatorio.',
                'document.required' => 'El número de documento es obligatorio.',
                'document.unique' => 'Este número de documento ya está registrado.',
            ]);

            try {
                $documentNumber = $request->document;
                $user = User::create([
                    'name'              => $request->name,
                    'email'             => $request->email,
                    'type_document'     => $request->type_document,
                    'document'          => $documentNumber,
                    'password'          => Hash::make($documentNumber),
                    'estado'            => 'inactivo', // Inactivo hasta que establezca contraseña
                    'email_verified_at' => null,
                ]);

                // Enviar el email para que el usuario establezca su contraseña real
                $token = app('auth.password.broker')->createToken($user);
                $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
                Mail::to($user->email)->send(new UserCreatedNotification($user, $resetUrl));

                Log::info('Usuario creado manualmente (admin) - Paso 1 completado. Se envió email para establecer contraseña.', [
                    'user_id'          => $user->id,
                    'email'            => $user->email,
                    'created_by'       => Auth::id(),
                ]);

                // Devuelve una respuesta JSON con el ID del usuario y un mensaje
                return response()->json([
                    'success' => true,
                    'message' => 'Usuario básico creado. Proceda a asignar roles y permisos.',
                    'user_id' => $user->id, // Esencial para el Paso 2
                ]);
            } catch (\Exception $e) {
                Log::error('Error al crear usuario manualmente (admin) - Paso 1.', [
                    'error'            => $e->getMessage(),
                    'user_data'        => $request->except(['_token', 'password', 'document', 'form_step']),
                    'created_by'       => Auth::id(),
                    'ip_address'       => $request->ip(),
                    'trace'            => $e->getTraceAsString(),
                ]);
                return response()->json(['message' => 'Ocurrió un error al crear el usuario. Por favor, inténtalo de nuevo.', 'errors' => ['general' => $e->getMessage()]], 500);
            }
        } elseif ($formStep === 'step2_create_mode') {
            // Lógica para el segundo paso cuando se está creando un usuario
            // Esto se ejecutaría si el paso 2 se enviara por separado para un nuevo usuario.
            // Generalmente, el Paso 2 de un *nuevo* usuario se maneja con el método `update`
            // después de que el usuario ya existe.
            // Para simplificar, asumiremos que si llegamos a step2, es para actualizar un usuario existente
            // o que la lógica de store se ha bifurcado para manejar creación Y asignación.
            // Para un flujo más limpio, el Paso 2 de *creación* lo manejará el `update` después de obtener el `user_id`.

            // Si el request incluye 'user_id', asumimos que es el Paso 2 de creación
            $userId = $request->input('user_id');
            $user = User::find($userId);

            if (!$user) {
                return response()->json(['message' => 'Usuario no encontrado para asignar roles/permisos.', 'errors' => ['user_id' => 'Usuario no válido.']], 404);
            }

            // Aplicar las mismas restricciones de seguridad que en el método `update`
            $loggedInUser = Auth::user();
            $targetUserRoles = $user->getRoleNames();

            if ($loggedInUser->hasAnyRole(['Operario', 'Funcionario'])) {
                return response()->json(['message' => 'Tu rol no te permite asignar roles/permisos.', 'errors' => ['roles' => 'Permiso denegado por rol.']], 403);
            }
            if ($loggedInUser->hasRole('Administrador')) {
                if ($user->id === $loggedInUser->id) {
                    return response()->json(['message' => 'Un Administrador no puede asignar roles/permisos a su propio perfil.', 'errors' => ['roles' => 'Permiso denegado.']], 403);
                }
                if ($targetUserRoles->contains('SuperAdmin') || $targetUserRoles->contains('Administrador')) {
                    return response()->json(['message' => 'Un Administrador no puede asignar roles/permisos a un SuperAdmin o a otro Administrador.', 'errors' => ['roles' => 'Permiso denegado.']], 403);
                }
            }
            // Validar roles y permisos
            $request->validate([
                'roles'         => 'nullable|array',
                'roles.*'       => 'string|exists:roles,name',
                'permissions'   => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',
            ]);

            try {
                $user->syncRoles($request->roles ?? []);
                $user->syncPermissions($request->permissions ?? []);

                Log::info('Roles y permisos asignados a usuario recién creado.', [
                    'user_id'          => $user->id,
                    'email'            => $user->email,
                    'assigned_roles'   => $request->roles ?? [],
                    'assigned_permissions' => $request->permissions ?? [],
                    'updated_by'       => Auth::id(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Usuario creado y roles/permisos asignados exitosamente.',
                    'redirect' => route('usuarios.index') // Podrías redirigir o simplemente cerrar el modal
                ]);
            } catch (\Exception $e) {
                Log::error('Error al asignar roles/permisos a usuario recién creado - Paso 2.', [
                    'error'            => $e->getMessage(),
                    'user_id'          => $userId,
                    'request_data'     => $request->only(['roles', 'permissions']),
                    'updated_by'       => Auth::id(),
                    'ip_address'       => $request->ip(),
                    'trace'            => $e->getTraceAsString(),
                ]);
                return response()->json(['message' => 'Ocurrió un error al asignar roles y permisos.', 'errors' => ['general' => $e->getMessage()]], 500);
            }
        }

        // Si no se especifica el paso, o no es reconocido
        return response()->json(['message' => 'Paso de formulario no reconocido.', 'errors' => ['form_step' => 'Paso inválido.']], 400);
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     * Este método NO DEBERÍA SER ACCEDIDO DIRECTAMENTE para abrir el modal en el `index`.
     * Solo para servir la página `usuarios.edit` si se carga directamente.
     */
    public function edit(User $usuario)
    {
        // Si la intención es que la edición también se haga en el mismo modal del index
        // y este método solo cargue los datos vía AJAX, entonces NO se retornaría una vista.
        // Pero si mantienes una ruta /usuarios/{id}/edit para una página de edición dedicada,
        // entonces esto es correcto.
        Gate::authorize('editar usuario');

        $loggedInUser = Auth::user();
        $targetUserRoles = $usuario->getRoleNames();

        if ($loggedInUser->hasAnyRole(['Operario', 'Funcionario'])) {
            // Devolver error si se accede vía AJAX o redirigir si es petición GET normal
            return redirect()->route('dashboard')->with('error', 'Tu rol no te permite editar perfiles de usuario.');
        }

        if ($loggedInUser->hasRole('Administrador')) {
            if ($usuario->id === $loggedInUser->id) {
                return redirect()->route('usuarios.index')->with('error', 'Un Administrador no puede editar su propio perfil.');
            }
            if ($targetUserRoles->contains('SuperAdmin') || $targetUserRoles->contains('Administrador')) {
                return redirect()->route('usuarios.index')->with('error', 'Un Administrador no puede editar el perfil de un SuperAdmin o de otro Administrador.');
            }
        }

        $roles = Role::all();
        $permissions = Permission::all();
        $userRoles = $usuario->roles->pluck('name')->toArray();
        $allUserGrantedPermissions = $usuario->getAllPermissions()->pluck('name')->toArray();

        return view('usuarios.edit', compact('usuario', 'roles', 'permissions', 'userRoles', 'allUserGrantedPermissions'));
    }

    /**
     * Actualiza los datos de un usuario y sus roles/permisos.
     * Este método será utilizado para el Paso 2 de la edición (o la creación si se envía todo junto).
     * ¡ESTE MÉTODO AHORA DEVUELVE JSON!
     */
    public function update(Request $request, User $usuario)
    {
        // Autorización y restricciones de rol (idénticas a las de edit, lo cual es correcto)
        Gate::authorize('editar usuario');

        $loggedInUser = Auth::user();
        $targetUserRoles = $usuario->getRoleNames();

        if ($loggedInUser->hasAnyRole(['Operario', 'Funcionario'])) {
            return response()->json(['message' => 'Tu rol no te permite actualizar perfiles de usuario.', 'errors' => ['roles' => 'Permiso denegado por rol.']], 403);
        }

        if ($loggedInUser->hasRole('Administrador')) {
            if ($usuario->id === $loggedInUser->id) {
                return response()->json(['message' => 'Un Administrador no puede actualizar su propio perfil.', 'errors' => ['roles' => 'Permiso denegado.']], 403);
            }
            if ($targetUserRoles->contains('SuperAdmin') || $targetUserRoles->contains('Administrador')) {
                return response()->json(['message' => 'Un Administrador no puede actualizar el perfil de un SuperAdmin o de otro Administrador.', 'errors' => ['roles' => 'Permiso denegado.']], 403);
            }
        }

        // Validación para roles y permisos (ahora para el Paso 2, o si se editan otros campos)
        $request->validate([
            // Incluir validación para campos básicos si este 'update' también los maneja,
            // si no, se asume que solo se actualizan roles y permisos.
            // Para la edición en un solo modal, probablemente necesites validar todos los campos.
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $usuario->id,
            'type_document' => 'required|string|max:10',
            'document'      => 'required|string|max:20|unique:users,document,' . $usuario->id,
            'roles'         => 'nullable|array',
            'roles.*'       => 'string|exists:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'El correo debe ser una dirección de email válida.',
            'email.unique' => 'Este correo ya está registrado.',
            'type_document.required' => 'El tipo de documento es obligatorio.',
            'document.required' => 'El número de documento es obligatorio.',
            'document.unique' => 'Este número de documento ya está registrado.',
        ]);

        try {
            // Actualizar campos básicos si se editan en el mismo formulario
            $usuario->update([
                'name'          => $request->name,
                'email'         => $request->email,
                'type_document' => $request->type_document,
                'document'      => $request->document,
            ]);

            // Actualización de roles y permisos
            $usuario->syncRoles($request->roles ?? []);

            // Sincronizar permisos directos (recibe directamente el array del frontend)
            if ($request->has('permissions') && is_array($request->permissions)) {
                $usuario->syncPermissions($request->permissions);
            } else {
                // Si no se enviaron permisos o no es un array, desvincula todos los permisos directos
                $usuario->syncPermissions([]);
            }

            Log::info('Usuario actualizado (modal en index).', [
                'user_id'          => $usuario->id,
                'email'            => $usuario->email,
                'updated_by'       => Auth::id(),
                'assigned_roles'   => $request->roles ?? [],
                'assigned_permissions' => $request->permissions ?? [],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario y sus roles/permisos actualizados exitosamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario (modal en index).', [
                'error'            => $e->getMessage(),
                'user_id'          => $usuario->id,
                'request_data'     => $request->all(),
                'updated_by'       => Auth::id(),
                'ip_address'       => $request->ip(),
                'trace'            => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Ocurrió un error al actualizar el usuario.', 'errors' => ['general' => $e->getMessage()]], 500);
        }
    }

    // Nuevo método para cargar datos de usuario por AJAX para la edición
    public function getUserData(User $usuario)
    {
        Gate::authorize('editar usuario');

        $loggedInUser = Auth::user();
        $targetUserRoles = $usuario->getRoleNames();

        // Las mismas restricciones de seguridad que en `edit` y `update`
        if ($loggedInUser->hasAnyRole(['Operario', 'Funcionario'])) {
            return response()->json(['message' => 'Tu rol no te permite ver este perfil.', 'errors' => ['auth' => 'Permiso denegado por rol.']], 403);
        }
        if ($loggedInUser->hasRole('Administrador')) {
            if ($usuario->id === $loggedInUser->id) {
                return response()->json(['message' => 'Un Administrador no puede ver su propio perfil.', 'errors' => ['auth' => 'Permiso denegado.']], 403);
            }
            if ($targetUserRoles->contains('SuperAdmin') || $targetUserRoles->contains('Administrador')) {
                return response()->json(['message' => 'Un Administrador no puede ver el perfil de un SuperAdmin o de otro Administrador.', 'errors' => ['auth' => 'Permiso denegado.']], 403);
            }
        }

        // Obtener roles y permisos asociados al usuario
        $userRoles = $usuario->roles->pluck('name')->toArray();
        $allUserGrantedPermissions = $usuario->getAllPermissions()->pluck('name')->toArray();

        // NUEVA LÓGICA: Obtener permisos por defecto para CADA rol usando Spatie
        $allRoles = Role::all();
        $roleDefaultPermissions = [];

        foreach ($allRoles as $role) {
            $roleDefaultPermissions[$role->name] = $role->permissions->pluck('name')->toArray();
        }

        return response()->json([
            'id' => $usuario->id,
            'name' => $usuario->name,
            'email' => $usuario->email,
            'type_document' => $usuario->type_document,
            'document' => $usuario->document,
            'userRoles' => $userRoles,
            'allUserGrantedPermissions' => $allUserGrantedPermissions,
            'roleDefaultPermissions' => $roleDefaultPermissions, 
        ]);
    }

    /**
     * Devuelve el mapeo de permisos por defecto para cada rol.
     *
     * @param  \Spatie\Permission\Models\Role  $roleModel // Inyectamos el modelo Role
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRolePermissionsMap(Role $roleModel)
    {
        $allRoles = $roleModel->all(); // Usamos la instancia inyectada del modelo
        $roleDefaultPermissions = [];

        foreach ($allRoles as $role) {
            $roleDefaultPermissions[$role->name] = $role->permissions->pluck('name')->toArray();
        }

        return response()->json(['roleDefaultPermissions' => $roleDefaultPermissions]);
    }

    /**
     * Alterna el estado de un usuario entre 'activo' e 'inactivo'.
     * Requiere el permiso 'gestionar estado usuario'.
     *
     * Reglas de negocio adicionales:
     * 1. El usuario autenticado no puede cambiar su propio estado.
     * 2. Un Administrador solo puede cambiar el estado de Operario y Funcionario, no de un SuperAdmin.
     * (El SuperAdmin puede cambiar el estado de cualquiera, pero tampoco puede auto-desactivarse).
     */
    public function toggle(User $usuario)
    {
        // 2. Regla: Evita que un usuario cambie su propio estado
        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes cambiar tu propio estado. Si necesitas asistencia, contacta al SuperAdmin.');
        }

        // 3. NUEVA REGLA: Operario o Funcionario no pueden activar/desactivar a NINGÚN otro usuario.
        // Si el usuario autenticado tiene el rol 'Operario' o 'Funcionario',
        // se le impide realizar la acción sobre cualquier otro usuario.
        if (Auth::user()->hasAnyRole(['Operario', 'Funcionario'])) {
            return redirect()->route('usuarios.index')->with('error', 'Tu rol no te permite activar o desactivar el estado de otros usuarios.');
        }

        // 4. Regla: Restricciones para el rol 'Administrador'
        // Solo aplica si el usuario autenticado tiene el rol 'Administrador'.
        if (Auth::user()->hasRole('Administrador')) {
            // Obtener los nombres de los roles del usuario *objetivo*
            $targetUserRoles = $usuario->getRoleNames();

            // Si el usuario objetivo tiene el rol 'SuperAdmin' o 'Administrador', el Administrador no puede cambiarlo
            if ($targetUserRoles->contains('SuperAdmin') || $targetUserRoles->contains('Administrador')) {
                return redirect()->route('usuarios.index')->with('error', 'Un Administrador no puede cambiar el estado de un SuperAdmin o de otro Administrador.');
            }
        }

        // Si todas las verificaciones de seguridad y rol pasan, procede a cambiar el estado
        $usuario->estado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'El estado del usuario ' . $usuario->name . ' ha sido actualizado a ' . ucfirst($usuario->estado) . '.');
    }

    /**
     * Exporta la lista de usuarios a un archivo CSV.
     * Requiere el permiso 'exportar usuarios csv'.
     */
    public function exportarCSV(Request $request)
    {
        $query = $request->input('q');
        $rol = $request->input('rol');
        $estado = $request->input('estado');

        $usuarios = User::with('roles');

        if ($query) {
            $usuarios->where(function ($q2) use ($query) {
                $q2->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($query) . '%']);
            });
        }

        if ($rol) {
            $usuarios->whereHas('roles', function ($q3) use ($rol) {
                $q3->where('name', $rol);
            });
        }

        if ($estado) {
            $usuarios->where('estado', $estado);
        }

        $usuarios = $usuarios->get();

        $nombreArchivo = 'usuarios_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$nombreArchivo\"",
        ];

        $columnas = ['ID', 'Nombre', 'Correo', 'Rol', 'Estado', 'Creado'];

        $callback = function () use ($usuarios, $columnas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columnas);

            foreach ($usuarios as $usuario) {
                fputcsv($file, [
                    $usuario->id,
                    $usuario->name,
                    $usuario->email,
                    $usuario->roles->pluck('name')->implode(', '),
                    $usuario->estado,
                    $usuario->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Importa usuarios desde un archivo CSV.
     * Requiere el permiso 'importar usuarios csv'.
     */
    public function importarCsv(Request $request)
    {
        // Restricción de rol (si es necesaria aquí también para la importación)
        // if (Auth::user()->hasAnyRole(['Operario', 'Funcionario'])) {
        //     return redirect()->route('dashboard')->with('error', 'Tu rol no te permite importar usuarios.');
        // }

        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $archivo = $request->file('archivo');
        $contenido = array_map('str_getcsv', file($archivo->getRealPath()));
        $encabezados = array_map('strtolower', array_shift($contenido));

        $importedCount = 0;
        $failedCount = 0;
        $skippedCount = 0;

        foreach ($contenido as $fila) {
            // Asegurarse de que la fila tenga el mismo número de elementos que encabezados
            if (count($fila) !== count($encabezados)) {
                Log::warning('Fila saltada debido a un número inconsistente de columnas.', [
                    'fila_original' => $fila,
                    'encabezados' => $encabezados
                ]);
                $skippedCount++;
                continue;
            }

            $datos = array_combine($encabezados, $fila);

            // Validar campos esenciales para la importación
            if (!isset($datos['name'], $datos['email'], $datos['type_document'], $datos['document'], $datos['rol'])) {
                Log::warning('Fila saltada, faltan campos esenciales (name, email, type_document, document, rol).', [
                    'datos' => $datos
                ]);
                $skippedCount++;
                continue;
            }

            // Sanitizar y recortar espacios en blanco
            $name           = trim($datos['name']);
            $email          = trim($datos['email']);
            $typeDocument   = trim($datos['type_document']);
            $document       = trim($datos['document']);
            $rolName        = trim($datos['rol']);

            // Verificar si el usuario ya existe por email o documento
            $existingUser = User::where('email', $email)->orWhere('document', $document)->first();
            if ($existingUser) {
                Log::warning('Usuario con email o documento ya existente, saltando importación.', [
                    'email' => $email,
                    'document' => $document,
                    'existing_user_id' => $existingUser->id,
                ]);
                $skippedCount++;
                continue;
            }

            // Validar que el rol exista
            $role = Role::where('name', $rolName)->first();
            if (!$role) {
                Log::warning("Rol '{$rolName}' no encontrado al importar usuario '{$email}'. Saltando.", [
                    'email' => $email,
                    'document' => $document,
                    'rol_csv' => $rolName
                ]);
                $skippedCount++;
                continue;
            }

            try {
                // *** LÓGICA CLAVE: Contraseña inicial = número de documento hasheado ***
                $initialPassword = $document; // Tu número de documento
                $hashedPassword = Hash::make($initialPassword);

                $usuario = User::create([
                    'name'          => $name,
                    'email'         => $email,
                    'type_document' => $typeDocument,
                    'document'      => $document,
                    'password'      => $hashedPassword, // <-- Aquí se guarda el hash del documento
                    'email_verified_at' => null, // Opcional: Para que se verifique al cambiar la contraseña
                ]);

                // Asignar el rol
                $usuario->syncRoles([$role->name]);

                // **LOGGING:** Registrar la creación del usuario por importación
                Log::info('Usuario importado y creado exitosamente.', [
                    'user_id'       => $usuario->id,
                    'email'         => $usuario->email,
                    'document'      => $usuario->document,
                    'assigned_role' => $role->name,
                    'imported_by'   => Auth::id() ?? 'System', // Quién realizó la importación (si está autenticado)
                ]);

                // Generar el token de restablecimiento de contraseña
                $token = app('auth.password.broker')->createToken($usuario);
                $resetUrl = route('password.reset', ['token' => $token, 'email' => $usuario->email]);

                // **LOGGING:** Registrar el envío del email de activación
                Log::info('Email de activación enviado a usuario importado.', [
                    'user_id'   => $usuario->id,
                    'email'     => $usuario->email,
                    // 'reset_url' => $resetUrl // Evitar loggear URLs sensibles en prod, solo para depuración
                ]);

                // Enviar el correo electrónico al usuario para que establezca su contraseña
                Mail::to($usuario->email)->send(new UserCreatedNotification($usuario, $resetUrl));

                $importedCount++;
            } catch (\Exception $e) {
                Log::error('Error al importar o procesar usuario desde CSV.', [
                    'error'     => $e->getMessage(),
                    'user_data' => $datos,
                    'trace'     => $e->getTraceAsString(), // Solo en desarrollo/debug
                    'imported_by' => Auth::id() ?? 'System',
                ]);
                $failedCount++;
            }
        }

        return back()->with('success', "Proceso de importación completado. Usuarios creados: {$importedCount}, Omitidos (existentes/datos faltantes/rol no encontrado): {$skippedCount}, Fallidos (errores internos): {$failedCount}.");
    }

    /**
     * Verifica si un correo electrónico ya existe en la base de datos.
     */
    public function checkEmailExists(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }

    /**
     * Verifica si un número de documento ya existe en la base de datos.
     * Útil para validación en tiempo real en formularios.
     */
    public function checkDocumentExists(Request $request)
    {
        $request->validate([
            'document' => 'required|string',
        ]);

        $exists = User::where('document', $request->document)->exists();

        return response()->json(['exists' => $exists]);
    }

    /*
     * Muestra el formulario para solicitar el reenvío del email de activación.
     * Esta es una ruta pública para usuarios que no han activado su cuenta.
     */
    public function showResendActivationForm()
    {
        // Esta vista será simple, con un campo para el email y un botón.
        return view('auth.resend-activation');
    }

    /**
     * Procesa la solicitud para reenviar el email de activación.
     * Esta es una ruta pública.
     */
    public function resendActivationEmail(Request $request)
    {
        // 1. Validar que el email sea requerido y exista en la tabla de usuarios
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        // 2. Buscar al usuario por el email proporcionado
        $user = User::where('email', $request->email)->first();

        // Si por alguna razón el usuario no se encuentra (aunque la validación 'exists' debería prevenirlo),
        // lanzamos una excepción de validación para evitar revelar si el usuario existe o no.
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')], // Mensaje genérico para seguridad
            ]);
        }

        // 3. Generar un nuevo token de restablecimiento de contraseña para el usuario
        // Utilizamos el "password broker" de Laravel, que es el mismo mecanismo que usa "olvidé mi contraseña".
        $token = app('auth.password.broker')->createToken($user);

        // 4. Construir la URL de restablecimiento/activación
        // Esta URL es la que el usuario usará para establecer su nueva contraseña.
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // 5. Enviar el correo electrónico al usuario con el enlace de activación
        try {
            Mail::to($user->email)->send(new UserCreatedNotification($user, $resetUrl));

            // **LOGGING:** Registrar el reenvío exitoso del email
            Log::info('Email de activación reenviado exitosamente.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
            ]);

            // 6. Redirigir de vuelta con un mensaje de éxito
            return back()->with('status', 'Hemos enviado un nuevo enlace de activación a tu correo electrónico. Por favor, revisa tu bandeja de entrada (y la carpeta de spam).');
        } catch (\Exception $e) {
            // **LOGGING:** Registrar cualquier error al intentar enviar el email
            Log::error('Error al reenviar email de activación.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Solo para depuración
            ]);
            // Redirigir con un mensaje de error
            return back()->withInput()->with('error', 'Ocurrió un error al intentar reenviar el enlace. Por favor, inténtalo de nuevo más tarde.');
        }
    }
}
