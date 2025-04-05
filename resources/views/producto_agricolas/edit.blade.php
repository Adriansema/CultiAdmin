@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Editar producto agrícola</h2>
    <form action="{{ route('productos-agricolas.update', $productoAgricola) }}" method="POST" enctype="multipart/form-data">
        @method('PUT')
        @include('productos_agricolas._form')
    </form>
</div>
@endsection
