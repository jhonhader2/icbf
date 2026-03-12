<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Importar Toma Parque (Data Equipos)') }}</h2>
            <a href="{{ route('cpus.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('← CPUs') }}</a>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))<p class="mb-4 text-green-600">{{ session('success') }}</p>@endif
            @if (session('error'))<p class="mb-4 text-red-600">{{ session('error') }}</p>@endif
            <div class="bg-white shadow-sm sm:rounded-lg p-6" x-data="{ importing: false }">
                <p class="mb-4">Sube el archivo <strong>Formato_Toma_Parque_2025.xlsx</strong>, hoja <strong>Data Equipos</strong>. La llave es <strong>PLACA CPU / PORTATIL</strong> (columna U). Se actualizarán o crearán registros en la tabla CPUs con: nombre máquina, tipo equipo, memoria RAM, SO, tipo SO, bits, discos, capacidad disco, IP, MAC, serial (en activo CRV si aplica), en garantía, Office, tarjeta red inalámbrica, fecha inventario, observaciones.</p>
                <form action="{{ route('toma-parque.import.store') }}" method="POST" enctype="multipart/form-data" @submit="importing = true">
                    @csrf
                    <input type="file" name="archivo" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-gray-100">
                    @error('archivo')<p class="mt-1 text-red-600 text-sm">{{ $message }}</p>@enderror
                    <div class="mt-4 flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md disabled:opacity-70 disabled:cursor-not-allowed" :disabled="importing">
                            <span x-show="!importing">{{ __('Importar Toma Parque') }}</span>
                            <span x-show="importing" x-cloak class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('Importando…') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
