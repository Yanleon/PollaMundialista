<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Polla Mundialista Empresarial 2026') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oxanium:wght@400;500;600;700;800&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    @php
        $companyName = \App\Models\AppSetting::getValue('company_name', 'Polla Mundialista Empresarial 2026');
        $heroTitle = \App\Models\AppSetting::getValue('hero_title', 'Compite con tu equipo de trabajo en la Polla Mundialista Empresarial.');
        $heroSubtitle = \App\Models\AppSetting::getValue('hero_subtitle', 'Registra tus marcadores, acumula puntos automaticamente y escala en el ranking general de la compania durante todo el torneo.');
        $logoPath = \App\Models\AppSetting::getValue('company_logo_path');

        $allMatches = \App\Models\MatchGame::query()
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('match_date')
            ->get();

        $roundConfig = collect([
            [
                'key' => 'round_of_16',
                'label' => 'Octavos',
                'slots' => 8,
                'keywords' => ['octavos', 'octavo', 'round of 16', 'round_of_16', 'r16', '1/8'],
            ],
            [
                'key' => 'quarterfinals',
                'label' => 'Cuartos',
                'slots' => 4,
                'keywords' => ['cuartos', 'cuarto', 'quarterfinal', 'quarter-final', 'quarter final', '1/4'],
            ],
            [
                'key' => 'semifinals',
                'label' => 'Semifinal',
                'slots' => 2,
                'keywords' => ['semifinal', 'semi-final', 'semi final', 'semi'],
            ],
            [
                'key' => 'final',
                'label' => 'Final',
                'slots' => 1,
                'keywords' => ['final'],
            ],
        ]);

        $detectRound = function (?string $phase) use ($roundConfig): ?string {
            if (! $phase) {
                return null;
            }

            $normalized = \Illuminate\Support\Str::lower($phase);

            foreach ($roundConfig as $round) {
                foreach ($round['keywords'] as $keyword) {
                    if (str_contains($normalized, $keyword)) {
                        return $round['key'];
                    }
                }
            }

            return null;
        };

        $bracketRounds = $roundConfig
            ->map(function (array $round) use ($allMatches, $detectRound): array {
                $roundMatches = $allMatches
                    ->filter(fn ($match) => $detectRound($match->phase) === $round['key'])
                    ->values();

                $filledSlots = collect(range(0, $round['slots'] - 1))
                    ->map(fn (int $index) => $roundMatches->get($index));

                return [
                    'key' => $round['key'],
                    'label' => $round['label'],
                    'matches' => $filledSlots,
                ];
            })
            ->values();

        $finalRound = $bracketRounds->firstWhere('key', 'final');
        $finalMatch = data_get($finalRound, 'matches.0');

        $championName = null;

        if ($finalMatch && $finalMatch->status === 'finished' && $finalMatch->home_score !== null && $finalMatch->away_score !== null) {
            if ($finalMatch->home_score > $finalMatch->away_score) {
                $championName = $finalMatch->homeTeam?->name;
            } elseif ($finalMatch->away_score > $finalMatch->home_score) {
                $championName = $finalMatch->awayTeam?->name;
            }
        }
    @endphp

    <div class="relative min-h-screen overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_12%_10%,rgba(254,6,60,0.32),transparent_34%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_84%_3%,rgba(255,255,255,0.12),transparent_26%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.08)_1px,transparent_1px)] [background-size:4px_4px] opacity-20"></div>

        <header class="container-sport py-6">
            <div class="flex items-center justify-between rounded-3xl border border-slate-700/80 bg-slate-950/75 px-4 py-3 backdrop-blur-lg md:px-6">
                <div class="inline-flex items-center gap-3">
                    @if ($logoPath)
                        <img src="{{ asset('storage/'.$logoPath) }}" alt="Logo empresa" class="h-10 w-10 rounded-xl bg-slate-800 object-contain p-1">
                    @else
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-rose-600 text-sm font-black text-white">PM</span>
                    @endif
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-400">Polla Mundialista</p>
                        <p class="text-sm font-semibold text-slate-100">{{ $companyName }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full border border-rose-400/50 bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-[0_0_18px_rgba(255,31,69,0.35)] hover:bg-rose-500">Entrar al panel</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-slate-200 hover:bg-slate-800">Iniciar sesion</a>
                        <a href="{{ route('register') }}" class="rounded-full border border-rose-400/50 bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-[0_0_18px_rgba(255,31,69,0.35)] hover:bg-rose-500">Registrarse</a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="container-sport pb-16 pt-10">
            <section class="grid gap-8 lg:grid-cols-[1.2fr_1fr]">
                <div class="glass-panel rounded-3xl p-8 md:p-10">
                    <p class="mb-4 inline-flex rounded-full border border-rose-500/50 bg-rose-500/15 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-rose-200">Mundial 2026</p>
                    <h1 class="text-3xl font-black leading-tight text-slate-100 md:text-5xl">{{ $heroTitle }}</h1>
                    <p class="mt-5 max-w-2xl text-base text-slate-300 md:text-lg">{{ $heroSubtitle }}</p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="rounded-full bg-gradient-to-r from-white via-white to-rose-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:scale-[1.03] hover:shadow-[0_0_24px_rgba(255,31,69,0.45)]">Crear cuenta</a>
                        <a href="{{ route('login') }}" class="rounded-full border border-slate-700 bg-slate-900/75 px-5 py-3 text-sm font-semibold text-slate-100 transition hover:border-rose-500/50 hover:text-rose-200">Ya tengo cuenta</a>
                    </div>
                </div>

                <div class="grid gap-4">
                    <x-card title="Sistema de puntos" subtitle="Reglas oficiales de la plataforma">
                        <ul class="space-y-3 text-sm text-slate-300">
                            <li class="flex items-center justify-between"><span>Marcador exacto</span><x-badge variant="success">5 pts</x-badge></li>
                            <li class="flex items-center justify-between"><span>Ganador o empate</span><x-badge variant="info">3 pts</x-badge></li>
                            <li class="flex items-center justify-between"><span>Goles exactos local</span><x-badge variant="warning">+1 pt</x-badge></li>
                            <li class="flex items-center justify-between"><span>Goles exactos visitante</span><x-badge variant="warning">+1 pt</x-badge></li>
                        </ul>
                    </x-card>

                    <x-card title="Cobertura del torneo" subtitle="Modulos activos">
                        <div class="grid grid-cols-2 gap-2 text-sm text-slate-300">
                            <span class="rounded-xl border border-slate-700 bg-slate-900/70 px-3 py-2">Predicciones</span>
                            <span class="rounded-xl border border-slate-700 bg-slate-900/70 px-3 py-2">Ranking</span>
                            <span class="rounded-xl border border-slate-700 bg-slate-900/70 px-3 py-2">Panel Admin</span>
                            <span class="rounded-xl border border-slate-700 bg-slate-900/70 px-3 py-2">Resultados</span>
                        </div>
                    </x-card>
                </div>
            </section>

            <section class="mt-10">
                <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="section-title">Cuadro oficial de eliminacion</h2>
                        <p class="section-subtitle">Sigue el camino hacia la final y mira como queda definido el campeon.</p>
                    </div>
                    <x-badge variant="warning">Fase final</x-badge>
                </div>

                @if ($bracketRounds->isEmpty())
                    <x-card>
                        <p class="text-sm text-slate-300">Aun no hay cruces de eliminacion cargados.</p>
                    </x-card>
                @else
                    <div class="bracket-poster">
                        <div class="bracket-deco-layer" aria-hidden="true">
                            <img src="{{ asset('references/mascota_zayu.png') }}" alt="" class="bracket-mascot mascot-zayu">
                            <img src="{{ asset('references/mascota_clutch.png') }}" alt="" class="bracket-mascot mascot-clutch">
                            <img src="{{ asset('references/mascota_maple.png') }}" alt="" class="bracket-mascot mascot-maple">
                        </div>

                        <div class="bracket-poster-head">
                            <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Llave final</p>
                            <h3 class="text-3xl font-semibold text-white md:text-5xl">Fase Final</h3>
                        </div>

                        <div class="bracket-layout">
                            @foreach ($bracketRounds as $round)
                                <section class="bracket-column bracket-column-{{ $round['key'] }}">
                                    <header class="bracket-column-head">
                                        <h4 class="text-xs uppercase tracking-[0.14em] text-slate-100">{{ $round['label'] }}</h4>
                                        <p class="text-[11px] text-slate-400">Ronda de {{ $round['matches']->count() }}</p>
                                    </header>

                                    <div class="bracket-column-body">
                                        @foreach ($round['matches'] as $matchGame)
                                            @if ($matchGame)
                                                @php
                                                    $statusVariant = match($matchGame->status) {
                                                        'finished' => 'success',
                                                        'live' => 'warning',
                                                        'scheduled' => 'info',
                                                        'cancelled' => 'danger',
                                                        default => 'muted',
                                                    };
                                                @endphp

                                                <article class="bracket-slot {{ $loop->odd ? 'slot-odd' : 'slot-even' }}">
                                                    <p class="bracket-date">{{ $matchGame->match_date?->format('D, d M | H:i') }}</p>
                                                    <div class="bracket-team-row">
                                                        <span class="truncate text-sm font-semibold text-slate-100">{{ $matchGame->homeTeam?->name ?? 'Por definir' }}</span>
                                                        @if ($matchGame->status === 'finished' && $matchGame->home_score !== null && $matchGame->away_score !== null)
                                                            <span class="text-sm font-bold text-rose-300">{{ $matchGame->home_score }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="bracket-team-row">
                                                        <span class="truncate text-sm font-semibold text-slate-100">{{ $matchGame->awayTeam?->name ?? 'Por definir' }}</span>
                                                        @if ($matchGame->status === 'finished' && $matchGame->home_score !== null && $matchGame->away_score !== null)
                                                            <span class="text-sm font-bold text-rose-300">{{ $matchGame->away_score }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2"><x-badge :variant="$statusVariant" class="text-[10px]">{{ $matchGame->status }}</x-badge></div>
                                                </article>
                                            @else
                                                <article class="bracket-slot bracket-slot-empty {{ $loop->odd ? 'slot-odd' : 'slot-even' }}">
                                                    <p class="text-center text-xs text-slate-400">Cruce pendiente</p>
                                                </article>
                                            @endif
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach

                            <aside class="bracket-sidepanel">
                                <div class="bracket-side-block">
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-300">Final</p>
                                    @if ($finalMatch)
                                        <p class="mt-2 text-sm font-semibold text-white">{{ $finalMatch->homeTeam?->name }} vs {{ $finalMatch->awayTeam?->name }}</p>
                                        @if ($finalMatch->status === 'finished' && $finalMatch->home_score !== null && $finalMatch->away_score !== null)
                                            <p class="mt-1 text-sm font-bold text-rose-300">{{ $finalMatch->home_score }} - {{ $finalMatch->away_score }}</p>
                                        @else
                                            <p class="mt-1 text-xs text-slate-400">Pendiente de jugar</p>
                                        @endif
                                    @else
                                        <p class="mt-2 text-sm text-slate-400">Cruce no definido</p>
                                    @endif
                                </div>

                                <div class="bracket-side-block">
                                    <p class="text-xs uppercase tracking-[0.14em] text-slate-300">Campeon</p>
                                    <p class="mt-2 text-xl font-black text-amber-300">{{ $championName ?? 'Por definir' }}</p>
                                </div>
                            </aside>
                        </div>
                    </div>
                @endif

                @guest
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ route('login') }}" class="rounded-full border border-slate-700 bg-slate-900/80 px-5 py-3 text-sm font-semibold text-slate-100 hover:border-rose-500/60 hover:text-rose-200">Iniciar sesion</a>
                        <a href="{{ route('register') }}" class="rounded-full bg-gradient-to-r from-white via-white to-rose-500 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:scale-[1.03] hover:shadow-[0_0_24px_rgba(255,31,69,0.45)]">Crear cuenta</a>
                    </div>
                @endguest
            </section>
        </main>
    </div>
</body>
</html>
