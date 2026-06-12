<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));

        $participants = User::query()
            ->where('role', 'participant')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('participants', 'search'));
    }

    public function predictionReport(Request $request): View
    {
        $matches = MatchGame::query()
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('match_date')
            ->get();

        $selectedMatch = $request->filled('match_id')
            ? $matches->firstWhere('id', (int) $request->integer('match_id'))
            : $matches->first(fn (MatchGame $matchGame) => $matchGame->status !== 'finished') ?? $matches->first();

        $status = $request->string('status', 'all')->toString();

        $participants = collect();
        $withPredictionCount = 0;

        if ($selectedMatch) {
            $participants = User::query()
                ->where('role', 'participant')
                ->with(['predictions' => fn ($query) => $query->where('match_game_id', $selectedMatch->id)])
                ->orderBy('name')
                ->get()
                ->map(function (User $user): User {
                    $user->setAttribute('selected_prediction', $user->predictions->first());

                    return $user;
                });

            $withPredictionCount = $participants
                ->filter(fn (User $user) => $user->getAttribute('selected_prediction') !== null)
                ->count();

            $participants = $participants
                ->when($status === 'done', fn ($items) => $items->filter(fn (User $user) => $user->getAttribute('selected_prediction') !== null))
                ->when($status === 'missing', fn ($items) => $items->filter(fn (User $user) => $user->getAttribute('selected_prediction') === null))
                ->values();
        }

        $totalParticipants = User::query()->where('role', 'participant')->count();
        $missingPredictionCount = max(0, $totalParticipants - $withPredictionCount);

        return view('admin.users.prediction-report', compact(
            'matches',
            'selectedMatch',
            'participants',
            'status',
            'totalParticipants',
            'withPredictionCount',
            'missingPredictionCount',
        ));
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->role !== 'participant') {
            return back()->with('error', 'Solo se puede cambiar estado a participantes.');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        return back()->with('success', 'Estado del participante actualizado correctamente.');
    }

    public function edit(User $user): View|RedirectResponse
    {
        if ($user->role !== 'participant') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Solo se puede editar participantes.');
        }

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'participant') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Solo se puede editar participantes.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'department' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Participante actualizado correctamente.');
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'participant') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Solo se puede cambiar contrasena a participantes.');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update([
            'password' => $validated['password'],
        ]);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('success', 'Contrasena actualizada correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->role !== 'participant') {
            return back()->with('error', 'Solo se puede eliminar participantes.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Participante eliminado correctamente.');
    }
}
