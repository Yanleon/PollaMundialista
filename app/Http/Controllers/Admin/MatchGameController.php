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
}
