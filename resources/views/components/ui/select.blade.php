@props([
    'label' => null,
    'error' => null,
    'options' => [],
    'placeholder' => 'Select an option',
])

<div class="space-y-2">
    @if($label)
        <label class="text-sm font-medium text-[hsl(var(--foreground))]">
            {{ $label }}
        </label>
    @endif
    
    <select {{ $attributes->merge(['class' => 'input cursor-pointer' . ($error ? ' border-[hsl(var(--destructive))]' : '')]) }}>
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
        @endforeach
    </select>
    
    @if($error)
        <p class="text-sm text-[hsl(var(--destructive))]">{{ $error }}</p>
    @endif
</div>
