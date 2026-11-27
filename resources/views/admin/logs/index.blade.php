<x-app-layout>
    <div class="max-w-[1280px] mx-auto px-margin py-space-lg w-full">
        <!-- Breadcrumb & Header -->
        <div class="pb-space-lg border-b border-outline-variant mb-space-lg">
            <div class="flex items-center gap-space-sm text-outline font-label-sm text-label-sm mb-1">
                <span>Administration</span>
                <span>/</span>
                <span class="text-on-surface">Audit et traçabilité</span>
            </div>
            <h1 class="font-serif text-[32px] font-semibold text-on-surface tracking-tight leading-tight">
                Journal d'audit
            </h1>
            <p class="font-body-md text-body-md text-secondary mt-1">
                Historique des activités, des consultations et de la traçabilité des opérations sur les PVs
            </p>
        </div>

        <!-- Inventory Summary Strip -->
        <div class="py-space-sm flex flex-wrap items-center justify-between gap-space-sm text-body-sm text-secondary border-b border-outline-variant mb-space-sm">
            <div class="flex items-center gap-space-md">
                <div>
                    <span class="text-on-surface font-medium">{{ $logs->total() }}</span> événements enregistrés
                </div>
                <div class="w-1 h-1 rounded-full bg-outline-variant"></div>
                <div class="text-label-sm font-label-sm text-outline">
                    Traçabilité certifiée conforme
                </div>
            </div>

            <div class="text-label-sm font-label-sm text-outline">
                Page {{ $logs->currentPage() }} sur {{ max($logs->lastPage(), 1) }}
            </div>
        </div>

        <!-- Audit Log Table -->
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-outline-variant text-label-sm font-label-sm text-secondary">
                        <th class="py-space-md px-space-sm font-medium w-44" scope="col">Date et heure</th>
                        <th class="py-space-md px-space-sm font-medium w-48" scope="col">Utilisateur</th>
                        <th class="py-space-md px-space-sm font-medium w-36" scope="col">Action</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Description de l'opération</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm text-on-surface">
                    @forelse($logs as $log)
                        <tr class="transition-colors hover:bg-surface-container-low">
                            <!-- Timestamp -->
                            <td class="py-space-md px-space-sm text-secondary font-mono text-[12px] whitespace-nowrap">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '—' }}
                            </td>

                            <!-- User -->
                            <td class="py-space-md px-space-sm font-medium text-on-surface">
                                {{ $log->user?->name ?? 'Système' }}
                            </td>

                            <!-- Action Badge -->
                            <td class="py-space-md px-space-sm whitespace-nowrap">
                                @php
                                    $actionLabels = [
                                        'created' => 'Soumission',
                                        'submitted' => 'Soumission',
                                        'updated' => 'Mise à jour',
                                        'archived' => 'Archivage',
                                        'viewed' => 'Consultation',
                                        'deleted' => 'Suppression',
                                    ];
                                    $actionClass = match($log->action) {
                                        'archived' => 'bg-surface-container-high text-primary border border-outline-variant',
                                        'created', 'submitted' => 'bg-surface-container-low text-primary-container border border-outline-variant',
                                        'deleted' => 'bg-error-container text-on-error-container border border-error/30',
                                        default => 'bg-surface-container text-secondary border border-outline-variant',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-label-sm font-medium {{ $actionClass }}">
                                    {{ $actionLabels[$log->action] ?? ucfirst($log->action) }}
                                </span>
                            </td>

                            <!-- Description -->
                            <td class="py-space-md px-space-sm text-on-surface-variant">
                                {{ $log->details ?? 'Opération effectuée' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-secondary italic">
                                Aucun événement répertorié dans le journal d'audit.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-space-lg pt-space-md border-t border-outline-variant">
            {{ $logs->links() }}
        </div>
    </div>
</x-app-layout>
