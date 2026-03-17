<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-3">
            <div>
                <h2 class="font-semibold text-2xl text-gray-900 leading-tight tracking-tight">
                    {{ __('CPUs') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Consulta y gestiona el inventario de equipos de cómputo asignados.') }}
                </p>
            </div>
            @can('cpus.import')
            <a href="{{ route('toma-parque.import') }}" class="inline-flex items-center px-3 py-2 border border-gray-200 rounded-md text-xs font-semibold uppercase tracking-wide text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 shadow-sm">
                {{ __('Importar Toma Parque') }}
            </a>
            @endcan
            @can('cpus.create')
            <a href="{{ route('cpus.create') }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-md text-xs font-semibold uppercase tracking-wide shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Nuevo') }}
            </a>
            @endcan
        </div>
    </x-slot>
    <div class="py-8 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 flex items-start gap-2">
                    <span class="mt-0.5 h-2 w-2 rounded-full bg-emerald-500"></span>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            {{-- Tarjeta total equipos --}}
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-4 sm:px-6">
                    <p class="text-xs font-semibold tracking-wide text-gray-500 uppercase">{{ __('Total de equipos') }}</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 tabular-nums">{{ number_format($cpus->total()) }}</p>
                    <p class="mt-1 text-xs text-gray-500">
                        @if(request('q'))
                            {{ __('Mostrando resultados para la búsqueda actual.') }}
                        @else
                            {{ __('Incluye todos los equipos visibles según tus permisos.') }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- CPUs por tipo: barras --}}
            @if(!empty($cpusPorTipo))
                @php
                    $maxTotal = max(array_column($cpusPorTipo, 'total'));
                @endphp
                <section class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between gap-2">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('CPUs por tipo') }}</h3>
                        <p class="text-xs text-gray-500">
                            {{ __('Distribución visual de los equipos por categoría.') }}
                        </p>
                    </div>
                    <div class="p-4 sm:p-6">
                        <ul class="space-y-3" aria-label="{{ __('Distribución por tipo de CPU') }}">
                            @foreach($cpusPorTipo as $item)
                                @php
                                    $isActive = request('tipo') === $item['tipo'];
                                @endphp
                                <li class="flex items-center gap-3">
                                    <a
                                        href="{{ route('cpus.index', array_merge(request()->query(), ['tipo' => $item['tipo']])) }}"
                                        class="text-sm font-medium shrink-0 w-32 sm:w-40 truncate rounded-full px-2 py-1 text-left
                                            {{ $isActive ? 'bg-indigo-600 text-white' : 'text-gray-800 hover:text-indigo-700 hover:bg-indigo-50' }}"
                                        title="{{ $item['tipo'] }}"
                                    >
                                        {{ $item['tipo'] }}
                                    </a>
                                    <div class="flex-1 min-w-0 h-6 bg-gray-100 rounded-full overflow-hidden" aria-hidden="true">
                                        <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-sky-500" style="width: {{ $maxTotal > 0 ? round(($item['total'] / $maxTotal) * 100, 1) : 0 }}%;"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 tabular-nums shrink-0 w-8 text-right">{{ $item['total'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            @endif

            {{-- Búsqueda y tabla --}}
            <section class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <form method="GET" class="p-4 border-b border-gray-200 flex flex-wrap items-center gap-3">
                    <div class="flex-1 min-w-[220px]">
                        <label for="q" class="sr-only">{{ __('Buscar') }}</label>
                        <input
                            id="q"
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="{{ __('Buscar por nombre, placa o serial') }}"
                            class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500">
                            {{ __('Buscar') }}
                        </button>
                        @if(request('q'))
                            <a href="{{ route('cpus.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300">
                                {{ __('Limpiar') }}
                            </a>
                        @endif
                    </div>
                </form>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Nombre máquina') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Tipo') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Serial') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Placa') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Persona') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Fecha adquisición') }}</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Antigüedad') }}</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($cpus as $c)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-900 font-medium">
                                        {{ $c->nombre_maquina ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $c->tipo_equipo ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 font-mono">
                                        {{ $c->serial ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $c->placa ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $c->persona?->nombre ?? $c->persona?->documento_identidad ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                        {{ $c->fecha_adquisicion?->format('d/m/Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $c->antiguedad_texto ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm space-x-2">
                                        <a href="{{ route('cpus.show', $c) }}" class="text-gray-600 hover:text-gray-900 underline-offset-2 hover:underline">
                                            {{ __('Ver') }}
                                        </a>
                                        @can('cpus.update')
                                        <a href="{{ route('cpus.edit', $c) }}" class="text-indigo-600 hover:text-indigo-800 underline-offset-2 hover:underline">
                                            {{ __('Editar') }}
                                        </a>
                                        @endcan
                                        @can('cpus.delete')
                                        <form action="{{ route('cpus.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Eliminar?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 underline-offset-2 hover:underline">
                                                {{ __('Eliminar') }}
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">{{ __('No hay registros.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-200">{{ $cpus->links() }}</div>
            </section>
        </div>
    </div>

</x-app-layout>
