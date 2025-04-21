
@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Historial del Usuario</h2>
@endsection

@section('content')
<div class="px-6 py-4 space-y-3">
    <p><strong>Nombre:</strong> {{ $usuario->name }}</p>
    <p><strong>Email:</strong> {{ $usuario->email }}</p>
    <p><strong>Teléfono:</strong> {{ $usuario->telefono }}</p>
    <p><strong>Estado:</strong> {{ $usuario->estado }}</p>
    <p><strong>Rol:</strong> {{ $usuario->roles->pluck('name')->join(', ') }}</p>
    <p><strong>Clave visible:</strong> <span class="text-red-600">{{ $usuario->clave_visible }}</span></p>
</div>
@endsection
