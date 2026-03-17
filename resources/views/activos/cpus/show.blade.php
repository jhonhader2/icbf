<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <nav class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                    <a href="{{ route('cpus.index') }}" class="hover:text-gray-700">{{ __('CPUs') }}</a>
                    <span aria-hidden="true">/</span>
                    <span class="text-gray-800 font-medium">{{ $cpu->nombre_maquina ?? __('CPU #:id', ['id' => $cpu->id]) }}</span>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $cpu->nombre_maquina ?: __('CPU') }}</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('cpus.edit', $cpu) }}" class="inline-flex items-center px-3 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">{{ __('Editar') }}</a>
                <a href="{{ route('cpus.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">{{ __('Volver') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))<p class="text-green-600 text-sm">{{ session('success') }}</p>@endif
            @if (session('info'))<p class="text-blue-600 text-sm">{{ session('info') }}</p>@endif
            {{-- Identificación y asignación --}}
            <section class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800">{{ __('Identificación y asignación') }}</h3>
                </div>
                <dl class="p-4 sm:p-6 grid gap-4 sm:grid-cols-2">
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Nombre máquina') }}</dt><dd class="mt-1 text-gray-900 font-mono text-sm">{{ $cpu->nombre_maquina ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Serial') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->serial ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Placa') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->placa ?? '—' }}</dd></div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Persona') }}</dt>
                        <dd class="mt-1 text-gray-900">
                            @if($cpu->persona)
                                <a href="{{ route('personas.show', $cpu->persona) }}" class="text-indigo-600 hover:text-indigo-800">{{ $cpu->persona->nombre ?? $cpu->persona->documento_identidad }}</a>
                            @else
                                —
                            @endif
                        </dd>
                    </div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Estado') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->estado ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Tipo equipo') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->tipo_equipo ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Referencia equipo') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->referencia_equipo ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Regional') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->regional ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Fecha adquisición') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->fecha_adquisicion?->format('d/m/Y') ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Antigüedad') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->antiguedad_texto ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Año adquisición') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->año_adquisicion ?? '—' }}</dd></div>
                </dl>
            </section>

            {{-- Hardware y sistema --}}
            <section class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800">{{ __('Hardware y sistema') }}</h3>
                </div>
                <dl class="p-4 sm:p-6 grid gap-4 sm:grid-cols-2">
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Memoria RAM') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->memoria_ram ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('SO') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->so ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Procesador') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->procesador ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Tipo SO') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->tipo_so ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Bits') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->bits ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('N. discos físicos') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->n_discos_fisicos ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Capacidad disco') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->capacidad_disco ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Dirección IP') }}</dt><dd class="mt-1 text-gray-900 font-mono text-sm">{{ $cpu->direccion_ip ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('MAC address') }}</dt><dd class="mt-1 text-gray-900 font-mono text-sm">{{ $cpu->mac_address ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Office version') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->office_version ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Tarjeta red inalámbrica') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->tarjeta_red_inalambrica ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('En garantía') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->en_garantia ?? '—' }}</dd></div>
                </dl>
            </section>

            {{-- Inventario y otros --}}
            <section class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-800">{{ __('Inventario y otros') }}</h3>
                </div>
                <dl class="p-4 sm:p-6 grid gap-4 sm:grid-cols-2">
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Fecha inventario') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->fecha_inventario?->format('d/m/Y') ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Dependencias') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->dependencias ?? '—' }}</dd></div>
                    <div><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Nombre ingeniero diligenció') }}</dt><dd class="mt-1 text-gray-900">{{ $cpu->nombre_ingeniero_diligencio ?? '—' }}</dd></div>
                    @if($cpu->observaciones)
                        <div class="sm:col-span-2"><dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Observaciones') }}</dt><dd class="mt-1 text-gray-900 whitespace-pre-wrap text-sm">{{ $cpu->observaciones }}</dd></div>
                    @endif
                </dl>
            </section>

            {{-- Periféricos --}}
            @if($cpu->monitor || $cpu->teclado || $cpu->mouse)
                <section class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('Periféricos asociados') }}</h3>
                    </div>
                    <ul class="p-4 sm:p-6 space-y-3">
                        @if($cpu->monitor)
                            <li class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                                <span class="text-xs font-medium text-gray-500 uppercase shrink-0 w-16">{{ __('Monitor') }}</span>
                                <span class="text-gray-900 text-sm">{{ $cpu->monitor->marca ?? '' }} {{ $cpu->monitor->modelo ?? '' }}{{ $cpu->monitor->serial ? ' · ' . $cpu->monitor->serial : '' }}</span>
                            </li>
                        @endif
                        @if($cpu->teclado)
                            <li class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                                <span class="text-xs font-medium text-gray-500 uppercase shrink-0 w-16">{{ __('Teclado') }}</span>
                                <span class="text-gray-900 text-sm">{{ $cpu->teclado->marca ?? '' }} {{ $cpu->teclado->modelo ?? '' }}{{ $cpu->teclado->serial ? ' · ' . $cpu->teclado->serial : '' }}</span>
                            </li>
                        @endif
                        @if($cpu->mouse)
                            <li class="flex items-start gap-3 p-3 rounded-lg bg-gray-50">
                                <span class="text-xs font-medium text-gray-500 uppercase shrink-0 w-16">{{ __('Mouse') }}</span>
                                <span class="text-gray-900 text-sm">{{ $cpu->mouse->marca ?? '' }} {{ $cpu->mouse->modelo ?? '' }}{{ $cpu->mouse->serial ? ' · ' . $cpu->mouse->serial : '' }}</span>
                            </li>
                        @endif
                    </ul>
                </section>
            @endif

            {{-- Windows Defender TVM --}}
            @if($cpu->tvmAssets->isNotEmpty())
                <section class="bg-white shadow-sm sm:rounded-lg overflow-hidden border border-amber-200/60">
                    <div class="px-4 py-3 bg-amber-50 border-b border-amber-200/60 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <h3 class="text-sm font-semibold text-amber-900">{{ __('Windows Defender TVM') }}</h3>
                        </div>
                        <span class="text-xs font-medium text-amber-800/80">{{ $cpu->tvmAssets->count() }} {{ $cpu->tvmAssets->count() === 1 ? __('registro') : __('registros') }} · {{ $cpu->tvmAssets->where('state', 'open')->count() }} {{ __('abiertas') }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-amber-200/60">
                            <thead class="bg-amber-50/50">
                                <tr>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-amber-800/80 uppercase tracking-wide">{{ __('Reportado (importado)') }}</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-amber-800/80 uppercase tracking-wide">{{ __('DNS Name') }}</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-amber-800/80 uppercase tracking-wide">{{ __('Last seen') }}</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-amber-800/80 uppercase tracking-wide">{{ __('Sistema operativo') }}</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-amber-800/80 uppercase tracking-wide">{{ __('Estado') }}</th>
                                    <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-amber-800/80 uppercase tracking-wide">{{ __('Resuelto el') }}</th>
                                    <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-amber-800/80 uppercase tracking-wide">{{ __('Acción') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-100/80">
                                @foreach($cpu->tvmAssets as $tvm)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $tvm->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-900 break-all">{{ $tvm->dns_name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $tvm->last_seen ? $tvm->last_seen->format('d/m/Y H:i') : '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $tvm->operating_system ?? '—' }}</td>
                                        <td class="px-4 py-3">
                                            @if($tvm->isOpen())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">{{ __('Abierta') }}</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">{{ __('Resuelta') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $tvm->resolved_at ? $tvm->resolved_at->format('d/m/Y H:i') : '—' }}</td>
                                        <td class="px-4 py-3 text-right">
                                            @if($tvm->isOpen())
                                                <form action="{{ route('tvm-assets.resolve', $tvm) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Marcar este registro como resuelto?') }}');">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">{{ __('Marcar resuelto') }}</button>
                                                </form>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @else
                <section class="bg-white shadow-sm sm:rounded-lg overflow-hidden border border-dashed border-gray-300">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-600">{{ __('Windows Defender TVM') }}</h3>
                    </div>
                    <div class="p-4 sm:p-6 text-sm text-gray-500">
                        {{ __('No hay datos de Threat & Vulnerability Management para este equipo.') }}
                        <a href="{{ route('tvm.import') }}" class="text-indigo-600 hover:text-indigo-800 ml-1">{{ __('Importar TVM') }}</a>
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
