@extends('layouts.app')

@section('header')
    <h2 class="text-xl font-semibold">Detalle del Usuario</h2>
@endsection

@section('content')
<div class="px-6 py-4 space-y-3">
    <p><strong>Nombre:</strong> {{ $usuario->name }}</p>
    <p><strong>Email:</strong> {{ $usuario->email }}</p>
    <p><strong>Teléfono:</strong> {{ $usuario->telefono }}</p>
    <p><strong>Estado:</strong> {{ $usuario->estado }}</p>
    <p><strong>Rol:</strong> {{ $usuario->roles->pluck('name')->join(', ') }}</p>
</div>
@endsection

