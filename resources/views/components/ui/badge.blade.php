@props([
    'variant' => 'default',
])

@php
$classes = match($variant) {
    'secondary' => 'badge-secondary',
    'outline' => 'badge-outline',
    'success' => 'badge-success',
    'warning' => 'badge-warning',
    'destructive' => 'badge-destructive',
    default => 'badge-default',
};
@endphp

<span {{ $attributes->merge(['class' => "badge $classes"]) }}>
    {{ $slot }}
</span>
