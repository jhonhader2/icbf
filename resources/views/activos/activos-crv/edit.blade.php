<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar activo CRV') }}</h2></x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('activos-crv.update', $activoCrv) }}">@csrf @method('PUT')
                    <div class="mb-4"><label for="placa" class="block text-sm font-medium text-gray-700">Placa</label><input id="placa" name="placa" value="{{ old('placa', $activoCrv->placa) }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div class="mb-4"><label for="producto_id" class="block text-sm font-medium text-gray-700">Producto</label><select id="producto_id" name="producto_id" class="mt-1 block w-full rounded-md border-gray-300"><option value="">--</option>@foreach($productos as $prod)<option value="{{ $prod->id }}" @selected(old('producto_id', $activoCrv->producto_id) == $prod->id)>{{ $prod->codigo }} — {{ $prod->nombre ?? '-' }}</option>@endforeach</select></div>
                    <div class="mb-4"><label for="serie" class="block text-sm font-medium text-gray-700">N.º serie equipo</label><input id="serie" name="serie" value="{{ old('serie', $activoCrv->serie) }}" class="mt-1 block w-full rounded-md border-gray-300"></div>
                    <div class="mb-4"><label for="persona_id" class="block text-sm font-medium text-gray-700">Persona</label><select id="persona_id" name="persona_id" class="mt-1 block w-full rounded-md border-gray-300"><option value="">--</option>@foreach($personas as $p)<option value="{{ $p->id }}" @selected(old('persona_id', $activoCrv->persona_id) == $p->id)>{{ $p->nombre ?? $p->documento_identidad }}</option>@endforeach</select></div>
                    <div class="mb-4"><label for="regional_id" class="block text-sm font-medium text-gray-700">Regional</label><select id="regional_id" name="regional_id" class="mt-1 block w-full rounded-md border-gray-300"><option value="">--</option>@foreach($regionales as $r)<option value="{{ $r->id }}" @selected(old('regional_id', $activoCrv->regional_id) == $r->id)>{{ $r->nombre }}</option>@endforeach</select></div>
                    <div class="mb-4"><label for="bodega_codigo" class="block text-sm font-medium text-gray-700">Bodega</label><select id="bodega_codigo" name="bodega_codigo" class="mt-1 block w-full rounded-md border-gray-300"><option value="">--</option>@foreach($bodegas as $b)<option value="{{ $b->codigo }}" @selected(old('bodega_codigo', $activoCrv->bodega_codigo) == $b->codigo)>{{ $b->nombre }}</option>@endforeach</select></div>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">Actualizar</button><a href="{{ route('activos-crv.index') }}" class="ml-2 text-gray-600">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
