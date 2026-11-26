<x-app-layout>
    <div class="w-full space-y-6">
        <!-- Back Navigation & Title Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-outline-variant">
            <div class="flex items-center gap-4">
                <a
                    href="{{ route('pvs.index') }}"
                    class="inline-flex items-center gap-1.5 text-secondary hover:text-primary transition-colors font-label-md text-label-md shrink-0"
                >
                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Retour</span>
                </a>

                <div class="h-5 w-px bg-outline-variant shrink-0"></div>

                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="font-serif text-[28px] sm:text-[32px] text-on-surface tracking-tight font-semibold">
                        {{ $pv->class_name }}
                    </h1>
                    <x-pv-status-badge :status="$pv->status" />
                </div>
            </div>

            <!-- Action Buttons Toolbar -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Bouton Ouvrir directement dans le navigateur (Plein écran) -->
                <a
                    href="{{ route('pvs.file', $pv) }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded bg-primary hover:bg-brand-dark text-on-primary font-label-md text-label-md font-semibold transition-colors shadow-xs"
                    title="Ouvrir le document dans un nouvel onglet en plein écran"
                >
                    <svg class="w-4 h-4 text-on-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    <span>Ouvrir en plein écran</span>
                </a>

                @can('update', $pv)
                    <a
                        href="{{ route('pvs.edit', $pv) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded text-label-md font-label-md text-on-surface bg-surface-container-lowest border border-outline-variant hover:bg-surface-container transition-colors"
                    >
                        <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Modifier</span>
                    </a>
                @endcan

                @can('archive', $pv)
                    <form
                        method="POST"
                        action="{{ route('pvs.archive', $pv) }}"
                        class="inline"
                        onsubmit="return confirm('Confirmez-vous l\'archivage définitif de ce PV ? Cette action est irréversible.');"
                    >
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded text-label-md font-label-md text-on-primary bg-primary-container hover:bg-primary transition-colors cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-on-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                            <span>Archiver</span>
                        </button>
                    </form>
                @endcan

                @can('delete', $pv)
                    <form
                        method="POST"
                        action="{{ route('pvs.destroy', $pv) }}"
                        class="inline"
                        onsubmit="return confirm('Confirmez-vous la suppression de ce procès-verbal ?');"
                    >
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded text-label-md font-label-md text-error bg-transparent border border-error/30 hover:bg-error-container/40 transition-colors cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Supprimer</span>
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <!-- Metadata Summary Strip (Disposition horizontale synthétique) -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-4 shadow-2xs">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 text-body-sm">
                <div>
                    <span class="text-label-sm font-medium text-secondary block">Filière</span>
                    <span class="text-on-surface font-semibold block truncate" title="{{ $pv->program?->name }}">
                        {{ $pv->program?->name }} ({{ $pv->program?->code }})
                    </span>
                </div>

                <div>
                    <span class="text-label-sm font-medium text-secondary block">Niveau</span>
                    <span class="text-on-surface font-semibold block">
                        {{ $pv->level?->name }} ({{ $pv->level?->code }})
                    </span>
                </div>

                <div>
                    <span class="text-label-sm font-medium text-secondary block">Année académique</span>
                    <span class="text-on-surface font-semibold block font-reference-code">
                        {{ $pv->academicYear?->label ?? '—' }}
                    </span>
                </div>

                <div>
                    <span class="text-label-sm font-medium text-secondary block">Date délibération</span>
                    <span class="text-on-surface font-semibold block">
                        {{ $pv->deliberation_date ? $pv->deliberation_date->format('d/m/Y') : '—' }}
                    </span>
                </div>

                <div>
                    <span class="text-label-sm font-medium text-secondary block">Fichier scanné</span>
                    <span class="text-on-surface font-semibold block truncate" title="{{ $pv->file_name }}">
                        {{ $pv->file_name }}
                    </span>
                    <span class="text-[11px] text-secondary">
                        {{ $pv->file_size > 1048576 ? round($pv->file_size / 1048576, 2) . ' Mo' : round($pv->file_size / 1024, 1) . ' Ko' }}
                    </span>
                </div>

                <div>
                    <span class="text-label-sm font-medium text-secondary block">Enregistré par</span>
                    <span class="text-on-surface font-semibold block truncate">
                        {{ $pv->creator?->name ?? 'Système' }}
                    </span>
                    <span class="text-[11px] text-secondary">
                        Le {{ $pv->created_at->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Full-Width Document Viewer (Affichage direct, spacieux et lisible dans le navigateur) -->
        <div class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg p-2 shadow-sm flex flex-col">
            <div class="flex items-center justify-between px-3 py-2 border-b border-outline-variant/60 text-body-sm text-secondary mb-2">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                    <span class="font-medium text-on-surface">Visualisation du procès-verbal officiel</span>
                </div>
                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('pvs.file', $pv) }}"
                        target="_blank"
                        class="text-primary hover:underline font-label-md text-label-md flex items-center gap-1"
                    >
                        <span>Ouvrir dans un nouvel onglet</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="w-full">
                @if(str_contains($pv->file_type, 'pdf'))
                    <iframe
                        src="{{ route('pvs.file', $pv) }}"
                        class="w-full h-[950px] rounded border border-outline-variant/50"
                        title="Document complet du PV {{ $pv->class_name }}"
                    >
                        Votre navigateur ne prend pas en charge l'affichage PDF direct.
                        <a href="{{ route('pvs.file', $pv) }}" target="_blank" class="text-primary underline font-medium">
                            Cliquez ici pour ouvrir le document dans votre navigateur.
                        </a>
                    </iframe>
                @else
                    <div class="p-6 flex items-center justify-center bg-surface-container-low min-h-[600px] rounded">
                        <img
                            src="{{ route('pvs.file', $pv) }}"
                            alt="Document {{ $pv->class_name }}"
                            class="max-w-full h-auto max-h-[950px] object-contain rounded border border-outline-variant shadow-sm"
                        >
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
