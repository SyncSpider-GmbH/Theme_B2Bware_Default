{{-- Shared by the desktop filters sidebar and the mobile filters dialog (pages/products.blade.php). --}}

@if(!empty($selectedAttributes) || !empty($selectedDefinitions) || ($inStockSelected ?? false) || ($priceSelected['min'] ?? null) !== null || ($priceSelected['max'] ?? null) !== null || ($filters['q'] ?? ''))
    <a
        href="@routeUrl('store.products')@if(($view ?? 'grid') !== 'grid')?view={{ $view }}@endif"
        class="self-start text-sm text-body underline hover:text-primary"
    >@t('Clear filters')</a>
@endif

{{-- Categories — links into the catalog tree (with counts). --}}
@if(($categoryFacet ?? collect())->isNotEmpty())
    <section class="catalog-filter flex flex-col gap-2">
        <h2 class="text-sm font-semibold text-headings m-0">@t('Categories')</h2>
        <ul class="flex flex-col gap-1 list-none m-0 p-0">
            @foreach($categoryFacet as $cat)
                <li>
                    <a
                        href="@routeUrl('store.category', ['slug' => $cat['url']])"
                        @class([
                            'flex items-center justify-between gap-2 rounded-lg px-2 py-1.5 text-sm hover:no-underline',
                            'bg-primary-subtle text-primary font-semibold' => $cat['active'],
                            'text-body hover:bg-surface-hover' => !$cat['active'],
                        ])
                    >
                        <span class="truncate">{{ $cat['name'] }}</span>
                        <span @class(['shrink-0 text-xs', 'text-primary' => $cat['active'], 'text-body' => !$cat['active']])>{{ $cat['count'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
@endif

{{-- Attribute facets + price + availability (GET form). --}}
@if(($facets ?? collect())->isNotEmpty() || ($priceFilterEnabled ?? false) || ($inStockAvailable ?? false))
    <form method="get" action="@routeUrl('store.products')" class="catalog-filter-form flex flex-col gap-6">
        {{-- Preserve search / sort / view while changing filters. --}}
        @if($filters['q'] ?? '')
            <input type="hidden" name="q" value="{{ $filters['q'] }}">
        @endif
        @if(($sort ?? '') !== '')
            <input type="hidden" name="sort" value="{{ $sort }}">
        @endif
        @if(($view ?? 'grid') !== 'grid')
            <input type="hidden" name="view" value="{{ $view }}">
        @endif

        @include('partials.catalog-facet')

        @if($priceFilterEnabled ?? false)
            <section class="catalog-filter flex flex-col gap-3 border-t border-border-subtle pt-4">
                <h2 class="text-sm font-semibold text-headings m-0">@t('Price')</h2>
                <div class="flex items-center gap-2">
                    <input
                        type="number"
                        name="price_min"
                        inputmode="decimal"
                        min="0"
                        placeholder="@t('Min')"
                        value="{{ ($priceSelected['min'] ?? null) !== null ? $priceSelected['min'] : '' }}"
                        class="w-full rounded-lg border border-surface-input-stroke bg-surface-input px-2 py-1.5 text-sm text-body outline-none focus:border-primary"
                    >
                    <span class="text-body">&ndash;</span>
                    <input
                        type="number"
                        name="price_max"
                        inputmode="decimal"
                        min="0"
                        placeholder="@t('Max')"
                        value="{{ ($priceSelected['max'] ?? null) !== null ? $priceSelected['max'] : '' }}"
                        class="w-full rounded-lg border border-surface-input-stroke bg-surface-input px-2 py-1.5 text-sm text-body outline-none focus:border-primary"
                    >
                </div>
            </section>
        @endif

        @include('partials.catalog-availability')

        <noscript>
            <button type="submit" class="rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-primary-content cursor-pointer">@t('Apply filters')</button>
        </noscript>
    </form>
@endif
