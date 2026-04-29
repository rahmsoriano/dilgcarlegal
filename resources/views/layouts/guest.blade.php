<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GABAY-Lex') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased">
        @php
            $immersiveAuth = request()->routeIs('login');
        @endphp

        @if ($immersiveAuth)
            <div class="min-h-screen">
                {{ $slot }}
            </div>
        @else
            <div class="min-h-screen flex flex-col items-center justify-center bg-slate-100 px-4 py-8">
                <div>
                    <a href="/">
                        <x-application-logo class="h-20 w-20 fill-current text-slate-500" />
                    </a>
                </div>

                <div class="mt-6 w-full max-w-md overflow-hidden rounded-3xl border border-white/70 bg-white px-6 py-5 shadow-xl shadow-slate-900/5">
                    {{ $slot }}
                </div>
            </div>
        @endif
    </body>
</html>
