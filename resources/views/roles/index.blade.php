<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Roles y permisos') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('permissions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">{{ __('Permisos') }}</a>
                <a href="{{ route('roles.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700">{{ __('Nuevo rol') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))<p class="mb-4 px-4 py-2 rounded-md bg-green-50 text-green-700">{{ session('success') }}</p>@endif
            @if (session('error'))<p class="mb-4 px-4 py-2 rounded-md bg-red-50 text-red-700">{{ session('error') }}</p>@endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Rol') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Permisos') }}</th>
                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Usuarios') }}</th>
                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($roles as $role)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $role->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $role->permissions->pluck('name')->join(', ') ?: '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-right tabular-nums">{{ $role->users_count }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-800">{{ __('Editar') }}</a>
                                    @if($role->users_count === 0)
                                        <form action="{{ route('roles.destroy', $role) }}" method="POST" class="inline ml-3" onsubmit="return confirm('{{ __('¿Eliminar este rol?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">{{ __('Eliminar') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">{{ __('No hay roles.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
