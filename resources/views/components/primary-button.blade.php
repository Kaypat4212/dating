<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary auth-btn']) }}>
    {{ $slot }}
</button>
