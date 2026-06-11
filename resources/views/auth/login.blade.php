<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-100">Iniciar sesion</h1>
        <p class="mt-1 text-sm text-slate-300">Accede a tu panel de la Polla Mundialista Empresarial 2026.</p>
    </div>

    <x-auth-session-status class="mb-4 text-sm text-green-300" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="email" value="Correo corporativo" class="text-slate-200" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-slate-600 bg-slate-900 text-slate-100 focus:border-rose-500 focus:ring-rose-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Contrasena" class="text-slate-200" />
            <x-text-input id="password" class="mt-1 block w-full rounded-xl border-slate-600 bg-slate-900 text-slate-100 focus:border-rose-500 focus:ring-rose-500" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-300">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-600 bg-slate-900 text-rose-600 focus:ring-rose-500">
            <span>Recordarme</span>
        </label>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
            @if (Route::has('password.request'))
                <a class="text-sm text-slate-300 hover:text-slate-100" href="{{ route('password.request') }}">Olvidaste tu contrasena?</a>
            @endif

            <x-button type="submit" class="w-full sm:w-auto">Entrar</x-button>
        </div>
    </form>
</x-guest-layout>
