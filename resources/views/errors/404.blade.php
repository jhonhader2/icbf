@php
    $isActivoCrv = request()->is('activos-crv/*');
    $isPersona = request()->is('personas/*');
    $isCpu = request()->is('cpus/*');
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Página no encontrada') }}</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-8 text-center">
                    <p class="text-6xl font-light text-gray-300 select-none">404</p>
                    <h1 class="mt-4 text-xl font-semibold text-gray-800">{{ __('No se encontró la información') }}</h1>
                    <p class="mt-2 text-gray-600">
                        @if($isActivoCrv)
                            {{ __('El activo CRV que buscas no existe o no tienes permiso para verlo.') }}
                        @elseif($isPersona)
                            {{ __('La persona que buscas no existe o no tienes permiso para verla.') }}
                        @elseif($isCpu)
                            {{ __('El CPU que buscas no existe o no tienes permiso para verlo.') }}
                        @else
                            {{ __('La página o el recurso al que intentas acceder no existe o ya no está disponible.') }}
                        @endif
                    </p>
                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        @if($isActivoCrv)
                            <a href="{{ route('activos-crv.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Ver listado de Activos CRV') }}</a>
                        @elseif($isPersona)
                            <a href="{{ route('personas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Ver listado de Personas') }}</a>
                        @elseif($isCpu)
                            <a href="{{ route('cpus.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">{{ __('Ver listado de CPUs') }}</a>
                        @endif
                        <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Volver atrás') }}</a>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">{{ __('Ir al inicio') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
