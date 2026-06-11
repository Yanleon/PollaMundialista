@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <h1 class="section-title">Editar participante</h1>
            <p class="section-subtitle">Actualiza los datos de acceso de {{ $user->name }}.</p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-500/40 bg-green-600/10 px-4 py-3 text-sm text-green-200">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-500/40 bg-red-600/10 px-4 py-3 text-sm text-red-200">{{ session('error') }}</div>
        @endif

        <x-card>
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Nombre</label>
                        <input name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                        @error('name') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Correo</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                        @error('email') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Celular</label>
                        <input name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                        @error('phone_number') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Area</label>
                        <input name="department" value="{{ old('department', $user->department) }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                        @error('department') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Estado</label>
                        <select name="status" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                            <option value="active" @selected(old('status', $user->status) === 'active')>active</option>
                            <option value="inactive" @selected(old('status', $user->status) === 'inactive')>inactive</option>
                        </select>
                        @error('status') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-button type="submit">Actualizar participante</x-button>
                    <a href="{{ route('admin.users.index') }}" class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-600">Volver</a>
                </div>
            </form>
        </x-card>

        <x-card>
            <form method="POST" action="{{ route('admin.users.update-password', $user) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <h2 class="text-lg font-semibold text-slate-100">Cambiar contrasena</h2>
                    <p class="text-sm text-slate-400">Define una nueva contrasena para este participante.</p>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Nueva contrasena</label>
                        <input type="password" name="password" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                        @error('password') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Confirmar contrasena</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                    </div>
                </div>

                <x-button type="submit" variant="secondary">Cambiar contrasena</x-button>
            </form>
        </x-card>
    </div>
@endsection
