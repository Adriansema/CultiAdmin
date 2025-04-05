<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;


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
    return view('usuarios.create', compact('roles'));
}

public function store(Request $request)
{
    $user = User::create($request->only('name', 'email', 'password'));
    $user->assignRole($request->rol);
    return redirect()->route('usuarios.index');
}

public function edit(User $usuario)
{
    $roles = Role::all();
    return view('usuarios.edit', compact('usuario', 'roles'));
}

public function update(Request $request, User $usuario)
{
    $usuario->update($request->only('name', 'email'));
    $usuario->syncRoles($request->rol);
    return redirect()->route('usuarios.index');
}

public function destroy(User $usuario)
{
    $usuario->delete();
    return back();
}

}
