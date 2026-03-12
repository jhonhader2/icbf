<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar persona') }}</h2>
            <a href="{{ route('personas.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('← Volver al listado') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <nav class="mb-4 text-sm text-gray-500">
                <a href="{{ route('personas.index') }}" class="hover:text-gray-700">{{ __('Personas') }}</a>
                <span class="mx-1">/</span>
                <span class="text-gray-700">{{ __('Editar') }} {{ $persona->documento_identidad }}</span>
            </nav>
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('success'))<p class="mb-4 px-4 py-2 rounded-md bg-green-50 text-green-700">{{ session('success') }}</p>@endif
                @if ($errors->any())<p class="mb-4 px-4 py-2 rounded-md bg-red-50 text-red-700">{{ $errors->first() }}</p>@endif
                <form method="POST" action="{{ route('personas.update', $persona) }}">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="persona_documento_identidad" class="block text-sm font-medium text-gray-700">{{ __('Documento identidad') }}</label>
                            <input id="persona_documento_identidad" name="documento_identidad" value="{{ old('documento_identidad', $persona->documento_identidad) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('documento_identidad')<p class="mt-1 text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="persona_nombre" class="block text-sm font-medium text-gray-700">{{ __('Nombre') }}</label>
                            <input id="persona_nombre" name="nombre" value="{{ old('nombre', $persona->nombre) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="persona_department_id" class="block text-sm font-medium text-gray-700">{{ __('Departamento') }}</label>
                            <select id="persona_department_id" name="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">--</option>@foreach($departments as $d)<option value="{{ $d->id }}" @selected(old('department_id', $persona->department_id) == $d->id)>{{ $d->nombre }}</option>@endforeach</select>
                        </div>
                        <div>
                            <label for="persona_regional_id" class="block text-sm font-medium text-gray-700">{{ __('Regional') }}</label>
                            <select id="persona_regional_id" name="regional_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">--</option>@foreach($regionales as $r)<option value="{{ $r->id }}" @selected(old('regional_id', $persona->regional_id) == $r->id)>{{ $r->nombre }}</option>@endforeach</select>
                        </div>
                        <div>
                            <label for="persona_account_status" class="block text-sm font-medium text-gray-700">{{ __('Account Status') }}</label>
                            <select id="persona_account_status" name="account_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">--</option><option value="0" @selected(old('account_status', $persona->account_status) === '0')>{{ __('Inactivo') }}</option><option value="1" @selected(old('account_status', $persona->account_status) === '1')>{{ __('Activo') }}</option></select>
                        </div>
                        <div>
                            <label for="persona_office_id" class="block text-sm font-medium text-gray-700">{{ __('Oficina') }}</label>
                            <select id="persona_office_id" name="office_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">--</option>@foreach($offices as $o)<option value="{{ $o->id }}" @selected(old('office_id', $persona->office_id) == $o->id)>{{ $o->nombre }}</option>@endforeach</select>
                        </div>
                        <div>
                            <label for="persona_title_id" class="block text-sm font-medium text-gray-700">{{ __('Cargo') }}</label>
                            <select id="persona_title_id" name="title_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">--</option>@foreach($titles as $t)<option value="{{ $t->id }}" @selected(old('title_id', $persona->title_id) == $t->id)>{{ $t->nombre }}</option>@endforeach</select>
                        </div>
                        <div>
                            <label for="persona_email_address" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
                            <input id="persona_email_address" name="email_address" type="email" value="{{ old('email_address', $persona->email_address) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Actualizar') }}</button>
                        <a href="{{ route('personas.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Cancelar') }}</a>
                        <a href="{{ route('personas.show', $persona) }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">{{ __('Ver detalle') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
