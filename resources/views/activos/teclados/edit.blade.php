<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar teclado') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('teclados.update', $teclado) }}">@csrf @method('PUT')
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Marca</label><input name="marca" value="{{ old('marca', $teclado->marca) }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Modelo</label><input name="modelo" value="{{ old('modelo', $teclado->modelo) }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Serial</label><input name="serial" value="{{ old('serial', $teclado->serial) }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Placa</label><input name="placa" value="{{ old('placa', $teclado->placa) }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700">Estado</label><input name="estado" value="{{ old('estado', $teclado->estado) }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div class="mb-4"><label class="block text-sm font-medium text-gray-700">CPU</label><select name="cpu_id" class="mt-1 block w-full rounded-md border-gray-300"><option value="">--</option>@foreach($cpus as $c)<option value="{{ $c->id }}" @selected(old('cpu_id', $teclado->cpu_id) == $c->id)>{{ $c->nombre_maquina ?? $c->serial ?? $c->id }}</option>@endforeach</select></div>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">Actualizar</button><a href="{{ route('teclados.index') }}" class="ml-2 text-gray-600">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
