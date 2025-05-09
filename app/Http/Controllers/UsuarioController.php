<?php

//actualizacion 09/04/2025

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{

    public function index()
    {
        $usuarios = User::with('roles')->get();
        return view('usuarios.index', compact('usuarios'));
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
        // Este método carga la vista usuarios.showy le pasa al usuario que quiere mostrar.
        //Ideal para mostrar datos detallados de un solo usuario.
        return view('usuarios.show', compact('usuario'));
    }

    public function toggle(User $usuario)
    {
        //Cambia el estado del usuario ( activo ↔ inactivo) al contrario del actual.
        //Guarda el cambio y redirige con un mensaje.
        $usuario->estado = !$usuario->estado; // Cambia el valor actual por el contrario
        $usuario->save();

        return redirect()->route('usuarios.index')->with('success', 'Estado actualizado.');
    }


    public function edit(User $usuario)
    {
        $roles = Role::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
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
