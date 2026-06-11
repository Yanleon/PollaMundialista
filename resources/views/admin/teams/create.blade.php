@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div>
            <h1 class="section-title">Crear equipo</h1>
            <p class="section-subtitle">Registra un nuevo equipo para el torneo.</p>
        </div>

        <x-card>
            <form method="POST" action="{{ route('admin.teams.store') }}" class="space-y-4">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Nombre</label>
                        <input name="name" value="{{ old('name') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                        @error('name') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Codigo</label>
                        <input name="code" value="{{ old('code') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                        @error('code') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Grupo</label>
                        <input name="group_name" value="{{ old('group_name') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                        @error('group_name') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Estado</label>
                        <select name="status" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                            <option value="active" @selected(old('status') === 'active')>active</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>inactive</option>
                        </select>
                        @error('status') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-200">URL de bandera</label>
                    <input type="url" name="flag_url" value="{{ old('flag_url') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                    @error('flag_url') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-button type="submit">Guardar equipo</x-button>
                    <a href="{{ route('admin.teams.index') }}" class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-600">Cancelar</a>
                </div>
            </form>
        </x-card>
    </div>
@endsection
