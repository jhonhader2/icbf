<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Personas') }}</h2>
            @can('personas.import')
            <a href="{{ route('personas.import') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">{{ __('Importar') }}</a>
            @endcan
            @can('personas.create')
            <a href="{{ route('personas.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">{{ __('Nueva') }}</a>
            @endcan
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))<p class="mb-4 px-4 py-2 rounded-md bg-green-50 text-green-700">{{ session('success') }}</p>@endif
            @if (session('error'))<p class="mb-4 px-4 py-2 rounded-md bg-red-50 text-red-700">{{ session('error') }}</p>@endif
            <form method="GET" class="mb-4 flex gap-2 flex-wrap items-end">
                <div>
                    <label for="filter_q" class="block text-xs font-medium text-gray-500 mb-0.5">{{ __('Buscar') }}</label>
                    <input id="filter_q" type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Documento / nombre') }}" class="rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="filter_regional_id" class="block text-xs font-medium text-gray-500 mb-0.5">{{ __('Regional') }}</label>
                    <select id="filter_regional_id" name="regional_id" class="rounded-md border-gray-300 shadow-sm min-w-[140px]">
                        <option value="">{{ __('Todas') }}</option>
                        @foreach($regionales as $r)
                            <option value="{{ $r->id }}" @selected(request('regional_id') == $r->id)>{{ $r->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter_department_id" class="block text-xs font-medium text-gray-500 mb-0.5">{{ __('Departamento') }}</label>
                    <select id="filter_department_id" name="department_id" class="rounded-md border-gray-300 shadow-sm min-w-[160px]">
                        <option value="">{{ __('Todos') }}</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" @selected(request('department_id') == $d->id)>{{ $d->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter_account_status" class="block text-xs font-medium text-gray-500 mb-0.5">{{ __('Estado') }}</label>
                    <select id="filter_account_status" name="account_status" class="rounded-md border-gray-300 shadow-sm">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="0" @selected(request('account_status') === '0')>{{ __('Inactivo') }}</option>
                        <option value="1" @selected(request('account_status') === '1')>{{ __('Activo') }}</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Filtrar') }}</button>
                @if(request()->hasAny(['q','regional_id','department_id','account_status']))
                    <a href="{{ route('personas.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Limpiar') }}</a>
                @endif
            </form>

            @if($regionalSeleccionada && $personasPorDepartamentoEnRegional->isNotEmpty())
                <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    @php
                        $rowsDepto = $personasPorDepartamentoEnRegional->map(function ($r) {
                            return [
                                'department_name' => $r->department_name ?? __('Sin departamento'),
                                'total_activos' => (int) ($r->total_activos ?? 0),
                                'total_inactivos' => (int) ($r->total_inactivos ?? 0),
                                'total' => (int) ($r->total ?? 0),
                            ];
                        });
                        $totActivos = (int) $rowsDepto->sum('total_activos');
                        $totInactivos = (int) $rowsDepto->sum('total_inactivos');
                        $totGeneral = (int) $rowsDepto->sum('total');
                        $topN = 8;
                        $top = $rowsDepto->sortByDesc('total_activos')->values()->take($topN);
                        $otrosActivos = (int) ($totActivos - (int) $top->sum('total_activos'));
                        $maxActivosDepto = (int) ($top->max('total_activos') ?? 0);
                    @endphp

                    <div class="px-6 py-4 border-b border-gray-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">
                                {{ __('Personas por departamento') }}
                                <span class="text-gray-500 font-medium">· {{ $regionalSeleccionada->nombre }}</span>
                            </h3>
                            <p class="text-sm text-gray-500 mt-0.5">{{ __('Activos e inactivos por departamento (resumen).') }}</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-semibold">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                {{ __('Activos') }}: {{ number_format($totActivos) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">
                                <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                {{ __('Inactivos') }}: {{ number_format($totInactivos) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-50 text-indigo-800 text-xs font-semibold">
                                {{ __('Total') }}: {{ number_format($totGeneral) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <h4 class="text-sm font-semibold text-gray-900">{{ __('Activos por departamento') }}</h4>
                                <span class="text-xs text-gray-500">{{ __('Top') }} {{ $topN }}</span>
                            </div>

                            @if($maxActivosDepto > 0)
                                <ul class="space-y-2" aria-label="{{ __('Activos por departamento') }}">
                                    @foreach($top as $row)
                                        @php
                                            $label = $row['department_name'];
                                            $activos = $row['total_activos'];
                                            $pct = $maxActivosDepto > 0 ? round(($activos / $maxActivosDepto) * 100, 1) : 0;
                                        @endphp
                                        <li class="group">
                                            <div class="flex items-baseline justify-between gap-3">
                                                <span class="text-sm font-medium text-gray-800 truncate max-w-[70%]" title="{{ $label }}">{{ $label }}</span>
                                                <span class="text-sm font-semibold text-gray-900 tabular-nums">{{ number_format($activos) }}</span>
                                            </div>
                                            <div class="mt-1 h-2.5 bg-white rounded-full overflow-hidden border border-gray-200">
                                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-green-600" style="width: {{ $pct }}%;"></div>
                                            </div>
                                        </li>
                                    @endforeach
                                    @if($otrosActivos > 0)
                                        <li class="pt-2 mt-2 border-t border-gray-200">
                                            <div class="flex items-baseline justify-between gap-3">
                                                <span class="text-sm font-medium text-gray-600">{{ __('Otros') }}</span>
                                                <span class="text-sm font-semibold text-gray-800 tabular-nums">{{ number_format($otrosActivos) }}</span>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            @else
                                <p class="text-sm text-gray-500">{{ __('No hay personas activas para graficar en esta regional.') }}</p>
                            @endif
                        </div>

                        <div class="overflow-hidden border border-gray-200 rounded-xl bg-white">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50 sticky top-0 z-10">
                                        <tr class="text-xs uppercase tracking-wide text-gray-500">
                                            <th class="px-4 py-3 text-left font-semibold">{{ __('Departamento') }}</th>
                                            <th class="px-4 py-3 text-right font-semibold">{{ __('Activos') }}</th>
                                            <th class="px-4 py-3 text-right font-semibold">{{ __('Inactivos') }}</th>
                                            <th class="px-4 py-3 text-right font-semibold">{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($rowsDepto as $row)
                                            <tr class="odd:bg-white even:bg-gray-50/50 hover:bg-indigo-50/40">
                                                <td class="px-4 py-3 text-gray-800 font-medium">{{ $row['department_name'] }}</td>
                                                <td class="px-4 py-3 text-right tabular-nums">
                                                    <span class="inline-flex items-center justify-end gap-1.5 px-2 py-1 rounded-md bg-emerald-50 text-emerald-800 font-semibold">
                                                        {{ number_format($row['total_activos']) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right tabular-nums">
                                                    <span class="inline-flex items-center justify-end gap-1.5 px-2 py-1 rounded-md bg-gray-100 text-gray-700 font-semibold">
                                                        {{ number_format($row['total_inactivos']) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right tabular-nums font-semibold text-gray-900">{{ number_format($row['total']) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50 border-t border-gray-200">
                                        <tr>
                                            <td class="px-4 py-3 font-semibold text-gray-700">{{ __('Total') }}</td>
                                            <td class="px-4 py-3 text-right font-semibold text-emerald-800 tabular-nums">{{ number_format($totActivos) }}</td>
                                            <td class="px-4 py-3 text-right font-semibold text-gray-700 tabular-nums">{{ number_format($totInactivos) }}</td>
                                            <td class="px-4 py-3 text-right font-semibold text-gray-900 tabular-nums">{{ number_format($totGeneral) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @can('personas.crear-usuario')
                @if($hayPersonasSinUsuario)
                    <form action="{{ route('personas.crear-usuarios-todos') }}" method="POST" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex items-center gap-3">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">{{ __('Crear todos los usuarios que falten') }}</button>
                        <span class="text-sm text-gray-500">{{ __('Crea un usuario de acceso para cada persona con email que aún no tenga. Contraseña inicial: documento de identidad.') }}</span>
                    </form>
                @endif
                @endcan
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Documento') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nombre') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Departamento') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Estado') }}</th>
                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($personas as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">{{ $p->documento_identidad }}</td>
                                <td class="px-6 py-4 text-sm">{{ $p->nombre ?? $p->full_name }}</td>
                                <td class="px-6 py-4 text-sm">{{ $p->department?->nombre ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($p->account_status === '1')<span class="text-green-700">{{ __('Activo') }}</span>@elseif($p->account_status === '0')<span class="text-gray-500">{{ __('Inactivo') }}</span>@else<span class="text-gray-400">—</span>@endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('personas.show', $p) }}" class="text-gray-600 hover:text-gray-900">{{ __('Ver') }}</a>
                                    @can('personas.update')
                                    <a href="{{ route('personas.edit', $p) }}" class="ml-3 text-indigo-600 hover:text-indigo-800">{{ __('Editar') }}</a>
                                    @endcan
                                    @can('personas.delete')
                                    <form action="{{ route('personas.destroy', $p) }}" method="POST" class="inline ml-3" onsubmit="return confirm('{{ __('¿Eliminar?') }}');">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-800">{{ __('Eliminar') }}</button></form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">{{ __('No hay registros.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-2">{{ $personas->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
