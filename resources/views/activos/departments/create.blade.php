<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo departamento') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('departments.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700">{{ __('Nombre') }}</label>
                        <input id="nombre" name="nombre" type="text" value="{{ old('nombre') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('nombre')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">{{ __('Guardar') }}</button>
                    <a href="{{ route('departments.index') }}" class="ml-2 text-gray-600">{{ __('Cancelar') }}</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
