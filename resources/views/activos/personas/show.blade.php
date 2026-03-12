<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Persona') }}</h2>
            <a href="{{ route('personas.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('← Volver al listado') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <nav class="mb-4 text-sm text-gray-500">
                <a href="{{ route('personas.index') }}" class="hover:text-gray-700">{{ __('Personas') }}</a>
                <span class="mx-1">/</span>
                <span class="text-gray-700">{{ $persona->documento_identidad }}</span>
            </nav>
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <dl class="grid gap-3 sm:grid-cols-2">
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Documento') }}</dt><dd class="mt-0.5 text-gray-900">{{ $persona->documento_identidad }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Nombre') }}</dt><dd class="mt-0.5 text-gray-900">{{ $persona->nombre ?? $persona->full_name }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Account Status') }}</dt><dd class="mt-0.5 text-gray-900">@if($persona->account_status === '1')<span class="text-green-700">{{ __('Activo') }}</span>@elseif($persona->account_status === '0')<span class="text-gray-500">{{ __('Inactivo') }}</span>@else<span class="text-gray-400">—</span>@endif</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Departamento') }}</dt><dd class="mt-0.5 text-gray-900">{{ $persona->department?->nombre ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Oficina') }}</dt><dd class="mt-0.5 text-gray-900">{{ $persona->office?->nombre ?? '—' }}</dd></div>
                    <div><dt class="text-sm font-medium text-gray-500">{{ __('Cargo') }}</dt><dd class="mt-0.5 text-gray-900">{{ $persona->title?->nombre ?? '—' }}</dd></div>
                    @if($persona->email_address)<div class="sm:col-span-2"><dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt><dd class="mt-0.5 text-gray-900">{{ $persona->email_address }}</dd></div>@endif
                    <div class="sm:col-span-2"><dt class="text-sm font-medium text-gray-500">{{ __('Usuario de acceso') }}</dt><dd class="mt-0.5 text-gray-900">@if($tieneUsuario)<span class="text-green-700">{{ __('Sí') }}</span>@elseif($puedeCrearUsuario)<form action="{{ route('personas.crear-usuario', $persona) }}" method="POST" class="inline">@csrf<button type="submit" class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded-md hover:bg-indigo-700">{{ __('Crear usuario') }}</button></form><span class="ml-2 text-gray-500 text-sm">{{ __('Contraseña inicial: documento de identidad') }}</span>@else<span class="text-gray-400">—</span> {{ __('Indique email en Editar para crear usuario.') }}@endif</dd></div>
                </dl>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('personas.edit', $persona) }}" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Editar') }}</a>
                    @if($puedeCrearUsuario)
                        <form action="{{ route('personas.crear-usuario', $persona) }}" method="POST" class="inline">@csrf<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">{{ __('Crear usuario de acceso') }}</button></form>
                    @endif
                    <a href="{{ route('activos-crv.index', ['persona_id' => $persona->id]) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Ver activos CRV') }}</a>
                    <a href="{{ route('personas.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Volver') }}</a>
                </div>

                @if($persona->activosCrv->isNotEmpty())
                    <h3 class="mt-8 text-sm font-medium text-gray-700">{{ __('Activos asignados') }} ({{ $persona->activosCrv->count() }})</h3>
                    <div class="mt-2 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Placa') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Producto') }}</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($persona->activosCrv as $a)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ $a->placa ?? '—' }}</td>
                                        <td class="px-4 py-3">{{ $a->producto?->nombre ?? $a->producto?->codigo ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right">
                                        <a href="{{ route('activos-crv.show', $a) }}" class="text-gray-600 hover:text-gray-900">{{ __('Ver') }}</a>
                                        <a href="{{ route('activos-crv.edit', $a) }}" class="ml-3 text-indigo-600 hover:text-indigo-800">{{ __('Editar') }}</a>
                                    </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="mt-8 text-sm text-gray-500">{{ __('No tiene activos CRV asignados.') }}</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
