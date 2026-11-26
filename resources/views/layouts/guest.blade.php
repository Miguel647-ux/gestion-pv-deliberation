<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ENEAM PV Archive') }}</title>

        <!-- Fonts (Google Fonts – Public Sans + Source Serif 4) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,600;8..60,700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-login-canvas font-sans text-login-ink antialiased min-h-screen flex flex-col items-center justify-between">

        <!-- Zone centrale -->
        <div class="flex-1 flex items-center justify-center w-full px-4 py-8">
            {{ $slot }}
        </div>

        <!-- Pied de page discret -->
        <footer class="w-full py-4 text-center text-[12px] text-login-muted select-none">
            ENEAM PV Archive
        </footer>

    </body>
</html>
