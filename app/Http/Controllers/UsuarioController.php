<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Response;

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

    /**
     * Almacena un nuevo usuario en la base de datos.
     * Requiere el permiso 'crear usuario'.
     */
    public function store(Request $request)
    {

        // --- RESTRICCIÓN DE ROL: Operario/Funcionario no pueden crear usuarios ---
        if (Auth::user()->hasAnyRole(['Operario', 'Funcionario'])) {
            return redirect()->route('dashboard')->with('error', 'Tu rol no te permite crear usuarios.');
        }

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:15',
        ]);

        $usuario = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Redirigir a la vista de edición del usuario recién creado para asignar roles/permisos
        return redirect()->route('usuarios.edit', $usuario->id)
            ->with('success', 'Usuario creado exitosamente. Ahora asigna sus roles y permisos.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     * Requiere el permiso 'editar usuario'.
     * Las secciones de roles/permisos dentro de la vista pueden tener sus propias directivas @can.
     */
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
        Gate::authorize('actualizar usuario'); // Permiso general para actualizar usuarios

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

        // Si todas las verificaciones pasan, procede con la validación y actualización
        $request->validate([
            /*  'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $usuario->id,
            'password'  => 'nullable|string|min:8|confirmed', */
            'roles'     => 'nullable|array',
            'roles.*'   => 'string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $usuario->update([
            /*  'name'  => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : $usuario->password, */]);

        $usuario->syncRoles($request->roles ?? []);
        $usuario->syncPermissions($request->permissions ?? []);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
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
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $archivo = $request->file('archivo');
        $contenido = array_map('str_getcsv', file($archivo->getRealPath()));
        $encabezados = array_map('strtolower', array_shift($contenido));

        foreach ($contenido as $fila) {
            $datos = array_combine($encabezados, $fila);

            if (!isset($datos['email'], $datos['name'], $datos['rol'])) {
                continue; // Saltar fila si faltan datos esenciales
            }

            $usuario = User::updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => isset($datos['password']) ? Hash::make($datos['password']) : Hash::make('12345678'),
                ]
            );

            $rolName = trim($datos['rol']); // Usar el nombre del rol tal cual viene, sin strtolower
            $role = Role::where('name', $rolName)->first(); // Buscar el rol por su nombre

            if ($role) { // Solo asignar si el rol existe
                $usuario->syncRoles([$role->name]); // Sincronizar al rol encontrado
            } else {
                // Opcional: Loggear o manejar roles no existentes en el CSV
                // \Log::warning("Rol '$rolName' no encontrado al importar usuario '{$datos['email']}'.");
            }
        }

        return back()->with('success', 'Usuarios importados y roles asignados correctamente.');
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
}
