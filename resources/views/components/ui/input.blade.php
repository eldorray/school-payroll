@props([
    'type' => 'text',
    'label' => null,
    'error' => null,
])

<div class="space-y-2">
    @if($label)
        <label class="text-sm font-medium text-[hsl(var(--foreground))]">
            {{ $label }}
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        {{ $attributes->merge(['class' => 'input' . ($error ? ' border-[hsl(var(--destructive))]' : '')]) }}
    >
    
    @if($error)
        <p class="text-sm text-[hsl(var(--destructive))]">{{ $error }}</p>
    @endif
</div>
