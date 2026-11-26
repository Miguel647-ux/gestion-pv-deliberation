<x-app-layout>
    <div class="max-w-[1000px] mx-auto px-margin py-space-lg w-full">
        <!-- Back Navigation -->
        <div class="mb-space-md flex items-center">
            <a
                href="{{ route('pvs.index') }}"
                class="inline-flex items-center gap-space-xs text-secondary hover:text-primary transition-colors font-label-md text-label-md"
            >
                <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Retour aux procès-verbaux</span>
            </a>
        </div>

        <!-- Page Header -->
        <div class="pb-space-lg border-b border-outline-variant mb-space-lg">
            <div class="flex items-center gap-space-sm text-outline font-label-sm text-label-sm mb-1">
                <span>Archives</span>
                <span>/</span>
                <span>Procès-verbaux</span>
                <span>/</span>
                <span class="text-on-surface">Nouveau dépôt</span>
            </div>
            <h1 class="font-serif text-[32px] font-semibold text-on-surface tracking-tight leading-tight">
                Ajouter des procès-verbaux
            </h1>
            <p class="font-body-md text-body-md text-secondary mt-1">
                Enregistrez les informations communes puis déposez vos fichiers scannés par classe.
            </p>
        </div>

        <!-- Global Errors Notification -->
        @if ($errors->any())
            <div class="mb-space-lg p-space-md bg-error-container text-on-error-container border border-error rounded text-body-sm">
                <div class="font-medium mb-1">Veuillez corriger les erreurs ci-dessous :</div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            method="POST"
            action="{{ route('pvs.store') }}"
            enctype="multipart/form-data"
            class="space-y-space-xl"
            id="pv-create-form"
        >
            @csrf

            <!-- Section 1 : Informations académiques communes -->
            <div class="space-y-space-md bg-surface-container-lowest border border-outline-variant rounded p-space-lg">
                <div class="pb-space-xs border-b border-outline-variant flex items-center justify-between">
                    <span class="text-label-sm font-label-sm text-secondary uppercase tracking-wider font-semibold">
                        01. Contexte académique
                    </span>
                    <span class="text-label-sm text-outline">Champs communs à tout le lot</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
                    <!-- Année académique -->
                    <div class="flex flex-col gap-1">
                        <label for="academic_year_id" class="text-label-md font-label-md text-on-surface font-medium">
                            Année académique <span class="text-error">*</span>
                        </label>
                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            required
                            class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors cursor-pointer"
                        >
                            <option value="">Sélectionner une session...</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(old('academic_year_id') == $year->id || ($loop->first && !old('academic_year_id')))>
                                    {{ $year->label }} @if($year->is_active)(Session en cours)@endif
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('academic_year_id')" class="mt-1" />
                    </div>

                    <!-- Filière -->
                    <div class="flex flex-col gap-1">
                        <label for="program_id" class="text-label-md font-label-md text-on-surface font-medium">
                            Filière <span class="text-error">*</span>
                        </label>
                        @if(Auth::user()->isDa())
                            <select
                                name="program_id"
                                id="program_id"
                                required
                                class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors cursor-pointer"
                            >
                                <option value="">Sélectionner une filière...</option>
                                @foreach($programs as $prog)
                                    <option value="{{ $prog->id }}" @selected(old('program_id') == $prog->id) data-code="{{ $prog->code }}">
                                        {{ $prog->name }} ({{ $prog->code }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            @php
                                $userProg = $programs->firstWhere('id', Auth::user()->program_id);
                            @endphp
                            <div class="h-10 px-3 bg-surface-container-low border border-outline-variant rounded text-on-surface font-body-sm text-body-sm flex items-center justify-between">
                                <span class="font-medium">{{ $userProg?->name ?? 'Filière assignée' }} ({{ $userProg?->code }})</span>
                                <span class="text-label-sm text-outline">Filière automatique</span>
                            </div>
                            <input type="hidden" name="program_id" id="program_id" value="{{ Auth::user()->program_id }}" data-code="{{ $userProg?->code }}">
                        @endif
                        <x-input-error :messages="$errors->get('program_id')" class="mt-1" />
                    </div>

                    <!-- Niveau -->
                    <div class="flex flex-col gap-1">
                        <label for="level_id" class="text-label-md font-label-md text-on-surface font-medium">
                            Niveau <span class="text-error">*</span>
                        </label>
                        <select
                            name="level_id"
                            id="level_id"
                            required
                            class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors cursor-pointer"
                        >
                            <option value="">Sélectionner un niveau...</option>
                            @foreach($levels as $lvl)
                                <option value="{{ $lvl->id }}" @selected(old('level_id') == $lvl->id) data-code="{{ $lvl->code }}">
                                    {{ $lvl->name }} ({{ $lvl->code }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('level_id')" class="mt-1" />
                    </div>

                    <!-- Semestre -->
                    <div class="flex flex-col gap-1">
                        <label for="semester_id" class="text-label-md font-label-md text-on-surface font-medium">
                            Semestre <span class="text-error">*</span>
                        </label>
                        <select
                            name="semester_id"
                            id="semester_id"
                            required
                            class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors cursor-pointer"
                        >
                            <option value="">Sélectionner un semestre...</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" @selected(old('semester_id') == $sem->id) data-code="{{ $sem->code }}">
                                    {{ $sem->name }} ({{ $sem->code }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('semester_id')" class="mt-1" />
                    </div>

                    <!-- Date de délibération -->
                    <div class="flex flex-col gap-1">
                        <label for="deliberation_date" class="text-label-md font-label-md text-on-surface font-medium">
                            Date de délibération <span class="text-error">*</span>
                        </label>
                        <input
                            type="date"
                            name="deliberation_date"
                            id="deliberation_date"
                            required
                            value="{{ old('deliberation_date', date('Y-m-d')) }}"
                            class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors cursor-pointer"
                        >
                        <x-input-error :messages="$errors->get('deliberation_date')" class="mt-1" />
                    </div>

                    <!-- Spécialité (uniquement IG L3) -->
                    <div class="flex flex-col gap-1" id="specialty-wrapper">
                        <label for="specialty" class="text-label-md font-label-md text-on-surface font-medium">
                            Spécialité <span class="text-label-sm text-secondary font-normal">(Optionnelle / IG L3)</span>
                        </label>
                        <select
                            name="specialty"
                            id="specialty"
                            class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors cursor-pointer"
                        >
                            <option value="">Aucune spécialité (tronc commun)</option>
                            @foreach($specialties as $sp)
                                <option value="{{ $sp }}" @selected(old('specialty') === $sp)>{{ $sp }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('specialty')" class="mt-1" />
                    </div>
                </div>
            </div>

            <!-- Section 2 : Fichiers scannés & assignation de classe -->
            <div class="space-y-space-md bg-surface-container-lowest border border-outline-variant rounded p-space-lg">
                <div class="pb-space-xs border-b border-outline-variant flex items-center justify-between">
                    <span class="text-label-sm font-label-sm text-secondary uppercase tracking-wider font-semibold">
                        02. Dépôt des procès-verbaux
                    </span>
                </div>

                <!-- Datalist dynamique des vraies classes suggérées -->
                <datalist id="classes-suggestions"></datalist>

                <!-- Drag and drop zone -->
                <div
                    id="drop-zone"
                    class="relative w-full border-2 border-dashed border-outline-variant hover:border-primary rounded-lg p-space-xl flex flex-col items-center justify-center text-center cursor-pointer transition-colors bg-surface-container-low/40"
                    onclick="document.getElementById('file-picker').click()"
                >
                    <input
                        type="file"
                        id="file-picker"
                        multiple
                        accept=".pdf,.png,.jpg,.jpeg"
                        class="hidden"
                        onchange="handleSelectedFiles(this.files)"
                    >
                    <div class="w-12 h-12 rounded-full bg-surface-container-high flex items-center justify-center mb-space-sm text-primary">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                    <p class="font-label-lg text-label-lg text-on-surface font-medium">
                        Glissez vos fichiers ici ou <span class="text-primary underline">cliquez pour les choisir</span>
                    </p>
                    <p class="text-label-sm font-label-sm text-secondary mt-1">
                        PDF, JPG ou PNG, 5 Mo maximum par fichier
                    </p>
                </div>

                <!-- Selected items container -->
                <div id="items-section" class="space-y-3 pt-space-xs hidden">
                    <div class="flex items-center justify-between pb-1">
                        <span class="text-label-md font-label-md text-on-surface font-medium" id="items-counter">Fichiers sélectionnés (0)</span>
                        <span class="text-label-sm font-label-sm text-secondary">Assignez une classe à chaque document</span>
                    </div>

                    <div id="items-list" class="space-y-3">
                        <!-- Dynamically injected item rows -->
                    </div>
                </div>

                <x-input-error :messages="$errors->get('items')" class="mt-1" />
            </div>

            <!-- Submit action bar -->
            <div class="flex items-center justify-end gap-space-sm pt-space-md border-t border-outline-variant">
                <a
                    href="{{ route('pvs.index') }}"
                    class="px-space-lg py-space-sm border border-outline-variant rounded font-label-md text-label-md text-secondary hover:text-on-surface transition-colors"
                >
                    Annuler
                </a>
                <button
                    type="submit"
                    id="submit-btn"
                    class="px-space-xl py-space-sm bg-primary-container hover:bg-primary text-on-primary font-label-md text-label-md rounded flex items-center gap-space-xs transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4 text-on-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Enregistrer et soumettre le lot</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Client-side script for Drag & Drop and Dynamic Inputs -->
    <script>
        let filesList = [];
        const userClassNames = {};

        const dropZone = document.getElementById('drop-zone');
        const itemsSection = document.getElementById('items-section');
        const itemsList = document.getElementById('items-list');
        const itemsCounter = document.getElementById('items-counter');

        function getSuggestedClasses() {
            const levelSelect = document.getElementById('level_id');
            const levelCode = levelSelect ? (levelSelect.options[levelSelect.selectedIndex]?.getAttribute('data-code') || '') : '';
            
            let programCode = '';
            const programSelect = document.getElementById('program_id');
            if (programSelect) {
                if (programSelect.tagName === 'SELECT') {
                    programCode = programSelect.options[programSelect.selectedIndex]?.getAttribute('data-code') || '';
                } else {
                    programCode = programSelect.getAttribute('data-code') || '';
                }
            }
            
            const specialtySelect = document.getElementById('specialty');
            const specialtyVal = specialtySelect ? specialtySelect.value : '';

            if (!levelCode || !programCode) return [];

            let prefix = levelCode + ' ' + programCode;
            if (specialtyVal) {
                prefix += '-' + specialtyVal;
            }

            return [prefix + ' A', prefix + ' B', prefix + ' C', prefix + ' D'];
        }

        function updateSuggestionsDatalist() {
            const datalist = document.getElementById('classes-suggestions');
            if (!datalist) return;
            const suggestions = getSuggestedClasses();
            datalist.innerHTML = suggestions.map(s => `<option value="${s}"></option>`).join('');
            
            const placeholderText = suggestions.length > 0 ? `Ex. ${suggestions[0]}` : 'Ex. L3 IG-ARI A';
            document.querySelectorAll('.class-name-input').forEach(input => {
                input.placeholder = placeholderText;
            });
        }

        document.getElementById('level_id')?.addEventListener('change', updateSuggestionsDatalist);
        document.getElementById('program_id')?.addEventListener('change', updateSuggestionsDatalist);
        document.getElementById('specialty')?.addEventListener('change', updateSuggestionsDatalist);

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.add('border-primary', 'bg-surface-container-high');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropZone.classList.remove('border-primary', 'bg-surface-container-high');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            if (dt && dt.files && dt.files.length > 0) {
                handleSelectedFiles(dt.files);
            }
        });

        function handleSelectedFiles(files) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                // Check if already added
                const already = filesList.some(f => f.name === file.name && f.size === file.size);
                if (!already) {
                    filesList.push(file);
                }
            }
            renderItems();
        }

        function removeFileIndex(index) {
            const removedFile = filesList[index];
            if (removedFile) {
                delete userClassNames[removedFile.name];
            }
            filesList.splice(index, 1);
            renderItems();
        }

        function renderItems() {
            itemsList.innerHTML = '';

            if (filesList.length === 0) {
                itemsSection.classList.add('hidden');
                itemsCounter.textContent = 'Fichiers sélectionnés (0)';
                return;
            }

            itemsSection.classList.remove('hidden');
            itemsCounter.textContent = `Fichiers sélectionnés (${filesList.length})`;

            const suggestions = getSuggestedClasses();
            const placeholderText = suggestions.length > 0 ? `Ex. ${suggestions[0]}` : 'Ex. L3 IG-ARI A';
            updateSuggestionsDatalist();

            filesList.forEach((file, index) => {
                const row = document.createElement('div');
                row.className = 'flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-surface-container-lowest border border-outline-variant rounded-lg p-3 transition-colors';

                const fileSizeStr = file.size > 1048576 
                    ? (file.size / 1048576).toFixed(1) + ' Mo' 
                    : (file.size / 1024).toFixed(0) + ' Ko';

                const existingName = userClassNames[file.name] || '';

                row.innerHTML = `
                    <div class="flex items-center gap-3 min-w-0 sm:w-5/12">
                        <div class="w-9 h-9 rounded bg-surface-container-high text-primary flex items-center justify-center shrink-0 border border-outline-variant">
                            <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-body-sm font-medium text-on-surface truncate" title="${file.name}">${file.name}</p>
                            <p class="text-label-sm text-secondary">${fileSizeStr} • Document conforme</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 sm:w-5/12">
                        <label class="text-label-sm text-secondary whitespace-nowrap" for="class_name_${index}">Classe <span class="text-error">*</span></label>
                        <input
                            type="text"
                            id="class_name_${index}"
                            name="items[${index}][class_name]"
                            list="classes-suggestions"
                            value="${existingName}"
                            oninput="userClassNames['${file.name}'] = this.value"
                            required
                            maxlength="100"
                            placeholder="${placeholderText}"
                            class="class-name-input w-full h-9 px-2.5 bg-surface-container-lowest border border-outline-variant rounded text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors"
                        >
                    </div>

                    <div class="sm:w-2/12 flex sm:justify-end">
                        <button
                            type="button"
                            onclick="removeFileIndex(${index})"
                            class="inline-flex items-center gap-1.5 text-body-sm font-normal text-secondary hover:text-error transition-colors px-2 py-1 rounded hover:bg-surface-container cursor-pointer"
                            title="Retirer ce fichier"
                        >
                            <svg class="w-4 h-4 text-secondary hover:text-error shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Retirer</span>
                        </button>
                    </div>
                `;

                // Hidden file input attached via DataTransfer
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = `items[${index}][file]`;
                fileInput.className = 'hidden';

                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;

                row.appendChild(fileInput);
                itemsList.appendChild(row);
            });
        }
    </script>
</x-app-layout>
