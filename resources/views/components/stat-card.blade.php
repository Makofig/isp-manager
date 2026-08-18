@props([
    'title' => '',
    'value' => '',
    'subtitle' => null,
    'icon' => null,
    'trend' => null,  // '+4.2%' or '-2.1%'
    'trendUp' => true,
])

<div class="stat-card group">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-[11px] font-medium text-muted uppercase tracking-wider">{{ $title }}</p>
            <p class="mt-2 text-2xl font-semibold text-text-primary">{{ $value }}</p>
            @if($subtitle || $trend)
                <div class="mt-2 flex items-center gap-2">
                    @if($trend)
                        <span class="inline-flex items-center text-xs font-medium"
                              :class="$trendUp ? 'text-success' : 'text-danger'">
                            @if($trendUp)
                                <svg class="w-3 h-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" /></svg>
                            @else
                                <svg class="w-3 h-3 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                            @endif
                            {{ $trend }}
                        </span>
                    @endif
                    @if($subtitle)
                        <span class="text-xs text-muted">{{ $subtitle }}</span>
                    @endif
                </div>
            @endif
        </div>
        @if($icon)
            <div class="w-10 h-10 rounded-[8px] bg-accent/10 flex items-center justify-center flex-shrink-0">
                <x-icon :name="$icon" class="w-5 h-5 text-accent" />
            </div>
        @endif
    </div>
</div>
