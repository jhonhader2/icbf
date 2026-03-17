<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Activos CRV') }}</h2>
            @can('activos_crv.import')
            <a href="{{ route('crv.import') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-xs uppercase font-semibold text-gray-700 hover:bg-gray-50">{{ __('Importar activos') }}</a>
            @endcan
            @can('activos_crv.create')
            <a href="{{ route('activos-crv.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase">{{ __('Nuevo') }}</a>
            @endcan
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))<p class="mb-4 text-green-600">{{ session('success') }}</p>@endif
            <form method="GET" class="mb-4"><input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Buscar placa/producto/serie') }}" class="rounded-md border-gray-300"><button type="submit" class="px-4 py-2 bg-gray-200 rounded-md ml-2">{{ __('Buscar') }}</button></form>
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr><th class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Placa') }}</th><th class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Producto') }}</th><th class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Persona') }}</th><th class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Fecha adquisición') }}</th><th class="px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th></tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($activosCrv as $a)
                            <tr><td class="px-6 py-4 text-sm">{{ $a->placa ?? '—' }}</td><td class="px-6 py-4 text-sm">{{ $a->producto?->nombre ?? $a->producto?->codigo ?? '—' }}</td><td class="px-6 py-4 text-sm">{{ $a->persona?->nombre ?? $a->persona?->documento_identidad ?? '—' }}</td><td class="px-6 py-4 text-sm">{{ $a->fecha_adquisicion?->format('d/m/Y') ?? '—' }}</td><td class="px-6 py-4 text-right text-sm"><a href="{{ route('activos-crv.show', $a) }}" class="text-gray-600 hover:text-gray-900">{{ __('Ver') }}</a>@can('activos_crv.update')<a href="{{ route('activos-crv.edit', $a) }}" class="ml-3 text-indigo-600">Editar</a>@endcan @can('activos_crv.delete')<form action="{{ route('activos-crv.destroy', $a) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar?');">@csrf @method('DELETE')<button type="submit" class="text-red-600 ml-2">Eliminar</button></form>@endcan</td></tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay registros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-2">{{ $activosCrv->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
