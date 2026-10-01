@props([
    'label' => null,
    'error' => null,
    'options' => [],
    'placeholder' => 'Select an option',
])

@php $fieldId = $attributes->get('id') ?? $attributes->get('name') ?? 'field-' . \Illuminate\Support\Str::uuid(); @endphp

<div class="space-y-2">
    @if($label)
        <label for="{{ $fieldId }}" class="text-sm font-medium text-[hsl(var(--foreground))]">
            {{ $label }}
        </label>
    @endif
    
    <select {{ $attributes->merge(['id' => $fieldId, 'aria-invalid' => $error ? 'true' : null, 'aria-describedby' => $error ? $fieldId . '-error' : null, 'class' => 'input cursor-pointer' . ($error ? ' border-[hsl(var(--destructive))]' : '')]) }}>
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
        @endforeach
    </select>
    
    @if($error)
        <p id="{{ $fieldId }}-error" class="text-sm text-[hsl(var(--destructive))]">{{ $error }}</p>
    @endif
</div>
