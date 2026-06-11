<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-slate-100">Crear cuenta</h1>
        <p class="mt-1 text-sm text-slate-300">Registrate para participar en el torneo interno de pronosticos.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <x-input-label for="name" value="Nombre completo" class="text-slate-200" />
            <x-text-input id="name" class="mt-1 block w-full rounded-xl border-slate-600 bg-slate-900 text-slate-100 focus:border-rose-500 focus:ring-rose-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Correo corporativo" class="text-slate-200" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-slate-600 bg-slate-900 text-slate-100 focus:border-rose-500 focus:ring-rose-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="phone_number" value="Celular para WhatsApp" class="text-slate-200" />
            <x-text-input id="phone_number" class="mt-1 block w-full rounded-xl border-slate-600 bg-slate-900 text-slate-100 focus:border-rose-500 focus:ring-rose-500" type="tel" name="phone_number" :value="old('phone_number')" required autocomplete="tel" placeholder="+57 300 123 4567" />
            <p class="mt-1 text-xs text-slate-400">Incluye el indicativo del pais para poder enviarte notificaciones por WhatsApp.</p>
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Contrasena" class="text-slate-200" />
            <x-text-input id="password" class="mt-1 block w-full rounded-xl border-slate-600 bg-slate-900 text-slate-100 focus:border-rose-500 focus:ring-rose-500" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar contrasena" class="text-slate-200" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-xl border-slate-600 bg-slate-900 text-slate-100 focus:border-rose-500 focus:ring-rose-500" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
            <a class="text-sm text-slate-300 hover:text-slate-100" href="{{ route('login') }}">Ya tienes cuenta?</a>
            <x-button type="submit" class="w-full sm:w-auto">Registrarme</x-button>
        </div>
    </form>
</x-guest-layout>
