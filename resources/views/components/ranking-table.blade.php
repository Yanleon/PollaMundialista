@props([
    'entries' => collect(),
])

<div class="overflow-hidden rounded-3xl border border-slate-700 bg-slate-950/70">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-700">
            <thead class="bg-slate-900/95">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">Pos</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">Participante</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">Area</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">Puntos</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">Exactos</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-300">Aciertos</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/80">
                @forelse ($entries as $entry)
                    @php
                        $position = $loop->iteration;
                        $rowClass = $position === 1
                            ? 'bg-rose-500/18'
                            : ($position === 2
                                ? 'bg-slate-200/8'
                                : ($position === 3 ? 'bg-rose-900/30' : 'hover:bg-slate-800/70'));
                    @endphp

                    <tr class="{{ $rowClass }} transition">
                        <td class="px-4 py-3 text-sm font-bold text-white">#{{ $position }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-white">{{ data_get($entry, 'name', 'Participante') }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ data_get($entry, 'department', 'Sin area') }}</td>
                        <td class="px-4 py-3 text-sm font-bold text-rose-300">{{ data_get($entry, 'total_points', 0) }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ data_get($entry, 'exact_scores', 0) }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ data_get($entry, 'correct_results', 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-300">No hay datos para mostrar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
