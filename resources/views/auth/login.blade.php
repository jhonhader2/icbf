<x-guest-layout>
    <x-auth-session-status class="mb-4 text-center text-sm text-emerald-600" :status="session('status')" />

    <h1 class="text-xl font-semibold text-slate-800">{{ __('Log in') }}</h1>
    <p class="mt-1 text-sm text-slate-500">{{ __('Ingresa tus credenciales para acceder') }}</p>

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1.5 w-full rounded-lg border-slate-300 focus:border-slate-500 focus:ring-slate-500"
                type="email"
                name="email"
                :value="old('email')"
                placeholder="nombre@ejemplo.com"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <div x-data="{ showPassword: false }">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1.5">
                <input
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    :autocomplete="showPassword ? 'off' : 'current-password'"
                    name="password"
                    required
                    class="block w-full rounded-lg border-slate-300 focus:border-slate-500 focus:ring-slate-500 shadow-sm pr-10"
                />
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-400 rounded"
                    :aria-label="showPassword ? '{{ __('Ocultar contraseña') }}' : '{{ __('Mostrar contraseña') }}'"
                >
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-slate-300 text-slate-600 focus:ring-slate-500 size-4"
                    name="remember"
                >
                <span class="ms-2 text-sm text-slate-600">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="text-sm text-slate-600 hover:text-slate-800 underline underline-offset-2 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-1 rounded"
                >
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="pt-1">
            <x-primary-button class="w-full justify-center rounded-lg py-2.5 text-sm font-medium">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
