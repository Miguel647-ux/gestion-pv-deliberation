<x-app-layout>
    <div class="max-w-[700px] mx-auto px-margin py-space-lg w-full">
        <!-- Back Navigation -->
        <div class="mb-space-md flex items-center">
            <a
                href="{{ route('admin.years.index') }}"
                class="inline-flex items-center gap-space-xs text-secondary hover:text-primary transition-colors font-label-md text-label-md"
            >
                <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Retour aux années académiques</span>
            </a>
        </div>

        <!-- Page Header -->
        <div class="pb-space-lg border-b border-outline-variant mb-space-lg">
            <h1 class="font-serif text-[32px] font-semibold text-on-surface tracking-tight leading-tight">
                Modifier l'année académique
            </h1>
            <p class="font-body-md text-body-md text-secondary mt-1">
                Mise à jour des dates ou du statut pour la session {{ $year->label }}.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.years.update', $year) }}" class="space-y-space-lg bg-surface-container-lowest border border-outline-variant rounded p-space-lg">
            @csrf
            @method('PUT')

            <!-- Label -->
            <div class="flex flex-col gap-1">
                <label for="label" class="text-label-md font-label-md text-on-surface font-medium">
                    Libellé de la session <span class="text-error">*</span>
                </label>
                <input
                    type="text"
                    name="label"
                    id="label"
                    required
                    value="{{ old('label', $year->label) }}"
                    placeholder="Ex. 2025-2026"
                    class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors"
                >
                <x-input-error :messages="$errors->get('label')" class="mt-1" />
            </div>

            <!-- Dates grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                <div class="flex flex-col gap-1">
                    <label for="starts_on" class="text-label-md font-label-md text-on-surface font-medium">
                        Date de début <span class="text-error">*</span>
                    </label>
                    <input
                        type="date"
                        name="starts_on"
                        id="starts_on"
                        required
                        value="{{ old('starts_on', $year->starts_on ? $year->starts_on->format('Y-m-d') : '') }}"
                        class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors cursor-pointer"
                    >
                    <x-input-error :messages="$errors->get('starts_on')" class="mt-1" />
                </div>

                <div class="flex flex-col gap-1">
                    <label for="ends_on" class="text-label-md font-label-md text-on-surface font-medium">
                        Date de fin <span class="text-error">*</span>
                    </label>
                    <input
                        type="date"
                        name="ends_on"
                        id="ends_on"
                        required
                        value="{{ old('ends_on', $year->ends_on ? $year->ends_on->format('Y-m-d') : '') }}"
                        class="w-full h-10 px-3 bg-surface-container-lowest border border-outline-variant rounded font-body-sm text-body-sm text-on-surface focus:outline-none focus:border-primary transition-colors cursor-pointer"
                    >
                    <x-input-error :messages="$errors->get('ends_on')" class="mt-1" />
                </div>
            </div>

            <!-- Is Active / Current -->
            <div class="pt-space-xs">
                <label for="is_active" class="inline-flex items-start gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        name="is_active"
                        id="is_active"
                        value="1"
                        @checked(old('is_active', $year->is_active))
                        class="mt-0.5 rounded border-outline-variant text-primary focus:ring-primary"
                    >
                    <div>
                        <span class="font-label-md text-label-md text-on-surface font-medium block">Définir comme année académique courante</span>
                        <span class="font-body-sm text-body-sm text-secondary block">
                            Si cochée, cette session deviendra la période active par défaut et désactivera les autres années.
                        </span>
                    </div>
                </label>
                <x-input-error :messages="$errors->get('is_active')" class="mt-1" />
            </div>

            <!-- Submit bar -->
            <div class="flex items-center justify-end gap-space-sm pt-space-md border-t border-outline-variant">
                <a
                    href="{{ route('admin.years.index') }}"
                    class="px-space-md py-space-sm border border-outline-variant rounded font-label-md text-label-md text-secondary hover:text-on-surface transition-colors"
                >
                    Annuler
                </a>
                <button
                    type="submit"
                    class="px-space-lg py-space-sm bg-primary-container hover:bg-primary text-on-primary font-label-md text-label-md rounded flex items-center gap-space-xs transition-colors cursor-pointer"
                >
                    <span>Enregistrer les modifications</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
