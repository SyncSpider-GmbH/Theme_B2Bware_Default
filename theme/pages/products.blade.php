@extends('layouts.shop')

@section('title', t('All Products'))

{{-- Owner-editable, full-width catalog hero — rendered above the breadcrumbs
     via the layout's @yield('hero'). Renders nothing until a banner is assigned. --}}
@section('hero')
    @storefrontSlot('hero')
@endsection

@section('content')
    <div class="page page--products mx-auto flex w-full max-w-desktop flex-col gap-6 px-4 py-6">
        @storefrontSlot('content-top')
        @section('header')
            <header class="page__head flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="flex flex-col gap-1">
                    <h1 class="font-primary text-2xl font-semibold text-headings m-0">@t('All Products')</h1>
                    <p class="text-sm text-body m-0">@t('Browse our full catalog of wholesale products')</p>
                </div>

                @section('toolbar')
                    <form method="get" action="@routeUrl('store.products')" class="catalog-toolbar flex flex-wrap items-center gap-3 sm:justify-end">
                        {{-- Preserve the active filters while changing sort / view. --}}
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
                            {{-- Opens the mobile filters dialog below `desktop:` (see the
                                 aside + dialog further down); the sidebar itself takes over
                                 at `desktop:` and this button hides. --}}
                            <button type="button" data-storefront-modal-open="products-filters-dialog"
                                class="catalog-toolbar__filters-btn relative inline-flex h-9 items-center gap-2 rounded-lg border border-border-subtle px-3 text-sm font-medium text-headings transition-colors cursor-pointer hover:border-primary hover:text-primary desktop:hidden">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                    <line x1="4" y1="6" x2="20" y2="6"></line>
                                    <circle cx="9" cy="6" r="2" fill="currentColor" stroke="none"></circle>
                                    <line x1="4" y1="12" x2="20" y2="12"></line>
                                    <circle cx="15" cy="12" r="2" fill="currentColor" stroke="none"></circle>
                                    <line x1="4" y1="18" x2="20" y2="18"></line>
                                    <circle cx="7" cy="18" r="2" fill="currentColor" stroke="none"></circle>
                                </svg>
                                <span>@t('Filters')</span>
                                @if(!empty($selectedAttributes) || ($inStockSelected ?? false) || ($priceSelected['min'] ?? null) !== null || ($priceSelected['max'] ?? null) !== null || ($filters['q'] ?? ''))
                                    <span class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-primary" aria-hidden="true"></span>
                                @endif
                            </button>

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
                @show
            </header>
        @show

        <div class="catalog flex flex-col gap-6 desktop:flex-row desktop:items-start">
            @section('filters')
                <aside class="catalog__filters hidden w-full shrink-0 desktop:block desktop:w-64">
                    {{-- Owner-editable sidebar promo (above the filters). Empty
                         until a shop manager assigns a widget to the slot. --}}
                    @storefrontSlot('catalog.sidebar')
                    <div class="catalog__filters-card flex flex-col gap-6 rounded-xl border border-border-subtle bg-surface-card p-5">
                        @include('partials.catalog-filters')
                    </div>
                </aside>
            @show

            <div class="catalog__main flex min-w-0 flex-1 flex-col gap-4">
                @section('listing')
                    @if($products->total() === 0)
                        @include('components.empty-state', [
                            'title'   => t('No products found'),
                            'message' => t('Try adjusting your filters or search.'),
                        ])
                    @elseif(($view ?? 'grid') === 'list')
                        <ul class="catalog__list flex flex-col gap-3 list-none m-0 p-0">
                            @foreach($products as $product)
                                <li>@include('components.product-card', ['product' => $product, 'view' => 'list', 'redirect' => '/' . $locale . '/products', 'showStock' => $inStockAvailable ?? false])</li>
                            @endforeach
                        </ul>
                    @else
                        <ul class="catalog__grid grid grid-cols-1 gap-4 list-none m-0 p-0 sm:grid-cols-2 lg:grid-cols-3 desktop:grid-cols-4">
                            @foreach($products as $product)
                                <li>@include('components.product-card', ['product' => $product, 'view' => 'grid', 'redirect' => '/' . $locale . '/products'])</li>
                            @endforeach
                        </ul>
                    @endif
                @show

                @section('pagination')
                    @include('components.pagination', ['paginator' => $products])
                @show
            </div>
        </div>

        {{-- Mobile filters dialog (below `desktop:`). Rendered as a sibling of the
             filters aside above, never nested inside it — a `display:none` ancestor
             (the aside is `hidden` below `desktop:`) would stop `showModal()` from
             ever rendering the dialog on exactly the widths where it's needed. --}}
        <dialog id="products-filters-dialog" class="products-filters-dialog w-full max-w-sm bg-surface-card text-body rounded-lg shadow-xl p-0 border-0 backdrop:bg-backdrop/40">
            <div class="flex flex-col gap-4 p-6 max-h-[85vh] overflow-y-auto">
                <header class="flex items-center justify-between gap-3 pb-3 border-b border-border-subtle">
                    <h2 class="font-secondary text-lg text-headings m-0">@t('Filters')</h2>
                    <button type="button" class="inline-flex h-8 w-8 items-center justify-center text-2xl leading-none text-body hover:text-headings cursor-pointer bg-transparent border-0" data-modal-close aria-label="@t('Close')">&times;</button>
                </header>
                @include('partials.catalog-filters')
            </div>
        </dialog>
    </div>

    <script type="module">
        // Sort select auto-submits its toolbar form on change.
        for (const el of document.querySelectorAll('[data-autosubmit]')) {
            el.addEventListener('change', () => el.form?.requestSubmit());
        }

        // List-view live Total Price = unit price × quantity, formatted with the
        // storefront locale/currency. Progressive enhancement; the cart computes
        // the authoritative total server-side on add.
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
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection


