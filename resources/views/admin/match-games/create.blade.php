@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <div>
            <h1 class="section-title">Crear partido</h1>
            <p class="section-subtitle">Define fase, equipos y ventana de prediccion.</p>
        </div>

        <x-card>
            <form method="POST" action="{{ route('admin.match-games.store') }}" class="space-y-4">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Fase</label>
                        <input name="phase" value="{{ old('phase') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                        @error('phase') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Grupo</label>
                        <input name="group_name" value="{{ old('group_name') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                        @error('group_name') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Equipo local</label>
                        <select name="home_team_id" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                            <option value="">Seleccionar</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}" @selected(old('home_team_id') == $team->id)>{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('home_team_id') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Equipo visitante</label>
                        <select name="away_team_id" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                            <option value="">Seleccionar</option>
                            @foreach ($teams as $team)
                                <option value="{{ $team->id }}" @selected(old('away_team_id') == $team->id)>{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('away_team_id') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Fecha del partido</label>
                        <input type="datetime-local" name="match_date" value="{{ old('match_date') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                        @error('match_date') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Fecha limite de prediccion</label>
                        <input type="datetime-local" name="prediction_deadline" value="{{ old('prediction_deadline') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                        @error('prediction_deadline') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Estado</label>
                        <select name="status" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100" required>
                            @foreach (['scheduled', 'live', 'finished', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected(old('status', 'scheduled') === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Goles local</label>
                        <input type="number" min="0" max="30" name="home_score" value="{{ old('home_score') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                        @error('home_score') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">Goles visitante</label>
                        <input type="number" min="0" max="30" name="away_score" value="{{ old('away_score') }}" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                        @error('away_score') <p class="mt-1 text-xs text-red-300">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-button type="submit">Guardar partido</x-button>
                    <a href="{{ route('admin.match-games.index') }}" class="rounded-lg bg-slate-700 px-4 py-2 text-sm font-semibold text-slate-100 hover:bg-slate-600">Cancelar</a>
                </div>
            </form>
        </x-card>
    </div>
@endsection
