<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Monitor') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <p><strong>Marca:</strong> {{ $monitor->marca }}</p><p><strong>Modelo:</strong> {{ $monitor->modelo }}</p><p><strong>Serial:</strong> {{ $monitor->serial }}</p>
                <a href="{{ route('monitores.edit', $monitor) }}" class="inline-block mt-4 px-4 py-2 bg-gray-800 text-white rounded-md">Editar</a><a href="{{ route('monitores.index') }}" class="ml-2 text-gray-600">Volver</a>
            </div>
        </div>
    </div>
</x-app-layout>
