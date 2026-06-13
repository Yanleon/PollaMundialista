<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMatchGameRequest;
use App\Http\Requests\Admin\UpdateMatchGameRequest;
use App\Http\Requests\Admin\UpdateMatchResultRequest;
use App\Models\MatchGame;
use App\Models\Team;
use App\Services\MatchNotificationService;
use App\Services\ScoringService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MatchGameController extends Controller
{
    public function __construct(
        private readonly ScoringService $scoringService,
        private readonly MatchNotificationService $matchNotificationService,
    )
    {
    }

    public function index(): View
    {
        $matches = MatchGame::query()
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('match_date')
            ->paginate(15);

        $todayMatches = MatchGame::query()
            ->with(['homeTeam', 'awayTeam'])
            ->whereDate('match_date', now()->toDateString())
            ->orderBy('match_date')
            ->get();

        return view('admin.match-games.index', compact('matches', 'todayMatches'));
    }

    public function bracket(): View
    {
        $bracketMatches = MatchGame::query()
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('match_date')
            ->get()
            ->filter(fn (MatchGame $matchGame) => $this->detectBracketRound($matchGame->phase) !== null);

        $bracketRounds = $this->buildBracketRounds($bracketMatches);

        return view('admin.match-games.bracket', compact('bracketRounds'));
    }

    public function create(): View
    {
        $teams = Team::query()->where('status', 'active')->orderBy('name')->get();

        return view('admin.match-games.create', compact('teams'));
    }

    public function store(StoreMatchGameRequest $request): RedirectResponse
    {
        $matchGame = MatchGame::create($request->validated());

        if ($this->shouldRecalculate($matchGame)) {
            $this->scoringService->recalculateMatchPredictions($matchGame);
        }

        return redirect()
            ->route('admin.match-games.index')
            ->with('success', 'Partido creado correctamente.');
    }

    public function edit(MatchGame $matchGame): View
    {
        $teams = Team::query()->where('status', 'active')->orderBy('name')->get();

        return view('admin.match-games.edit', compact('matchGame', 'teams'));
    }

    public function update(UpdateMatchGameRequest $request, MatchGame $matchGame): RedirectResponse
    {
        $matchGame->update($request->validated());

        if ($this->shouldRecalculate($matchGame)) {
            $this->scoringService->recalculateMatchPredictions($matchGame);
        }

        return redirect()
            ->route('admin.match-games.index')
            ->with('success', 'Partido actualizado correctamente.');
    }

    public function updateResult(UpdateMatchResultRequest $request, MatchGame $matchGame): RedirectResponse
    {
        $matchGame->update($request->validated());
        $this->scoringService->recalculateMatchPredictions($matchGame);

        return redirect()
            ->route('admin.match-games.index')
            ->with('success', 'Resultado cargado y puntos recalculados correctamente.');
    }

    public function destroy(MatchGame $matchGame): RedirectResponse
    {
        $matchGame->delete();

        return redirect()
            ->route('admin.match-games.index')
            ->with('success', 'Partido eliminado correctamente.');
    }

    public function notifyToday(Request $request): RedirectResponse
    {
        $todayMatches = MatchGame::query()
            ->with(['homeTeam', 'awayTeam'])
            ->whereDate('match_date', now()->toDateString())
            ->orderBy('match_date')
            ->get();

        if ($todayMatches->isEmpty()) {
            return back()->with('error', 'No hay partidos para notificar hoy.');
        }

        $stats = $this->matchNotificationService->notifyTodayMatches($todayMatches);

        $message = "Notificaciones enviadas. Correos: {$stats['sent_emails']} enviados, {$stats['failed_emails']} fallidos.";

        if (! empty($stats['whatsapp_error'])) {
            $message .= ' WhatsApp: '.$stats['whatsapp_error'];
        } elseif ($stats['whatsapp_sent']) {
            $message .= ' WhatsApp: enviado.';
        } else {
            $message .= ' WhatsApp: no configurado.';
        }

        return back()->with('success', $message);
    }

    private function shouldRecalculate(MatchGame $matchGame): bool
    {
        return $matchGame->status === 'finished'
            && $matchGame->home_score !== null
            && $matchGame->away_score !== null;
    }

    private function buildBracketRounds(Collection $matches): Collection
    {
        return collect($this->bracketRoundConfig())
            ->map(function (array $round) use ($matches): array {
                $roundMatches = $matches
                    ->filter(fn (MatchGame $matchGame) => $this->detectBracketRound($matchGame->phase) === $round['key'])
                    ->values();

                return [
                    'key' => $round['key'],
                    'label' => $round['label'],
                    'phase' => $round['phase'],
                    'slots' => $round['slots'],
                    'matches' => $roundMatches,
                ];
            })
            ->values();
    }

    private function detectBracketRound(?string $phase): ?string
    {
        if (! $phase) {
            return null;
        }

        $normalized = Str::lower($phase);

        foreach ($this->bracketRoundConfig() as $round) {
            foreach ($round['keywords'] as $keyword) {
                if (str_contains($normalized, $keyword)) {
                    return $round['key'];
                }
            }
        }

        return null;
    }

    private function bracketRoundConfig(): array
    {
        return [
            [
                'key' => 'round_of_32',
                'label' => 'Dieciseisavos',
                'phase' => 'Dieciseisavos',
                'slots' => 16,
                'keywords' => ['dieciseisavos', 'dieciseisavo', 'treintaidosavos', 'ronda de 32', 'round of 32', 'round_of_32', 'r32', '1/16'],
            ],
            [
                'key' => 'round_of_16',
                'label' => 'Octavos',
                'phase' => 'Octavos',
                'slots' => 8,
                'keywords' => ['octavos', 'octavo', 'round of 16', 'round_of_16', 'r16', '1/8'],
            ],
            [
                'key' => 'quarterfinals',
                'label' => 'Cuartos',
                'phase' => 'Cuartos',
                'slots' => 4,
                'keywords' => ['cuartos', 'cuarto', 'quarterfinal', 'quarter-final', 'quarter final', '1/4'],
            ],
            [
                'key' => 'semifinals',
                'label' => 'Semifinal',
                'phase' => 'Semifinal',
                'slots' => 2,
                'keywords' => ['semifinal', 'semi-final', 'semi final', 'semi'],
            ],
            [
                'key' => 'final',
                'label' => 'Final',
                'phase' => 'Final',
                'slots' => 1,
                'keywords' => ['final'],
            ],
        ];
    }
}
