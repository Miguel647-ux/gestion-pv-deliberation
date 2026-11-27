<x-app-layout>
    <div class="max-w-[1280px] mx-auto px-margin py-space-lg w-full">
        <!-- Breadcrumb & Header -->
        <div class="pb-space-lg border-b border-outline-variant mb-space-lg">
            <div class="flex items-center gap-space-sm text-outline font-label-sm text-label-sm mb-1">
                <span>Administration</span>
                <span>/</span>
                <span class="text-on-surface">Comptes utilisateurs</span>
            </div>
            <h1 class="font-serif text-[32px] font-semibold text-on-surface tracking-tight leading-tight">
                Utilisateurs
            </h1>
            <p class="font-body-md text-body-md text-secondary mt-1">
                Liste institutionnelle des accès autorisés et affectation des filières
            </p>
        </div>

        <!-- Inventory Summary Strip -->
        <div class="py-space-sm flex flex-wrap items-center justify-between gap-space-sm text-body-sm text-secondary border-b border-outline-variant mb-space-sm">
            <div class="flex items-center gap-space-md">
                <div>
                    <span class="text-on-surface font-medium">{{ count($users) }}</span> comptes enregistrés
                </div>
                <div class="w-1 h-1 rounded-full bg-outline-variant"></div>
                <div class="text-label-sm font-label-sm text-outline">
                    Registre en lecture seule
                </div>
            </div>

            <div class="text-label-sm font-label-sm text-primary flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <span>Authentification institutionnelle ENEAM</span>
            </div>
        </div>

        <!-- Users Table -->
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-outline-variant text-label-sm font-label-sm text-secondary">
                        <th class="py-space-md px-space-sm font-medium" scope="col">Nom complet</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Adresse e-mail institutionnelle</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Rôle</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Filière assignée</th>
                        <th class="py-space-md px-space-sm font-medium text-right" scope="col">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm">
                    @forelse($users as $user)
                        <tr class="transition-colors hover:bg-surface-container-low">
                            <!-- Name -->
                            <td class="py-space-md px-space-sm font-reference-code text-reference-code text-on-surface">
                                {{ $user->name }}
                            </td>

                            <!-- Email -->
                            <td class="py-space-md px-space-sm font-body-sm text-body-sm text-secondary">
                                {{ $user->email }}
                            </td>

                            <!-- Role -->
                            <td class="py-space-md px-space-sm">
                                @if($user->isDa())
                                    <span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-primary bg-surface-container-high px-2 py-0.5 rounded border border-outline-variant font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                        Directeur Académique
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 font-label-sm text-label-sm text-secondary bg-surface-container-low px-2 py-0.5 rounded border border-outline-variant">
                                        Chef de filière
                                    </span>
                                @endif
                            </td>

                            <!-- Program -->
                            <td class="py-space-md px-space-sm text-on-surface">
                                @if($user->program)
                                    <span class="font-medium">{{ $user->program->name }}</span>
                                    <span class="text-xs text-secondary font-mono">({{ $user->program->code }})</span>
                                @else
                                    <span class="text-outline italic">Toutes filières (Global)</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-space-md px-space-sm text-right font-label-sm text-label-sm text-primary">
                                Actif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-secondary italic">
                                Aucun utilisateur répertorié.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
