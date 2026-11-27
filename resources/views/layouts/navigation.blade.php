<!-- Sidebar Navigation (ENEAM PV Archive) -->
<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-50 w-64 lg:w-72 bg-surface-container-lowest border-r border-outline-variant flex flex-col justify-start transition-transform duration-200 ease-in-out shrink-0"
>
    <!-- Top Branding & Navigation Links -->
    <div class="flex flex-col flex-1 overflow-y-auto">
        <!-- Institution Branding -->
        <div class="p-5 border-b border-outline-variant/60">
            <a href="{{ route('dashboard') }}" class="font-serif text-[20px] font-semibold text-primary block leading-tight hover:text-brand-dark transition-colors tracking-tight">
                ENEAM PV Archive
            </a>
            <span class="text-[12px] text-secondary block mt-0.5">
                Archivage des délibérations
            </span>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-3 space-y-1" aria-label="Navigation latérale">
            <!-- Tableau de bord -->
            <a
                href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-surface-container-high text-primary font-semibold border-l-4 border-primary' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}"
            >
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-secondary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span>Tableau de bord</span>
            </a>

            <!-- Procès-verbaux -->
            <a
                href="{{ route('pvs.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body-sm font-medium transition-colors {{ request()->routeIs('pvs.index', 'pvs.show', 'pvs.edit') ? 'bg-surface-container-high text-primary font-semibold border-l-4 border-primary' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}"
            >
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('pvs.index', 'pvs.show', 'pvs.edit') ? 'text-primary' : 'text-secondary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span>Procès-verbaux</span>
            </a>

            <!-- Ajouter des PV -->
            @can('create', App\Models\DeliberationPv::class)
                <a
                    href="{{ route('pvs.create') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body-sm font-medium transition-colors {{ request()->routeIs('pvs.create') ? 'bg-surface-container-high text-primary font-semibold border-l-4 border-primary' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}"
                >
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('pvs.create') ? 'text-primary' : 'text-secondary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Ajouter des PV</span>
                </a>
            @endcan

            <!-- Sections d'administration DA -->
            @if(Auth::user()->isDa())
                <div class="pt-3 pb-1 px-3">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-outline">
                        Administration
                    </span>
                </div>

                <!-- Journal -->
                <a
                    href="{{ route('admin.logs.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body-sm font-medium transition-colors {{ request()->routeIs('admin.logs.*') ? 'bg-surface-container-high text-primary font-semibold border-l-4 border-primary' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}"
                >
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.logs.*') ? 'text-primary' : 'text-secondary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Journal</span>
                </a>

                <!-- Utilisateurs -->
                <a
                    href="{{ route('admin.users.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body-sm font-medium transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-surface-container-high text-primary font-semibold border-l-4 border-primary' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}"
                >
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-primary' : 'text-secondary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Utilisateurs</span>
                </a>

                <!-- Années académiques -->
                <a
                    href="{{ route('admin.years.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-body-sm font-medium transition-colors {{ request()->routeIs('admin.years.*') ? 'bg-surface-container-high text-primary font-semibold border-l-4 border-primary' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}"
                >
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.years.*') ? 'text-primary' : 'text-secondary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Années académiques</span>
                </a>
            @endif
        </nav>
    </div>
</aside>

<!-- Mobile Overlay Backdrop -->
<div
    x-show="sidebarOpen"
    @click="sidebarOpen = false"
    x-cloak
    class="fixed inset-0 z-40 bg-on-surface/30 lg:hidden backdrop-blur-xs transition-opacity"
></div>
