    <article class="page page--product flex flex-col gap-6" data-product-slug="{{ $productSlug }}" @if(!empty($isVariantView)) data-variant-view="true" @endif>
        @if(!$product)
            @include('components.empty-state', [
                'title'   => t('Product not found'),
                'message' => t('We could not load this product right now.'),
            ])
        @else
            <div class="product grid grid-cols-1 gap-8 md:grid-cols-2">
                @section('gallery')
                    @include('components.product-gallery', ['product' => ($displayProduct ?? $product), 'images' => ($galleryImages ?? [])])
                @show

                <div class="product__info flex flex-col gap-4" data-product-detail data-product-id="{{ $product->id }}">
                    @if(!empty($isVariantView) && !empty($parentUrl))
                        <a href="{{ $parentUrl }}" class="product__back-to-parent inline-flex items-center gap-1 self-start text-sm font-medium text-primary hover:underline">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                            @t('Choose options')
                        </a>
                    @endif

                    @section('title')
                        <h1 class="product__title font-primary text-3xl text-headings m-0">{{ ($displayProduct ?? $product)->name }}</h1>
                    @show

                    @section('sku')
                        @if(!empty(($displayProduct ?? $product)->sku))
                            <p class="product__sku text-sm text-body m-0">@t('SKU') {{ ($displayProduct ?? $product)->sku }}</p>
                        @endif
                    @show
                    @storefrontSlot('product.detail.meta', [
                        'context' => [
                            'product' => 'product',
                        ],
                    ])

                    <div class="product__divider border-t border-border-subtle" aria-hidden="true"></div>

                    @section('variants')
                        {{-- Dropdown variant picker (tenant variation_display_mode =
                             'dropdown'): choosing a full combination re-renders the whole
                             product-details section server-side for that variant (and sets
                             ?variant=<id>). In table mode the bulk table below is used
                             instead, so nothing renders here. --}}
                        @if(empty($isVariantView) && $variants->isNotEmpty() && ($store['variation_display_mode'] ?? 'table') === 'dropdown' && !empty($variantFilters))
                            {{-- One <select> per varying axis (e.g. Size, Colour). Choosing a
                                 full combination resolves to the matching variant and updates
                                 the price, stock and add-to-cart in place (no reload). --}}
                            <div class="product__variants flex flex-col gap-3" data-variant-picker
                                data-product-id="{{ $product->id }}" data-current-variant="{{ (int) ($selectedVariant->id ?? 0) }}">
                                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                                    @foreach($variantFilters as $i => $filter)
                                        <label class="flex flex-col gap-1 min-w-40">
                                            <span class="text-sm font-semibold text-headings">{{ $filter['name'] }}</span>
                                            <select data-variant-axis data-axis-index="{{ $i }}"
                                                class="w-full rounded border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary">
                                                @foreach($filter['values'] as $value)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                    @endforeach
                                </div>
                                <p data-variant-unavailable hidden class="text-sm text-error m-0">@t('This combination is not available.')</p>

                                {{-- Variant combination → id map (scoped inside the picker). Each
                                     span carries one axis value per varying attribute; matching
                                     every select resolves the chosen variant id, which then
                                     re-renders the whole product-details section server-side. --}}
                                @foreach($variantRows as $row)
                                    <span hidden data-variant-combo data-variant-id="{{ $row['id'] }}"
                                        @foreach($variantFilters as $i => $filter)
                                            data-axis-{{ $i }}="{{ data_get(collect($row['attributes'])->firstWhere('name', $filter['name']), 'value', '') }}"
                                        @endforeach
                                    ></span>
                                @endforeach
                            </div>

                            <script type="module">
                                const picker = document.querySelector('[data-variant-picker]');
                                if (picker) {
                                    const selects = [...picker.querySelectorAll('[data-variant-axis]')];
                                    const combos = [...picker.querySelectorAll('[data-variant-combo]')];
                                    const unavailable = picker.querySelector('[data-variant-unavailable]');
                                    const productId = picker.getAttribute('data-product-id');
                                    const currentId = picker.getAttribute('data-current-variant') || '';

                                    const axisValue = (combo, sel) => combo.getAttribute('data-axis-' + sel.getAttribute('data-axis-index'));
                                    const matches = (combo) => selects.every((sel) => axisValue(combo, sel) === sel.value);

                                    // Pre-select each axis to the variant the server rendered.
                                    const current = combos.find((c) => c.getAttribute('data-variant-id') === currentId);
                                    if (current) {
                                        for (const sel of selects) {
                                            const value = axisValue(current, sel);
                                            if (value !== null) {
                                                sel.value = value;
                                            }
                                        }
                                    }

                                    // A full, available combination re-renders the whole product
                                    // detail server-side for that variant (price / stock / buy).
                                    const resolve = () => {
                                        if (unavailable) {
                                            unavailable.hidden = true;
                                        }
                                        const match = combos.find(matches);
                                        if (!match) {
                                            if (unavailable) {
                                                unavailable.hidden = false;
                                            }
                                            return;
                                        }
                                        const variantId = match.getAttribute('data-variant-id');
                                        if (variantId === currentId) {
                                            return;
                                        }
                                        window.Storefront?.selectVariant?.(productId, variantId);
                                    };

                                    for (const sel of selects) {
                                        sel.addEventListener('change', resolve);
                                    }
                                }
                            </script>
                        @elseif(empty($isVariantView) && $variants->isNotEmpty() && ($store['variation_display_mode'] ?? 'table') === 'dropdown')
                            {{-- Fallback: variants exist but no varying axis — plain variant select. --}}
                            <form method="get" class="product__variants flex flex-col gap-2">
                                <label class="text-sm font-semibold text-headings" for="product-variant-select">@t('Variant')</label>
                                <select id="product-variant-select" name="variant" data-autosubmit
                                    class="w-full rounded border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary">
                                    @foreach($variants as $variant)
                                        <option value="{{ $variant->id }}" @selected($selectedVariant && (int) $selectedVariant->id === (int) $variant->id)>{{ $variant->name ?? $variant->sku }}</option>
                                    @endforeach
                                </select>
                            </form>
                            <script type="module">
                                for (const el of document.querySelectorAll('[data-autosubmit]')) {
                                    el.addEventListener('change', () => el.form?.requestSubmit());
                                }
                            </script>
                        @endif
                    @show

                    {{-- Single-buy UI (price, availability, add-to-cart). Shown for
                         simple products, dropdown variant mode, and table-mode
                         variant view (?variant=<id>). In table mode without a
                         selected variant the bulk table below owns purchase. --}}
                    @unless($variants->isNotEmpty() && ($store['variation_display_mode'] ?? 'table') === 'table' && empty($isVariantView))
                    @section('price')
                        <div>
                            @include('components.price', [
                                'priceView' => $priceView ?? null,
                                'amount'    => data_get($selectedVariant ?: $product, 'finalPrice.price')
                                    ?? data_get($selectedVariant ?: $product, 'specialPrice.price')
                                    ?? data_get($selectedVariant ?: $product, 'price.price')
                                    ?? 0,
                                'compareAt' => data_get($selectedVariant ?: $product, 'specialPrice.price') !== null
                                    ? data_get($selectedVariant ?: $product, 'price.price')
                                    : null,
                                'size'      => 'lg',
                            ])
                        </div>
                    @show

                    @section('contract-price')
                        @if(!empty($contractPrice))
                            <div class="product__contract-price flex flex-wrap items-baseline gap-2 self-start rounded border border-primary bg-primary-subtle px-3 py-2">
                                <span class="text-sm font-semibold text-primary">@t('Your contracted price')</span>
                                <span class="text-lg font-semibold text-headings">{{ formatCurrency($contractPrice['price']) }}</span>
                                @if($contractPrice['has_saving'])
                                    <span class="text-sm text-body line-through">{{ formatCurrency($contractPrice['regular']) }}</span>
                                @endif
                            </div>
                        @endif
                    @show

                    @section('tier-prices')
                        @if($canSeePrices && isset($tierPrices) && count($tierPrices) > 0)
                            <div class="product__tier-prices flex flex-col gap-2 self-start w-full">
                                <p class="product__tier-label text-sm font-semibold text-headings m-0">@t('Volume discount')</p>
                                <div class="product__tier-cards flex flex-wrap gap-2">
                                    @foreach($tierPrices as $tier)
                                        <div
                                            class="product__tier-card flex flex-col gap-0.5 rounded-lg border border-border-subtle bg-surface-card px-3 py-2 text-center"
                                            data-tier-card
                                            data-tier-min="{{ (int) ($tier['min_quantity'] ?? 0) }}"
                                        >
                                            <span class="text-xs text-body">{{ (int) ($tier['min_quantity'] ?? 0) }}+ {{ t('pcs') }}</span>
                                            <span class="text-sm font-semibold text-headings">{{ formatCurrency($tier['price'] ?? 0) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @show

                    @section('availability')
                        <div>
                            @if($stockInfo['manage'])
                                @include('components.stock-badge', [
                                    'inStock'  => $stockInfo['available'],
                                    'quantity' => $stockInfo['quantity'],
                                    'low'      => $stockInfo['low'],
                                ])
                            @endif
                        </div>
                    @show

                    @section('purchase')
                        @if($canSeePrices && $canAddToCart)
                            <div class="product__purchase flex items-end gap-3">
                                @storefrontForm('cart-add', ['class' => 'product__cart-form flex flex-1 min-w-0 flex-col gap-3'])
                                    {{-- For a configurable product the SELECTED VARIANT is the
                                         purchasable product; add it by id (the cart adds by
                                         product_id). Simple products add themselves. The whole
                                         product detail (incl. this value) is re-rendered by the
                                         product-details section when a variant is chosen. --}}
                                    <input type="hidden" name="product_id" value="{{ ($selectedVariant ?: $product)->id }}">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="text-xs font-medium text-body">@t('Quantity')</span>
                                        @include('components.quantity-selector', [
                                            'value' => $stockInfo['min'],
                                            'min'   => $stockInfo['min'],
                                            'max'   => $stockInfo['max'],
                                        ])
                                    </div>
                                    <button
                                        type="submit"
                                        class="product__add inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 font-medium text-primary-content transition-colors hover:bg-primary-600 cursor-pointer"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                            <circle cx="9" cy="21" r="1"></circle>
                                            <circle cx="20" cy="21" r="1"></circle>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                        </svg>
                                        @t('Add to cart')
                                    </button>
                                @endstorefrontForm

                                @if($isAuthenticated && ($store['favorites_enabled'] ?? true))
                                    @storefrontForm('favorite-toggle', ['class' => 'product__favorite-form shrink-0'])
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="product_name" value="{{ $product->name }}">
                                        <input type="hidden" name="product_sku" value="{{ $product->sku }}">
                                        <input type="hidden" name="product_slug" value="{{ data_get($product, 'seo.slug') ?: $product->id }}">
                                        <input type="hidden" name="redirect" value="/{{ $locale }}/products/{{ $productSlug }}">
                                        <button
                                            type="submit"
                                            data-favorite-button
                                            data-favorited="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? 'true' : 'false' }}"
                                            class="product__favorite flex h-11 w-11 items-center justify-center rounded-lg border border-border-subtle bg-surface-card {{ collect($favoriteIds ?? [])->contains((int) $product->id) ? 'text-primary' : 'text-body' }} transition-colors hover:text-primary cursor-pointer"
                                            aria-label="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? t('Remove from favorites') : t('Add to favorites') }}"
                                            title="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? t('Remove from favorites') : t('Add to favorites') }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                            </svg>
                                        </button>
                                    @endstorefrontForm
                                @endif
                            </div>
                            @storefrontError('product_id')
                        @elseif($canSeePrices)
                            <a href="@routeUrl('store.login')" class="product__login inline-flex items-center justify-center self-start rounded-lg bg-primary px-4 py-2.5 font-medium text-primary-content transition-colors hover:bg-primary-600 hover:no-underline">@t('Log in to purchase')</a>
                        @else
                            <a href="@routeUrl('store.login')" class="product__login inline-flex items-center justify-center gap-2 self-start rounded-lg border border-primary bg-primary-subtle px-4 py-2.5 font-medium text-primary transition-colors hover:bg-primary-100 hover:no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                @t('Log in to see prices')
                            </a>
                        @endif
                    @show
                    @endunless

                    @section('short-description')
                        @if(!empty(($displayProduct ?? $product)->short_description))
                            <div class="product__short text-body">{!! ($displayProduct ?? $product)->short_description !!}</div>
                        @endif
                    @show

                    @if(!($variants->isNotEmpty() && ($store['variation_display_mode'] ?? 'table') === 'dropdown'))
                        @section('tabs')
                            @include('partials.product-details-tabs')
                        @show
                    @endif
                </div>
            </div>

            @section('variant-table')
                @if(empty($isVariantView) && $variants->isNotEmpty() && ($store['variation_display_mode'] ?? 'table') === 'table')
                    @push('head')
                        <style>[data-variant-submit]:disabled{opacity:.5;cursor:not-allowed}</style>
                    @endpush

                    {{--
                        Bulk variant table (B2B quick order): one row per variant with
                        a quantity field; "Add everything to cart" posts them all at
                        once via cart-bulk-add. The attribute selects filter rows
                        client-side; quantities persist across filters and are all
                        submitted. With JS off the form still posts (the runtime
                        enhances it to AJAX + the added-to-cart dialog).
                    --}}
                    <section class="product__variant-table flex flex-col gap-4" data-variant-table>
                        <h2 class="font-primary text-2xl text-headings m-0">@t('Choose variants')</h2>

                        @if($canSeePrices && $canAddToCart)
                            @if(!empty($variantFilters))
                                <div class="product__variant-filters flex flex-wrap items-end gap-3">
                                    @foreach($variantFilters as $i => $filter)
                                        <label class="flex flex-col gap-1 text-sm">
                                            <span class="font-medium text-body">{{ $filter['name'] }}</span>
                                            <select data-variant-filter data-filter-index="{{ $i }}" data-filter-mode="{{ data_get($filter, 'mode', 'options') }}"
                                                class="rounded border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary">
                                                <option value="">@t('All')</option>
                                                @foreach(($filter['values'] ?? []) as $value)
                                                    <option value="{{ $value }}">{{ data_get($filter, 'mode') === 'boolean' ? ($value === 'true' ? t('Yes') : t('No')) : $value }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                    @endforeach
                                    <button type="button" data-variant-filters-clear
                                        class="rounded border border-border-subtle bg-surface-card px-3 py-2 text-sm text-body hover:bg-surface-hover cursor-pointer">
                                        @t('Clear filters')
                                    </button>
                                </div>
                            @endif

                            @storefrontForm('cart-bulk-add', ['class' => 'product__variant-form flex flex-col gap-4'])
                                <input type="hidden" name="redirect" value="/{{ $locale }}/products/{{ $productSlug }}">

                                <div class="bg-surface-card border border-border-subtle rounded-lg overflow-hidden max-md:border-0 max-md:rounded-none max-md:bg-transparent max-md:overflow-visible">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm text-left border-collapse max-md:block">
                                            <thead class="max-md:hidden">
                                                <tr class="text-xs uppercase tracking-wide text-body/70 border-b border-border-subtle">
                                                    <th class="px-4 py-3 font-medium" scope="col"><span class="sr-only">@t('Image')</span></th>
                                                    @foreach($variantColumns as $column)
                                                        <th class="px-4 py-3 font-medium" scope="col">{{ $column }}</th>
                                                    @endforeach
                                                    <th class="px-4 py-3 font-medium" scope="col">@t('SKU')</th>
                                                    <th class="px-4 py-3 font-medium text-right" scope="col">@t('Price')</th>
                                                    <th class="px-4 py-3 font-medium text-right" scope="col">@t('Quantity')</th>
                                                </tr>
                                            </thead>
                                            <tbody class="max-md:block">
                                                @foreach($variantRows as $row)
                                                    <tr class="border-b border-border-subtle md:last:border-b-0 hover:bg-surface-hover transition-colors max-md:block max-md:border max-md:border-border-subtle max-md:rounded-lg max-md:bg-surface-card max-md:p-4 max-md:mb-3" data-variant-row
                                                        @foreach($variantFilters as $i => $filter)
                                                            data-filter-{{ $i }}="{{ data_get(collect($row['attributes'])->firstWhere('name', $filter['name']), 'value', '') }}"
                                                        @endforeach
                                                    >
                                                        <td class="px-4 py-3 max-md:flex max-md:justify-center max-md:py-2">
                                                            <a href="/{{ $locale }}/products/{{ $productSlug }}?variant={{ $row['id'] }}"
                                                                class="block h-12 w-12 overflow-hidden rounded bg-surface hover:opacity-90"
                                                                aria-label="{{ t('View') }} {{ $row['name'] }}">
                                                                @if($row['image'])
                                                                    <img src="@storefrontImage($row['image'], 96, 96, 80)" alt="{{ $row['name'] }}" class="h-full w-full object-contain" loading="lazy">
                                                                @else
                                                                    @include('partials.__image-placeholder', ['size' => 'h-4 w-4'])
                                                                @endif
                                                            </a>
                                                        </td>
                                                        @foreach($variantColumns as $column)
                                                            <td class="px-4 py-3 text-headings max-md:flex max-md:items-center max-md:justify-between max-md:gap-4 max-md:px-0 max-md:py-1.5">
                                                                <span class="md:hidden font-medium text-body/70">{{ $column }}</span>
                                                                <span>{{
                                                                    data_get(collect($row['attributes'])->firstWhere('name', $column), 'type') === 'boolean'
                                                                        ? (data_get(collect($row['attributes'])->firstWhere('name', $column), 'value') === 'true' ? t('Yes') : t('No'))
                                                                        : data_get(collect($row['attributes'])->firstWhere('name', $column), 'value', '')
                                                                }}</span>
                                                            </td>
                                                        @endforeach
                                                        <td class="px-4 py-3 text-body max-md:flex max-md:items-center max-md:justify-between max-md:gap-4 max-md:px-0 max-md:py-1.5">
                                                            <span class="md:hidden font-medium text-body/70">@t('SKU')</span>
                                                            <span>{{ $row['sku'] }}</span>
                                                        </td>
                                                        <td class="px-4 py-3 text-right max-md:flex max-md:items-center max-md:justify-between max-md:gap-4 max-md:px-0 max-md:py-1.5">
                                                            <span class="md:hidden font-medium text-body/70">@t('Price')</span>
                                                            @include('components.price', ['priceView' => $row['priceView'], 'amount' => $row['unit_amount'] ?? 0])
                                                        </td>
                                                        <td class="px-4 py-3 text-right max-md:flex max-md:items-center max-md:justify-between max-md:gap-4 max-md:px-0 max-md:py-1.5">
                                                            <span class="md:hidden font-medium text-body/70">@t('Quantity')</span>
                                                            <input type="number" name="items[{{ $row['id'] }}]" value="0"
                                                                min="0" step="1" inputmode="numeric"
                                                                @if(data_get($row, 'stock.max') !== null) max="{{ (int) data_get($row, 'stock.max') }}" @endif
                                                                data-variant-qty
                                                                aria-label="{{ t('Quantity') }} {{ $row['name'] }}"
                                                                class="w-20 rounded border border-surface-input-stroke bg-surface-input px-2 py-1 text-right text-body outline-none focus:border-primary">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr class="border-b border-border-subtle max-md:block max-md:border max-md:border-border-subtle max-md:rounded-lg max-md:bg-surface-card max-md:p-4" data-variant-empty style="display: none">
                                                    <td colspan="{{ count($variantColumns) + 4 }}" class="px-4 py-6 text-center text-body">
                                                        @t('No variants match your filters')
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex gap-6">
                                        <span class="flex flex-col">
                                            <span class="text-lg font-semibold text-headings" data-variant-total-items>0</span>
                                            <span class="text-sm text-body">@t('Total items')</span>
                                        </span>
                                        <span class="flex flex-col">
                                            <span class="text-lg font-semibold text-headings" data-variant-subtotal>{{ formatCurrency(0) }}</span>
                                            <span class="text-sm text-body">@t('Subtotal')</span>
                                        </span>
                                    </div>
                                    <button type="submit" data-variant-submit disabled
                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 font-medium text-primary-content transition-colors hover:bg-primary-600 cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                            <circle cx="9" cy="21" r="1"></circle>
                                            <circle cx="20" cy="21" r="1"></circle>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                        </svg>
                                        @t('Add everything to cart')
                                    </button>
                                </div>
                                <p class="text-xs text-body m-0">@t('Taxes, discounts, and shipping will be applied at checkout.')</p>
                            @endstorefrontForm
                            @storefrontError('items')
                        @else
                            <a href="@routeUrl('store.login')" class="inline-flex items-center justify-center gap-2 self-start rounded-lg bg-primary px-4 py-2.5 font-medium text-primary-content transition-colors hover:bg-primary-600 hover:no-underline">@t('Log in to see prices')</a>
                        @endif
                    </section>

                    <script type="module">
                        const table = document.querySelector('[data-variant-table]');
                        if (table) {
                            const rows = [...table.querySelectorAll('[data-variant-row]')];
                            const emptyRow = table.querySelector('[data-variant-empty]');
                            const totalItemsEl = table.querySelector('[data-variant-total-items]');
                            const subtotalEl = table.querySelector('[data-variant-subtotal]');
                            const submitEl = table.querySelector('[data-variant-submit]');
                            const filters = [...table.querySelectorAll('[data-variant-filter]')];
                            const clearBtn = table.querySelector('[data-variant-filters-clear]');
                            const currencyCode = window.__STOREFRONT__?.currency || 'EUR';
                            const localeTag = document.documentElement.lang || undefined;

                            const formatMoney = (value) => {
                                try {
                                    return new Intl.NumberFormat(localeTag, { style: 'currency', currency: currencyCode }).format(value);
                                } catch {
                                    return value.toFixed(2);
                                }
                            };

                            const unitOf = (row) => {
                                const priceEl = row.querySelector('.price[data-amount]');
                                return priceEl ? Number(priceEl.getAttribute('data-amount')) || 0 : 0;
                            };

                            const recalc = () => {
                                let items = 0;
                                let subtotal = 0;
                                for (const row of rows) {
                                    const input = row.querySelector('[data-variant-qty]');
                                    const qty = Math.max(0, Number(input?.value) || 0);
                                    items += qty;
                                    subtotal += qty * unitOf(row);
                                }
                                if (totalItemsEl) totalItemsEl.textContent = String(items);
                                if (subtotalEl) subtotalEl.textContent = formatMoney(subtotal);
                                if (submitEl) submitEl.disabled = items <= 0;
                            };

                            const filterIndexes = [...new Set(filters.map((el) => el.getAttribute('data-filter-index')))];

                            const activeCriteria = () => {
                                const criteria = {};
                                for (const index of filterIndexes) {
                                    const controls = filters.filter((el) => el.getAttribute('data-filter-index') === index);
                                    if (controls.length === 0) continue;
                                    const mode = controls[0].getAttribute('data-filter-mode') || 'options';
                                    const want = (controls[0].value ?? '').trim();
                                    if (want === '') continue;
                                    criteria[index] = { mode, want };
                                }
                                return criteria;
                            };

                            const rowMatches = (row, criteria) => {
                                for (const [index, rule] of Object.entries(criteria)) {
                                    const cell = row.getAttribute('data-filter-' + index) ?? '';
                                    if (cell !== rule.want) return false;
                                }
                                return true;
                            };

                            const applyFilters = () => {
                                const criteria = activeCriteria();
                                let visibleCount = 0;
                                for (const row of rows) {
                                    const visible = rowMatches(row, criteria);
                                    row.style.display = visible ? '' : 'none';
                                    if (visible) visibleCount += 1;
                                }
                                if (emptyRow) {
                                    emptyRow.style.display = visibleCount === 0 ? '' : 'none';
                                }
                            };

                            table.addEventListener('input', (event) => {
                                if (event.target.matches('[data-variant-qty]')) {
                                    recalc();
                                }
                                if (event.target.matches('[data-variant-filter]')) {
                                    applyFilters();
                                }
                            });
                            table.addEventListener('change', (event) => {
                                if (event.target.matches('[data-variant-filter]')) {
                                    applyFilters();
                                }
                            });
                            clearBtn?.addEventListener('click', () => {
                                for (const el of filters) {
                                    el.value = '';
                                }
                                applyFilters();
                            });

                            recalc();
                        }
                    </script>
                @endif
            @show

            @section('tier-highlight')
                @if($canSeePrices && isset($tierPrices) && count($tierPrices) > 0)
                    <script type="module">
                        const scope = document.querySelector('[data-product-detail]');
                        if (scope) {
                            const qtyInput = scope.querySelector('input[name="quantity"]');
                            const cards = [...scope.querySelectorAll('[data-tier-card]')];
                            const sync = () => {
                                const qty = Number(qtyInput?.value || 0);
                                let activeMin = -1;
                                for (const card of cards) {
                                    const min = Number(card.dataset.tierMin || 0);
                                    if (qty >= min && min > activeMin) {
                                        activeMin = min;
                                    }
                                }
                                for (const card of cards) {
                                    const active = Number(card.dataset.tierMin || 0) === activeMin;
                                    card.classList.toggle('border-primary', active);
                                    card.classList.toggle('bg-primary-subtle', active);
                                    card.classList.toggle('border-border-subtle', !active);
                                }
                            };
                            qtyInput?.addEventListener('input', sync);
                            qtyInput?.addEventListener('change', sync);
                            sync();
                        }
                    </script>
                @endif
            @show

            @if($variants->isNotEmpty() && ($store['variation_display_mode'] ?? 'table') === 'dropdown')
                @section('tabs')
                    @include('partials.product-details-tabs')
                @show
            @endif


            @section('related-products')
                @if($crossSells->isNotEmpty())
                    <section class="product__related mt-6 flex flex-col gap-4">
                        <h2 class="font-primary text-2xl text-headings m-0">@t('Related products')</h2>
                        <ul class="catalog__grid m-0 grid list-none grid-cols-2 gap-4 p-0 md:grid-cols-3 lg:grid-cols-4">
                            @foreach($crossSells as $cs)
                                <li>@include('components.product-card', [
                                    'product'   => $cs,
                                    'view'      => 'grid',
                                    'priceView' => data_get($crossSellPriceViews ?? [], (string) ($cs->id ?? '')),
                                    'redirect'  => '/' . $locale . '/products/' . $productSlug,
                                ])</li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            @show
        @endif
    </article>
