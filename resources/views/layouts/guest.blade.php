<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Polla Mundialista Empresarial 2026') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oxanium:wght@400;500;600;700;800&family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    @php
        $companyName = \App\Models\AppSetting::getValue('company_name', 'Polla Mundialista Empresarial 2026');
        $logoPath = \App\Models\AppSetting::getValue('company_logo_path');
    @endphp

    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_10%,rgba(254,6,60,0.28),transparent_35%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_0%,rgba(255,255,255,0.08),transparent_30%)]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(rgba(255,255,255,0.08)_1px,transparent_1px)] [background-size:4px_4px] opacity-20"></div>

        <div class="relative w-full max-w-md">
            <div class="mb-6 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    @if ($logoPath)
                        <img src="{{ asset('storage/'.$logoPath) }}" alt="Logo empresa" class="h-12 w-12 rounded-2xl bg-slate-800 object-contain p-1">
                    @else
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-600 text-xl font-black text-white">PM</span>
                    @endif
                    <span class="text-left">
                        <span class="block text-sm uppercase tracking-[0.18em] text-slate-300">Polla Mundialista</span>
                        <span class="block text-base font-semibold text-slate-50">{{ $companyName }}</span>
                    </span>
                </a>
            </div>

            <div class="glass-panel rounded-3xl p-6 shadow-2xl shadow-black/35 sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
