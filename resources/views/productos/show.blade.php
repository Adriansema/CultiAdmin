@extends('layouts.app')

@section('title', 'Detalle del Producto')

@section('content')
    <div class="inline-block px-8 py-10">
        <div class="flex items-center space-x-2">
            <img src="{{ asset('images/reverse.svg') }}" class="w-4 h-4" alt="Icono Volver">
            <h1 class="text-3xl whitespace-nowrap font-bold">Detalles del Producto</h1>
        </div>
        {{-- Asegúrate de que Breadcrumbs::render esté correctamente configurado en tu proyecto --}}
        {!! Breadcrumbs::render('productos.show', $producto) !!}
    </div>

    <div class="container max-w-4xl py-4 mx-auto bg-[var(--color-formulario)] shadow-xl px-8 space-x-4 rounded-lg">

        {{-- Sección de Estado del Producto (Más Prominente) --}}
        <div
            class="mb-6 p-4 rounded-lg
                @if ($producto->estado === 'aprobado') bg-green-100 text-green-800 border border-green-300
                @elseif ($producto->estado === 'rechazado') bg-red-100 text-red-800 border border-red-300
                @elseif ($producto->estado === 'pendiente') bg-yellow-100 text-yellow-800 border border-yellow-300
                @else bg-gray-100 text-gray-800 border border-gray-300 @endif">
            <h3 class="text-base font-semibold">Estado Actual:
                <span class="font-bold">{{ ucfirst($producto->estado) }}</span>
            </h3>

            @if ($producto->estado === 'rechazado')
                <p class="text-sm mt-2">
                    <strong>Observación del Operador:</strong> {{ $producto->observaciones ?? 'N/A' }}
                </p>
                @if ($producto->rechazador)
                    <p class="text-sm mt-1 text-red-700">
                        Rechazado por: <span class="font-medium">{{ $producto->rechazador->name }}</span>
                    </p>
                @endif
                {{-- Botón para editar producto: Visible solo si el producto está rechazado Y el usuario puede 'editar productos' --}}
                @can('update', $producto)
                    <div class="mt-4">
                        <a href="{{ route('productos.edit', $producto->id) }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Ir a Editar Producto →
                        </a>
                    </div>
                @endcan
            @elseif ($producto->estado === 'aprobado')
                <p class="text-sm mt-2">¡Tu producto ha sido aprobado y está listo para ser consumido!</p>
                @if ($producto->validador)
                    <p class="text-sm mt-1 text-green-700">
                        Validado por: <span class="font-medium">{{ $producto->validador->name }}</span>
                    </p>
                @endif
            @elseif ($producto->estado === 'pendiente')
                <p class="text-sm mt-2">Tu producto está pendiente de revisión por parte del operador.</p>
            @endif
        </div>

        @if ($producto->imagen)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto"
                    class="w-full rounded shadow">
            </div>
        @endif

        <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-600">Tipo de producto</h3>
            <p class="text-lg text-gray-800">{{ ucfirst($producto->tipo) }}</p>
        </div>

        <div class="mb-4 p-3 bg-gray-50 rounded-md">
            <h3 class="text-sm font-semibold text-gray-600">Observaciones</h3>
            <p class="text-gray-800 whitespace-pre-line break-words">{{ $producto->observaciones ?? 'N/A' }}</p>
        </div>

        {{-- Mostrar detalles específicos según el tipo de producto --}}
        @if ($producto->tipo === 'café' && $producto->cafe)
            <h2 class="text-xl font-bold mt-6 mb-4">Detalles de Café</h2>

            @if ($producto->cafe->cafInfor)
                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                    <h3 class="text-sm font-semibold text-gray-600">Información General del Café</h3>
                    <p class="text-gray-800 whitespace-pre-line">{{ $producto->cafe->cafInfor->informacion ?? 'N/A' }}</p>
                </div>
            @endif

            @if ($producto->cafe->cafInsumos)
                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                    <h3 class="text-sm font-semibold text-gray-600">Detalles de Insumos del Café</h3>
                    <p class="text-gray-800 whitespace-pre-line">{{ $producto->cafe->cafInsumos->informacion ?? 'N/A' }}
                    </p>
                </div>
            @endif

            @if ($producto->cafe->cafPatoge)
                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                    <h3 class="text-sm font-semibold text-gray-600">Nombre del Patógeno de la Mora</h3>
                    <p class="text-gray-800">{{ $producto->cafe->cafPatoge->patogeno ?? 'N/A' }}</p>
                </div>
                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                    <h3 class="text-sm font-semibold text-gray-600">Información de Patógenos del Café</h3>
                    <p class="text-gray-800 whitespace-pre-line">{{ $producto->cafe->cafPatoge->informacion ?? 'N/A' }}</p>
                </div>
            @endif
        @elseif ($producto->tipo === 'mora' && $producto->mora)
            <h2 class="text-xl font-bold mt-6 mb-4">Detalles de Mora</h2>

            @if ($producto->mora->moraInf)
                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                    <h3 class="text-sm font-semibold text-gray-600">Información General de la Mora</h3>
                    <p class="text-gray-800 whitespace-pre-line">{{ $producto->mora->moraInf->informacion ?? 'N/A' }}</p>
                </div>
            @endif

            @if ($producto->mora->moraInsu)
                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                    <h3 class="text-sm font-semibold text-gray-600">Detalles de Insumos de la Mora</h3>
                    <p class="text-gray-800 whitespace-pre-line">{{ $producto->mora->moraInsu->informacion ?? 'N/A' }}</p>
                </div>
            @endif

            @if ($producto->mora->moraPatoge)
                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                    <h3 class="text-sm font-semibold text-gray-600">Nombre del Patógeno de la Mora</h3>
                    <p class="text-gray-800">{{ $producto->mora->moraPatoge->patogeno ?? 'N/A' }}</p>
                </div>
                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                    <h3 class="text-sm font-semibold text-gray-600">Información del Patógeno de la Mora</h3>
                    <p class="text-gray-800 whitespace-pre-line">{{ $producto->mora->moraPatoge->informacion ?? 'N/A' }}
                    </p>
                </div>
            @endif
        @else
            <p class="text-gray-600 mt-4">No hay detalles específicos disponibles para este tipo de producto.</p>
        @endif

        <div class="mt-6">
            <a href="{{ route('productos.index') }}"
                class="inline-block px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">
                Volver al listado
            </a>
        </div>
    </div>
@endsection
