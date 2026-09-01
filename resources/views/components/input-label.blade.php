@props(['value'])

<label {{ $attributes->merge(['class' => 'label mb-0']) }}>
    {{ $value ?? $slot }}
</label>
