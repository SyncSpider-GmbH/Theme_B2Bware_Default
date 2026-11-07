{{-- Applied catalog filters. Each chip links to the same listing with that one filter removed. --}}
@if(!empty($activeFilters))
    <div class="catalog-active-filters flex flex-wrap items-center gap-2">
        <ul class="flex flex-wrap gap-2 list-none m-0 p-0" aria-label="@t('Active filters')">
            @foreach($activeFilters as $filter)
                <li>
                    <a
                        href="{{ $filter['url'] }}"
                        data-active-filter-kind="{{ $filter['kind'] }}"
                        @if(($filter['name'] ?? '') !== '') data-active-filter-name="{{ $filter['name'] }}" @endif
                        @if(($filter['value'] ?? null) !== null && $filter['value'] !== '') data-active-filter-value="{{ $filter['value'] }}" @endif
                        class="catalog-active-filter inline-flex items-center gap-1.5 rounded-full border border-border-subtle bg-surface-card px-2.5 py-1 text-sm text-body hover:border-primary hover:text-primary hover:no-underline"
                    >
                        <span>
                            @if($filter['kind'] === 'attribute' || $filter['kind'] === 'definition')
                                @if(($filter['name'] ?? '') !== '')
                                    {{ $filter['name'] }}: {{ $filter['value'] }}
                                @else
                                    {{ $filter['value'] }}
                                @endif
                            @elseif($filter['kind'] === 'price_min')
                                @t('Min price'): {{ $filter['value'] }}
                            @elseif($filter['kind'] === 'price_max')
                                @t('Max price'): {{ $filter['value'] }}
                            @elseif($filter['kind'] === 'in_stock')
                                @t('In stock only')
                            @elseif($filter['kind'] === 'search')
                                @t('Search'): {{ $filter['value'] }}
                            @endif
                        </span>
                        <span aria-hidden="true">&times;</span>
                        <span class="sr-only">@t('Remove')</span>
                    </a>
                </li>
            @endforeach
        </ul>
        @if(!empty($categorySlug))
            <a
                href="@routeUrl('store.category', ['slug' => $categorySlug])@if(($view ?? 'grid') !== 'grid')?view={{ $view }}@endif"
                class="text-sm text-body underline hover:text-primary"
            >@t('Clear filters')</a>
        @else
            <a
                href="@routeUrl('store.products')@if(($view ?? 'grid') !== 'grid')?view={{ $view }}@endif"
                class="text-sm text-body underline hover:text-primary"
            >@t('Clear filters')</a>
        @endif
    </div>
@endif
