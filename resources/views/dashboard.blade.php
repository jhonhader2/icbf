<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $isAdmin ? __('Dashboard') : __('Mis activos') }}</h2>
            <a href="{{ route('personas.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ $isAdmin ? __('Ver personas') : __('Mi ficha') }}</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if($isAdmin)
            {{-- KPIs (solo admin) --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('personas.index') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ __('Total personas') }}</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ number_format($totalPersonas) }}</p>
                </a>
                <a href="{{ route('personas.index', ['account_status' => '1']) }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ __('Activas') }}</p>
                    <p class="mt-1 text-3xl font-semibold text-green-700">{{ number_format($personasActivas) }}</p>
                </a>
                <a href="{{ route('personas.index', ['account_status' => '0']) }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 hover:shadow-md transition-shadow border-l-4 border-gray-400">
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ __('Inactivas') }}</p>
                    <p class="mt-1 text-3xl font-semibold text-gray-600">{{ number_format($personasInactivas) }}</p>
                </a>
            </div>
            @endif

            {{-- KPIs CPUs --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-3">{{ __('CPUs por tipo') }}</h3>
                    <p class="text-xs text-gray-500 mb-4">
                        {{ __('Distribución de equipos por categoría principal (top 6).') }}
                    </p>
                    @php
                        $cpusPorTipoTop = array_slice($cpusPorTipo ?? [], 0, 6);
                        $maxTotalCpusTipo = !empty($cpusPorTipoTop) ? max(array_column($cpusPorTipoTop, 'total')) : 0;
                    @endphp
                    @if(!empty($cpusPorTipoTop))
                        <ul class="space-y-3" aria-label="{{ __('Distribución por tipo de CPU') }}">
                            @foreach($cpusPorTipoTop as $item)
                                <li class="flex items-center gap-3">
                                    <span class="text-sm text-gray-800 font-medium shrink-0 w-32 sm:w-36 truncate" title="{{ $item['tipo'] }}">
                                        {{ $item['tipo'] }}
                                    </span>
                                    <div class="flex-1 min-w-0 h-4 bg-gray-100 rounded-full overflow-hidden" aria-hidden="true">
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r from-indigo-500 to-sky-500"
                                            style="width: {{ $maxTotalCpusTipo > 0 ? round(($item['total'] / $maxTotalCpusTipo) * 100, 1) : 0 }}%;"
                                        ></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 tabular-nums shrink-0 w-10 text-right">
                                        {{ $item['total'] }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-4 text-xs text-gray-500">
                            {{ __('Total CPUs visibles:') }}
                            <span class="font-semibold text-gray-900 tabular-nums">{{ number_format($totalCpus) }}</span>
                        </p>
                        <a href="{{ route('cpus.index') }}" class="mt-2 inline-block text-sm text-indigo-600 hover:text-indigo-800">
                            {{ __('Ver detalle por equipo') }}
                        </a>
                    @else
                        <p class="text-sm text-gray-500">{{ __('No hay datos de CPUs para mostrar.') }}</p>
                    @endif
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">{{ __('CPUs por antigüedad') }}</h3>
                    <p class="text-sm text-gray-500 mb-2">{{ __('Desde fecha adquisición en activo CRV') }}</p>
                    <div class="flex items-center gap-6">
                        <div class="flex-1" style="max-height: 200px;">
                            <canvas id="chartCpusAntiguedad"></canvas>
                        </div>
                        <div class="text-center space-y-2">
                            <div>
                                <p class="text-xl font-semibold text-amber-700">{{ number_format($cpusMasDe5Anios) }}</p>
                                <p class="text-xs text-gray-500">{{ __('Más de 5 años') }}</p>
                            </div>
                            <div>
                                <p class="text-lg font-medium text-gray-700">{{ number_format($cpusMenosOIgual5) }}</p>
                                <p class="text-xs text-gray-500">{{ __('5 años o menos') }}</p>
                            </div>
                            <div>
                                <p class="text-lg font-medium text-gray-500">{{ number_format($cpusSinFecha) }}</p>
                                <p class="text-xs text-gray-500">{{ __('Sin fecha') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($isAdmin)
            {{-- Gráficos personas (solo admin) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-4">{{ __('Personas por estado') }}</h3>
                <div class="flex justify-center" style="max-height: 280px;">
                    <canvas id="chartEstado" width="280" height="280"></canvas>
                </div>
                <div class="mt-3 flex flex-wrap justify-center gap-4 text-sm">
                    <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-500"></span> {{ __('Activo') }}</span>
                    <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-400"></span> {{ __('Inactivo') }}</span>
                    @if ($personasSinEstado > 0)<span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-300"></span> {{ __('Sin estado') }}</span>@endif
                </div>
            </div>

            {{-- Tablas resumen personas: departamentos y regional (solo admin) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">{{ __('Personas por departamento') }} (top 12)</h3>
                    <div class="relative" style="min-height: 280px;">
                        <canvas id="chartDepartamentos"></canvas>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">{{ __('Total de personas por regional') }}</h3>
                    <div class="relative mb-4" style="min-height: 280px;">
                        <canvas id="chartRegionales"></canvas>
                    </div>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Regional') }}</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Total personas') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($personasPorRegional as $r)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            @if($r->regional_id)
                                                <a href="{{ route('personas.index', ['regional_id' => $r->regional_id]) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $r->regional_name }}</a>
                                            @else
                                                {{ $r->regional_name ?? __('Sin regional') }}
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium">{{ number_format($r->total) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-3 text-center text-gray-500">{{ __('No hay datos.') }}</td></tr>
                                @endforelse
                            </tbody>
                            @if($personasPorRegional->isNotEmpty())
                                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-700">{{ __('Total') }}</td>
                                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($personasPorRegional->sum('total')) }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const colores = {
                activo: 'rgb(34, 197, 94)',
                inactivo: 'rgb(156, 163, 175)',
                sinEstado: 'rgb(209, 213, 219)',
                bars: ['rgb(59, 130, 246)', 'rgb(99, 102, 241)', 'rgb(139, 92, 246)', 'rgb(236, 72, 153)', 'rgb(249, 115, 22)', 'rgb(234, 179, 8)', 'rgb(20, 184, 166)', 'rgb(14, 165, 233)', 'rgb(168, 85, 247)', 'rgb(244, 63, 94)', 'rgb(251, 146, 60)', 'rgb(132, 204, 22)'],
                regionalBars: ['rgb(30, 64, 175)', 'rgb(67, 56, 202)', 'rgb(126, 34, 206)', 'rgb(190, 24, 93)', 'rgb(194, 65, 12)', 'rgb(161, 98, 7)', 'rgb(15, 118, 110)', 'rgb(21, 94, 117)', 'rgb(107, 33, 168)', 'rgb(225, 29, 72)', 'rgb(180, 83, 9)', 'rgb(22, 101, 52)', 'rgb(30, 58, 138)', 'rgb(49, 46, 95)', 'rgb(88, 28, 135)']
            };

            new Chart(document.getElementById('chartCpusAntiguedad'), {
                type: 'doughnut',
                data: {
                    labels: ['{{ __("Más de 5 años") }}', '{{ __("5 años o menos") }}', '{{ __("Sin fecha") }}'],
                    datasets: [{
                        data: [{{ $cpusMasDe5Anios }}, {{ $cpusMenosOIgual5 }}, {{ $cpusSinFecha }}],
                        backgroundColor: ['rgb(217, 119, 6)', 'rgb(59, 130, 246)', 'rgb(156, 163, 175)'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: { legend: { position: 'bottom' } },
                    cutout: '50%'
                }
            });

            @if($isAdmin)
            if (document.getElementById('chartEstado')) {
                new Chart(document.getElementById('chartEstado'), {
                    type: 'doughnut',
                    data: (function () {
                        const datos = [{{ $personasActivas }}, {{ $personasInactivas }}, {{ $personasSinEstado }}];
                        const etiquetas = ['{{ __("Activo") }}', '{{ __("Inactivo") }}', '{{ __("Sin estado") }}'];
                        const coloresSlice = [colores.activo, colores.inactivo, colores.sinEstado];
                        const idx = datos.map((n, i) => n > 0 ? i : -1).filter(i => i >= 0);
                        return {
                            labels: idx.map(i => etiquetas[i]),
                            datasets: [{
                                data: idx.map(i => datos[i]),
                                backgroundColor: idx.map(i => coloresSlice[i]),
                                borderWidth: 0
                            }]
                        };
                    })(),
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { display: false } }
                    }
                });
            }
            if (document.getElementById('chartDepartamentos')) {
                new Chart(document.getElementById('chartDepartamentos'), {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($personasPorDepartamento->map(fn ($d) => $d->department_name ?? __('Sin departamento'))->values()) !!},
                        datasets: [{
                            label: '{{ __("Personas") }}',
                            data: {!! json_encode($personasPorDepartamento->pluck('total')->values()) !!},
                            backgroundColor: colores.bars.slice(0, {{ $personasPorDepartamento->count() }}),
                            borderRadius: 4
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, ticks: { precision: 0 } },
                            y: { ticks: { font: { size: 11 } } }
                        }
                    }
                });
            }
            if (document.getElementById('chartRegionales')) {
                const regionalLabels = {!! json_encode($personasPorRegional->map(fn ($r) => $r->regional_name ?? __('Sin regional'))->values()) !!};
                const regionalData = {!! json_encode($personasPorRegional->pluck('total')->values()) !!};
                const regionalColors = colores.regionalBars.slice(0, regionalLabels.length);
                new Chart(document.getElementById('chartRegionales'), {
                    type: 'bar',
                    data: {
                        labels: regionalLabels,
                        datasets: [{
                            label: '{{ __("Personas") }}',
                            data: regionalData,
                            backgroundColor: regionalColors.length ? regionalColors : ['rgb(156, 163, 175)'],
                            borderRadius: 4
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { beginAtZero: true, ticks: { precision: 0 } },
                            y: { ticks: { font: { size: 11 } } }
                        }
                    }
                });
            }
            @endif
        })();
    </script>
</x-app-layout>
