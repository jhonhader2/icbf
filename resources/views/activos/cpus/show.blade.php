<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('CPU') }}</h2>
            <a href="{{ route('cpus.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('← Volver al listado') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <dl class="grid gap-3 sm:grid-cols-2">
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Nombre máquina') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->nombre_maquina ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Serial') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->serial ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Placa') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->placa ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Persona') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->persona?->nombre ?? $cpu->persona?->documento_identidad ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Estado') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->estado ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Tipo equipo') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->tipo_equipo ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Referencia equipo') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->referencia_equipo ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Fecha adquisición') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->fecha_adquisicion?->format('d/m/Y') ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Antigüedad') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->antiguedad_texto ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Año adquisición') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->año_adquisicion ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Regional') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->regional ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Memoria RAM') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->memoria_ram ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('SO') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->so ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Procesador') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->procesador ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Tipo SO') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->tipo_so ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Bits') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->bits ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('N. discos físicos') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->n_discos_fisicos ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Capacidad disco') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->capacidad_disco ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Dirección IP') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->direccion_ip ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('MAC address') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->mac_address ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Office version') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->office_version ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Tarjeta red inalámbrica') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->tarjeta_red_inalambrica ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('En garantía') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->en_garantia ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Fecha inventario') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->fecha_inventario?->format('d/m/Y') ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Dependencias') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->dependencias ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Nombre ingeniero diligenció') }}</dt><dd class="mt-0.5 text-gray-900">{{ $cpu->nombre_ingeniero_diligencio ?? '—' }}</dd></div>
                    @if($cpu->observaciones)
                        <div class="sm:col-span-2"><dt class="text-sm font-medium text-gray-500">{{ __('Observaciones') }}</dt><dd class="mt-0.5 text-gray-900 whitespace-pre-wrap">{{ $cpu->observaciones }}</dd></div>
                    @endif
                </dl>
                @if($cpu->monitor || $cpu->teclado || $cpu->mouse)
                    <h3 class="mt-6 text-sm font-medium text-gray-700">{{ __('Periféricos asociados') }}</h3>
                    <ul class="mt-2 text-sm text-gray-600 space-y-1">
                        @if($cpu->monitor)<li>{{ __('Monitor') }}: {{ $cpu->monitor->marca ?? '' }} {{ $cpu->monitor->modelo ?? '' }} {{ $cpu->monitor->serial ? '(' . $cpu->monitor->serial . ')' : '' }}</li>@endif
                        @if($cpu->teclado)<li>{{ __('Teclado') }}: {{ $cpu->teclado->marca ?? '' }} {{ $cpu->teclado->modelo ?? '' }} {{ $cpu->teclado->serial ? '(' . $cpu->teclado->serial . ')' : '' }}</li>@endif
                        @if($cpu->mouse)<li>{{ __('Mouse') }}: {{ $cpu->mouse->marca ?? '' }} {{ $cpu->mouse->modelo ?? '' }} {{ $cpu->mouse->serial ? '(' . $cpu->mouse->serial . ')' : '' }}</li>@endif
                    </ul>
                @endif
                <div class="mt-6 flex gap-3">
                    <a href="{{ route('cpus.edit', $cpu) }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Editar') }}</a>
                    <a href="{{ route('cpus.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Volver') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
