<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Bodega') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p><strong>Código:</strong> {{ $bodega->codigo }}</p><p><strong>Nombre:</strong> {{ $bodega->nombre }}</p>
                <a href="{{ route('bodegas.edit', $bodega) }}" class="inline-block mt-4 px-4 py-2 bg-gray-800 text-white rounded-md">Editar</a><a href="{{ route('bodegas.index') }}" class="ml-2 text-gray-600">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
