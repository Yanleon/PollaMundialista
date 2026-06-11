@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="section-title">Participantes</h1>
            <p class="section-subtitle">Administra usuarios participantes y su estado de acceso.</p>
        </div>

        @if (session('success'))
            <div class="rounded-xl border border-green-500/40 bg-green-600/10 px-4 py-3 text-sm text-green-200">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-500/40 bg-red-600/10 px-4 py-3 text-sm text-red-200">{{ session('error') }}</div>
        @endif

        <x-card>
            <form method="GET" class="flex flex-col gap-3 sm:flex-row">
                <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por nombre, correo, celular o area" class="w-full rounded-lg border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100">
                <x-button type="submit" class="sm:w-auto">Buscar</x-button>
            </form>
        </x-card>

        <x-table :headers="['Nombre', 'Correo', 'Celular', 'Area', 'Estado', 'Acciones']">
            @forelse ($participants as $participant)
                <tr class="hover:bg-slate-800/60 transition">
                    <td class="px-4 py-3 text-sm font-semibold text-slate-100">{{ $participant->name }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $participant->email }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $participant->phone_number ?? 'Sin celular' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $participant->department ?? 'Sin area' }}</td>
                    <td class="px-4 py-3">
                        <x-badge :variant="$participant->status === 'active' ? 'success' : 'danger'">{{ $participant->status }}</x-badge>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('admin.users.edit', $participant) }}" class="rounded-md border border-slate-700 bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-100 hover:border-rose-500/60 hover:text-rose-200">Editar</a>
                            <form method="POST" action="{{ route('admin.users.toggle-status', $participant) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded-md px-3 py-1.5 text-xs font-semibold {{ $participant->status === 'active' ? 'bg-amber-500 text-slate-950 hover:bg-amber-400' : 'bg-emerald-500 text-slate-950 hover:bg-emerald-400' }}">
                                    {{ $participant->status === 'active' ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $participant) }}" onsubmit="return confirm('Deseas eliminar este participante? Se borraran tambien sus predicciones.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-md bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-500">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-400">No hay participantes registrados.</td>
                </tr>
            @endforelse
        </x-table>

        <div>{{ $participants->links() }}</div>
    </div>
@endsection
