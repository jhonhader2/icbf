<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('CPUs') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('toma-parque.import') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-xs uppercase font-semibold text-gray-700 hover:bg-gray-50">{{ __('Importar Toma Parque') }}</a>
                <a href="{{ route('cpus.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase">{{ __('Nuevo') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))<p class="mb-4 text-green-600">{{ session('success') }}</p>@endif
            <form method="GET" class="mb-4"><input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Buscar') }}" class="rounded-md border-gray-300"><button type="submit" class="px-4 py-2 bg-gray-200 rounded-md ml-2">{{ __('Buscar') }}</button></form>
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr><th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nombre máquina') }}</th><th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Serial') }}</th><th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Placa') }}</th><th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Persona') }}</th><th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Fecha adquisición') }}</th><th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Antigüedad') }}</th><th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th></tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($cpus as $c)
                            <tr><td class="px-6 py-4 text-sm">{{ $c->nombre_maquina ?? '—' }}</td><td class="px-6 py-4 text-sm">{{ $c->serial ?? '—' }}</td><td class="px-6 py-4 text-sm">{{ $c->placa ?? '—' }}</td><td class="px-6 py-4 text-sm">{{ $c->persona?->nombre ?? $c->persona?->documento_identidad ?? '—' }}</td><td class="px-6 py-4 text-sm">{{ $c->fecha_adquisicion?->format('d/m/Y') ?? '—' }}</td><td class="px-6 py-4 text-sm">{{ $c->antiguedad_texto ?? '—' }}</td><td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('cpus.show', $c) }}" class="text-gray-600 hover:text-gray-900">{{ __('Ver') }}</a>
                                <a href="{{ route('cpus.edit', $c) }}" class="ml-3 text-indigo-600 hover:text-indigo-800">{{ __('Editar') }}</a>
                                <form action="{{ route('cpus.destroy', $c) }}" method="POST" class="inline ml-3" onsubmit="return confirm('{{ __('¿Eliminar?') }}');">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-800">{{ __('Eliminar') }}</button></form>
                            </td></tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay registros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-2">{{ $cpus->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
