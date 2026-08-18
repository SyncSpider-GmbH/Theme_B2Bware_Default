@extends('layouts.shop')

@section('title', data_get($category, 'seo.meta_title') ?: data_get($category, 'name') ?: t('Category'))

{{-- Owner-editable, full-width catalog hero — rendered above the breadcrumbs
     via the layout's @yield('hero'). Renders nothing until a banner is assigned. --}}
@section('hero')
    @storefrontSlot('hero')
@endsection

@section('content')
    <div class="page page--category mx-auto flex w-full max-w-desktop flex-col gap-6 px-4 py-6" data-category-slug="{{ $categorySlug }}">
        @storefrontSlot('content-top')
        @if(!$isLeaf)
            <header class="page__head flex flex-col gap-2">
                <h1 class="font-primary text-2xl font-semibold text-headings m-0">{{ $category->name ?? t('Category') }}</h1>
                @if(data_get($category, 'description'))
                    <div class="text-body text-sm storefront-richtext">{!! data_get($category, 'description') !!}</div>
                @endif
            </header>

            {{-- Branch category: sibling nav + subcategory grid. --}}
            <div class="catalog flex flex-col gap-6 desktop:flex-row desktop:items-start">
                @if(($categoryFacet ?? collect())->isNotEmpty())
                    <aside class="catalog__filters w-full shrink-0 desktop:w-64">
                        <div class="flex flex-col gap-2 rounded-xl border border-border-subtle bg-surface-card p-5">
                            <h2 class="text-sm font-semibold text-headings m-0">@t('Categories')</h2>
                            <ul class="flex flex-col gap-1 list-none m-0 p-0">
                                @foreach($categoryFacet as $cat)
                                    <li>
                                        <a
                                            href="@routeUrl('store.category', ['slug' => $cat['url']])"
                                            @class([
                                                'block rounded-lg px-2 py-1.5 text-sm hover:no-underline',
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
                        </div>
                    </aside>
                @endif

                <div class="catalog__main flex min-w-0 flex-1 flex-col gap-4">
                    @if($children->isEmpty())
                        @include('components.empty-state', ['title' => t('No categories yet')])
                    @else
                        <ul class="categories grid grid-cols-2 gap-4 list-none m-0 p-0 lg:grid-cols-3 desktop:grid-cols-4">
                            @foreach($children as $child)
                                <li>
                                    <a
                                        href="@routeUrl('store.category', ['slug' => $categorySlug . '/' . (data_get($child, 'seo.slug') ?: data_get($child, 'slug', ''))])"
                                        class="category-card flex h-full flex-col overflow-hidden rounded-xl border border-border-subtle bg-surface-card transition-colors hover:border-primary hover:no-underline"
                                    >
                                        <span class="category-card__image flex aspect-square w-full items-center justify-center overflow-hidden bg-surface">
                                            @if(data_get($child, 'resolved_main_media.media_url') ?? data_get($child, 'media.0.media_url') ?? data_get($child, 'seo.image.public_url') ?? data_get($child, 'seo.seo_image.public_url'))
                                                <img
                                                    src="@storefrontImage(data_get($child, 'resolved_main_media.media_url') ?? data_get($child, 'media.0.media_url') ?? data_get($child, 'seo.image.public_url') ?? data_get($child, 'seo.seo_image.public_url'), 320, 320, 85)"
                                                    alt="{{ data_get($child, 'name', '') }}"
                                                    class="h-full w-full object-contain"
                                                    loading="lazy"
                                                >
                                            @else
                                                @include('partials.__image-placeholder', ['size' => 'h-12 w-12'])
                                            @endif
                                        </span>
                                        <span class="category-card__name block px-3 py-4 text-center font-medium text-headings break-words">{{ data_get($child, 'name', '') }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        @else
            {{-- Leaf category: same B2B catalog layout as the products index.
                 The product count + sort + view toolbar sits inline with the
                 title / description (mirrors pages/products.blade.php), so the
                 catalog grid below top-aligns with the filter card. --}}
            <header class="page__head flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex flex-col gap-2">
                    <h1 class="font-primary text-2xl font-semibold text-headings m-0">{{ $category->name ?? t('Category') }}</h1>
                    @if(data_get($category, 'description'))
                        <div class="text-body text-sm storefront-richtext">{!! data_get($category, 'description') !!}</div>
                    @endif
                </div>

                <form method="get" action="@routeUrl('store.category', ['slug' => $categorySlug])" class="catalog-toolbar flex flex-wrap items-center gap-3 sm:justify-end">
                    @if($filters['q'] ?? '')
                        <input type="hidden" name="q" value="{{ $filters['q'] }}">
                    @endif
                    @foreach($selectedAttributes ?? [] as $attrId => $vals)
                        @foreach($vals as $val)
                            <input type="hidden" name="attr[{{ $attrId }}][]" value="{{ $val }}">
                        @endforeach
                    @endforeach
                    @if(($priceSelected['min'] ?? null) !== null)
                        <input type="hidden" name="price_min" value="{{ $priceSelected['min'] }}">
                    @endif
                    @if(($priceSelected['max'] ?? null) !== null)
                        <input type="hidden" name="price_max" value="{{ $priceSelected['max'] }}">
                    @endif
                    @if($inStockSelected ?? false)
                        <input type="hidden" name="in_stock" value="1">
                    @endif

                    <p class="catalog-toolbar__count m-0 text-sm text-body">
                        @if($products->total() > 0)
                            {{ $products->firstItem() }}&ndash;{{ $products->lastItem() }} @t('of') {{ $products->total() }} @t('products')
                        @else
                            {{ $products->total() }} @t('products')
                        @endif
                    </p>

                    <div class="catalog-toolbar__controls flex items-center gap-3">
                        <label class="flex items-center gap-2 text-sm text-body">
                            <span class="hidden sm:inline">@t('Sort by')</span>
                            <select name="sort" data-autosubmit class="rounded-lg border border-surface-input-stroke bg-surface-input px-2 py-1.5 text-sm text-body outline-none focus:border-primary">
                                <option value="relevance" @selected($sort === 'relevance')>@t('Relevance')</option>
                                <option value="newest" @selected($sort === 'newest')>@t('Newest')</option>
                                <option value="oldest" @selected($sort === 'oldest')>@t('Oldest')</option>
                                <option value="name_asc" @selected($sort === 'name_asc')>@t('Name: A–Z')</option>
                                <option value="name_desc" @selected($sort === 'name_desc')>@t('Name: Z–A')</option>
                                @foreach($sortOptions ?? [] as $option)
                                    <option value="{{ $option['key'] }}" @selected($sort === $option['key'])>{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                        </label>

                        <div class="catalog-toolbar__view inline-flex overflow-hidden rounded-lg border border-border-subtle" role="group" aria-label="@t('View')">
                            <button
                                type="submit"
                                name="view"
                                value="grid"
                                @class([
                                    'flex h-9 w-9 items-center justify-center transition-colors cursor-pointer',
                                    'bg-primary text-primary-content' => ($view ?? 'grid') === 'grid',
                                    'bg-surface-card text-body hover:bg-surface-hover' => ($view ?? 'grid') !== 'grid',
                                ])
                                aria-label="@t('Grid view')"
                                aria-pressed="{{ ($view ?? 'grid') === 'grid' ? 'true' : 'false' }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </button>
                            <button
                                type="submit"
                                name="view"
                                value="list"
                                @class([
                                    'flex h-9 w-9 items-center justify-center transition-colors cursor-pointer',
                                    'bg-primary text-primary-content' => ($view ?? 'grid') === 'list',
                                    'bg-surface-card text-body hover:bg-surface-hover' => ($view ?? 'grid') !== 'list',
                                ])
                                aria-label="@t('List view')"
                                aria-pressed="{{ ($view ?? 'grid') === 'list' ? 'true' : 'false' }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                    <line x1="8" y1="6" x2="21" y2="6"></line>
                                    <line x1="8" y1="12" x2="21" y2="12"></line>
                                    <line x1="8" y1="18" x2="21" y2="18"></line>
                                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </header>

            <div class="catalog flex flex-col gap-6 desktop:flex-row desktop:items-start">
                <aside class="catalog__filters w-full shrink-0 desktop:w-64">
                    <div class="catalog__filters-card flex flex-col gap-6 rounded-xl border border-border-subtle bg-surface-card p-5">
                        {{-- Categories — this category and its siblings (with counts). --}}
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

                        @if(($facets ?? collect())->isNotEmpty() || ($priceFilterEnabled ?? false) || ($inStockAvailable ?? false))
                            <form method="get" action="@routeUrl('store.category', ['slug' => $categorySlug])" class="catalog-filter-form flex flex-col gap-6">
                                @if($filters['q'] ?? '')
                                    <input type="hidden" name="q" value="{{ $filters['q'] }}">
                                @endif
                                @if(($sort ?? 'relevance') !== 'relevance')
                                    <input type="hidden" name="sort" value="{{ $sort }}">
                                @endif
                                @if(($view ?? 'grid') !== 'grid')
                                    <input type="hidden" name="view" value="{{ $view }}">
                                @endif

                                @foreach($facets as $facet)
                                    <section class="catalog-filter flex flex-col gap-2 {{ ($categoryFacet ?? collect())->isNotEmpty() || !$loop->first ? 'border-t border-border-subtle pt-4' : '' }}">
                                        <h2 class="text-sm font-semibold text-headings m-0">{{ $facet['name'] }}</h2>
                                        <div class="flex flex-col gap-1.5">
                                            @foreach($facet['values'] as $value)
                                                <label class="flex items-center gap-2 text-sm text-body cursor-pointer">
                                                    <input
                                                        type="checkbox"
                                                        name="attr[{{ $facet['id'] }}][]"
                                                        value="{{ $value }}"
                                                        class="shrink-0"
                                                        @checked(in_array($value, $selectedAttributes[$facet['id']] ?? [], true))
                                                    >
                                                    <span class="truncate">{{ $value }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach

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

                                @if($inStockAvailable ?? false)
                                    <section class="catalog-filter flex flex-col gap-2 border-t border-border-subtle pt-4">
                                        <h2 class="text-sm font-semibold text-headings m-0">@t('Availability')</h2>
                                        <label class="flex items-center gap-2 text-sm text-body cursor-pointer">
                                            <input
                                                type="checkbox"
                                                name="in_stock"
                                                value="1"
                                                class="shrink-0"
                                                @checked($inStockSelected ?? false)
                                            >
                                            <span>@t('In stock only')</span>
                                        </label>
                                    </section>
                                @endif

                                <div class="flex items-center gap-3 border-t border-border-subtle pt-4">
                                    <button type="submit" class="rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-primary-content transition-colors cursor-pointer hover:bg-primary-600">@t('Apply filters')</button>
                                    @if(!empty($selectedAttributes) || ($inStockSelected ?? false) || ($priceSelected['min'] ?? null) !== null || ($priceSelected['max'] ?? null) !== null || ($filters['q'] ?? ''))
                                        <a
                                            href="@routeUrl('store.category', ['slug' => $categorySlug])@if(($view ?? 'grid') !== 'grid')?view={{ $view }}@endif"
                                            class="text-sm text-body underline hover:text-primary"
                                        >@t('Clear')</a>
                                    @endif
                                </div>
                            </form>
                        @endif
                    </div>
                </aside>

                <div class="catalog__main flex min-w-0 flex-1 flex-col gap-4">
                    @if($products->total() === 0)
                        @include('components.empty-state', [
                            'title'   => t('No products in this category yet'),
                            'message' => t('Try adjusting your filters or search.'),
                        ])
                    @elseif(($view ?? 'grid') === 'list')
                        <ul class="catalog__list flex flex-col gap-3 list-none m-0 p-0">
                            @foreach($products as $product)
                                <li>@include('components.product-card', ['product' => $product, 'view' => 'list', 'redirect' => '/' . $locale . '/category/' . $categorySlug, 'showStock' => $inStockAvailable ?? false])</li>
                            @endforeach
                        </ul>
                    @else
                        <ul class="catalog__grid grid grid-cols-2 gap-4 list-none m-0 p-0 lg:grid-cols-3 desktop:grid-cols-4">
                            @foreach($products as $product)
                                <li>@include('components.product-card', ['product' => $product, 'view' => 'grid', 'redirect' => '/' . $locale . '/category/' . $categorySlug])</li>
                            @endforeach
                        </ul>
                    @endif

                    @include('components.pagination', ['paginator' => $products])
                </div>
            </div>

            <script type="module">
                for (const el of document.querySelectorAll('[data-autosubmit]')) {
                    el.addEventListener('change', () => el.form?.requestSubmit());
                }

                const storefrontConfig = window.__STOREFRONT__ ?? {};
                let moneyFormatter = null;
                try {
                    moneyFormatter = new Intl.NumberFormat(storefrontConfig.locale || undefined, {
                        style: 'currency',
                        currency: storefrontConfig.currency || 'EUR',
                    });
                } catch (e) {
                    moneyFormatter = null;
                }
                for (const row of document.querySelectorAll('[data-line-row]')) {
                    const quantityInput = row.querySelector('input[name="quantity"]');
                    const totalEl = row.querySelector('[data-line-total]');
                    const unitPrice = parseFloat(row.getAttribute('data-line-unit') || '');
                    if (!quantityInput || !totalEl || !Number.isFinite(unitPrice)) continue;
                    const updateTotal = () => {
                        const qty = Math.max(1, parseInt(quantityInput.value, 10) || 1);
                        const total = unitPrice * qty;
                        totalEl.textContent = moneyFormatter ? moneyFormatter.format(total) : total.toFixed(2);
                    };
                    quantityInput.addEventListener('change', updateTotal);
                    quantityInput.addEventListener('input', updateTotal);
                }
            </script>
        @endif
        {{-- Owner-editable content region (bottom of page). --}}
        @storefrontSlot('content-bottom')
    </div>
@endsection

