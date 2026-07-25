@if($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex items-center justify-between gap-3">
        @if($paginator->onFirstPage())<span class="btn-secondary cursor-not-allowed opacity-50">Sebelumnya</span>@else<a href="{{ $paginator->previousPageUrl() }}" class="btn-secondary" rel="prev">Sebelumnya</a>@endif
        @if($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" class="btn-secondary" rel="next">Berikutnya</a>@else<span class="btn-secondary cursor-not-allowed opacity-50">Berikutnya</span>@endif
    </nav>
@endif
