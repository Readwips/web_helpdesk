@props(['compact' => false, 'inverse' => false, 'href' => null])

@php
    $classes = 'inline-flex items-center gap-3';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        <span class="grid h-10 w-10 grid-cols-2 gap-1 rounded-lg {{ $inverse ? 'bg-white' : 'bg-ink' }} p-2" aria-hidden="true">
            <span class="rounded-[2px] {{ $inverse ? 'bg-ink' : 'bg-white' }}"></span>
            <span class="rounded-[2px] bg-accent"></span>
            <span class="rounded-[2px] bg-accent"></span>
            <span class="rounded-[2px] {{ $inverse ? 'bg-ink' : 'bg-white' }}"></span>
        </span>
        @unless($compact)
            <span class="leading-tight">
                <span class="block text-[15px] font-bold tracking-[-0.02em] {{ $inverse ? 'text-white' : 'text-ink' }}">IT Helpdesk</span>
                <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-[0.14em] {{ $inverse ? 'text-white/60' : 'text-muted' }}">Support workspace</span>
            </span>
        @endunless
    </a>
@else
    <span {{ $attributes->merge(['class' => $classes]) }}>
        <span class="grid h-10 w-10 grid-cols-2 gap-1 rounded-lg {{ $inverse ? 'bg-white' : 'bg-ink' }} p-2" aria-hidden="true">
            <span class="rounded-[2px] {{ $inverse ? 'bg-ink' : 'bg-white' }}"></span>
            <span class="rounded-[2px] bg-accent"></span>
            <span class="rounded-[2px] bg-accent"></span>
            <span class="rounded-[2px] {{ $inverse ? 'bg-ink' : 'bg-white' }}"></span>
        </span>
        @unless($compact)
            <span class="leading-tight">
                <span class="block text-[15px] font-bold tracking-[-0.02em] {{ $inverse ? 'text-white' : 'text-ink' }}">IT Helpdesk</span>
                <span class="mt-0.5 block text-[10px] font-semibold uppercase tracking-[0.14em] {{ $inverse ? 'text-white/60' : 'text-muted' }}">Support workspace</span>
            </span>
        @endunless
    </span>
@endif
