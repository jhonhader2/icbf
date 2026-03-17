<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar rol') }}: {{ $role->name }}</h2>
            <a href="{{ route('roles.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">{{ __('Volver a roles') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <ul class="mb-4 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            @endif

            <form action="{{ route('roles.update', $role) }}" method="POST" class="bg-white shadow-sm sm:rounded-lg p-6">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nombre del rol') }}</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $role->name) }}" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="mb-4">
                    <span class="block text-sm font-medium text-gray-700 mb-2">{{ __('Permisos') }}</span>
                    <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                        @php
                            $rolePermissionIds = $role->permissions->pluck('id')->all();
                            $selected = old('permissions', $rolePermissionIds);
                        @endphp
                        @forelse($permissions as $p)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="permissions[]" value="{{ $p->id }}" {{ in_array($p->id, $selected) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $p->name }}</span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500">{{ __('No hay permisos.') }}</p>
                        @endforelse
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Guardar') }}</button>
                    <a href="{{ route('roles.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Cancelar') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
