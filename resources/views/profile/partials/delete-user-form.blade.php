<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-slate-100">
            {{ __('Eliminar cuenta') }}
        </h2>

        <p class="mt-1 text-sm text-slate-300">
            {{ __('Esta accion elimina tu cuenta y todos sus datos de forma permanente.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Eliminar cuenta') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-slate-100">
                {{ __('Estas seguro de eliminar tu cuenta?') }}
            </h2>

            <p class="mt-1 text-sm text-slate-300">
                {{ __('Confirma tu contrasena para continuar con la eliminacion definitiva de la cuenta.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Contrasena') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Eliminar cuenta') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
