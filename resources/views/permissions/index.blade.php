<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Permisos') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">{{ __('Roles') }}</a>
                <a href="{{ route('permissions.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-700">{{ __('Nuevo permiso') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))<p class="mb-4 px-4 py-2 rounded-md bg-green-50 text-green-700">{{ session('success') }}</p>@endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nombre') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Guard') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($permissions as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $p->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $p->guard_name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-6 py-4 text-center text-gray-500">{{ __('No hay permisos.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
