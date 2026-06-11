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
    <div class="app-shell">
        @include('layouts.navigation')

        @isset($header)
            <header class="container-sport py-6">
                <div class="glass-panel px-5 py-4 md:px-7">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main class="container-sport pb-10">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
