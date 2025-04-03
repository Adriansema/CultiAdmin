@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Información</h1>

    <form action="{{ route('informaciones.update', $informacion->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="{{ $informacion->titulo }}" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3" required>{{ $informacion->descripcion }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Actualizar</button>
        <a href="{{ route('informaciones.index') }}" class="btn btn-secondary">Volver</a>
    </form>
</div>
@endsection
