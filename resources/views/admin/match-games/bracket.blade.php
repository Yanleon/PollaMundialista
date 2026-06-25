@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="section-title">Llaves eliminatorias</h1>
                <p class="section-subtitle">Crea y asigna equipos para dieciseisavos, octavos, cuartos, semifinal y final. Estos cruces alimentan automaticamente la pagina principal, dashboards y pronosticos.</p>
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
                <p class="mt-1">Cuando se conozcan los clasificados, crea el cruce pendiente o entra a "Asignar equipos". Al guardar el partido con fase Dieciseisavos, Octavos, Cuartos, Semifinal o Final, se actualizan las vistas publicas y de participantes.</p>
            </div>

            @php
                $splitRounds = $bracketRounds->whereIn('key', ['round_of_32', 'round_of_16', 'quarterfinals']);
                $remainingRounds = $bracketRounds->whereNotIn('key', ['round_of_32', 'round_of_16', 'quarterfinals']);
                $roundMeta = [
                    'round_of_32' => [
                        'subtitle' => 'Configura los dos caminos de la llave por separado.',
                        'accent' => 'border-rose-500/45',
                        'badge' => 'bg-amber-400/15 text-amber-100 ring-amber-300/40',
                    ],
                    'round_of_16' => [
                        'subtitle' => 'Mantiene la misma separacion por caminos para continuar la llave.',
                        'accent' => 'border-cyan-400/45',
                        'badge' => 'bg-rose-400/15 text-rose-100 ring-rose-300/40',
                    ],
                    'quarterfinals' => [
                        'subtitle' => 'Separa los dos lados de la llave antes de semifinales.',
                        'accent' => 'border-emerald-400/45',
                        'badge' => 'bg-emerald-400/15 text-emerald-100 ring-emerald-300/40',
                    ],
                ];
            @endphp

            <div class="space-y-4">
                @foreach ($splitRounds as $round)
                    @php
                        $meta = $roundMeta[$round['key']];
                        $halfSlots = (int) ($round['slots'] / 2);
                        $paths = [
                            ['label' => 'Camino A', 'start' => 1, 'end' => $halfSlots],
                            ['label' => 'Camino B', 'start' => $halfSlots + 1, 'end' => $round['slots']],
                        ];
                    @endphp

                    <section class="rounded-3xl border {{ $meta['accent'] }} bg-slate-950/60 p-4 shadow-[0_18px_50px_rgba(2,6,23,0.32)]">
                        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-50">{{ $round['label'] }}</h2>
                                <p class="text-sm text-slate-300">{{ $meta['subtitle'] }}</p>
                            </div>
                            <span class="inline-flex w-max items-center rounded-full px-3 py-1 text-xs font-bold ring-1 ring-inset {{ $meta['badge'] }}">
                                {{ $round['matches']->count() }} de {{ $round['slots'] }} cruces
                            </span>
                        </div>

                        <div class="grid gap-4 xl:grid-cols-2">
                            @foreach ($paths as $path)
                                <div class="rounded-2xl border border-slate-600/70 bg-slate-950/55 p-4">
                                    <div class="mb-3 flex items-center justify-between gap-3 border-b border-slate-700/70 pb-3">
                                        <div>
                                            <h3 class="text-base font-semibold text-slate-100">{{ $path['label'] }}</h3>
                                            <p class="text-xs text-slate-400">Cruces {{ $path['start'] }} al {{ $path['end'] }}</p>
                                        </div>
                                        <span class="inline-flex items-center rounded-full bg-slate-800 px-3 py-1 text-xs font-bold text-slate-200 ring-1 ring-inset ring-slate-600/70">
                                            {{ $halfSlots }} cruces
                                        </span>
                                    </div>

                                    <div class="grid gap-2 sm:grid-cols-2">
                                        @foreach (range($path['start'], $path['end']) as $slot)
                                            @php
                                                $match = $round['matches']->get($slot - 1);
                                            @endphp

                                            <div class="min-h-28 rounded-xl border border-dashed border-slate-600/80 bg-slate-950/70 p-3">
                                                <div class="mb-3 flex items-start justify-between gap-2">
                                                    <p class="text-xs font-black uppercase tracking-[0.18em] text-slate-400">Cruce {{ $slot }}</p>
                                                    <span class="inline-flex shrink-0 items-center rounded-full bg-slate-700/70 px-2.5 py-0.5 text-[11px] font-bold text-slate-200 ring-1 ring-inset ring-slate-500/60">{{ $path['label'] }}</span>
                                                </div>

                                                @if ($match)
                                                    <div class="space-y-1 text-sm font-semibold text-slate-100">
                                                        <p class="flex items-center gap-2 truncate"><x-team-flag :team="$match->homeTeam" /> <span class="truncate">{{ $match->homeTeam?->name }}</span></p>
                                                        <p class="flex items-center gap-2 truncate"><x-team-flag :team="$match->awayTeam" /> <span class="truncate">{{ $match->awayTeam?->name }}</span></p>
                                                    </div>
                                                    <p class="mt-1 text-xs text-slate-400">{{ $match->match_date?->format('d/m/Y H:i') }} · {{ $match->status }}</p>
                                                    <div class="mt-3 flex flex-wrap gap-2">
                                                        <a href="{{ route('admin.match-games.edit', $match) }}" class="inline-flex rounded-md border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:border-rose-500/60 hover:text-rose-200">Asignar equipos</a>
                                                        <form method="POST" action="{{ route('admin.match-games.destroy', $match) }}" onsubmit="return confirm('Deseas eliminar este cruce?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="rounded-md bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500">Eliminar</button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <p class="text-sm text-slate-400">Pendiente de crear</p>
                                                    <a href="{{ route('admin.match-games.create', ['phase' => $round['phase']]) }}" class="mt-3 inline-flex rounded-md bg-rose-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-rose-500">Crear cruce</a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                @foreach ($remainingRounds as $round)
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
