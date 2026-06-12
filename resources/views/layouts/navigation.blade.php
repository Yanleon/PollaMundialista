<nav x-data="{ open: false }" class="border-b border-slate-800/80 bg-slate-950/80 backdrop-blur-md">
    @php
        $companyName = \App\Models\AppSetting::getValue('company_name', 'Empresarial 2026');
        $logoPath = \App\Models\AppSetting::getValue('company_logo_path');
    @endphp

    <div class="container-sport">
        <div class="flex h-16 items-center justify-between">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-3">
                @if ($logoPath)
                    <img src="{{ asset('storage/'.$logoPath) }}" alt="Logo empresa" class="h-9 w-9 rounded-xl bg-slate-800 object-contain p-1">
                @else
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-rose-600 text-sm font-black text-white">PM</span>
                @endif
                <span>
                    <span class="block text-xs uppercase tracking-[0.16em] text-slate-400">Polla Mundialista</span>
                    <span class="block text-sm font-semibold text-slate-100">{{ $companyName }}</span>
                </span>
            </a>

            <div class="hidden items-center gap-1 md:flex">
                @php($user = auth()->user())
                @php($baseLink = 'rounded-full px-3 py-2 text-sm font-medium transition')

                <a href="{{ route('dashboard') }}" class="{{ $baseLink }} {{ request()->routeIs('dashboard') ? 'bg-rose-600 text-white shadow-[0_0_18px_rgba(255,31,69,0.4)]' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Inicio</a>
                <a href="{{ route('leaderboard.index') }}" class="{{ $baseLink }} {{ request()->routeIs('leaderboard.*') ? 'bg-rose-600 text-white shadow-[0_0_18px_rgba(255,31,69,0.4)]' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Ranking</a>

                @if ($user?->role === 'participant')
                    <a href="{{ route('participant.dashboard') }}" class="{{ $baseLink }} {{ request()->routeIs('participant.dashboard') ? 'bg-rose-600 text-white shadow-[0_0_18px_rgba(255,31,69,0.4)]' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Mi panel</a>
                    <a href="{{ route('participant.predictions.index') }}" class="{{ $baseLink }} {{ request()->routeIs('participant.predictions.*') ? 'bg-rose-600 text-white shadow-[0_0_18px_rgba(255,31,69,0.4)]' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Mis pronosticos</a>
                @endif

                @if ($user?->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="{{ $baseLink }} {{ request()->routeIs('admin.dashboard') ? 'bg-rose-600 text-white shadow-[0_0_18px_rgba(255,31,69,0.4)]' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Admin</a>
                    <a href="{{ route('admin.teams.index') }}" class="{{ $baseLink }} {{ request()->routeIs('admin.teams.*') ? 'bg-rose-600 text-white shadow-[0_0_18px_rgba(255,31,69,0.4)]' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Equipos</a>
                    <a href="{{ route('admin.match-games.index') }}" class="{{ $baseLink }} {{ request()->routeIs('admin.match-games.index', 'admin.match-games.create', 'admin.match-games.edit') ? 'bg-rose-600 text-white shadow-[0_0_18px_rgba(255,31,69,0.4)]' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Partidos</a>
                    <a href="{{ route('admin.match-games.bracket') }}" class="{{ $baseLink }} {{ request()->routeIs('admin.match-games.bracket') ? 'bg-rose-600 text-white shadow-[0_0_18px_rgba(255,31,69,0.4)]' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Llaves</a>
                    <a href="{{ route('admin.prediction-report.index') }}" class="{{ $baseLink }} {{ request()->routeIs('admin.prediction-report.*') ? 'bg-rose-600 text-white shadow-[0_0_18px_rgba(255,31,69,0.4)]' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Pronosticos</a>
                    <a href="{{ route('admin.users.index') }}" class="{{ $baseLink }} {{ request()->routeIs('admin.users.*') ? 'bg-rose-600 text-white shadow-[0_0_18px_rgba(255,31,69,0.4)]' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Participantes</a>
                    <a href="{{ route('admin.settings.edit') }}" class="{{ $baseLink }} {{ request()->routeIs('admin.settings.*') ? 'bg-rose-600 text-white shadow-[0_0_18px_rgba(255,31,69,0.4)]' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Configuracion</a>
                @endif
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <a href="{{ route('profile.edit') }}" class="text-sm text-slate-300 hover:text-white">{{ Auth::user()->name }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-full border border-rose-500/60 bg-rose-600/80 px-3 py-2 text-sm font-medium text-white hover:bg-rose-500">Salir</button>
                </form>
            </div>

            <button @click="open = !open" class="inline-flex rounded-xl border border-slate-700 p-2 text-slate-200 md:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-transition class="border-t border-slate-800 bg-slate-900/95 md:hidden">
        <div class="container-sport space-y-2 py-3">
            <a href="{{ route('dashboard') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-slate-800">Inicio</a>
            <a href="{{ route('leaderboard.index') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-slate-800">Ranking</a>

            @if (auth()->user()?->role === 'participant')
                <a href="{{ route('participant.dashboard') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-slate-800">Mi panel</a>
                <a href="{{ route('participant.predictions.index') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-slate-800">Mis pronosticos</a>
            @endif

            @if (auth()->user()?->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-slate-800">Admin</a>
                <a href="{{ route('admin.teams.index') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-slate-800">Equipos</a>
                <a href="{{ route('admin.match-games.index') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-slate-800">Partidos</a>
                <a href="{{ route('admin.match-games.bracket') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-slate-800">Llaves</a>
                <a href="{{ route('admin.prediction-report.index') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-slate-800">Pronosticos</a>
                <a href="{{ route('admin.users.index') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-slate-800">Participantes</a>
                <a href="{{ route('admin.settings.edit') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-200 hover:bg-slate-800">Configuracion</a>
            @endif

            <div class="soft-divider"></div>
            <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3 py-2 text-sm text-slate-300 hover:bg-slate-800">Perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full rounded-xl bg-rose-600 px-3 py-2 text-left text-sm font-medium text-white">Salir</button>
            </form>
        </div>
    </div>
</nav>
