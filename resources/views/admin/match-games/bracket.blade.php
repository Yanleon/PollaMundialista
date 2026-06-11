@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="section-title">Llaves eliminatorias</h1>
                <p class="section-subtitle">Crea y asigna equipos para octavos, cuartos, semifinal y final. Estos cruces alimentan automaticamente la pagina principal, dashboards y pronosticos.</p>
            </div>
            <a href="{{ route('admin.match-games.index') }}" class="inline-flex items-center rounded-full border border-slate-700 bg-slate-900 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-rose-500/60 hover:text-rose-200">Volver a partidos</a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-500/40 bg-green-600/10 px-4 py-3 text-sm text-green-200">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-500/40 bg-red-600/10 px-4 py-3 text-sm text-red-200">{{ session('error') }}</div>
        @endif

        <x-card>
            <div class="mb-5 rounded-2xl border border-sky-400/30 bg-sky-500/10 p-4 text-sm text-sky-100/80">
                <p class="font-semibold text-sky-100">Como funciona</p>
                <p class="mt-1">Cuando se conozcan los clasificados, crea el cruce pendiente o entra a "Asignar equipos". Al guardar el partido con fase Octavos, Cuartos, Semifinal o Final, se actualizan las vistas publicas y de participantes.</p>
            </div>

            <div class="grid gap-4 lg:grid-cols-4">
                @foreach ($bracketRounds as $round)
                    <div class="rounded-2xl border border-slate-700 bg-slate-900/70 p-4">
                        <div class="mb-3 flex items-center justify-between gap-2">
                            <div>
                                <h2 class="text-base font-semibold text-slate-100">{{ $round['label'] }}</h2>
                                <p class="text-xs text-slate-400">{{ $round['matches']->count() }} de {{ $round['slots'] }} cruces</p>
                            </div>
                            <x-badge variant="muted">{{ $round['slots'] }}</x-badge>
                        </div>

                        <div class="space-y-2">
                            @foreach (range(1, $round['slots']) as $slot)
                                @php
                                    $match = $round['matches']->get($slot - 1);
                                @endphp

                                @if ($match)
                                    <div class="rounded-xl border border-slate-700 bg-slate-950/70 p-3">
                                        <div class="space-y-1 text-sm font-semibold text-slate-100">
                                            <p class="flex items-center gap-2 truncate"><x-team-flag :team="$match->homeTeam" /> <span class="truncate">{{ $match->homeTeam?->name }}</span></p>
                                            <p class="flex items-center gap-2 truncate"><x-team-flag :team="$match->awayTeam" /> <span class="truncate">{{ $match->awayTeam?->name }}</span></p>
                                        </div>
                                        <p class="mt-1 text-xs text-slate-400">{{ $match->match_date?->format('d/m/Y H:i') }} · {{ $match->status }}</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <a href="{{ route('admin.match-games.edit', $match) }}" class="inline-flex rounded-md border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:border-rose-500/60 hover:text-rose-200">Asignar equipos</a>
                                            <form method="POST" action="{{ route('admin.match-games.destroy', $match) }}" onsubmit="return confirm('Deseas eliminar este cruce?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-md bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500">Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <div class="rounded-xl border border-dashed border-slate-700 bg-slate-950/40 p-3">
                                        <p class="text-sm text-slate-400">Cruce {{ $slot }} pendiente</p>
                                        <a href="{{ route('admin.match-games.create', ['phase' => $round['phase']]) }}" class="mt-2 inline-flex rounded-md bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500">Crear cruce</a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    </div>
@endsection
