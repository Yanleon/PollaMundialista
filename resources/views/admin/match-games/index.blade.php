@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="section-title">Partidos</h1>
                <p class="section-subtitle">Programa partidos, actualiza estados y carga resultados finales.</p>
            </div>
            <a href="{{ route('admin.match-games.create') }}" class="inline-flex items-center rounded-full bg-gradient-to-r from-white via-white to-rose-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:scale-[1.02] hover:shadow-[0_0_22px_rgba(255,31,69,0.35)]">Nuevo partido</a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-500/40 bg-green-600/10 px-4 py-3 text-sm text-green-200">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-500/40 bg-red-600/10 px-4 py-3 text-sm text-red-200">{{ session('error') }}</div>
        @endif

        <x-card title="Notificaciones del dia" subtitle="Envio por correo a participantes activos y opcional webhook de WhatsApp.">
            <div class="mb-3 flex flex-wrap items-center gap-2">
                <x-badge variant="info">Partidos hoy: {{ $todayMatches->count() }}</x-badge>
                <x-badge variant="muted">Fecha: {{ now()->format('d/m/Y') }}</x-badge>
            </div>

            @if ($todayMatches->isEmpty())
                <p class="text-sm text-slate-300">No hay partidos programados para hoy.</p>
            @else
                <ul class="mb-4 space-y-2 text-sm text-slate-200">
                    @foreach ($todayMatches as $todayMatch)
                        <li class="rounded-lg bg-slate-800/70 px-3 py-2">
                            {{ $todayMatch->match_date?->format('H:i') }} -
                            {{ $todayMatch->homeTeam?->name }} vs {{ $todayMatch->awayTeam?->name }}
                            <span class="text-slate-400">({{ $todayMatch->phase }})</span>
                        </li>
                    @endforeach
                </ul>

                <form method="POST" action="{{ route('admin.match-games.notify-today') }}">
                    @csrf
                    <x-button type="submit" variant="primary">Enviar notificaciones de hoy</x-button>
                </form>
            @endif
        </x-card>

        @if ($matchDays->isEmpty())
            <x-card>
                <p class="text-center text-sm text-slate-400">No hay partidos registrados.</p>
            </x-card>
        @else
            @php
                $initialDay = $matchDays->firstWhere(fn (array $day): bool => (bool) $day['date']?->isToday()) ?? $matchDays->first();
            @endphp

            <div x-data="{ selectedDay: '{{ $initialDay['anchor'] }}' }" x-init="if (window.location.hash && window.location.hash.length > 1) selectedDay = window.location.hash.slice(1)">
            <div class="-mx-4 mb-6 overflow-x-auto border-y border-slate-800 bg-slate-950/95 px-4 py-3 sm:mx-0 sm:rounded-2xl sm:border sm:bg-slate-950/80">
                <div class="flex min-w-max gap-2">
                    @foreach ($matchDays as $day)
                        @php
                            $isToday = $day['date']?->isToday();
                            $isTomorrow = $day['date']?->isTomorrow();
                            $label = $day['date'] ? ($isToday ? 'Hoy' : ($isTomorrow ? 'Manana' : $day['date']->locale('es')->isoFormat('dddd, D MMM'))) : 'Sin fecha';
                            $allFinished = $day['pending_matches'] === 0;
                        @endphp
                        <button type="button" x-on:click="selectedDay = '{{ $day['anchor'] }}'; history.replaceState(null, '', '#{{ $day['anchor'] }}')" class="group flex min-w-36 flex-col rounded-2xl border px-4 py-3 text-left transition" x-bind:class="selectedDay === '{{ $day['anchor'] }}' ? 'border-white bg-white text-slate-950 shadow-[0_0_24px_rgba(255,255,255,0.18)]' : 'border-slate-800 bg-slate-900/85 text-slate-200 hover:border-rose-500/60 hover:bg-slate-900'">
                            <span class="text-sm font-bold capitalize leading-tight">{{ $label }}</span>
                            <span class="mt-1 text-xs" x-bind:class="selectedDay === '{{ $day['anchor'] }}' ? 'text-slate-600' : 'text-slate-400 group-hover:text-slate-300'">{{ $day['matches']->count() }} {{ $day['matches']->count() === 1 ? 'partido' : 'partidos' }}</span>
                            <span class="mt-2 h-1 rounded-full {{ $allFinished ? 'bg-emerald-400' : 'bg-rose-500/70' }}"></span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="space-y-8">
                @foreach ($matchDays as $day)
                    @php
                        $heading = $day['date'] ? $day['date']->locale('es')->isoFormat('dddd, D [de] MMMM') : 'Sin fecha';
                    @endphp

                    <section id="{{ $day['anchor'] }}" x-show="selectedDay === '{{ $day['anchor'] }}'" class="space-y-4">
                        <div class="flex flex-col gap-2 border-b border-slate-800 pb-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rose-300">Fecha de juego</p>
                                <h2 class="mt-1 text-2xl font-black capitalize text-slate-100">{{ $heading }}</h2>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <x-badge variant="info">{{ $day['matches']->count() }} {{ $day['matches']->count() === 1 ? 'partido' : 'partidos' }}</x-badge>
                                <x-badge variant="success">{{ $day['finished_matches'] }} finalizados</x-badge>
                                @if ($day['pending_matches'] > 0)
                                    <x-badge variant="warning">{{ $day['pending_matches'] }} pendientes</x-badge>
                                @endif
                            </div>
                        </div>

                        <div class="grid gap-4 xl:grid-cols-2">
                            @foreach ($day['matches'] as $matchGame)
                                @php
                                    $badgeVariant = match($matchGame->status) {
                                        'scheduled' => 'info',
                                        'live' => 'warning',
                                        'finished' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'muted',
                                    };
                                @endphp

                                <article class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-950/70 shadow-[0_22px_60px_rgba(2,6,23,0.32)]">
                                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 bg-slate-900/65 px-4 py-3">
                                        <div class="text-sm text-slate-300">
                                            <span class="font-semibold text-slate-100">{{ $matchGame->match_date?->format('H:i') ?? '--:--' }}</span>
                                            <span class="mx-2 text-slate-600">/</span>
                                            {{ $matchGame->phase }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <x-badge :variant="$badgeVariant">{{ $matchGame->status }}</x-badge>
                                            @if ($matchGame->getFinalResult())
                                                <span class="rounded-full bg-rose-500/15 px-3 py-1 text-xs font-bold text-rose-200 ring-1 ring-inset ring-rose-400/30">{{ $matchGame->getFinalResult() }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="space-y-4 p-4">
                                        <form id="result-form-{{ $matchGame->id }}" method="POST" action="{{ route('admin.match-games.update-result', $matchGame) }}" class="grid gap-3">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="finished">
                                            <input type="hidden" name="return_anchor" value="{{ $day['anchor'] }}">

                                            <label class="grid grid-cols-[1fr_5rem] items-center gap-3 rounded-2xl border border-slate-800 bg-slate-900/55 px-3 py-3">
                                                <span class="flex min-w-0 items-center gap-3 text-sm font-bold text-slate-100">
                                                    <x-team-flag :team="$matchGame->homeTeam" />
                                                    <span class="truncate">{{ $matchGame->homeTeam?->name ?? 'Equipo local' }}</span>
                                                </span>
                                                <input type="number" name="home_score" min="0" max="30" value="{{ old('home_score', $matchGame->home_score) }}" aria-label="Goles de {{ $matchGame->homeTeam?->name ?? 'equipo local' }}" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-center text-lg font-black text-slate-100 focus:border-rose-500 focus:outline-none">
                                            </label>

                                            <label class="grid grid-cols-[1fr_5rem] items-center gap-3 rounded-2xl border border-slate-800 bg-slate-900/55 px-3 py-3">
                                                <span class="flex min-w-0 items-center gap-3 text-sm font-bold text-slate-100">
                                                    <x-team-flag :team="$matchGame->awayTeam" />
                                                    <span class="truncate">{{ $matchGame->awayTeam?->name ?? 'Equipo visitante' }}</span>
                                                </span>
                                                <input type="number" name="away_score" min="0" max="30" value="{{ old('away_score', $matchGame->away_score) }}" aria-label="Goles de {{ $matchGame->awayTeam?->name ?? 'equipo visitante' }}" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 text-center text-lg font-black text-slate-100 focus:border-rose-500 focus:outline-none">
                                            </label>
                                        </form>

                                        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-800 pt-4">
                                            <p class="text-xs text-slate-400">Limite: {{ $matchGame->prediction_deadline?->format('d/m/Y H:i') ?? 'Sin limite' }}</p>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a href="{{ route('admin.match-games.edit', $matchGame) }}" class="rounded-full border border-slate-700 bg-slate-900 px-3 py-2 text-xs font-semibold text-slate-100 hover:border-rose-500/60 hover:text-rose-200">Editar</a>
                                                <form method="POST" action="{{ route('admin.match-games.destroy', $matchGame) }}" onsubmit="return confirm('Deseas eliminar este partido?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-full bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-500">Eliminar</button>
                                                </form>
                                                <x-button type="submit" variant="success" form="result-form-{{ $matchGame->id }}" class="px-4 py-2 text-xs">Guardar resultado</x-button>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
            </div>
        @endif
    </div>
@endsection
