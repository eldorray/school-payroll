@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'default',
    'disabled' => false,
])

@php
$baseClasses = 'btn';
$variantClasses = match($variant) {
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'outline' => 'btn-outline',
    'ghost' => 'btn-ghost',
    'destructive' => 'btn-destructive',
    default => 'btn-primary',
};
$sizeClasses = match($size) {
    'lg' => 'btn-lg',
    'sm' => 'btn-sm',
    'icon' => 'btn-icon',
    default => '',
};
@endphp

<button 
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => "$baseClasses $variantClasses $sizeClasses"]) }}
>
    {{ $slot }}
</button>
