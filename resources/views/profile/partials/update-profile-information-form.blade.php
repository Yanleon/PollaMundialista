<section>
    <header>
        <h2 class="text-lg font-medium text-slate-100">
            {{ __('Informacion del perfil') }}
        </h2>

        <p class="mt-1 text-sm text-slate-300">
            {{ __('Actualiza tu informacion personal y tu correo de acceso.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="mt-2 text-sm text-slate-200">
                        {{ __('Tu correo aun no ha sido verificado.') }}

                        <button form="send-verification" class="rounded-md text-sm text-slate-300 underline transition hover:text-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-0">
                            {{ __('Haz clic aqui para reenviar el correo de verificacion.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-emerald-300">
                            {{ __('Enviamos un nuevo enlace de verificacion a tu correo.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Guardar') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-300"
                >{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
</section>
