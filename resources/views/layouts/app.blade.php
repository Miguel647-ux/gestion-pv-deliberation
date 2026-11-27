<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ENEAM PV Archive') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;0,8..60,700;1,8..60,400&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        x-data="{ sidebarOpen: false }"
        class="bg-surface font-sans text-on-surface antialiased min-h-screen flex flex-col"
    >
        <!-- Left Sidebar Navigation -->
        @include('layouts.navigation')

        <!-- Main Wrapper (Offset for Left Sidebar on large screens) -->
        <div class="lg:pl-72 flex-1 flex flex-col min-h-screen min-w-0">
            <!-- Top Header Bar -->
            <header class="h-16 bg-surface-container-lowest border-b border-outline-variant flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 sticky top-0 z-30">
                <!-- Left: Hamburger toggle + Breadcrumb -->
                <div class="flex items-center gap-3">
                    <button
                        @click="sidebarOpen = true"
                        type="button"
                        class="lg:hidden inline-flex items-center justify-center p-2 rounded text-secondary hover:text-on-surface hover:bg-surface-container focus:outline-none transition-colors"
                        aria-label="Ouvrir le menu latéral"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div class="flex items-center gap-2 text-label-sm text-secondary font-medium truncate">
                        <span>Portail d'Archivage Institutionnel</span>
                        <span class="text-outline">/</span>
                        <span class="text-primary font-semibold">Session active</span>
                    </div>
                </div>

                <!-- Right Corner: User Profile (Forme avec avatar cercle vert + nom et rôle) & Bouton Déconnexion -->
                <div class="flex items-center gap-4 sm:gap-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white shrink-0">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-[14px] font-bold text-on-surface leading-tight">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="text-[12px] text-secondary leading-tight mt-0.5">
                                {{ Auth::user()->isDa() ? 'Administratrice' : 'Chef de filière' }}
                            </span>
                        </div>
                    </div>

                    <div class="h-8 w-px bg-outline-variant/60 hidden sm:block"></div>

                    <!-- Bouton Déconnexion repositionné en haut à droite -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="group inline-flex items-center gap-2 text-body-sm font-medium text-secondary hover:text-error transition-colors cursor-pointer py-1"
                            title="Se déconnecter de l'application"
                        >
                            <svg class="w-4 h-4 text-secondary group-hover:text-error transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Se déconnecter</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Session Alerts -->
            @if (session('success') || session('status'))
                <div class="w-full px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="bg-surface-container-low border border-primary text-primary px-4 py-3 rounded text-body-sm flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ session('success') ?? session('status') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="w-full px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="bg-error-container border border-error text-on-error-container px-4 py-3 rounded text-body-sm flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-error shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Page Content Slot -->
            <main class="w-full flex-1 px-4 sm:px-6 lg:px-8 py-6">
                {{ $slot }}
            </main>

            <!-- Institutional Footer -->
            <footer class="w-full bg-surface-container-lowest border-t border-outline-variant py-4 px-4 sm:px-6 lg:px-8 mt-auto text-body-sm text-secondary flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="flex items-center gap-2 text-label-sm">
                    <svg class="w-4 h-4 text-primary shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Conformité avec l'arrêté rectoral d'archivage des délibérations de l'UAC</span>
                </div>
                <span class="text-outline text-label-sm">Dernière synchronisation : Aujourd'hui</span>
            </footer>
        </div>
    </body>
</html>
