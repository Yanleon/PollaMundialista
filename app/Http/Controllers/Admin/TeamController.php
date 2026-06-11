<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeamRequest;
use App\Http\Requests\Admin\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        $teams = Team::query()->latest()->paginate(15);

        return view('admin.teams.index', compact('teams'));
    }

    public function create(): View
    {
        return view('admin.teams.create');
    }

    public function store(StoreTeamRequest $request): RedirectResponse
    {
        Team::create($request->validated());

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Equipo creado correctamente.');
    }

    public function edit(Team $team): View
    {
        return view('admin.teams.edit', compact('team'));
    }

    public function update(UpdateTeamRequest $request, Team $team): RedirectResponse
    {
        $team->update($request->validated());

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Team $team): RedirectResponse
    {
        $team->delete();

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Equipo eliminado correctamente.');
    }
}
