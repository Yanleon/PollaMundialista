<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="section-title">Ranking general</h1>
                <p class="section-subtitle">Desempate por marcadores exactos y resultados acertados.</p>
            </div>
            <x-badge variant="warning">Top 3 destacado</x-badge>
        </div>
    </x-slot>

    @php
        $hasPrizes = collect($prizes ?? [])->contains(fn ($prize) => filled($prize['name'] ?? null) || filled($prize['image_path'] ?? null));
        $revealedPrizeCount = collect($prizes ?? [])->filter(fn ($prize) => $prize['is_revealed'] ?? false)->count();
    @endphp

    <section class="leaderboard-hero mb-6">
        <div class="leaderboard-hero-glow"></div>

        <div class="relative z-10 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.28em] text-cyan-200">Top 3 actual</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-white md:text-5xl">El podio de la polla</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-200 md:text-base">Los tres participantes con mayor puntaje aparecen destacados. El desempate mantiene marcadores exactos y resultados acertados.</p>
            </div>
            <div class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-right backdrop-blur">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Participantes activos</p>
                <p class="text-3xl font-black text-white">{{ $ranking->total() }}</p>
            </div>
        </div>

        @if ($topRanking->isNotEmpty())
            @php
                $podiumOrder = [2, 1, 3];
                $podiumLabels = [1 => 'Primer lugar', 2 => 'Segundo lugar', 3 => 'Tercer lugar'];
                $podiumClass = [1 => 'podium-first', 2 => 'podium-second', 3 => 'podium-third'];
            @endphp

            <div class="relative z-10 mt-8 grid gap-4 lg:grid-cols-3 lg:items-end">
                @foreach ($podiumOrder as $position)
                    @php
                        $entry = $topRanking->get($position - 1);
                    @endphp

                    @if ($entry)
                        <article class="podium-card {{ $podiumClass[$position] }} {{ $position === 1 ? 'lg:order-2' : ($position === 2 ? 'lg:order-1' : 'lg:order-3') }}">
                            <div class="podium-medal medal-{{ $position }}" aria-hidden="true">
                                <span>{{ $position }}</span>
                            </div>

                            <div class="mt-4 text-center">
                                <p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-300">{{ $podiumLabels[$position] }}</p>
                                <h3 class="mt-2 text-2xl font-black text-white">{{ $entry->name }}</h3>
                                <p class="mt-1 text-sm text-slate-300">{{ $entry->department ?: 'Sin area' }}</p>
                            </div>

                            <div class="mt-5 grid grid-cols-3 gap-2 text-center">
                                <div class="podium-stat">
                                    <p>{{ $entry->total_points }}</p>
                                    <span>Puntos</span>
                                </div>
                                <div class="podium-stat">
                                    <p>{{ $entry->exact_scores }}</p>
                                    <span>Exactos</span>
                                </div>
                                <div class="podium-stat">
                                    <p>{{ $entry->correct_results }}</p>
                                    <span>Aciertos</span>
                                </div>
                            </div>
                        </article>
                    @endif
                @endforeach
            </div>
        @else
            <div class="relative z-10 mt-6 rounded-2xl border border-white/15 bg-white/10 p-5 text-sm text-slate-200">
                Aun no hay participantes activos en el ranking.
            </div>
        @endif
    </section>

    @if ($hasPrizes)
        <div class="mb-6 rounded-3xl border border-amber-400/30 bg-gradient-to-br from-amber-500/15 via-slate-900 to-rose-500/10 p-5 shadow-[0_0_35px_rgba(251,191,36,0.08)]">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-200">Premios top 3</p>
                    <h2 class="mt-1 text-2xl font-bold text-white">{{ $revealedPrizeCount > 0 || auth()->user()?->isAdmin() ? 'Premios en destape' : 'Premios secretos' }}</h2>
                    <p class="mt-1 text-sm text-slate-300">
                        @if (auth()->user()?->isAdmin() && ! $prizesAreRevealed)
                            Vista privada de administrador. Los participantes solo ven los premios que ya llegaron a su fecha.
                        @elseif ($prizesAreRevealed)
                            Todos los premios ya estan visibles.
                        @elseif ($revealedPrizeCount > 0)
                            Algunos premios ya estan visibles y otros siguen secretos.
                        @elseif ($prizesRevealAt)
                            El primer premio se destapa el {{ $prizesRevealAt->format('d/m/Y') }}.
                        @else
                            El admin definira las fechas de destape.
                        @endif
                    </p>
                </div>
                <x-badge variant="warning">{{ auth()->user()?->isAdmin() ? 'Admin' : $revealedPrizeCount.'/3 visibles' }}</x-badge>
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-3">
                @foreach ([1 => 'Primer lugar', 2 => 'Segundo lugar', 3 => 'Tercer lugar'] as $place => $label)
                    @php
                        $prizeVisible = auth()->user()?->isAdmin() || ($prizes[$place]['is_revealed'] ?? false);
                        $prizeRevealAt = $prizes[$place]['reveal_at'] ?? null;
                    @endphp

                    <div class="rounded-2xl border border-slate-700/80 bg-slate-950/60 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $label }}</p>

                        @if ($prizeVisible && ($prizes[$place]['image_path'] ?? null))
                            <img src="{{ asset('storage/'.$prizes[$place]['image_path']) }}" alt="Imagen premio {{ strtolower($label) }}" class="mt-3 h-36 w-full rounded-xl object-cover">
                        @elseif (! $prizeVisible)
                            <div class="mt-3 flex h-36 w-full items-center justify-center rounded-xl border border-dashed border-amber-300/30 bg-amber-400/10 text-sm font-semibold text-amber-100">Imagen secreta</div>
                        @endif

                        <p class="mt-2 text-lg font-bold text-slate-100">
                            {{ $prizeVisible ? (($prizes[$place]['name'] ?? null) ?: 'Sin premio definido') : 'Premio secreto' }}
                        </p>
                        @unless ($prizeVisible)
                            <p class="mt-1 text-xs text-slate-400">{{ $prizeRevealAt ? 'Se destapa el '.$prizeRevealAt->format('d/m/Y') : 'Fecha de destape pendiente.' }}</p>
                        @endunless
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <x-ranking-table :entries="$ranking" />
</x-app-layout>
