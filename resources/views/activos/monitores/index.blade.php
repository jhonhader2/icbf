<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Monitores') }}</h2>
            <a href="{{ route('monitores.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase">{{ __('Nuevo') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))<p class="mb-4 text-green-600">{{ session('success') }}</p>@endif
            <form method="GET" class="mb-4"><input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Buscar') }}" class="rounded-md border-gray-300"><button type="submit" class="px-4 py-2 bg-gray-200 rounded-md ml-2">{{ __('Buscar') }}</button></form>
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr><th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Marca</th><th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Modelo</th><th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Serial</th><th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th></tr></thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($monitores as $m)
                            <tr><td class="px-6 py-4 text-sm">{{ $m->marca }}</td><td class="px-6 py-4 text-sm">{{ $m->modelo }}</td><td class="px-6 py-4 text-sm">{{ $m->serial }}</td><td class="px-6 py-4 text-right text-sm"><a href="{{ route('monitores.edit', $m) }}" class="text-indigo-600">Editar</a><form action="{{ route('monitores.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar?');">@csrf @method('DELETE')<button type="submit" class="text-red-600 ml-2">Eliminar</button></form></td></tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay registros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-2">{{ $monitores->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
