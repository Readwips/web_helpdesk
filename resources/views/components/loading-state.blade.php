@props(['label' => 'Memuat'])
<span {{ $attributes->merge(['class'=>'inline-flex items-center gap-2 text-sm text-muted']) }}><span class="loading-spinner" aria-hidden="true"></span><span>{{ $label }}</span></span>
