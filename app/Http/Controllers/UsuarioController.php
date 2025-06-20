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
        return view('usuarios.index', compact('usuarios'));
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

        $roles = Role::all();
        $usuario = new User(); // Instancia de usuario vacía para el formulario
        return view('usuarios.create', compact('roles', 'usuario'));
    }

    public function store(Request $request)
    {
        // --- RESTRICCIÓN DE ROL: Operario/Funcionario no pueden crear usuarios ---
        if (Auth::user()->hasAnyRole(['Operario', 'Funcionario'])) {
            return redirect()->route('dashboard')->with('error', 'Tu rol no te permite crear usuarios.');
        }

        // 1. Validar la solicitud
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'type_document' => 'required|string|max:10',
            'document'      => 'required|string|max:20|unique:users,document',
        ]);

        try {
            // El número de documento se usará como contraseña inicial hasheada
            $documentNumber = $request->document;

            // 2. Crear el nuevo usuario
            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'type_document'     => $request->type_document,
                'document'          => $documentNumber,
                'password'          => Hash::make($documentNumber),
                'estado'            => 'activo',
                'email_verified_at' => null,
            ]);

            // 3. Enviar el email para que el usuario establezca su contraseña real (y al mismo tiempo verifique su email)
            $token = app('auth.password.broker')->createToken($user);
            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
            Mail::to($user->email)->send(new UserCreatedNotification($user, $resetUrl));

            // 4. Registrar el log de creación de usuario
            Log::info('Usuario creado manualmente (admin). Estado inicial: activo. Se envió email para establecer contraseña/activación.', [
                'user_id'          => $user->id,
                'email'            => $user->email,
                'document'         => $user->document,
                'created_by'       => Auth::id(),
                'created_by_email' => Auth::user()->email,
                'initial_estado'   => $user->estado,
            ]);

            // 5. Redirigir a la vista de edición para asignar roles y permisos
            return redirect()->route('usuarios.edit', $user->id)
                ->with('success', 'Usuario creado exitosamente. Se ha enviado un enlace a su correo electrónico para que establezca su contraseña y active su cuenta. Por favor, asigna los roles y permisos necesarios.');
        } catch (\Exception $e) {
            Log::error('Error al crear usuario manualmente (admin).', [
                'error'            => $e->getMessage(),
                'user_data'        => $request->except(['document']),
                'trace'            => $e->getTraceAsString(),
                'created_by'       => Auth::id(),
                'ip_address'       => $request->ip(),
            ]);
            return back()->withInput()->with('error', 'Ocurrió un error al crear el usuario. Por favor, inténtalo de nuevo.');
        }
    }

    public function edit(User $usuario)
    {
        Gate::authorize('editar usuario'); // Permiso general para editar usuarios

        $loggedInUser = Auth::user();
        $targetUserRoles = $usuario->getRoleNames(); // Roles del usuario que se intenta editar

        // --- RESTRICCIÓN DE ROL: Operario/Funcionario no pueden editar ningún usuario ---
        if ($loggedInUser->hasAnyRole(['Operario', 'Funcionario'])) {
            return redirect()->route('dashboard')->with('error', 'Tu rol no te permite editar perfiles de usuario.');
        }

        // --- RESTRICCIÓN DE ROL: Administrador no puede editar SuperAdmin o a otro Administrador (incluido a sí mismo) ---
        if ($loggedInUser->hasRole('Administrador')) {
            // Regla 1: Administrador no puede editar su propio perfil
            if ($usuario->id === $loggedInUser->id) {
                return redirect()->route('usuarios.index')->with('error', 'Un Administrador no puede editar su propio perfil.');
            }
            // Regla 2: Administrador no puede editar SuperAdmin o a otro Administrador
            if ($targetUserRoles->contains('SuperAdmin') || $targetUserRoles->contains('Administrador')) {
                return redirect()->route('usuarios.index')->with('error', 'Un Administrador no puede editar el perfil de un SuperAdmin o de otro Administrador.');
            }
        }

        // Si todas las verificaciones pasan, procede a cargar la vista
        $roles = Role::all();
        $permissions = Permission::all();
        $userRoles = $usuario->roles->pluck('name')->toArray();
        $allUserGrantedPermissions = $usuario->getAllPermissions()->pluck('name')->toArray();

        return view('usuarios.edit', compact('usuario', 'roles', 'permissions', 'userRoles', 'allUserGrantedPermissions'));
    }

    /**
     * Actualiza los datos de un usuario y sus roles/permisos.
     * Requiere el permiso 'actualizar usuario'.
     */
    public function update(Request $request, User $usuario)
    {
        Gate::authorize('editar usuario'); // Permiso general para actualizar usuarios

        $loggedInUser = Auth::user();
        $targetUserRoles = $usuario->getRoleNames(); // Roles del usuario que se intenta actualizar

        // --- RESTRICCIÓN DE ROL: Operario/Funcionario no pueden actualizar ningún usuario ---
        if ($loggedInUser->hasAnyRole(['Operario', 'Funcionario'])) {
            return redirect()->route('dashboard')->with('error', 'Tu rol no te permite actualizar perfiles de usuario.');
        }

        // --- RESTRICCIÓN DE ROL: Administrador no puede actualizar SuperAdmin o a otro Administrador (incluido a sí mismo) ---
        if ($loggedInUser->hasRole('Administrador')) {
            // Regla 1: Administrador no puede actualizar su propio perfil
            if ($usuario->id === $loggedInUser->id) {
                return redirect()->route('usuarios.index')->with('error', 'Un Administrador no puede actualizar su propio perfil.');
            }
            // Regla 2: Administrador no puede actualizar SuperAdmin o a otro Administrador
            if ($targetUserRoles->contains('SuperAdmin') || $targetUserRoles->contains('Administrador')) {
                return redirect()->route('usuarios.index')->with('error', 'Un Administrador no puede actualizar el perfil de un SuperAdmin o de otro Administrador.');
            }
        }

        // Restablecer las reglas de validación y añadir las nuevas columnas
        // Mantener las validaciones de los otros campos comentadas si no se van a usar ahora
        // pero se desean para el futuro. Sin embargo, para roles/permisos, sí deben validarse.
        $request->validate([
            // 'name'          => 'required|string|max:255', // Comentar si no se actualiza el nombre
            // 'email'         => 'required|email|unique:users,email,' . $usuario->id, // Comentar si no se actualiza el email
            // 'type_document' => 'required|string|max:10', // Comentar si no se actualiza el tipo de documento
            // 'document'      => 'required|string|unique:users,document,' . $usuario->id, // Comentar si no se actualiza el documento
            // 'password'      => 'nullable|string|min:8|max:15', // Comentar si no se actualiza la contraseña
            'roles'         => 'nullable|array',
            'roles.*'       => 'string|exists:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        try {
            // --- CÓDIGO COMENTADO PARA NO ACTUALIZAR OTROS CAMPOS POR AHORA ---
            // $userData = [
            //     'name'          => $request->name,
            //     'email'         => $request->email,
            //     'type_document' => $request->type_document,
            //     'document'      => $request->document,     
            // ];

            // if ($request->filled('password')) {
            //     $userData['password'] = Hash::make($request->password);
            // }

            // $usuario->update($userData); // <-- Esta línea se comenta
            // ---------------------------------------------------------------

            // --- LÓGICA DE ACTUALIZACIÓN DE ROLES Y PERMISOS (Activa) ---
            $usuario->syncRoles($request->roles ?? []);
            $usuario->syncPermissions($request->permissions ?? []);

            // **LOGGING:** Registrar la actualización de roles/permisos
            Log::info('Roles y permisos de usuario actualizados manualmente.', [
                'user_id'          => $usuario->id,
                'email'            => $usuario->email,
                'updated_by'       => Auth::id(),
                'updated_by_email' => Auth::user()->email,
                'assigned_roles'   => $request->roles ?? [], // Log de los roles asignados
                'assigned_permissions' => $request->permissions ?? [], // Log de los permisos asignados
            ]);

            return redirect()->route('usuarios.index')->with('success', 'Roles y permisos del usuario actualizados exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar roles y permisos del usuario manualmente.', [
                'error'            => $e->getMessage(),
                'user_id'          => $usuario->id,
                'request_data'     => $request->only(['roles', 'permissions']), // Solo loguear datos relevantes
                'trace'            => $e->getTraceAsString(),
                'updated_by'       => Auth::id(),
                'ip_address'       => $request->ip(), // Asegúrate de loguear la IP también
            ]);
            return back()->withInput()->with('error', 'Ocurrió un error al actualizar los roles y permisos del usuario. Por favor, inténtalo de nuevo.');
        }
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
