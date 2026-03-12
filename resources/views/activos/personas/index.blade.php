<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center flex-wrap gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Personas') }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('personas.import') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">{{ __('Importar') }}</a>
                <a href="{{ route('personas.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">{{ __('Nueva') }}</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))<p class="mb-4 px-4 py-2 rounded-md bg-green-50 text-green-700">{{ session('success') }}</p>@endif
            @if (session('error'))<p class="mb-4 px-4 py-2 rounded-md bg-red-50 text-red-700">{{ session('error') }}</p>@endif
            <form method="GET" class="mb-4 flex gap-2 flex-wrap items-end">
                <div>
                    <label for="filter_q" class="block text-xs font-medium text-gray-500 mb-0.5">{{ __('Buscar') }}</label>
                    <input id="filter_q" type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Documento / nombre') }}" class="rounded-md border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="filter_regional_id" class="block text-xs font-medium text-gray-500 mb-0.5">{{ __('Regional') }}</label>
                    <select id="filter_regional_id" name="regional_id" class="rounded-md border-gray-300 shadow-sm min-w-[140px]">
                        <option value="">{{ __('Todas') }}</option>
                        @foreach($regionales as $r)
                            <option value="{{ $r->id }}" @selected(request('regional_id') == $r->id)>{{ $r->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter_department_id" class="block text-xs font-medium text-gray-500 mb-0.5">{{ __('Departamento') }}</label>
                    <select id="filter_department_id" name="department_id" class="rounded-md border-gray-300 shadow-sm min-w-[160px]">
                        <option value="">{{ __('Todos') }}</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" @selected(request('department_id') == $d->id)>{{ $d->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter_account_status" class="block text-xs font-medium text-gray-500 mb-0.5">{{ __('Estado') }}</label>
                    <select id="filter_account_status" name="account_status" class="rounded-md border-gray-300 shadow-sm">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="0" @selected(request('account_status') === '0')>{{ __('Inactivo') }}</option>
                        <option value="1" @selected(request('account_status') === '1')>{{ __('Activo') }}</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Filtrar') }}</button>
                @if(request()->hasAny(['q','regional_id','department_id','account_status']))
                    <a href="{{ route('personas.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Limpiar') }}</a>
                @endif
            </form>

            @if($regionalSeleccionada && $personasPorDepartamentoEnRegional->isNotEmpty())
                <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-3">{{ __('Total de personas por departamento en') }} {{ $regionalSeleccionada->nombre }}</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Departamento') }}</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Total personas') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($personasPorDepartamentoEnRegional as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ $row->department_name ?? __('Sin departamento') }}</td>
                                        <td class="px-4 py-3 text-right font-medium">{{ number_format($row->total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-700">{{ __('Total') }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">{{ number_format($personasPorDepartamentoEnRegional->sum('total')) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if($hayPersonasSinUsuario)
                    <form action="{{ route('personas.crear-usuarios-todos') }}" method="POST" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex items-center gap-3">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">{{ __('Crear todos los usuarios que falten') }}</button>
                        <span class="text-sm text-gray-500">{{ __('Crea un usuario de acceso para cada persona con email que aún no tenga. Contraseña inicial: documento de identidad.') }}</span>
                    </form>
                @endif
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Documento') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nombre') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Regional') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Departamento') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Estado') }}</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Usuario') }}</th>
                            <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($personas as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm">{{ $p->documento_identidad }}</td>
                                <td class="px-6 py-4 text-sm">{{ $p->nombre ?? $p->full_name }}</td>
                                <td class="px-6 py-4 text-sm">{{ $p->regional?->nombre ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm">{{ $p->department?->nombre ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($p->account_status === '1')<span class="text-green-700">{{ __('Activo') }}</span>@elseif($p->account_status === '0')<span class="text-gray-500">{{ __('Inactivo') }}</span>@else<span class="text-gray-400">—</span>@endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($p->email_address && isset($emailsConUsuario[$p->email_address]))<span class="text-green-700">{{ __('Sí') }}</span>@elseif($p->email_address)<span class="text-amber-600">{{ __('Pendiente') }}</span>@else<span class="text-gray-400">—</span>@endif
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <a href="{{ route('personas.show', $p) }}" class="text-gray-600 hover:text-gray-900">{{ __('Ver') }}</a>
                                    <a href="{{ route('personas.edit', $p) }}" class="ml-3 text-indigo-600 hover:text-indigo-800">{{ __('Editar') }}</a>
                                    <form action="{{ route('personas.destroy', $p) }}" method="POST" class="inline ml-3" onsubmit="return confirm('{{ __('¿Eliminar?') }}');">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-800">{{ __('Eliminar') }}</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">{{ __('No hay registros.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-2">{{ $personas->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
