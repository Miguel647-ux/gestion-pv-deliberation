@props([
    'title' => 'Aucun procès-verbal trouvé',
    'message' => 'Aucun document ne correspond aux filtres sélectionnés.',
    'actionUrl' => null,
    'actionText' => null,
])

<div class="w-full py-12 px-6 flex flex-col items-center justify-center text-center bg-surface-container-lowest border border-outline-variant rounded">
    <div class="w-10 h-10 rounded bg-surface-container-low flex items-center justify-center mb-3 text-secondary">
        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
    </div>
    <h3 class="font-serif text-[18px] font-semibold text-on-surface mb-1">{{ $title }}</h3>
    <p class="font-body-sm text-secondary max-w-sm mb-4">{{ $message }}</p>
    @if($actionUrl && $actionText)
        <a href="{{ $actionUrl }}" class="inline-flex items-center gap-1.5 font-label-md text-label-md text-primary hover:underline">
            <span>{{ $actionText }}</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
        </a>
    @endif
</div>
