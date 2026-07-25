@if($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman" class="flex items-center justify-between gap-4">
        <div class="flex flex-1 justify-between sm:hidden">
            @if($paginator->onFirstPage())<span class="btn-secondary cursor-not-allowed opacity-50">Sebelumnya</span>@else<a href="{{ $paginator->previousPageUrl() }}" class="btn-secondary" rel="prev">Sebelumnya</a>@endif
            @if($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" class="btn-secondary" rel="next">Berikutnya</a>@else<span class="btn-secondary cursor-not-allowed opacity-50">Berikutnya</span>@endif
        </div>
        <div class="hidden flex-1 items-center justify-between sm:flex">
            <p class="text-sm text-muted">Menampilkan <span class="font-semibold text-ink">{{ $paginator->firstItem() }}</span>–<span class="font-semibold text-ink">{{ $paginator->lastItem() }}</span> dari <span class="font-semibold text-ink">{{ $paginator->total() }}</span> data</p>
            <div class="inline-flex overflow-hidden rounded-lg border border-line bg-white">
                @if($paginator->onFirstPage())<span class="grid h-10 w-10 place-items-center border-r border-line text-muted opacity-50" aria-disabled="true">&lsaquo;</span>@else<a href="{{ $paginator->previousPageUrl() }}" class="grid h-10 w-10 place-items-center border-r border-line text-ink hover:bg-[#FAFAF8]" rel="prev" aria-label="Halaman sebelumnya">&lsaquo;</a>@endif
                @foreach($elements as $element)
                    @if(is_string($element))<span class="grid h-10 min-w-10 place-items-center border-r border-line px-2 text-sm text-muted">{{ $element }}</span>@endif
                    @if(is_array($element)) @foreach($element as $page => $url) @if($page == $paginator->currentPage())<span class="grid h-10 min-w-10 place-items-center border-r border-line bg-accent-soft px-2 text-sm font-bold text-accent" aria-current="page">{{ $page }}</span>@else<a href="{{ $url }}" class="grid h-10 min-w-10 place-items-center border-r border-line px-2 text-sm font-semibold text-ink hover:bg-[#FAFAF8]" aria-label="Buka halaman {{ $page }}">{{ $page }}</a>@endif @endforeach @endif
                @endforeach
                @if($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" class="grid h-10 w-10 place-items-center text-ink hover:bg-[#FAFAF8]" rel="next" aria-label="Halaman berikutnya">&rsaquo;</a>@else<span class="grid h-10 w-10 place-items-center text-muted opacity-50" aria-disabled="true">&rsaquo;</span>@endif
            </div>
        </div>
    </nav>
@endif
