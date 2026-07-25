@props(['eyebrow' => null, 'title', 'description' => null])
<div {{ $attributes->merge(['class' => 'mb-8 flex flex-wrap items-end justify-between gap-5']) }}>
    <div>@if($eyebrow)<div class="eyebrow">{{ $eyebrow }}</div>@endif<h1 class="page-title {{ $eyebrow ? 'mt-2' : '' }}">{{ $title }}</h1>@if($description)<p class="page-copy">{{ $description }}</p>@endif</div>
    @isset($actions)<div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>@endisset
</div>
