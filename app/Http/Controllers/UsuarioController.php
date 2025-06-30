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
use Illuminate\Support\Facades\Validator;
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
     * Este método ahora recibe y valida TODOS los datos de los 3 pasos
     * y realiza la creación completa del usuario.
     * ¡ESTE MÉTODO AHORA DEVUELVE JSON!
     */
    public function store(Request $request)
    {
        // --- RESTRICCIÓN DE ROL: Operario/Funcionario no pueden crear usuarios ---
        if (Auth::user()->hasAnyRole(['Operario', 'Funcionario'])) {
            return response()->json(['message' => 'Tu rol no te permite crear usuarios.', 'errors' => ['general' => 'Permiso denegado por rol.']], 403);
        }

        // Validación para TODOS los campos de los 3 pasos para la creación
        $rules = [
            'name'          => 'required|string|max:255',
            'lastname'      => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone'      => 'required|string|max:20',
            'type_document' => 'required|string|max:10',
            'document'      => 'required|string|max:20|unique:users,document',
            'roles'         => 'required|array', // Rol es obligatorio para la creación
            'roles.*'       => 'string|exists:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
            'password'      => 'required|string|min:8|confirmed', // Contraseña es obligatoria para la creación
            'password_confirmation' => 'required|string|min:8',
        ];

        $messages = [
            'name.required'             => 'El nombre es obligatorio.',
            'lastname.required'         => 'El lastname es obligatorio.',
            'email.required'            => 'El correo es obligatorio.',
            'email.email'               => 'El correo debe ser una dirección de email válida.',
            'email.unique'              => 'Este correo ya está registrado.',
            'phone.required'         => 'El teléfono es obligatorio.',
            'type_document.required'    => 'El tipo de documento es obligatorio.',
            'document.required'         => 'El número de documento es obligatorio.',
            'document.unique'           => 'Este número de documento ya está registrado.',
            'roles.required'            => 'Debe seleccionar al menos un rol.',
            'roles.*.exists'            => 'El rol seleccionado no es válido.',
            'password.required'         => 'La contraseña es obligatoria.',
            'password.min'              => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
            'password_confirmation.required' => 'La confirmación de contraseña es obligatoria.',
            'password_confirmation.min' => 'La confirmación de contraseña debe tener al menos :min caracteres.',
        ];

        try {
            $request->validate($rules, $messages);

            $user = User::create([
                'name'              => $request->name,
                'lastname'          => $request->lastname,
                'email'             => $request->email,
                'phone'             => $request->phone,
                'type_document'     => $request->type_document,
                'document'          => $request->document,
                'password'          => Hash::make($request->password), // Hashear la contraseña final del formulario
                'estado'            => 'activo', // El usuario se crea activo ya con todos los datos
                'email_verified_at' => null,
            ]);

            // Asignar roles y permisos directamente
            $user->syncRoles($request->roles);
            if ($request->has('permissions') && is_array($request->permissions)) {
                $user->syncPermissions($request->permissions);
            } else {
                $user->syncPermissions([]);
            }

            // Enviar notificación por correo con la contraseña en texto plano
            Mail::to($user->email)->send(new UserCreatedNotification($user, $request->password));

            Log::info('Usuario creado completamente (admin) con todos los pasos.', [
                'user_id'          => $user->id,
                'email'            => $user->email,
                'created_by'       => Auth::id(),
                'assigned_roles'   => $request->roles,
                'assigned_permissions' => $request->permissions ?? [],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente y notificación enviada.',
                'user_id' => $user->id, // Puedes devolver el ID si lo necesitas en el frontend para algo más
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Errores de validación.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear usuario completo (admin).', [
                'error'            => $e->getMessage(),
                'user_data'        => $request->except(['_token', 'password', 'password_confirmation']),
                'created_by'       => Auth::id(),
                'ip_address'       => $request->ip(),
                'trace'            => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Ocurrió un error al crear el usuario. Por favor, inténtalo de nuevo.', 'errors' => ['general' => $e->getMessage()]], 500);
        }
    }

    /**
     * Actualiza los datos de un usuario y sus roles/permisos.
     * Este método se usa EXCLUSIVAMENTE para la edición de un usuario existente.
     * ¡ESTE MÉTODO AHORA DEVUELVE JSON!
     */
    public function update(Request $request, User $usuario)
    {
        // Autorización y restricciones de rol (se mantienen igual)
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

        // Validación para la edición: todos los campos son requeridos excepto la contraseña (nullable)
        $rules = [
            'name'          => 'required|string|max:255',
            'lastname'      => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $usuario->id,
            'phone'      => 'required|string|max:20',
            'type_document' => 'required|string|max:10',
            'document'      => 'required|string|max:20|unique:users,document,' . $usuario->id,
            'roles'         => 'nullable|array',
            'roles.*'       => 'string|exists:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
            'password'          => 'nullable|string|min:8|confirmed', // Contraseña es opcional en edición
            'password_confirmation' => 'nullable|string|min:8', // Solo si se provee password
        ];

        $messages = [
            'name.required'             => 'El nombre es obligatorio.',
            'lastname.required'         => 'El lastname es obligatorio.',
            'email.required'            => 'El correo es obligatorio.',
            'email.email'               => 'El correo debe ser una dirección de email válida.',
            'email.unique'              => 'Este correo ya está registrado.',
            'phone.required'         => 'El teléfono es obligatorio.',
            'type_document.required'    => 'El tipo de documento es obligatorio.',
            'document.required'         => 'El número de documento es obligatorio.',
            'document.unique'           => 'Este número de documento ya está registrado.',
            'password.min'              => 'La contraseña debe tener al menos :min caracteres.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
        ];

        try {
            $request->validate($rules, $messages);

            $updateData = [
                'name'          => $request->name,
                'lastname'      => $request->lastname,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'type_document' => $request->type_document,
                'document'      => $request->document,
            ];

            // Si se proporcionó una nueva contraseña, hashearla y actualizarla
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
                $updateData['estado'] = 'activo'; // Si se cambia la contraseña, asumimos que el usuario está activo
                // Enviar notificación por correo con la NUEVA contraseña (solo si se cambió)
                Mail::to($usuario->email)->send(new UserCreatedNotification($usuario, $request->password));
            }

            $usuario->update($updateData);

            // Actualización de roles y permisos
            $usuario->syncRoles($request->roles ?? []);

            if ($request->has('permissions') && is_array($request->permissions)) {
                $usuario->syncPermissions($request->permissions);
            } else {
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
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Errores de validación.',
                'errors' => $e->errors()
            ], 422);
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

    /**
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(User $usuario)
    {
        Gate::authorize('editar usuario');

        $loggedInUser = Auth::user();
        $targetUserRoles = $usuario->getRoleNames();

        if ($loggedInUser->hasAnyRole(['Operario', 'Funcionario'])) {
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
     * Nuevo método para cargar datos de usuario por AJAX para la edición (incluye nuevos campos)
     */
    public function getUserData(User $usuario)
    {
        Gate::authorize('editar usuario');

        $loggedInUser = Auth::user();
        $targetUserRoles = $usuario->getRoleNames();

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

        $userRoles = $usuario->roles->pluck('name')->toArray();
        $allUserGrantedPermissions = $usuario->getAllPermissions()->pluck('name')->toArray();

        $allRoles = Role::all();
        $roleDefaultPermissions = [];

        foreach ($allRoles as $role) {
            $roleDefaultPermissions[$role->name] = $role->permissions->pluck('name')->toArray();
        }

        return response()->json([
            'id'                        => $usuario->id,
            'name'                      => $usuario->name,
            'lastname'                  => $usuario->lastname,
            'email'                     => $usuario->email,
            'phone'                     => $usuario->phone,
            'type_document'             => $usuario->type_document,
            'document'                  => $usuario->document,
            'userRoles'                 => $userRoles,
            'allUserGrantedPermissions' => $allUserGrantedPermissions,
            'roleDefaultPermissions'    => $roleDefaultPermissions,
        ]);
    }

    /**
     * Devuelve el mapeo de permisos por defecto para cada rol.
     */
    public function getRolePermissionsMap(Role $roleModel)
    {
        $allRoles = $roleModel->all();
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
     * Importa usuarios desde datos JSON pre-parseados del CSV.
     * Requiere el permiso 'importar usuarios csv'.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importarCsv(Request $request)
    {
        // Validar que se reciba la clave 'users_data' y que sea un string (representando un JSON)
        $request->validate([
            'users_data' => 'required|string',
        ]);

        try {
            // Decodificar el JSON de los datos de usuarios
            $usersToImport = json_decode($request->input('users_data'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Error al decodificar JSON de usuarios a importar.', ['json_error' => json_last_error_msg()]);
                return response()->json(['message' => 'Datos de usuarios inválidos (JSON mal formado).', 'errors' => ['general' => 'Datos de usuarios inválidos.']], 400);
            }

            if (!is_array($usersToImport)) {
                return response()->json(['message' => 'Formato de datos de usuarios incorrecto. Se esperaba un array.', 'errors' => ['general' => 'Formato de datos de usuarios incorrecto.']], 400);
            }

            $importedCount = 0;
            $failedCount = 0;
            $detailedErrors = []; // Para capturar errores específicos por fila (validación y otros)

            foreach ($usersToImport as $index => $userData) {
                $lineNumber = $index + 2; // +1 por índice 0, +1 por encabezados del CSV

                // Definir las reglas de validación para cada usuario
                $rules = [
                    'name'          => 'required|string|max:255',
                    'lastname'      => 'nullable|string|max:255', 
                    'email'         => 'required|string|email|max:255|unique:users,email',
                    'phone'         => 'nullable|string|max:20', 
                    'type_document' => 'required|string|max:10',
                    'document'      => 'required|string|max:50|unique:users,document',
                    'role'          => 'required|string|exists:roles,name', 
                ];

                // Crear un validador manual para cada fila de usuario
                $validator = Validator::make($userData, $rules, [
                    'required' => 'La fila ' . $lineNumber . ': El campo :attribute es obligatorio.',
                    'email.email' => 'La fila ' . $lineNumber . ': El correo electrónico no es válido.',
                    'email.unique' => 'La fila ' . $lineNumber . ': El correo electrónico ya existe en el sistema.',
                    'document.unique' => 'La fila ' . $lineNumber . ': El número de documento ya existe en el sistema.',
                    'role.exists' => 'La fila ' . $lineNumber . ': El rol asignado no existe.',
                    'max' => 'La fila ' . $lineNumber . ': El campo :attribute excede la longitud máxima permitida.',
                ]);

                if ($validator->fails()) {
                    $failedCount++;
                    // Almacenar los mensajes de error del validador con la línea para el frontend
                    foreach ($validator->errors()->all() as $errorMsg) {
                        $detailedErrors['Línea ' . $lineNumber][] = $errorMsg;
                    }
                    Log::warning('Fila CSV saltada debido a errores de validación.', [
                        'line_number' => $lineNumber,
                        'user_data' => $userData,
                        'errors' => $validator->errors()->all()
                    ]);
                    continue; // Saltar a la siguiente fila
                }

                // Si la validación de la fila pasa, procedemos a la creación
                $name           = trim($userData['name']);
                $lastname       = isset($userData['lastname']) ? trim($userData['lastname']) : null;
                $email          = trim($userData['email']);
                $phone          = isset($userData['phone']) ? trim($userData['phone']) : null;
                $typeDocument   = trim($userData['type_document']);
                $document       = trim($userData['document']);
                $rolName        = trim($userData['role']); 

                try {
                    $initialPassword = $document; 
                    $hashedPassword = Hash::make($initialPassword);

                    $usuario = User::create([
                        'name'          => $name,
                        'lastname'      => $lastname, 
                        'email'         => $email,
                        'phone'         => $phone,    
                        'type_document' => $typeDocument,
                        'document'      => $document,
                        'password'      => $hashedPassword,
                        'email_verified_at' => null, 
                    ]);

                    $usuario->syncRoles([$rolName]); 

                    Log::info('Usuario importado y creado exitosamente.', [
                        'user_id'       => $usuario->id,
                        'email'         => $usuario->email,
                        'document'      => $usuario->document,
                        'assigned_role' => $rolName,
                        'imported_by'   => Auth::id() ?? 'System',
                    ]);

                    $token = app('auth.password.broker')->createToken($usuario);
                    $resetUrl = route('password.reset', ['token' => $token, 'email' => $usuario->email]);

                    Log::info('Email de activación enviado a usuario importado.', [
                        'user_id'   => $usuario->id,
                        'email'     => $usuario->email,
                    ]);

                    Mail::to($usuario->email)->send(new UserCreatedNotification($usuario, $resetUrl));

                    $importedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    // Capturar errores internos de la creación/mail aquí
                    $detailedErrors['Línea ' . $lineNumber][] = 'Error interno del servidor: ' . $e->getMessage();
                    Log::error('Error interno al crear o procesar usuario desde CSV.', [
                        'error'     => $e->getMessage(),
                        'user_data' => $userData,
                        'trace'     => $e->getTraceAsString(), 
                        'imported_by' => Auth::id() ?? 'System',
                    ]);
                }
            }

            $message = "Proceso de importación completado. Usuarios creados: {$importedCount}.";
            if ($failedCount > 0) {
                $message .= " Usuarios con errores: {$failedCount}.";
            }

            // Si hay errores detallados, devolverlos en la respuesta
            if (!empty($detailedErrors)) {
                return response()->json([
                    'message' => $message,
                    'imported_count' => $importedCount,
                    'failed_count' => $failedCount,
                    'detailed_errors' => $detailedErrors // Array de errores detallados
                ], 422); // 422 Unprocessable Entity
            } else {
                return response()->json([
                    'message' => $message,
                    'imported_count' => $importedCount
                ], 200);
            }
            
        } catch (\Exception $e) {
            Log::error('Error fatal al procesar la solicitud de importación CSV.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['message' => 'Ocurrió un error inesperado en el servidor durante la importación.', 'errors' => ['general' => 'Error de servidor.']], 500);
        }
    }

    /**
     * Valida si los correos electrónicos o documentos de los usuarios a importar ya existen en el sistema.
     * Esta función está diseñada para ser llamada antes de la importación final para verificar duplicados.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkCsvDuplicates(Request $request)
    {
        // Validar que se reciba la clave 'users_data' y que sea un string (representando un JSON)
        $request->validate([
            'users_data' => 'required|string',
        ]);

        try {
            // Decodificar el JSON de los datos de usuarios
            $usersToCheck = json_decode($request->input('users_data'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Error al decodificar JSON para verificación de duplicados.', ['json_error' => json_last_error_msg()]);
                return response()->json(['message' => 'Datos de usuarios inválidos (JSON mal formado).', 'errors' => ['general' => 'Datos de usuarios inválidos.']], 400);
            }

            if (!is_array($usersToCheck)) {
                return response()->json(['message' => 'Formato de datos de usuarios incorrecto. Se esperaba un array.', 'errors' => ['general' => 'Formato de datos de usuarios incorrecto.']], 400);
            }

            $detailedErrors = []; // Para capturar errores específicos de duplicados por fila

            // Iterar sobre cada usuario para aplicar solo las reglas de unicidad
            foreach ($usersToCheck as $index => $userData) {
                $lineNumber = $index + 2; // +1 por índice 0, +1 por encabezados del CSV

                // Solo reglas de unicidad para email y document
                $rules = [
                    'email'    => 'unique:users,email',
                    'document' => 'unique:users,document',
                ];

                // Crear un validador manual
                $validator = Validator::make($userData, $rules, [
                    'email.unique'    => 'La fila ' . $lineNumber . ': El correo electrónico ya existe en el sistema.',
                    'document.unique' => 'La fila ' . $lineNumber . ': El número de documento ya existe en el sistema.',
                ]);

                if ($validator->fails()) {
                    // Si hay fallos de validación (es decir, duplicados), añadirlos a los errores detallados
                    foreach ($validator->errors()->all() as $errorMsg) {
                        $detailedErrors['Línea ' . $lineNumber][] = $errorMsg;
                    }
                    Log::info('Duplicado detectado en verificación temprana.', [
                        'line_number' => $lineNumber,
                        'user_data' => $userData,
                        'errors' => $validator->errors()->all()
                    ]);
                }
            }

            // Si se encontraron errores detallados (duplicados), devolver un 422
            if (!empty($detailedErrors)) {
                return response()->json([
                    'message' => 'Se encontraron usuarios duplicados.',
                    'detailed_errors' => $detailedErrors
                ], 422); // 422 Unprocessable Entity
            } else {
                // Si no hay duplicados, devolver un 200 OK
                return response()->json(['message' => 'No se encontraron duplicados.'], 200);
            }

        } catch (\Exception $e) {
            Log::error('Error fatal al procesar la solicitud de verificación de duplicados CSV.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['message' => 'Ocurrió un error inesperado en el servidor durante la verificación de duplicados.', 'errors' => ['general' => 'Error de servidor.']], 500);
        }
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
