<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar bodega') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('bodegas.update', $bodega) }}">@csrf @method('PUT')
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Código</label><input name="codigo" value="{{ old('codigo', $bodega->codigo) }}" required class="mt-1 block w-full rounded-md border-gray-300">@error('codigo')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror</div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Nombre</label><input name="nombre" value="{{ old('nombre', $bodega->nombre) }}" required class="mt-1 block w-full rounded-md border-gray-300">@error('nombre')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror</div>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">Actualizar</button><a href="{{ route('bodegas.index') }}" class="ml-2 text-gray-600">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
