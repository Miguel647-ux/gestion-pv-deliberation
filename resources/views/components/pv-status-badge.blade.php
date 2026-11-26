@props(['status'])

@php
    $isSoumis = $status === \App\Models\DeliberationPv::STATUS_SOUMIS || $status === 'soumis';
@endphp

@if($isSoumis)
    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-label-sm font-medium bg-surface-container-high text-primary border border-outline-variant">
        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
        Soumis
    </span>
@else
    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-label-sm font-medium bg-surface-container-low text-secondary border border-outline-variant">
        <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
        Archivé
    </span>
@endif
