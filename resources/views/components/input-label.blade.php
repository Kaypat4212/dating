@props(['value' => ''])
<label {{ $attributes->merge(['class' => 'form-label auth-label']) }}>
    {{ $value ?? $slot }}
</label>
