@props([
    'type' => 'text',
    'label' => null,
    'error' => null,
])

@php $fieldId = $attributes->get('id') ?? $attributes->get('name') ?? 'field-' . \Illuminate\Support\Str::uuid(); @endphp

<div class="space-y-2">
    @if($label)
        <label for="{{ $fieldId }}" class="text-sm font-medium text-[hsl(var(--foreground))]">
            {{ $label }}
        </label>
    @endif
    
    <input 
        type="{{ $type }}"
        {{ $attributes->merge(['id' => $fieldId, 'aria-invalid' => $error ? 'true' : null, 'aria-describedby' => $error ? $fieldId . '-error' : null, 'class' => 'input' . ($error ? ' border-[hsl(var(--destructive))]' : '')]) }}
    >
    
    @if($error)
        <p id="{{ $fieldId }}-error" class="text-sm text-[hsl(var(--destructive))]">{{ $error }}</p>
    @endif
</div>
