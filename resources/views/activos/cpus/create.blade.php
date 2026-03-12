<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo CPU') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('cpus.store') }}">@csrf
                    <div class="mb-4"><label for="nombre_maquina" class="block text-sm font-medium text-gray-700">Nombre máquina</label><input id="nombre_maquina" name="nombre_maquina" value="{{ old('nombre_maquina') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div class="mb-4"><label for="placa" class="block text-sm font-medium text-gray-700">Placa</label><input id="placa" name="placa" value="{{ old('placa') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div class="mb-4"><label for="estado" class="block text-sm font-medium text-gray-700">Estado</label><input id="estado" name="estado" value="{{ old('estado') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div class="mb-4"><label for="referencia_equipo" class="block text-sm font-medium text-gray-700">Referencia equipo</label><input id="referencia_equipo" name="referencia_equipo" value="{{ old('referencia_equipo') }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div class="mb-4"><label for="persona_id" class="block text-sm font-medium text-gray-700">Persona</label><select id="persona_id" name="persona_id" class="mt-1 block w-full rounded-md border-gray-300"><option value="">--</option>@foreach($personas as $p)<option value="{{ $p->id }}" @selected(old('persona_id') == $p->id)>{{ $p->nombre ?? $p->documento_identidad }}</option>@endforeach</select></div>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">{{ __('Guardar') }}</button><a href="{{ route('cpus.index') }}" class="ml-2 text-gray-600">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
