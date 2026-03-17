<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo permiso') }}</h2>
            <a href="{{ route('permissions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('Volver a permisos') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <ul class="mb-4 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            @endif

            <form action="{{ route('permissions.store') }}" method="POST" class="bg-white shadow-sm sm:rounded-lg p-6">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nombre del permiso') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="255" placeholder="ej. manage users" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Crear permiso') }}</button>
                    <a href="{{ route('permissions.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Cancelar') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
