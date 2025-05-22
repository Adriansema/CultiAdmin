<?php

//actualizacion 09/04/2025

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request; // ¡Importa la clase Request!
use App\Services\UserService; // Importa los filtros, paginacion, etc...
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
        $roles = Role::all();
        $usuario = new user(); // Inicializa la variable $usuario como nueva instancia de User
        return view('usuarios.create', compact('roles', 'usuario'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
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
        $roles = Role::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
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
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'role'  => 'required|string|exists:roles,name',
        ]);

        $usuario->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        $usuario->syncRoles($request->role);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
    }
}
