@extends('layouts.admin')

@section('content')
    @php
        $metrics = $metrics ?? [
            'total_users' => 124,
            'total_matches' => 48,
            'total_predictions' => 920,
            'pending_matches' => 12,
            'finished_matches' => 36,
        ];
    @endphp

    <div class="space-y-6">
        <div>
            <h1 class="section-title">Dashboard administrativo</h1>
            <p class="section-subtitle">Monitoreo general de la Polla Mundialista Empresarial 2026.</p>
        </div>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Usuarios</p>
                <p class="mt-2 text-3xl font-black text-slate-100">{{ $metrics['total_users'] }}</p>
            </x-card>
            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Partidos</p>
                <p class="mt-2 text-3xl font-black text-slate-100">{{ $metrics['total_matches'] }}</p>
            </x-card>
            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Predicciones</p>
                <p class="mt-2 text-3xl font-black text-slate-100">{{ $metrics['total_predictions'] }}</p>
            </x-card>
            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Pendientes</p>
                <p class="mt-2 text-3xl font-black text-rose-300">{{ $metrics['pending_matches'] }}</p>
            </x-card>
            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Finalizados</p>
                <p class="mt-2 text-3xl font-black text-green-300">{{ $metrics['finished_matches'] }}</p>
            </x-card>
        </section>

        <section class="grid gap-4 md:grid-cols-2">
            <x-card title="Gestion rapida" subtitle="Accesos directos">
                <div class="grid gap-3 sm:grid-cols-2">
                    <a href="{{ route('admin.teams.index') }}" class="rounded-xl border border-slate-700 bg-slate-900/75 px-4 py-3 text-sm font-semibold text-slate-100 transition hover:border-rose-500/60 hover:text-rose-200">Administrar equipos</a>
                    <a href="{{ route('admin.match-games.index') }}" class="rounded-xl border border-slate-700 bg-slate-900/75 px-4 py-3 text-sm font-semibold text-slate-100 transition hover:border-rose-500/60 hover:text-rose-200">Administrar partidos</a>
                    <a href="{{ route('admin.match-games.bracket') }}" class="rounded-xl border border-slate-700 bg-slate-900/75 px-4 py-3 text-sm font-semibold text-slate-100 transition hover:border-rose-500/60 hover:text-rose-200">Armar llaves</a>
                    <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-700 bg-slate-900/75 px-4 py-3 text-sm font-semibold text-slate-100 transition hover:border-rose-500/60 hover:text-rose-200">Ver participantes</a>
                    <a href="{{ route('admin.settings.edit') }}" class="rounded-xl border border-slate-700 bg-slate-900/75 px-4 py-3 text-sm font-semibold text-slate-100 transition hover:border-rose-500/60 hover:text-rose-200">Configuracion</a>
                </div>
            </x-card>

            <x-card title="Estado del sistema" subtitle="Condicion actual">
                <ul class="space-y-2 text-sm text-slate-300">
                    <li class="flex items-center justify-between"><span>Base de datos</span><x-badge variant="success">Operativa</x-badge></li>
                    <li class="flex items-center justify-between"><span>Scoring service</span><x-badge variant="success">Activo</x-badge></li>
                    <li class="flex items-center justify-between"><span>Panel administrativo</span><x-badge variant="info">Disponible</x-badge></li>
                </ul>
            </x-card>
        </section>
    </div>
@endsection
