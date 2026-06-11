<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
