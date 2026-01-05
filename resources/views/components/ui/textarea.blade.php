@props([
    'label' => null,
    'error' => null,
    'rows' => 4,
])

<div class="space-y-2">
    @if($label)
        <label class="text-sm font-medium text-[hsl(var(--foreground))]">
            {{ $label }}
        </label>
    @endif
    
    <textarea 
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'input textarea' . ($error ? ' border-[hsl(var(--destructive))]' : '')]) }}
    >{{ $slot }}</textarea>
    
    @if($error)
        <p class="text-sm text-[hsl(var(--destructive))]">{{ $error }}</p>
    @endif
</div>
