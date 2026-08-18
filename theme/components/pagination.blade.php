@props(['paginator' => null])

{{--
    Windowed numbered pager (B2B Figma). Built from the paginator's own
    helpers so query-string filters survive (the catalog query uses
    withQueryString()). Renders nothing for a single page. Window =
    current page ±2, with leading/trailing 1/last + ellipses.
--}}
@if($paginator && method_exists($paginator, 'lastPage') && $paginator->lastPage() > 1)
    <nav class="pagination flex flex-wrap items-center justify-center gap-1 mt-6" aria-label="{{ t('Pagination') }}">
        @if($paginator->onFirstPage())
            <span class="pagination__btn inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-border-subtle px-3 text-sm text-placeholder cursor-not-allowed" aria-disabled="true">&lsaquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination__btn inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-border-subtle px-3 text-sm text-body transition-colors hover:bg-surface-hover hover:no-underline" aria-label="{{ t('Previous') }}">&lsaquo;</a>
        @endif

        @if($paginator->currentPage() > 3)
            <a href="{{ $paginator->url(1) }}" class="pagination__btn inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-border-subtle px-3 text-sm text-body transition-colors hover:bg-surface-hover hover:no-underline">1</a>
            @if($paginator->currentPage() > 4)
                <span class="pagination__gap inline-flex h-9 min-w-9 items-center justify-center text-sm text-body">&hellip;</span>
            @endif
        @endif

        @foreach($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
            @if($page === $paginator->currentPage())
                <span aria-current="page" class="pagination__btn pagination__btn--active inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-primary bg-primary px-3 text-sm font-medium text-primary-content">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="pagination__btn inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-border-subtle px-3 text-sm text-body transition-colors hover:bg-surface-hover hover:no-underline">{{ $page }}</a>
            @endif
        @endforeach

        @if($paginator->currentPage() < $paginator->lastPage() - 2)
            @if($paginator->currentPage() < $paginator->lastPage() - 3)
                <span class="pagination__gap inline-flex h-9 min-w-9 items-center justify-center text-sm text-body">&hellip;</span>
            @endif
            <a href="{{ $paginator->url($paginator->lastPage()) }}" class="pagination__btn inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-border-subtle px-3 text-sm text-body transition-colors hover:bg-surface-hover hover:no-underline">{{ $paginator->lastPage() }}</a>
        @endif

        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination__btn inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-border-subtle px-3 text-sm text-body transition-colors hover:bg-surface-hover hover:no-underline" aria-label="{{ t('Next') }}">&rsaquo;</a>
        @else
            <span class="pagination__btn inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-border-subtle px-3 text-sm text-placeholder cursor-not-allowed" aria-disabled="true">&rsaquo;</span>
        @endif
    </nav>
@endif
