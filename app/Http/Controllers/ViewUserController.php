<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class ViewUserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles')->paginate(10);
        return view('VistasUsers.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'id');
        return view('VistasUsers.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:cliente_admin,email',
            'password' => 'required|min:6',
            'telefono' => 'nullable',
            'estado'   => 'required',
            'roles'    => 'required|array',
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'telefono'       => $request->telefono,
            'estado'         => $request->estado,
            'password'       => Hash::make($request->password),
            'clave_visible'  => $request->password, // Solo visible para el superadmin
        ]);

        $user->syncRoles($request->roles);

        return redirect()->route('view-user.index')->with('success', 'Usuario creado.');
    }

    public function show($id)
    {
        $usuario = User::with('roles')->findOrFail($id);
        return view('VistasUsers.show', compact('usuario'));
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $roles   = Role::pluck('name', 'id');
        return view('VistasUsers.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:cliente_admin,email,'.$id,
            'password' => 'nullable|min:6',
            'estado'   => 'required',
        ]);

        $usuario->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->Hash::make($request->password),
            'estado'   => $request->estado,
        ]);

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
            $usuario->clave_visible = $request->password;
        }

        $usuario->save();

        if ($request->has('roles')) {
            $usuario->syncRoles($request->roles);
        }

        return redirect()->route('view-user.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();
        return redirect()->route('view-user.index')->with('success', 'Usuario eliminado.');
    }

    public function historial($id)
    {
        $usuario = User::with('roles')->findOrFail($id);

        // En el futuro puedes cargar un historial de logs aquí si lo deseas.
        return view('VistasUsers.historial', compact('usuario'));
    }
}
