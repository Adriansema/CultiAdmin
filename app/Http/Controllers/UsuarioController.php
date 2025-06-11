<?php

//actualizacion 09/04/2025 (y ahora con UserPolicy 06/06/2025)

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Permission;

class UsuarioController extends Controller
{
    public function index(Request $request, UserService $userService)
    {
        $this->authorize('viewAny', User::class);

        $usuarios = $userService->obtenerUsuariosFiltrados($request);
        return view('usuarios.index', compact('usuarios'));
    }

    public function getFilteredUsers(Request $request, UserService $userService)
    {
        // Autorización: Mismo permiso que viewAny.
        $this->authorize('viewAny', User::class);

        $usuarios = $userService->obtenerUsuariosFiltrados($request);
        return response()->json($usuarios);
    }

    public function create()
    {
        // Autorización: El usuario debe tener permiso para crear usuarios.
        $this->authorize('create', User::class);

        $roles = Role::all();
        $usuario = new User();
        return view('usuarios.create', compact('roles', 'usuario'));
    }

    public function store(Request $request)
    {
        // Autorización: El usuario debe tener permiso para crear usuarios.
        $this->authorize('create', User::class);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'nullable|string|exists:roles,name',
        ]);

        $usuario = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar el rol inicial
        if ($request->filled('role')) {
            $usuario->assignRole($request->role); // Usar assignRole para añadir el primer rol
        }

        // Aquí no se autoriza porque el usuario se acaba de crear y el creador (el admin)
        // tiene permiso para verlo/editarlo.
        return redirect()->route('usuarios.edit', $usuario->id)->with('success', 'Usuario creado exitosamente. Ahora puedes asignar roles y permisos adicionales.');
    }


    public function show(User $usuario)
    {
        // Autorización: El usuario debe tener permiso para ver este usuario específico.
        // O si es su propio perfil.
        $this->authorize('view', $usuario);

        return view('usuarios.show', compact('usuario'));
    }

    public function toggle(User $usuario)
    {
        // Autorización: El usuario debe tener permiso para activar/desactivar este usuario.
        // La Policy ya incluye la lógica de no permitirse a sí mismo o a administradores.
        $this->authorize('toggle', $usuario);

        // Alternar entre 'activo' e 'inactivo'
        $usuario->estado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Estado actualizado.');
    }

    public function edit(User $usuario)
    {
        $this->authorize('update', $usuario);

        // Obtener todos los roles y permisos disponibles en el sistema
        $roles = Role::all();
        $permissions = Permission::all(); // Esto es importante: todos los permisos disponibles

        $userRoles = $usuario->roles->pluck('name')->toArray();
        $userDirectPermissions = $usuario->getDirectPermissions()->pluck('name')->toArray(); // Permisos asignados DIRECTAMENTE
        // --- ¡ESTA ES LA VARIABLE AÑADIDA PARA EL NUEVO FLUJO! ---
        $userPermissionsViaRoles = $usuario->getPermissionsViaRoles()->pluck('name')->toArray(); // Permisos que el usuario obtiene a través de sus roles

        return view('usuarios.edit', compact(
            'usuario',
            'roles',
            'permissions',
            'userRoles',
            'userDirectPermissions',
            'userPermissionsViaRoles' // Asegúrate de pasar esta variable a la vista
        ));
    }

    public function update(Request $request, User $usuario)
    {
        // Autorización para la acción de actualización general (ej. UserPolicy@update)
        $this->authorize('update', $usuario);

        $validatedData = $request->validate([
            /* 'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $usuario->id, */
            'password'  => 'nullable|string|min:8|confirmed',
            'roles'     => 'required|array',
            'roles.*'   => 'exists:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

       /*  $usuario->name = $validatedData['name'];
        $usuario->email = $validatedData['email']; */

        if ($request->filled('password')) {
            $usuario->password = Hash::make($validatedData['password']);
        }
        $usuario->save();

        // Esta es la única fuente de la lógica de roles/permisos
        $canManageRolesAndPermissions = Auth::user()->can('manageRolesAndPermissions', $usuario);

        if ($canManageRolesAndPermissions) {
            $usuario->syncRoles($request->input('roles', []));
            $usuario->syncPermissions($request->input('permissions', []));

            return redirect()->route('usuarios.index', $usuario->id)->with('success', 'Usuario, roles y permisos actualizados exitosamente.');
        } else {
            return redirect()->route('usuarios.edit', $usuario->id);
        }
    }

    public function exportarCSV(Request $request)
    {
        // Autorización: El usuario debe tener permiso para exportar usuarios.
        $this->authorize('export', User::class);

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

    public function importarCsv(Request $request)
    {
        // Autorización: El usuario debe tener permiso para importar usuarios.
        $this->authorize('import', User::class);

        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $archivo = $request->file('archivo');
        $contenido = array_map('str_getcsv', file($archivo->getRealPath()));
        $encabezados = array_map('strtolower', array_shift($contenido));

        foreach ($contenido as $fila) {
            $datos = array_combine($encabezados, $fila);

            if (!isset($datos['email'], $datos['name'], $datos['rol'])) {
                continue;
            }

            $usuario = User::updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => isset($datos['password']) ? Hash::make($datos['password']) : Hash::make('12345678'),
                ]
            );

            $rol = strtolower(trim($datos['rol']));

            if (in_array($rol, ['administrador', 'operador'])) {
                $usuario->syncRoles([$rol]);
            }
        }

        return back()->with('success', 'Usuarios importados y roles asignados correctamente.');
    }

    public function destroy(User $usuario)
    {
        // Autorización: El usuario debe tener permiso para eliminar este usuario.
        // La Policy ya incluye la lógica de no eliminarse a sí mismo o a administradores.
        $this->authorize('delete', $usuario);
        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function checkEmailExists(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }
}
