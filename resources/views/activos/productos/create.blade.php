<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo producto') }}</h2>
            <a href="{{ route('productos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('← Volver') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('productos.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="codigo" class="block text-sm font-medium text-gray-700">{{ __('Código') }}</label>
                            <input id="codigo" name="codigo" value="{{ old('codigo') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('codigo')<p class="mt-1 text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700">{{ __('Nombre / Descripción') }}</label>
                            <input id="nombre" name="nombre" value="{{ old('nombre') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('nombre')<p class="mt-1 text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="marca" class="block text-sm font-medium text-gray-700">{{ __('Marca') }}</label>
                            <input id="marca" name="marca" value="{{ old('marca') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('marca')<p class="mt-1 text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="modelo" class="block text-sm font-medium text-gray-700">{{ __('Modelo') }}</label>
                            <input id="modelo" name="modelo" value="{{ old('modelo') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('modelo')<p class="mt-1 text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Guardar') }}</button>
                        <a href="{{ route('productos.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Cancelar') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
