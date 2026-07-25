@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-2 border-accent bg-accent-soft py-2.5 pe-4 ps-3 text-start text-sm font-semibold text-accent transition focus:outline-none'
            : 'block w-full border-l-2 border-transparent py-2.5 pe-4 ps-3 text-start text-sm font-medium text-muted transition hover:bg-[#FAFAF8] hover:text-ink focus:outline-none';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
