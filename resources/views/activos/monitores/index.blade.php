<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Monitores') }}</h2>
            @can('monitores.create')
                <a href="{{ route('monitores.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase">{{ __('Nuevo') }}</a>
            @endcan
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))<p class="mb-4 px-4 py-2 rounded-md bg-green-50 text-green-700">{{ session('success') }}</p>@endif
            @if (session('error'))<p class="mb-4 px-4 py-2 rounded-md bg-red-50 text-red-700">{{ session('error') }}</p>@endif

            {{-- Tarjeta total monitores --}}
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-4 sm:px-6">
                    <p class="text-xs font-semibold tracking-wide text-gray-500 uppercase">{{ __('Total de monitores') }}</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900 tabular-nums">{{ number_format($monitores->total()) }}</p>
                </div>
            </div>

            {{-- Monitores por marca --}}
            @if(!empty($monitoresPorMarca))
                @php
                    $maxTotal = max(array_column($monitoresPorMarca, 'total'));
                @endphp
                <section class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between gap-2">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Monitores por marca') }}</h3>
                        <p class="text-xs text-gray-500">{{ __('Distribución visual por fabricante.') }}</p>
                    </div>
                    <div class="p-4 sm:p-6">
                        <ul class="space-y-3" aria-label="{{ __('Distribución por marca') }}">
                            @foreach(array_slice($monitoresPorMarca, 0, 10) as $item)
                                <li class="flex items-center gap-3">
                                    <span class="text-sm text-gray-800 font-medium shrink-0 w-32 sm:w-40 truncate" title="{{ $item['marca'] }}">{{ $item['marca'] }}</span>
                                    <div class="flex-1 min-w-0 h-6 bg-gray-100 rounded-full overflow-hidden" aria-hidden="true">
                                        <div class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-sky-500" style="width: {{ $maxTotal > 0 ? round(($item['total'] / $maxTotal) * 100, 1) : 0 }}%;"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 tabular-nums shrink-0 w-10 text-right">{{ $item['total'] }}</span>
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
                        <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Buscar por marca, modelo, serial o placa') }}" class="block w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Buscar') }}</button>
                        @if(request('q'))<a href="{{ route('monitores.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Limpiar') }}</a>@endif
                    </div>
                </form>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Placa') }}</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Marca') }}</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Modelo') }}</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Serial') }}</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Propietario') }}</th>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($monitores as $m)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm">{{ $m->placa ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $m->marca ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $m->modelo ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm font-mono">{{ $m->serial ?? '—' }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @php
                                            $prop = $propietariosPorId[$m->id] ?? $m->activoCrv?->persona ?? $m->cpu?->activoCrv?->persona ?? $m->cpu?->persona;
                                        @endphp
                                        {{ $prop?->nombre ?? $prop?->full_name ?? $prop?->documento_identidad ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <a href="{{ route('monitores.show', $m) }}" class="text-gray-600 hover:text-gray-900">{{ __('Ver') }}</a>
                                        @can('monitores.update')
                                            <a href="{{ route('monitores.edit', $m) }}" class="ml-3 text-indigo-600 hover:text-indigo-800">{{ __('Editar') }}</a>
                                        @endcan
                                        @can('monitores.delete')
                                            <form action="{{ route('monitores.destroy', $m) }}" method="POST" class="inline ml-3" onsubmit="return confirm('{{ __('¿Eliminar?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800">{{ __('Eliminar') }}</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">{{ __('No hay registros.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-2">{{ $monitores->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
