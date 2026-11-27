<x-app-layout>
    <div class="max-w-[1280px] mx-auto px-margin py-space-lg w-full">
        <!-- Title & Subtitle -->
        <div class="pb-space-lg border-b border-outline-variant">
            <h1 class="font-serif text-[32px] font-semibold text-on-surface tracking-tight leading-tight">
                Tableau de bord
            </h1>
            <p class="font-body-md text-body-md text-secondary mt-1">
                Vue synthétique du fonds documentaire et de la conservation des délibérations
            </p>
        </div>

        <!-- 3 Grands chiffres sobres alignés -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-lg py-space-lg border-b border-outline-variant">
            <!-- Total -->
            <div class="flex flex-col">
                <span class="font-serif text-[46px] leading-none text-on-surface font-semibold">
                    {{ $stats['total'] }}
                </span>
                <span class="font-body-sm text-body-sm text-secondary mt-2">
                    PV au total
                </span>
            </div>

            <!-- Soumis -->
            <div class="flex flex-col">
                <span class="font-serif text-[46px] leading-none text-primary-container font-semibold">
                    {{ $stats['soumis'] }}
                </span>
                <span class="font-body-sm text-body-sm text-secondary mt-2">
                    PV soumis en attente d'archivage
                </span>
            </div>

            <!-- Archivés -->
            <div class="flex flex-col">
                <span class="font-serif text-[46px] leading-none text-on-surface font-semibold">
                    {{ $stats['archive'] }}
                </span>
                <span class="font-body-sm text-body-sm text-secondary mt-2">
                    PV archivés définitivement
                </span>
            </div>
        </div>

        <!-- Deux listes de statistiques côte à côte -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-16 gap-y-12 py-space-lg border-b border-outline-variant">
            <!-- PV par filière -->
            <div class="flex flex-col">
                <div class="flex items-baseline justify-between pb-space-md">
                    <h2 class="font-serif text-[18px] font-semibold text-on-surface">PV par filière</h2>
                    <span class="text-label-sm font-label-sm text-outline">{{ count($stats['parFiliere']) }} filières</span>
                </div>

                <div class="space-y-4">
                    @forelse($stats['parFiliere'] as $filiere)
                        @php
                            $pctFiliere = $stats['total'] > 0 ? round(($filiere->total / $stats['total']) * 100, 1) : 0;
                        @endphp
                        <div class="flex flex-col gap-1.5">
                            <div class="flex justify-between items-baseline text-body-sm font-body-sm">
                                <span class="text-on-surface font-medium">{{ $filiere->name }} ({{ $filiere->code }})</span>
                                <span class="text-secondary font-medium">{{ $filiere->total }} <span class="text-outline text-xs">({{ $pctFiliere }}%)</span></span>
                            </div>
                            <div class="w-full bg-surface-container-high h-[4px] rounded-full overflow-hidden">
                                <div class="bg-primary-container h-full rounded-full transition-all duration-300" style="width: {{ $pctFiliere }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-body-sm text-secondary italic">Aucune donnée de filière enregistrée.</p>
                    @endforelse
                </div>
            </div>

            <!-- PV par année académique -->
            <div class="flex flex-col">
                <div class="flex items-baseline justify-between pb-space-md">
                    <h2 class="font-serif text-[18px] font-semibold text-on-surface">PV par année académique</h2>
                    <span class="text-label-sm font-label-sm text-outline">{{ count($stats['parAnnee']) }} sessions</span>
                </div>

                <div class="space-y-4">
                    @forelse($stats['parAnnee'] as $annee)
                        @php
                            $pctAnnee = $stats['total'] > 0 ? round(($annee->total / $stats['total']) * 100, 1) : 0;
                        @endphp
                        <div class="flex flex-col gap-1.5">
                            <div class="flex justify-between items-baseline text-body-sm font-body-sm">
                                <span class="text-on-surface font-medium font-reference-code">{{ $annee->label }}</span>
                                <span class="text-secondary font-medium">{{ $annee->total }} <span class="text-outline text-xs">({{ $pctAnnee }}%)</span></span>
                            </div>
                            <div class="w-full bg-surface-container-high h-[4px] rounded-full overflow-hidden">
                                <div class="bg-primary-container h-full rounded-full transition-all duration-300" style="width: {{ $pctAnnee }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-body-sm text-secondary italic">Aucune session académique répertoriée.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Derniers PV ajoutés -->
        <div class="pt-space-lg">
            <div class="flex items-baseline justify-between pb-space-md">
                <h2 class="font-serif text-[18px] font-semibold text-on-surface">Derniers PV répertoriés</h2>
                <a
                    href="{{ route('pvs.index') }}"
                    class="font-label-md text-label-md text-primary hover:underline transition-colors inline-flex items-center gap-1"
                >
                    <span>Consulter tout le registre</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>

            <div class="w-full overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-outline-variant text-label-sm font-label-sm text-secondary">
                            <th class="py-3 pr-6 font-medium" scope="col">Classe</th>
                            <th class="py-3 px-6 font-medium" scope="col">Filière</th>
                            <th class="py-3 px-6 font-medium" scope="col">Année académique</th>
                            <th class="py-3 px-6 font-medium" scope="col">Date de délibération</th>
                            <th class="py-3 px-6 font-medium" scope="col">Statut</th>
                            <th class="py-3 pl-6 text-right font-medium" scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant text-body-sm font-body-sm">
                        @forelse($latest as $pv)
                            <tr class="hover:bg-surface-container-low transition-colors">
                                <td class="py-3.5 pr-6 whitespace-nowrap font-serif font-semibold text-[15px] text-on-surface">
                                    <a href="{{ route('pvs.show', $pv) }}" class="hover:text-primary transition-colors">
                                        {{ $pv->class_name }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-6 whitespace-nowrap text-on-surface-variant">
                                    {{ $pv->program?->name }} ({{ $pv->program?->code }})
                                </td>
                                <td class="py-3.5 px-6 whitespace-nowrap font-reference-code text-on-surface-variant">
                                    {{ $pv->academicYear?->label ?? '—' }}
                                </td>
                                <td class="py-3.5 px-6 whitespace-nowrap text-secondary">
                                    {{ $pv->deliberation_date ? $pv->deliberation_date->format('d/m/Y') : '—' }}
                                </td>
                                <td class="py-3.5 px-6 whitespace-nowrap">
                                    <x-pv-status-badge :status="$pv->status" />
                                </td>
                                <td class="py-3.5 pl-6 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-3 justify-end">
                                        <a
                                            href="{{ route('pvs.file', $pv) }}"
                                            target="_blank"
                                            class="text-primary hover:underline font-label-md text-label-md inline-flex items-center gap-1 transition-colors"
                                            title="Ouvrir directement dans le navigateur"
                                        >
                                            <span>Ouvrir</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                        <a
                                            href="{{ route('pvs.show', $pv) }}"
                                            class="text-secondary hover:text-on-surface hover:underline font-label-md text-label-md inline-flex items-center gap-1 transition-colors"
                                        >
                                            <span>Fiche</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-secondary italic">
                                    Aucun procès-verbal disponible actuellement.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
