@props([
    'title' => 'Sin resultados',
    'message' => null,
    'action' => null,
    'icon' => 'inbox',
])

@php
$icons = [
    'inbox' => 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4',
    'search' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
    'document' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'users' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
];
$iconPath = $icons[$icon] ?? $icons['inbox'];
@endphp

<div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div class="w-16 h-16 mb-4 text-gray-300">
        <svg class="w-full h-full" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPath }}" />
        </svg>
    </div>

    <h3 class="text-base font-medium text-gray-900">{{ $title }}</h3>

    @if($message)
        <p class="mt-1 text-sm text-gray-500 max-w-sm">{{ $message }}</p>
    @endif

    @if($action)
        <div class="mt-6">{{ $action }}</div>
    @endif
</div>
