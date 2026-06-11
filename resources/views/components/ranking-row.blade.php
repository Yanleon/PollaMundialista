@props([
    'position',
    'entry',
])

@php
    $positionClass = $position === 1
        ? 'text-rose-300'
        : ($position <= 3 ? 'text-rose-200' : 'text-slate-200');
@endphp

<tr class="hover:bg-slate-800/60 transition">
    <td class="px-4 py-3 text-sm font-bold {{ $positionClass }}">#{{ $position }}</td>
    <td class="px-4 py-3 text-sm font-semibold text-slate-100">{{ $entry->name }}</td>
    <td class="px-4 py-3 text-sm text-slate-300">{{ $entry->department ?? 'Sin area' }}</td>
    <td class="px-4 py-3 text-sm font-bold text-slate-100">{{ $entry->total_points }}</td>
    <td class="px-4 py-3 text-sm text-slate-300">{{ $entry->exact_scores }}</td>
    <td class="px-4 py-3 text-sm text-slate-300">{{ $entry->correct_results }}</td>
</tr>
