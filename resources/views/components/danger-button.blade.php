<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-destructive']) }}>
    {{ $slot }}
</button>
