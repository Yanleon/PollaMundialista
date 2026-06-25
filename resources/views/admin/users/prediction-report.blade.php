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
                                {{ $matchGame->match_date?->format('d/m/Y H:i') }} - {{ $matchGame->home_display_name }} vs {{ $matchGame->away_display_name }}
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

        <x-card>
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-100">Recordatorios WhatsApp de hoy</h2>
                    <p class="text-sm text-slate-400">Mensajes para participantes activos que tienen partidos de hoy sin pronosticar.</p>
                </div>
                <span class="rounded-full border border-amber-400/40 bg-amber-500/10 px-3 py-1 text-sm font-semibold text-amber-200">
                    {{ $whatsappReminders->count() }} pendientes
                </span>
            </div>

            @if ($todayMatches->isEmpty())
                <p class="text-sm text-slate-300">No hay partidos abiertos para pronosticar hoy.</p>
            @elseif ($whatsappReminders->isEmpty())
                <p class="text-sm text-green-300">Todos los participantes activos ya pronosticaron los partidos abiertos de hoy.</p>
            @else
                <div class="space-y-4">
                    @foreach ($whatsappReminders as $reminder)
                        <div class="rounded-2xl border border-slate-700 bg-slate-950/45 p-4">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <p class="font-semibold text-slate-100">{{ $reminder['user']->name }}</p>
                                    <p class="text-sm text-slate-400">{{ $reminder['user']->phone_number ?: 'Sin celular registrado' }}</p>
                                    <p class="mt-1 text-xs uppercase tracking-wide text-rose-300">
                                        Faltan {{ $reminder['missing_matches']->count() }} partido(s) de hoy
                                    </p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" data-copy-message="{{ $reminder['message'] }}" class="inline-flex items-center justify-center rounded-full border border-slate-600 bg-slate-900 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-amber-400/70 hover:text-amber-100">
                                        Copiar mensaje
                                    </button>
                                    @if ($reminder['whatsapp_url'])
                                        <a href="{{ $reminder['whatsapp_url'] }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-full bg-emerald-400 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300">
                                            Enviar por WhatsApp
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3 rounded-xl border border-slate-800 bg-slate-900/70 p-3 text-sm leading-relaxed text-slate-200">
                                {{ $reminder['message'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>

        @if ($selectedMatch)
            @php
                $canShowPredictions = $selectedMatch->match_date?->lte(now()) ?? false;
            @endphp

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
                    <h2 class="text-lg font-semibold text-slate-100">{{ $selectedMatch->home_display_name }} vs {{ $selectedMatch->away_display_name }}</h2>
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
                            <td class="px-4 py-3 text-sm font-bold">
                                @if (! $prediction)
                                    <span class="text-slate-500">-</span>
                                @elseif ($canShowPredictions)
                                    <span class="text-rose-300">{{ $prediction->getPredictedResult() }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-full border border-slate-700 bg-slate-900/80 px-3 py-1 text-xs font-semibold text-slate-300">
                                        Oculto hasta {{ $selectedMatch->match_date?->format('H:i') }}
                                    </span>
                                @endif
                            </td>
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

    <script>
        document.querySelectorAll('[data-copy-message]').forEach((button) => {
            button.addEventListener('click', async () => {
                const originalText = button.textContent;

                try {
                    await navigator.clipboard.writeText(button.dataset.copyMessage);
                    button.textContent = 'Mensaje copiado';
                } catch (error) {
                    button.textContent = 'No se pudo copiar';
                }

                setTimeout(() => {
                    button.textContent = originalText;
                }, 1800);
            });
        });
    </script>
@endsection
