<?php

//actualizacion 09/04/2025

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request; // ¡Importa la clase Request!
use App\Services\UserService; // Importa los filtros, paginacion, etc...
use Spatie\Permission\Models\Role; //Importar el modelo role
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission; //Importar el modelo permissions
use Illuminate\Support\Facades\Response; //para exportar la tabla

class UsuarioController extends Controller
{
    public function index(Request $request, UserService $userService)
    {
        $usuarios = $userService->obtenerUsuariosFiltrados($request);
        return view('usuarios.index', compact('usuarios'));
    }

    public function getFilteredUsers(Request $request, UserService $userService)
    {
        $usuarios = $userService->obtenerUsuariosFiltrados($request);
        // Devuelve una respuesta JSON, incluyendo la paginación
        return response()->json($usuarios);
    }

    public function create()
    {
        $roles = Role::all(); // Puedes mantener esto si quieres que el administrador elija un rol inicial
        $usuario = new User();
        return view('usuarios.create', compact('roles', 'usuario'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'nullable|string|exists:roles,name', // Mantienes la asignación de un rol inicial
        ]);

        $usuario = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $usuario->syncRoles([$request->role]);

        // *** CAMBIO CLAVE AQUÍ: REDIRIGIMOS A LA VISTA DE EDICIÓN DEL USUARIO ***
        return redirect()->route('usuarios.edit', $usuario->id)->with('success', 'Usuario creado exitosamente. Ahora puedes asignar roles y permisos adicionales.');
    }


    public function show(User $usuario)
    {
        //Este método carga la vista usuarios.showy le pasa al usuario que quiere mostrar.
        //Ideal para mostrar datos detallados de un solo usuario.
        return view('usuarios.show', compact('usuario'));
    }

    public function toggle(User $usuario)
    {
        // Alternar entre 'activo' e 'inactivo'
        $usuario->estado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Estado actualizado.');
    }

    public function edit(User $usuario)
    {
        $roles = Role::all(); // Obtiene todos los roles disponibles
        $permissions = Permission::all(); // ¡Obtiene todos los permisos disponibles!

        // Obtiene los nombres de los roles que el usuario TIENE actualmente
        $userRoles = $usuario->roles->pluck('name')->toArray();
        // Obtiene los nombres de los permisos directos que el usuario TIENE actualmente
        $userDirectPermissions = $usuario->getDirectPermissions()->pluck('name')->toArray();

        // Pasa todas estas variables a la vista
        return view('usuarios.edit', compact('usuario', 'roles', 'permissions', 'userRoles', 'userDirectPermissions'));
    }

    public function exportarCSV(Request $request)
    {
        $query = $request->input('q');
        $rol = $request->input('rol');
        $estado = $request->input('estado'); // nuevo filtro

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
                    $usuario->estado, // acá ya es texto, 'Activo' o 'Inactivo'
                    $usuario->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

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
                continue; // saltar si faltan campos esenciales
            }

            // Crear o actualizar usuario
            $usuario = User::updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => isset($datos['password']) ? Hash::make($datos['password']) : Hash::make('12345678'),
                ]
            );

            // Asignar rol dinámicamente
            $rol = strtolower(trim($datos['rol']));

            if (in_array($rol, ['administrador', 'operador'])) {
                $usuario->syncRoles([$rol]); // elimina roles previos y asigna el nuevo
            }
        }

        return back()->with('success', 'Usuarios importados y roles asignados correctamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $usuario->id,
            'password'  => 'nullable|string|min:8|confirmed', // 'nullable' si no es obligatorio cambiarla
            'roles'     => 'nullable|array', // Ahora 'roles' será un array
            'roles.*'   => 'exists:roles,name', // Valida que cada elemento del array sea un nombre de rol existente
            'permissions' => 'nullable|array', // Para permisos directos, también un array
            'permissions.*' => 'exists:permissions,name', // Valida que cada permiso exista
        ]);

        $usuario->name = $request->name;
        $usuario->email = $request->email;

        if ($request->filled('password')) { // Solo actualiza la contraseña si se proporcionó
            $usuario->password = Hash::make($request->password);
        }
        $usuario->save();

        // *** Sincronizar Roles: Usa syncRoles para asignar los roles seleccionados ***
        // Si no se seleccionó ningún rol, $request->input('roles', []) devuelve un array vacío
        // esto asegura que todos los roles existentes del usuario sean revocados.
        $usuario->syncRoles($request->input('roles', []));

        // *** Sincronizar Permisos Directos: Usa syncPermissions para asignar los permisos directos seleccionados ***
        // Similar a syncRoles, pero para permisos directos.
        // Revisa la sección de 'Consideraciones sobre Permisos Directos' abajo.
        $usuario->syncPermissions($request->input('permissions', []));

        return redirect()->route('usuarios.edit', $usuario->id)->with('success', 'Usuario y sus roles/permisos actualizados correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function checkEmailExists(Request $request)
    {
        // 1. Validar la petición: Asegura que el 'email' esté presente y sea un formato válido.
        $request->validate([
            'email' => 'required|email',
        ]);

        // 2. Consultar la base de datos para ver si el correo existe.
        $exists = User::where('email', $request->email)->exists();

        // Puedes usar Log::info() para depurar si necesitas
        // Log::info('Checking email: ' . $request->email . ' - Exists: ' . ($exists ? 'Yes' : 'No'));

        // 3. Devolver una respuesta JSON.
        return response()->json(['exists' => $exists]);
    }
}
