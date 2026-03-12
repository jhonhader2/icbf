<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Productos') }}</h2>
            <a href="{{ route('productos.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md text-xs uppercase">{{ __('Nuevo') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))<p class="mb-4 px-4 py-2 rounded-md bg-green-50 text-green-700">{{ session('success') }}</p>@endif
            <form method="GET" class="mb-4 flex gap-2">
                <label for="q" class="sr-only">{{ __('Buscar') }}</label>
                <input id="q" type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Código, nombre o marca') }}" class="rounded-md border-gray-300 shadow-sm">
                <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md">{{ __('Buscar') }}</button>
            </form>
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Código') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nombre') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Marca') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Modelo') }}</th>
                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($productos as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">{{ $p->codigo }}</td>
                                <td class="px-6 py-4 text-sm">{{ $p->nombre ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $p->marca ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $p->modelo ?? '—' }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('productos.show', $p) }}" class="text-gray-600 hover:text-gray-900">{{ __('Ver') }}</a>
                                    <a href="{{ route('productos.edit', $p) }}" class="ml-3 text-indigo-600 hover:text-indigo-800">{{ __('Editar') }}</a>
                                    <form action="{{ route('productos.destroy', $p) }}" method="POST" class="inline ml-3" onsubmit="return confirm('{{ __('¿Eliminar?') }}');">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-800">{{ __('Eliminar') }}</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">{{ __('No hay registros.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-2">{{ $productos->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
