<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Departamentos') }}</h2>
            <a href="{{ route('departments.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">{{ __('Nuevo') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <p class="mb-4 text-green-600">{{ session('success') }}</p>
            @endif
            <form method="GET" class="mb-4 flex gap-2">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Buscar por nombre') }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit" class="px-4 py-2 bg-gray-200 rounded-md">{{ __('Buscar') }}</button>
            </form>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nombre') }}</th>
                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($departments as $department)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $department->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $department->nombre }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('departments.edit', $department) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Editar') }}</a>
                                    <form action="{{ route('departments.destroy', $department) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('¿Eliminar?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 ml-2">{{ __('Eliminar') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">{{ __('No hay registros.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-2">{{ $departments->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
