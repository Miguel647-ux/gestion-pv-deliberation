<x-app-layout>
    <div class="max-w-[1280px] mx-auto px-margin py-space-lg w-full">
        <!-- Breadcrumb & Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-space-md pb-space-lg border-b border-outline-variant">
            <div class="space-y-space-xs">
                <div class="flex items-center gap-space-sm text-outline font-label-sm text-label-sm">
                    <span>Archives</span>
                    <span>/</span>
                    <span class="text-on-surface">Procès-verbaux de délibération</span>
                </div>
                <h1 class="font-serif text-[32px] font-semibold text-on-surface tracking-tight leading-tight">
                    Procès-verbaux
                </h1>
                <p class="font-body-md text-body-md text-secondary">
                    Consultation, recherche et conservation des registres officiels de délibération
                </p>
            </div>

            <!-- Header Action Button -->
            @can('create', \App\Models\DeliberationPv::class)
                <div class="flex items-center gap-space-sm pt-space-xs sm:pt-0">
                    <a
                        href="{{ route('pvs.create') }}"
                        class="inline-flex items-center gap-space-xs bg-primary-container hover:bg-primary text-on-primary font-label-md text-label-md px-space-md py-space-sm rounded transition-colors"
                    >
                        <svg class="w-4 h-4 text-on-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Ajouter des PV</span>
                    </a>
                </div>
            @endcan
        </div>

        @php
            $userIsDa = Auth::user()->isDa();
            $userProgram = Auth::user()->isCd() ? $programs->firstWhere('id', Auth::user()->program_id) : null;
            $activeFiltersCount = collect($filters)->filter(fn($v) => filled($v))->count();
        @endphp

        <!-- Filter Section (Épurée : Recherche par classe, Année, Filière) -->
        <section aria-label="Filtres de recherche" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg p-space-md my-space-lg shadow-2xs">
            <form method="GET" action="{{ route('pvs.index') }}" class="flex flex-col md:flex-row items-stretch md:items-end gap-space-sm" id="pvs-filter-form">
                <!-- Search by Class Name Bar -->
                <div class="flex-1 flex flex-col gap-1">
                    <label for="filter_class_name" class="font-label-sm text-label-sm text-secondary font-medium">Recherche</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="class_name"
                            id="filter_class_name"
                            value="{{ request('class_name', $filters['class_name'] ?? '') }}"
                            placeholder="Rechercher par classe (ex. L3 IG-ARI A, L1 GFC...)"
                            class="w-full h-10 pl-9 pr-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:border-primary transition-colors"
                        >
                    </div>
                </div>

                <!-- Academic Year Filter -->
                <div class="w-full md:w-52 flex flex-col gap-1">
                    <label for="filter_academic_year" class="font-label-sm text-label-sm text-secondary font-medium">Année académique</label>
                    <select
                        name="academic_year_id"
                        id="filter_academic_year"
                        onchange="this.form.submit()"
                        class="w-full h-10 bg-surface-container-lowest border border-outline-variant rounded px-3 text-on-surface font-body-sm text-body-sm focus:outline-none focus:border-primary transition-colors cursor-pointer"
                    >
                        <option value="">Toutes les années</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" @selected(request('academic_year_id', $filters['academic_year_id'] ?? '') == $year->id)>
                                {{ $year->label }} @if($year->is_active)(Active)@endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Program / Filière Filter (Pour DA: menu déroulant ; Pour CD: fixé) -->
                @if($userIsDa)
                    <div class="w-full md:w-60 flex flex-col gap-1">
                        <label for="filter_program" class="font-label-sm text-label-sm text-secondary font-medium">Filière</label>
                        <select
                            name="program_id"
                            id="filter_program"
                            onchange="this.form.submit()"
                            class="w-full h-10 bg-surface-container-lowest border border-outline-variant rounded px-3 text-on-surface font-body-sm text-body-sm focus:outline-none focus:border-primary transition-colors cursor-pointer"
                        >
                            <option value="">Toutes les filières</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}" @selected(request('program_id', $filters['program_id'] ?? '') == $prog->id)>
                                    {{ $prog->code }} - {{ $prog->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex items-center gap-space-xs shrink-0">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-1.5 bg-primary-container hover:bg-primary text-on-primary font-label-md text-label-md h-10 px-4 rounded transition-colors cursor-pointer"
                    >
                        <span>Filtrer</span>
                    </button>

                    @if($activeFiltersCount > 0)
                        <a
                            href="{{ route('pvs.index') }}"
                            class="inline-flex items-center justify-center h-10 px-3 border border-outline-variant hover:bg-surface-container text-secondary hover:text-on-surface rounded transition-colors text-label-sm"
                            title="Réinitialiser les filtres"
                        >
                            <svg class="w-4 h-4 mr-1 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>Effacer</span>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        <!-- Inventory Summary Strip -->
        <div class="py-space-sm flex flex-wrap items-center justify-between gap-space-sm text-body-sm text-secondary border-b border-outline-variant mb-space-sm">
            <div class="flex items-center gap-space-md">
                <div>
                    <span class="text-on-surface font-medium">{{ $pvs->total() }}</span> procès-verbaux répertoriés
                </div>
                @if(request()->hasAny(['class_name', 'academic_year_id', 'program_id', 'level_id', 'semester_id', 'specialty', 'status']))
                    <div class="w-1 h-1 rounded-full bg-outline-variant"></div>
                    <span class="text-label-sm font-label-sm text-primary">Filtres appliqués</span>
                @endif
            </div>

            <div class="text-label-sm font-label-sm text-outline">
                Page {{ $pvs->currentPage() }} sur {{ max($pvs->lastPage(), 1) }}
            </div>
        </div>

        <!-- Archival Records Table -->
        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-outline-variant text-label-sm font-label-sm text-secondary">
                        <th class="py-space-md px-space-sm font-medium" scope="col">Classe</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Filière</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Niveau & Semestre</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Année académique</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Délibération</th>
                        <th class="py-space-md px-space-sm font-medium" scope="col">Statut</th>
                        <th class="py-space-md px-space-sm font-medium text-right" scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant font-body-sm text-body-sm text-on-surface">
                    @forelse($pvs as $pv)
                        <tr class="transition-colors hover:bg-surface-container-low">
                            <!-- Class Name -->
                            <td class="py-space-md px-space-sm">
                                <a
                                    href="{{ route('pvs.show', $pv) }}"
                                    class="font-serif font-semibold text-[15px] text-on-surface hover:text-primary transition-colors"
                                >
                                    {{ $pv->class_name }}
                                </a>
                                @if($pv->file_name)
                                    <span class="block text-label-sm text-outline truncate max-w-xs" title="{{ $pv->file_name }}">
                                        {{ $pv->file_name }}
                                    </span>
                                @endif
                            </td>

                            <!-- Program -->
                            <td class="py-space-md px-space-sm text-on-surface-variant">
                                <span class="font-medium text-on-surface">{{ $pv->program?->code }}</span>
                                <span class="block text-label-sm text-secondary truncate max-w-xs">{{ $pv->program?->name }}</span>
                            </td>

                            <!-- Level, Semester, Specialty -->
                            <td class="py-space-md px-space-sm text-on-surface-variant">
                                <span>{{ $pv->level?->code }} — {{ $pv->semester?->code }}</span>
                                @if($pv->specialty)
                                    <span class="inline-block text-label-sm font-medium text-primary">({{ $pv->specialty }})</span>
                                @endif
                            </td>

                            <!-- Academic Year -->
                            <td class="py-space-md px-space-sm text-on-surface-variant font-reference-code text-[14px]">
                                {{ $pv->academicYear?->label ?? '—' }}
                            </td>

                            <!-- Deliberation Date -->
                            <td class="py-space-md px-space-sm text-secondary">
                                {{ $pv->deliberation_date ? $pv->deliberation_date->format('d/m/Y') : '—' }}
                            </td>

                            <!-- Status Badge -->
                            <td class="py-space-md px-space-sm">
                                <x-pv-status-badge :status="$pv->status" />
                            </td>

                            <!-- Actions -->
                            <td class="py-space-md px-space-sm text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-space-sm justify-end">
                                    <!-- Direct Open PDF in Browser Link -->
                                    <a
                                        href="{{ route('pvs.file', $pv) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 font-label-md text-label-md text-on-surface hover:text-primary hover:underline transition-colors"
                                        title="Ouvrir directement dans le navigateur"
                                    >
                                        <span>Ouvrir</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>

                                    <!-- View Details Link -->
                                    <a
                                        href="{{ route('pvs.show', $pv) }}"
                                        class="inline-flex items-center gap-1 font-label-md text-label-md text-primary hover:underline transition-colors ml-2"
                                    >
                                        <span>Fiche</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <!-- Edit Link if Authorized -->
                                    @can('update', $pv)
                                        <a
                                            href="{{ route('pvs.edit', $pv) }}"
                                            class="inline-flex items-center gap-1 font-label-md text-label-md text-secondary hover:text-on-surface hover:underline transition-colors ml-2"
                                            title="Modifier ce PV"
                                        >
                                            <span>Modifier</span>
                                        </a>
                                    @endcan

                                    <!-- Archive Button if Authorized -->
                                    @can('archive', $pv)
                                        <form
                                            method="POST"
                                            action="{{ route('pvs.archive', $pv) }}"
                                            class="inline ml-2"
                                            onsubmit="return confirm('Confirmez-vous l\'archivage définitif du PV {{ $pv->class_name }} ?');"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                class="inline-flex items-center gap-1 font-label-md text-label-md text-primary-container hover:text-primary hover:underline transition-colors cursor-pointer"
                                                title="Archiver ce PV"
                                            >
                                                <span>Archiver</span>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8">
                                <x-empty-state
                                    title="Aucun procès-verbal trouvé"
                                    message="Aucun enregistrement ne correspond à vos filtres actuels. Modifiez vos critères de recherche ou réinitialisez les filtres."
                                    :actionUrl="route('pvs.index')"
                                    actionText="Effacer tous les filtres"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <div class="mt-space-lg pt-space-md border-t border-outline-variant">
            {{ $pvs->links() }}
        </div>
    </div>
</x-app-layout>
