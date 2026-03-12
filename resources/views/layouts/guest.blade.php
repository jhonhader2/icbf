<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} — {{ __('Login') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if (file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>
        @endif
        <style>
            body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="text-slate-800 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-8 sm:py-12 bg-gradient-to-br from-slate-50 via-slate-100 to-blue-50">
            <a href="/" class="flex flex-col items-center gap-2 text-slate-600 hover:text-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 rounded-lg">
                <x-application-logo class="w-16 h-16 sm:w-20 sm:h-20 fill-current" />
                <span class="text-sm font-semibold tracking-tight">{{ config('app.name') }}</span>
            </a>

            <div class="w-full sm:max-w-md mt-8 sm:mt-10 px-6 sm:px-8 py-8 bg-white/90 backdrop-blur-sm border border-slate-200/80 shadow-xl shadow-slate-200/50 rounded-2xl">
                {{ $slot }}
            </div>
            <p class="mt-6 text-xs text-slate-500">{{ __('Sistema de gestión de activos') }}</p>
        </div>
    </body>
</html>
