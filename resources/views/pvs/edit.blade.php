<x-app-layout>
    <div class="max-w-[900px] mx-auto px-margin py-space-lg w-full">
        <!-- Back Navigation -->
        <div class="mb-space-md flex items-center">
            <a
                href="{{ route('pvs.show', $pv) }}"
                class="inline-flex items-center gap-space-xs text-secondary hover:text-primary transition-colors font-label-md text-label-md"
            >
                <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Retour au PV</span>
            </a>
        </div>

        <!-- Page Header -->
        <div class="pb-space-lg border-b border-outline-variant mb-space-lg">
            <div class="flex items-center gap-space-sm text-outline font-label-sm text-label-sm mb-1">
                <span>Archives</span>
                <span>/</span>
                <span>Procès-verbaux</span>
                <span>/</span>
                <span>{{ $pv->class_name }}</span>
                <span>/</span>
                <span class="text-on-surface">Modification</span>
            </div>
            <h1 class="font-serif text-[32px] font-semibold text-on-surface tracking-tight leading-tight">
                Modifier le procès-verbal
            </h1>
            <p class="font-body-md text-body-md text-secondary mt-1">
                Mise à jour des métadonnées ou remplacement du fichier scanné pour la classe {{ $pv->class_name }}.
            </p>
        </div>

        <!-- Global Errors -->
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
            action="{{ route('pvs.update', $pv) }}"
            enctype="multipart/form-data"
            class="space-y-space-xl"
        >
            @csrf
            @method('PUT')

            <div class="space-y-space-md bg-surface-container-lowest border border-outline-variant rounded p-space-lg">
                <!-- Class Name -->
                <div class="flex flex-col gap-1">
                    <label for="class_name" class="text-label-md font-label-md text-on-surface font-medium">
                        Nom de la classe <span class="text-error">*</span>
                    </label>
                    <input
                        type="text"
                        name="class_name"
                        id="class_name"
                        required
                        value="{{ old('class_name', $pv->class_name) }}"
                        placeholder="Ex. L3 IG-ARI A"
                        class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors"
                    >
                    <x-input-error :messages="$errors->get('class_name')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md pt-space-xs">
                    <!-- Academic Year -->
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
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(old('academic_year_id', $pv->academic_year_id) == $year->id)>
                                    {{ $year->label }} @if($year->is_active)(Active)@endif
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('academic_year_id')" class="mt-1" />
                    </div>

                    <!-- Program -->
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
                                @foreach($programs as $prog)
                                    <option value="{{ $prog->id }}" @selected(old('program_id', $pv->program_id) == $prog->id)>
                                        {{ $prog->name }} ({{ $prog->code }})
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="h-10 px-3 bg-surface-container-low border border-outline-variant rounded text-on-surface font-body-sm text-body-sm flex items-center">
                                <span class="font-medium">{{ $pv->program?->name }} ({{ $pv->program?->code }})</span>
                            </div>
                        @endif
                        <x-input-error :messages="$errors->get('program_id')" class="mt-1" />
                    </div>

                    <!-- Level -->
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
                            @foreach($levels as $lvl)
                                <option value="{{ $lvl->id }}" @selected(old('level_id', $pv->level_id) == $lvl->id)>
                                    {{ $lvl->name }} ({{ $lvl->code }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('level_id')" class="mt-1" />
                    </div>

                    <!-- Semester -->
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
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" @selected(old('semester_id', $pv->semester_id) == $sem->id)>
                                    {{ $sem->name }} ({{ $sem->code }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('semester_id')" class="mt-1" />
                    </div>

                    <!-- Deliberation Date -->
                    <div class="flex flex-col gap-1">
                        <label for="deliberation_date" class="text-label-md font-label-md text-on-surface font-medium">
                            Date de délibération <span class="text-error">*</span>
                        </label>
                        <input
                            type="date"
                            name="deliberation_date"
                            id="deliberation_date"
                            required
                            value="{{ old('deliberation_date', $pv->deliberation_date ? $pv->deliberation_date->format('Y-m-d') : '') }}"
                            class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors cursor-pointer"
                        >
                        <x-input-error :messages="$errors->get('deliberation_date')" class="mt-1" />
                    </div>

                    <!-- Specialty -->
                    <div class="flex flex-col gap-1">
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
                                <option value="{{ $sp }}" @selected(old('specialty', $pv->specialty) === $sp)>{{ $sp }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('specialty')" class="mt-1" />
                    </div>
                </div>

                <!-- Replace File Field -->
                <div class="pt-space-md border-t border-outline-variant">
                    <label class="text-label-md font-label-md text-on-surface font-medium block mb-1">
                        Fichier du procès-verbal
                    </label>
                    <div class="text-body-sm text-secondary mb-2 flex items-center gap-2">
                        <span>Fichier actuel :</span>
                        <span class="font-medium text-on-surface">{{ $pv->file_name }}</span>
                        <a href="{{ route('pvs.file', $pv) }}" target="_blank" class="text-primary hover:underline text-xs">(Consulter)</a>
                    </div>
                    <input
                        type="file"
                        name="file"
                        id="file"
                        accept=".pdf,.png,.jpg,.jpeg"
                        class="w-full text-body-sm text-secondary file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-label-sm file:font-medium file:bg-surface-container-high file:text-primary hover:file:bg-primary hover:file:text-on-primary file:cursor-pointer transition-colors"
                    >
                    <p class="text-label-sm text-outline mt-1">Laissez vide pour conserver le document existant. Formats : PDF, JPG, PNG (Max 5 Mo).</p>
                    <x-input-error :messages="$errors->get('file')" class="mt-1" />
                </div>
            </div>

            <!-- Submit action bar -->
            <div class="flex items-center justify-end gap-space-sm pt-space-md border-t border-outline-variant">
                <a
                    href="{{ route('pvs.show', $pv) }}"
                    class="px-space-lg py-space-sm border border-outline-variant rounded font-label-md text-label-md text-secondary hover:text-on-surface transition-colors"
                >
                    Annuler
                </a>
                <button
                    type="submit"
                    class="px-space-xl py-space-sm bg-primary-container hover:bg-primary text-on-primary font-label-md text-label-md rounded flex items-center gap-space-xs transition-colors cursor-pointer"
                >
                    <svg class="w-4 h-4 text-on-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Mettre à jour le PV</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
