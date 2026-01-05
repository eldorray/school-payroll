@props([
    'header' => null,
    'footer' => null,
])

<div {{ $attributes->merge(['class' => 'card p-6']) }}>
    @if($header)
        <div class="mb-4 pb-4 border-b border-[hsl(var(--border))]">
            {{ $header }}
        </div>
    @endif
    
    {{ $slot }}
    
    @if($footer)
        <div class="mt-4 pt-4 border-t border-[hsl(var(--border))]">
            {{ $footer }}
        </div>
    @endif
</div>
