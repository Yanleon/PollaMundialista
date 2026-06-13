@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="section-title">Equipos</h1>
                <p class="section-subtitle">Gestiona el catalogo oficial de selecciones participantes.</p>
            </div>
            <a href="{{ route('admin.teams.create') }}" class="inline-flex items-center rounded-full bg-gradient-to-r from-white via-white to-rose-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:scale-[1.02] hover:shadow-[0_0_22px_rgba(255,31,69,0.35)]">Nuevo equipo</a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-500/40 bg-green-600/10 px-4 py-3 text-sm text-green-200">{{ session('success') }}</div>
        @endif

        @php
            $groupedTeams = $teams->groupBy(fn ($team) => $team->group_name ?: 'Sin grupo');
        @endphp

        @if ($teams->isEmpty())
            <x-card>
                <p class="text-center text-sm text-slate-400">No hay equipos registrados.</p>
            </x-card>
        @else
            <div class="team-groups-board">
                @foreach ($groupedTeams as $groupName => $groupTeams)
                    <section class="team-group-card">
                        <header class="team-group-head">
                            <div>
                                <p class="team-group-kicker">Grupo</p>
                                <h2>{{ $groupName }}</h2>
                            </div>
                            <x-badge variant="muted">{{ $groupTeams->count() }} equipos</x-badge>
                        </header>

                        <div class="team-group-list">
                            @foreach ($groupTeams as $team)
                                <article class="team-strip">
                                    <div class="team-strip-main">
                                        <x-team-flag :team="$team" size="lg" class="team-strip-flag" />
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-black text-slate-950">{{ $team->name }}</p>
                                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                                <span class="rounded-full bg-slate-950/10 px-2 py-0.5 text-[10px] font-black uppercase tracking-[0.12em] text-slate-700">{{ $team->code }}</span>
                                                <span class="rounded-full px-2 py-0.5 text-[10px] font-black uppercase tracking-[0.12em] {{ $team->status === 'active' ? 'bg-emerald-500/15 text-emerald-800' : 'bg-rose-500/15 text-rose-800' }}">{{ $team->status }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="team-strip-actions">
                                        <a href="{{ route('admin.teams.edit', $team) }}" class="team-action-edit">Editar</a>
                                        <form method="POST" action="{{ route('admin.teams.destroy', $team) }}" onsubmit="return confirm('Deseas eliminar este equipo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="team-action-delete">Eliminar</button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif
    </div>
@endsection
