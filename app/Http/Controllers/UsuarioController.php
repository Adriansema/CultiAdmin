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
use Illuminate\Validation\Rule;
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
     * Considera si esta ruta tambien necesita un permiso.
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
        // --- RESTRICCIoN DE ROL: Operario/Funcionario no pueden crear usuarios ---
        if (Auth::user()->hasAnyRole(['Operario', 'Funcionario'])) {
            return redirect()->route('dashboard')->with('error', 'Tu rol no te permite crear usuarios.');
        }

        return redirect()->route('usuarios.index')->with('success', 'Preparado para crear un nuevo usuario.');
    }

    /**
     * Almacena un nuevo usuario.
     * Este metodo ahora recibe y valida TODOS los datos de los 3 pasos
     * y realiza la creacion completa del usuario.
     * !ESTE MeTODO AHORA DEVUELVE JSON!
     */
    public function store(Request $request)
    {
        // --- RESTRICCIoN DE ROL: Operario/Funcionario no pueden crear usuarios ---
        if (Auth::user()->hasAnyRole(['Operario', 'Funcionario'])) {
            return response()->json(['message' => 'Tu rol no te permite crear usuarios.', 'errors' => ['general' => 'Permiso denegado por rol.']], 403);
        }

        // Validacion para TODOS los campos de los 3 pasos para la creacion
        $rules = [
            'name'          => 'required|string|max:255',
            'lastname'      => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required|string|digits:10', // Corregido para validar exactamente 10 digitos
            'type_document' => ['required', 'string', Rule::in(['CC', 'TI', 'CE', 'PPT', 'PEP'])], // Regla mas segura
            'document'      => 'required|string|max:10|unique:users,document',
            'roles'         => 'required|array', // Rol es obligatorio para la creacion
            'roles.*'       => 'string|exists:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
            'password'      => 'required|string|min:8|confirmed', // Contrasena es obligatoria para la creacion
            'password_confirmation' => 'required|string|min:8',
        ];

        // --- LoGICA DINaMICA PARA EL DOCUMENTO ---

        // 1. Define las reglas base para el documento
        $documentValidation = ['required', 'string', 'unique:users,document'];

        // 2. Anade la regla de longitud correcta segun el tipo
        switch ($request->input('type_document')) {
            case 'PPT':
                $documentValidation[] = 'digits:7';
                break;
            case 'PEP':
                $documentValidation[] = 'digits:15';
                break;
            case 'CC':
                $documentValidation[] = 'digits_between:8,10';
                break;
            case 'TI':
            case 'CE':
                $documentValidation[] = 'digits:10';
                break;
        }

        // 3. Anade la regla construida dinamicamente al array principal
        $rules['document'] = $documentValidation;

        $messages = [
            'name.required'             => 'El nombre es obligatorio.',
            'lastname.required'         => 'El lastname es obligatorio.',
            'email.required'            => 'El correo es obligatorio.',
            'email.email'               => 'El correo debe ser una direccion de email valida.',
            'email.unique'              => 'Este correo ya esta registrado.',
            'phone.required'            => 'El telefono es obligatorio.',
            'type_document.required'    => 'El tipo de documento es obligatorio.',
            'document.required'         => 'El numero de documento es obligatorio.',
            'document.unique'           => 'Este numero de documento ya esta registrado.',
            'roles.required'            => 'Debe seleccionar al menos un rol.',
            'roles.*.exists'            => 'El rol seleccionado no es valido.',
            'password.required'         => 'La contrasena es obligatoria.',
            'password.min'              => 'La contrasena debe tener al menos :min caracteres.',
            'password.confirmed'        => 'Las contrasenas no coinciden.',
            'password_confirmation.required' => 'La confirmacion de contrasena es obligatoria.',
            'password_confirmation.min' => 'La confirmacion de contrasena debe tener al menos :min caracteres.',
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
                'password'          => Hash::make($request->password), // Hashear la contrasena final del formulario
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

            dd([
                'user' => $user,
                'password' => $request->password
            ]);
            // Enviar notificacion por correo con la contrasena en texto plano
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
                'message' => 'Usuario creado exitosamente y notificacion enviada.',
                'user_id' => $user->id, // Puedes devolver el ID si lo necesitas en el frontend para algo mas
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Errores de validacion.',
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
            return response()->json(['message' => 'Ocurrio un error al crear el usuario. Por favor, intentalo de nuevo.', 'errors' => ['general' => $e->getMessage()]], 500);
        }
    }

    /**
     * Actualiza los datos de un usuario y sus roles/permisos.
     * Este metodo se usa EXCLUSIVAMENTE para la edicion de un usuario existente.
     * !ESTE MeTODO AHORA DEVUELVE JSON!
     */
    public function update(Request $request, User $usuario)
    {
        // Autorizacion y restricciones de rol (se mantienen igual)
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

        // Validacion para la edicion: todos los campos son requeridos excepto la contrasena (nullable)
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
            'password'          => 'nullable|string|min:8|confirmed', // Contrasena es opcional en edicion
            'password_confirmation' => 'nullable|string|min:8', // Solo si se provee password
        ];

        $messages = [
            'name.required'             => 'El nombre es obligatorio.',
            'lastname.required'         => 'El lastname es obligatorio.',
            'email.required'            => 'El correo es obligatorio.',
            'email.email'               => 'El correo debe ser una direccion de email valida.',
            'email.unique'              => 'Este correo ya esta registrado.',
            'phone.required'         => 'El telefono es obligatorio.',
            'type_document.required'    => 'El tipo de documento es obligatorio.',
            'document.required'         => 'El numero de documento es obligatorio.',
            'document.unique'           => 'Este numero de documento ya esta registrado.',
            'password.min'              => 'La contrasena debe tener al menos :min caracteres.',
            'password.confirmed'        => 'Las contrasenas no coinciden.',
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

            // Si se proporciono una nueva contrasena, hashearla y actualizarla
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
                $updateData['estado'] = 'activo'; // Si se cambia la contrasena, asumimos que el usuario esta activo
                // Enviar notificacion por correo con la NUEVA contrasena (solo si se cambio)
                Mail::to($usuario->email)->send(new UserCreatedNotification($usuario, $request->password));
            }

            $usuario->update($updateData);

            // Actualizacion de roles y permisos
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
                'message' => 'Errores de validacion.',
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
            return response()->json(['message' => 'Ocurrio un error al actualizar el usuario.', 'errors' => ['general' => $e->getMessage()]], 500);
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
     * Nuevo metodo para cargar datos de usuario por AJAX para la edicion (incluye nuevos campos)
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

        // 3. NUEVA REGLA: Operario o Funcionario no pueden activar/desactivar a NINGuN otro usuario.
        // Si el usuario autenticado tiene el rol 'Operario' o 'Funcionario',
        // se le impide realizar la accion sobre cualquier otro usuario.
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
                return response()->json(['message' => 'Datos de usuarios invalidos (JSON mal formado).', 'errors' => ['general' => 'Datos de usuarios invalidos.']], 400);
            }

            if (!is_array($usersToImport)) {
                return response()->json(['message' => 'Formato de datos de usuarios incorrecto. Se esperaba un array.', 'errors' => ['general' => 'Formato de datos de usuarios incorrecto.']], 400);
            }

            $importedCount = 0;
            $failedCount = 0;
            $detailedErrors = []; // Para capturar errores especificos por fila (validacion y otros)

            foreach ($usersToImport as $index => $userData) {
                $lineNumber = $index + 2; // +1 por indice 0, +1 por encabezados del CSV

                // Definir las reglas de validacion para cada usuario
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
                    'email.email' => 'La fila ' . $lineNumber . ': El correo electronico no es valido.',
                    'email.unique' => 'La fila ' . $lineNumber . ': El correo electronico ya existe en el sistema.',
                    'document.unique' => 'La fila ' . $lineNumber . ': El numero de documento ya existe en el sistema.',
                    'role.exists' => 'La fila ' . $lineNumber . ': El rol asignado no existe.',
                    'max' => 'La fila ' . $lineNumber . ': El campo :attribute excede la longitud maxima permitida.',
                ]);

                if ($validator->fails()) {
                    $failedCount++;
                    // Almacenar los mensajes de error del validador con la linea para el frontend
                    foreach ($validator->errors()->all() as $errorMsg) {
                        $detailedErrors['Linea ' . $lineNumber][] = $errorMsg;
                    }
                    Log::warning('Fila CSV saltada debido a errores de validacion.', [
                        'line_number' => $lineNumber,
                        'user_data' => $userData,
                        'errors' => $validator->errors()->all()
                    ]);
                    continue; // Saltar a la siguiente fila
                }

                // Si la validacion de la fila pasa, procedemos a la creacion
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

                    Log::info('Email de activacion enviado a usuario importado.', [
                        'user_id'   => $usuario->id,
                        'email'     => $usuario->email,
                    ]);

                    Mail::to($usuario->email)->send(new UserCreatedNotification($usuario, $resetUrl));

                    $importedCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    // Capturar errores internos de la creacion/mail aqui
                    $detailedErrors['Linea ' . $lineNumber][] = 'Error interno del servidor: ' . $e->getMessage();
                    Log::error('Error interno al crear o procesar usuario desde CSV.', [
                        'error'     => $e->getMessage(),
                        'user_data' => $userData,
                        'trace'     => $e->getTraceAsString(),
                        'imported_by' => Auth::id() ?? 'System',
                    ]);
                }
            }

            $message = "Proceso de importacion completado. Usuarios creados: {$importedCount}.";
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
            Log::error('Error fatal al procesar la solicitud de importacion CSV.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['message' => 'Ocurrio un error inesperado en el servidor durante la importacion.', 'errors' => ['general' => 'Error de servidor.']], 500);
        }
    }

    /**
     * Valida si los correos electronicos o documentos de los usuarios a importar ya existen en el sistema.
     * Esta funcion esta disenada para ser llamada antes de la importacion final para verificar duplicados.
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
                Log::error('Error al decodificar JSON para verificacion de duplicados.', ['json_error' => json_last_error_msg()]);
                return response()->json(['message' => 'Datos de usuarios invalidos (JSON mal formado).', 'errors' => ['general' => 'Datos de usuarios invalidos.']], 400);
            }

            if (!is_array($usersToCheck)) {
                return response()->json(['message' => 'Formato de datos de usuarios incorrecto. Se esperaba un array.', 'errors' => ['general' => 'Formato de datos de usuarios incorrecto.']], 400);
            }

            $detailedErrors = []; // Para capturar errores especificos de duplicados por fila

            // Iterar sobre cada usuario para aplicar solo las reglas de unicidad
            foreach ($usersToCheck as $index => $userData) {
                $lineNumber = $index + 2; // +1 por indice 0, +1 por encabezados del CSV

                // Solo reglas de unicidad para email y document
                $rules = [
                    'email'    => 'unique:users,email',
                    'document' => 'unique:users,document',
                ];

                // Crear un validador manual
                $validator = Validator::make($userData, $rules, [
                    'email.unique'    => 'La fila ' . $lineNumber . ': El correo electronico ya existe en el sistema.',
                    'document.unique' => 'La fila ' . $lineNumber . ': El numero de documento ya existe en el sistema.',
                ]);

                if ($validator->fails()) {
                    // Si hay fallos de validacion (es decir, duplicados), anadirlos a los errores detallados
                    foreach ($validator->errors()->all() as $errorMsg) {
                        $detailedErrors['Linea ' . $lineNumber][] = $errorMsg;
                    }
                    Log::info('Duplicado detectado en verificacion temprana.', [
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
            Log::error('Error fatal al procesar la solicitud de verificacion de duplicados CSV.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json(['message' => 'Ocurrio un error inesperado en el servidor durante la verificacion de duplicados.', 'errors' => ['general' => 'Error de servidor.']], 500);
        }
    }

    /**
     * Verifica si un correo electronico ya existe en la base de datos.
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
     * Verifica si un numero de documento ya existe en la base de datos.
     * util para validacion en tiempo real en formularios.
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
     * Muestra el formulario para solicitar el reenvio del email de activacion.
     * Esta es una ruta publica para usuarios que no han activado su cuenta.
     */
    public function showResendActivationForm()
    {
        // Esta vista sera simple, con un campo para el email y un boton.
        return view('auth.resend-activation');
    }

    /**
     * Procesa la solicitud para reenviar el email de activacion.
     * Esta es una ruta publica.
     */
    public function resendActivationEmail(Request $request)
    {
        // 1. Validar que el email sea requerido y exista en la tabla de usuarios
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        // 2. Buscar al usuario por el email proporcionado
        $user = User::where('email', $request->email)->first();

        // Si por alguna razon el usuario no se encuentra (aunque la validacion 'exists' deberia prevenirlo),
        // lanzamos una excepcion de validacion para evitar revelar si el usuario existe o no.
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')], // Mensaje generico para seguridad
            ]);
        }

        // 3. Generar un nuevo token de restablecimiento de contrasena para el usuario
        // Utilizamos el "password broker" de Laravel, que es el mismo mecanismo que usa "olvide mi contrasena".
        $token = app('auth.password.broker')->createToken($user);

        // 4. Construir la URL de restablecimiento/activacion
        // Esta URL es la que el usuario usara para establecer su nueva contrasena.
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // 5. Enviar el correo electronico al usuario con el enlace de activacion
        try {
            Mail::to($user->email)->send(new UserCreatedNotification($user, $resetUrl));

            // **LOGGING:** Registrar el reenvio exitoso del email
            Log::info('Email de activacion reenviado exitosamente.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
            ]);

            // 6. Redirigir de vuelta con un mensaje de exito
            return back()->with('status', 'Hemos enviado un nuevo enlace de activacion a tu correo electronico. Por favor, revisa tu bandeja de entrada (y la carpeta de spam).');
        } catch (\Exception $e) {
            // **LOGGING:** Registrar cualquier error al intentar enviar el email
            Log::error('Error al reenviar email de activacion.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Solo para depuracion
            ]);
            // Redirigir con un mensaje de error
            return back()->withInput()->with('error', 'Ocurrio un error al intentar reenviar el enlace. Por favor, intentalo de nuevo mas tarde.');
        }
    }
}
