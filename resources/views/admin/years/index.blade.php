<x-app-layout>
    <div class="max-w-[1280px] mx-auto px-margin py-space-lg w-full">
        <!-- Breadcrumb & Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-space-md pb-space-lg border-b border-outline-variant">
            <div class="space-y-space-xs">
                <div class="flex items-center gap-space-sm text-outline font-label-sm text-label-sm">
                    <span>Administration</span>
                    <span>/</span>
                    <span class="text-on-surface">Années académiques</span>
                </div>
                <h1 class="font-serif text-[32px] font-semibold text-on-surface tracking-tight leading-tight">
                    Années académiques
                </h1>
                <p class="font-body-md text-body-md text-secondary">
                    Gestion des sessions universitaires et désignation de l'année en cours
                </p>
            </div>

            <div class="flex items-center gap-space-sm pt-space-xs sm:pt-0">
                <a
                    href="{{ route('admin.years.create') }}"
                    class="inline-flex items-center gap-space-xs bg-primary-container hover:bg-primary text-on-primary font-label-md text-label-md px-space-md py-space-sm rounded transition-colors"
                >
                    <svg class="w-4 h-4 text-on-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Ajouter une année</span>
                </a>
            </div>
        </div>

        <!-- Inventory Summary Strip -->
        <div class="py-space-md flex flex-wrap items-center justify-between gap-space-md text-body-sm text-secondary border-b border-outline-variant">
            <div class="flex items-center gap-space-lg">
                <div>
                    <span class="text-on-surface font-medium">{{ count($years) }}</span> sessions enregistrées
                </div>
                @php
                    $currentYear = $years->firstWhere('is_active', true);
                @endphp
                @if($currentYear)
                    <div class="w-1 h-1 rounded-full bg-outline-variant"></div>
                    <div>
                        Année courante active :
                        <span class="font-reference-code text-reference-code text-primary-container font-semibold">
                            {{ $currentYear->label }}
                        </span>
                    </div>
                @endif
            </div>

            <div class="text-label-sm font-label-sm text-outline">
                Configuration certifiée ENEAM
            </div>
        </div>

        <!-- Academic Years Table -->
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-outline-variant text-label-sm font-label-sm text-secondary">
                        <th class="py-space-md px-space-sm font-medium" scope="col">Session</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Date de début</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Date de fin</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Statut</th>
                        <th class="py-space-md px-space-sm font-medium text-right" scope="col">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant text-body-md font-body-md">
                    @forelse($years as $year)
                        <tr class="transition-colors hover:bg-surface-container-low">
                            <!-- Label -->
                            <td class="py-space-md px-space-sm font-reference-code text-reference-code text-on-surface">
                                <span>{{ $year->label }}</span>
                                @if($year->external_id)
                                    <span class="block text-xs text-outline font-mono">Réf. {{ $year->external_id }}</span>
                                @endif
                            </td>

                            <!-- Starts on -->
                            <td class="py-space-md px-space-sm text-on-surface-variant font-body-sm text-body-sm">
                                {{ $year->starts_on ? $year->starts_on->format('d/m/Y') : '—' }}
                            </td>

                            <!-- Ends on -->
                            <td class="py-space-md px-space-sm text-on-surface-variant font-body-sm text-body-sm">
                                {{ $year->ends_on ? $year->ends_on->format('d/m/Y') : '—' }}
                            </td>

                            <!-- Active Status -->
                            <td class="py-space-md px-space-sm">
                                @if($year->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-label-sm font-label-sm bg-surface-container-high text-primary border border-outline-variant font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                        Année courante
                                    </span>
                                @else
                                    <span class="text-secondary font-body-sm text-body-sm">Inactive</span>
                                @endif
                            </td>

                            <!-- Edit Action -->
                            <td class="py-space-md px-space-sm text-right">
                                <a
                                    href="{{ route('admin.years.edit', $year) }}"
                                    class="inline-flex items-center gap-1 font-label-md text-label-md text-primary-container hover:text-primary hover:underline transition-colors"
                                >
                                    <span>Modifier</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-secondary italic">
                                Aucune année académique enregistrée pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
