@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Agregar Cliente</h1>

    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
