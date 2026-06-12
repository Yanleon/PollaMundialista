@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="section-title">Control de pronosticos</h1>
            <p class="section-subtitle">Revisa por partido quienes ya enviaron marcador y quienes faltan.</p>
        </div>

        <x-card>
            <form method="GET" action="{{ route('admin.prediction-report.index') }}" class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-end">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-200">Partido</label>
                    <select name="match_id" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                        @foreach ($matches as $matchGame)
                            <option value="{{ $matchGame->id }}" @selected($selectedMatch?->id === $matchGame->id)>
                                {{ $matchGame->match_date?->format('d/m/Y H:i') }} - {{ $matchGame->homeTeam?->name }} vs {{ $matchGame->awayTeam?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-200">Estado</label>
                    <select name="status" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                        <option value="all" @selected($status === 'all')>Todos</option>
                        <option value="done" @selected($status === 'done')>Ya hicieron</option>
                        <option value="missing" @selected($status === 'missing')>Faltan</option>
                    </select>
                </div>

                <x-button type="submit" class="md:w-auto">Filtrar</x-button>
            </form>
        </x-card>

        @if ($selectedMatch)
            <section class="grid gap-4 md:grid-cols-4">
                <x-card>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Participantes</p>
                    <p class="mt-2 text-3xl font-black text-slate-100">{{ $totalParticipants }}</p>
                </x-card>
                <x-card>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Ya hicieron</p>
                    <p class="mt-2 text-3xl font-black text-green-300">{{ $withPredictionCount }}</p>
                </x-card>
                <x-card>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Faltan</p>
                    <p class="mt-2 text-3xl font-black text-rose-300">{{ $missingPredictionCount }}</p>
                </x-card>
                <x-card>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Cierre</p>
                    <p class="mt-2 text-lg font-black text-amber-300">{{ $selectedMatch->prediction_deadline?->format('d/m H:i') }}</p>
                </x-card>
            </section>

            <x-card>
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-slate-100">{{ $selectedMatch->homeTeam?->name }} vs {{ $selectedMatch->awayTeam?->name }}</h2>
                    <p class="text-sm text-slate-400">{{ $selectedMatch->phase }} · {{ $selectedMatch->match_date?->format('d/m/Y H:i') }}</p>
                </div>

                <x-table :headers="['Participante', 'Correo', 'Celular', 'Estado', 'Pronostico']">
                    @forelse ($participants as $participant)
                        @php($prediction = $participant->getAttribute('selected_prediction'))
                        <tr class="transition hover:bg-slate-800/60">
                            <td class="px-4 py-3 text-sm font-semibold text-slate-100">{{ $participant->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-300">{{ $participant->email }}</td>
                            <td class="px-4 py-3 text-sm text-slate-300">{{ $participant->phone_number ?? '-' }}</td>
                            <td class="px-4 py-3">
                                @if ($prediction)
                                    <x-badge variant="success">Listo</x-badge>
                                @else
                                    <x-badge variant="danger">Falta</x-badge>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm font-bold text-rose-300">{{ $prediction?->getPredictedResult() ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-400">No hay participantes para este filtro.</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>
        @else
            <x-card>
                <p class="text-sm text-slate-300">No hay partidos registrados para revisar pronosticos.</p>
            </x-card>
        @endif
    </div>
@endsection
