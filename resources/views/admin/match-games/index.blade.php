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

        <x-card title="Llave eliminatoria" subtitle="Crea los cruces de octavos, cuartos, semifinal y final cuando ya conozcas los clasificados.">
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
                                        <p class="truncate text-sm font-semibold text-slate-100">{{ $match->homeTeam?->name }} vs {{ $match->awayTeam?->name }}</p>
                                        <p class="mt-1 text-xs text-slate-400">{{ $match->match_date?->format('d/m/Y H:i') }} · {{ $match->status }}</p>
                                        <a href="{{ route('admin.match-games.edit', $match) }}" class="mt-2 inline-flex rounded-md border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:border-rose-500/60 hover:text-rose-200">Asignar equipos</a>
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

        <x-table :headers="['Partido', 'Fase', 'Fecha', 'Limite', 'Estado', 'Resultado', 'Acciones']">
            @forelse ($matches as $matchGame)
                @php
                    $badgeVariant = match($matchGame->status) {
                        'scheduled' => 'info',
                        'live' => 'warning',
                        'finished' => 'success',
                        'cancelled' => 'danger',
                        default => 'muted',
                    };
                @endphp

                <tr class="hover:bg-slate-800/60 transition">
                    <td class="px-4 py-3 text-sm font-semibold text-slate-100">{{ $matchGame->homeTeam?->name }} vs {{ $matchGame->awayTeam?->name }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $matchGame->phase }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $matchGame->match_date?->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $matchGame->prediction_deadline?->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3"><x-badge :variant="$badgeVariant">{{ $matchGame->status }}</x-badge></td>
                    <td class="px-4 py-3 text-sm font-bold text-rose-300">{{ $matchGame->getFinalResult() ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.match-games.edit', $matchGame) }}" class="rounded-md border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:border-rose-500/60 hover:text-rose-200">Editar</a>
                            <form method="POST" action="{{ route('admin.match-games.destroy', $matchGame) }}" onsubmit="return confirm('Deseas eliminar este partido?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="7" class="bg-slate-900/50 px-4 py-3">
                        <form method="POST" action="{{ route('admin.match-games.update-result', $matchGame) }}" class="grid gap-3 md:grid-cols-[auto_auto_auto_auto] md:items-end">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-300">Goles local</label>
                                <input type="number" name="home_score" min="0" max="30" value="{{ old('home_score', $matchGame->home_score) }}" class="w-full rounded-xl border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100 focus:border-rose-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-300">Goles visitante</label>
                                <input type="number" name="away_score" min="0" max="30" value="{{ old('away_score', $matchGame->away_score) }}" class="w-full rounded-xl border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100 focus:border-rose-500 focus:outline-none">
                            </div>
                            <input type="hidden" name="status" value="finished">
                            <x-button type="submit" variant="success" class="md:w-auto">Cargar resultado</x-button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-400">No hay partidos registrados.</td>
                </tr>
            @endforelse
        </x-table>

        <div>
            {{ $matches->links() }}
        </div>
    </div>
@endsection
