@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="section-title">Equipos</h1>
                <p class="section-subtitle">Gestiona el catalogo oficial de selecciones participantes.</p>
            </div>
            <a href="{{ route('admin.teams.create') }}" class="inline-flex items-center rounded-full bg-gradient-to-r from-white via-white to-rose-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:scale-[1.02] hover:shadow-[0_0_22px_rgba(255,31,69,0.35)]">Nuevo equipo</a>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-500/40 bg-green-600/10 px-4 py-3 text-sm text-green-200">{{ session('success') }}</div>
        @endif

        <x-table :headers="['Equipo', 'Codigo', 'Grupo', 'Estado', 'Acciones']">
            @forelse ($teams as $team)
                <tr class="hover:bg-slate-800/60 transition">
                    <td class="px-4 py-3 text-sm font-semibold text-slate-100">
                        <span class="flex items-center gap-2"><x-team-flag :team="$team" size="md" /> {{ $team->name }}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $team->code }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $team->group_name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <x-badge :variant="$team->status === 'active' ? 'success' : 'danger'">{{ $team->status }}</x-badge>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.teams.edit', $team) }}" class="rounded-md border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:border-rose-500/60 hover:text-rose-200">Editar</a>
                            <form method="POST" action="{{ route('admin.teams.destroy', $team) }}" onsubmit="return confirm('Deseas eliminar este equipo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-5 text-center text-sm text-slate-400">No hay equipos registrados.</td>
                </tr>
            @endforelse
        </x-table>

        <div>
            {{ $teams->links() }}
        </div>
    </div>
@endsection
