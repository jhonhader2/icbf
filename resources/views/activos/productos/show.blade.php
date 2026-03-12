<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Producto') }}</h2>
            <a href="{{ route('productos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('← Volver') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <dl class="grid gap-3 sm:grid-cols-2">
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Código') }}</dt><dd class="mt-0.5 text-gray-900">{{ $producto->codigo }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Nombre') }}</dt><dd class="mt-0.5 text-gray-900">{{ $producto->nombre ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Marca') }}</dt><dd class="mt-0.5 text-gray-900">{{ $producto->marca ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Modelo') }}</dt><dd class="mt-0.5 text-gray-900">{{ $producto->modelo ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Activos CRV asignados') }}</dt><dd class="mt-0.5 text-gray-900">{{ $producto->activos_crv_count ?? 0 }}</dd></div>
                </dl>
                <div class="mt-6 flex gap-3">
                    <a href="{{ route('productos.edit', $producto) }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Editar') }}</a>
                    <a href="{{ route('activos-crv.index', ['producto_id' => $producto->id]) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Ver activos CRV') }}</a>
                    <a href="{{ route('productos.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">{{ __('Volver al listado') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
