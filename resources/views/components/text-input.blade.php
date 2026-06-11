@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border border-slate-600 bg-slate-900 text-slate-100 shadow-sm focus:border-rose-500 focus:ring-rose-500']) }}>
