@props(['product' => null, 'view' => 'grid', 'redirect' => null, 'showStock' => false, 'priceView' => null])

@if($product)
    {{--
        Catalog product card. Two layouts share one component (Nexus parity):
          - grid ("CARD VIEW"): image on top, details below.
          - list ("LIST VIEW"): a B2B quick-order row — image · title/SKU/price ·
            Quantity stepper · Stock · live Total Price · Add to cart.
        Derived values are computed inline (no @php) per theme rules. Prices
        defer to the canonical `price` component, which honors $canSeePrices.
        The list row's add-to-cart is inlined (not the shared button) so the
        quantity stepper feeds the same cart-add form. Stock shows only when the
        catalog tracks stock ($showStock) and the product manages it; the live
        Total Price (qty × unit) is wired by the page's inline script.
    --}}
    @if($view === 'list')
        <article
            class="product-card product-card--list relative flex flex-col gap-4 rounded-xl border border-border-subtle bg-surface-card p-4 transition-colors hover:border-primary desktop:flex-row desktop:items-center desktop:gap-5"
            data-product-id="{{ $product->id ?? '' }}"
            data-analytics-item-id="{{ ($product->sku ?? '') ?: ($product->id ?? '') }}"
            data-analytics-item-name="{{ $product->name ?? '' }}"
            data-analytics-item-category="{{ data_get($product, 'categories.0.category.name') ?? data_get($product, 'category.name') ?? '' }}"
            @if($canSeePrices) data-analytics-item-price="{{ data_get($product, 'finalPrice.price') ?? data_get($product, 'specialPrice.price') ?? data_get($product, 'price.price') ?? '' }}"@endif
            data-line-row
            data-line-unit="{{ data_get($product, 'finalPrice.price') ?? data_get($product, 'specialPrice.price') ?? data_get($product, 'price.price') ?? 0 }}"
        >
            <a
                href="@routeUrl('store.product', ['identifier' => data_get($product, 'seo.slug') ?: $product->id])"
                class="product-card__image block h-20 w-20 shrink-0 overflow-hidden rounded-lg bg-surface hover:no-underline sm:h-24 sm:w-24"
            >
                @if(data_get($product, 'resolved_main_media.media_url') ?? data_get($product, 'main_media.media_url'))
                    <img
                        src="@storefrontImage(data_get($product, 'resolved_main_media.media_url') ?? data_get($product, 'main_media.media_url'), 200, 200, 85)"
                        alt="{{ $product->name ?? '' }}"
                        class="h-full w-full object-cover"
                        loading="lazy"
                    >
                @elseif(!empty($store['product_placeholder']))
                    <img
                        src="{{ $store['product_placeholder'] }}"
                        alt="{{ $product->name ?? '' }}"
                        class="h-full w-full object-cover"
                        loading="lazy"
                    >
                @else
                    @include('partials.__image-placeholder', ['size' => 'h-8 w-8'])
                @endif
            </a>

            <div class="flex min-w-0 flex-1 flex-col gap-1">
                <h3 class="product-card__title font-primary text-base font-medium m-0">
                    <a
                        href="@routeUrl('store.product', ['identifier' => data_get($product, 'seo.slug') ?: $product->id])"
                        class="text-headings hover:text-primary hover:no-underline"
                    >{{ $product->name ?? '' }}</a>
                </h3>
                @if($product->sku ?? '')
                    <span class="product-card__sku text-xs text-body">{{ $product->sku }}</span>
                @endif
                @storefrontSlot('product.card.meta', [
                    'context' => [
                        'product' => 'product',
                    ],
                ])

                <div class="mt-1">
                    @if($canSeePrices)
                        @include('components.price', [
                            'priceView' => $priceView ?? data_get($priceViews ?? [], (string) ($product->id ?? '')),
                            'amount'    => data_get($product, 'finalPrice.price')
                                ?? data_get($product, 'specialPrice.price')
                                ?? data_get($product, 'price.price')
                                ?? 0,
                            'compareAt' => data_get($product, 'specialPrice.price') !== null
                                ? data_get($product, 'price.price')
                                : null,
                        ])
                    @else
                        <span class="product-card__price-hint inline-flex items-center gap-1.5 text-sm text-body">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            @t('Price visible after login')
                        </span>
                    @endif
                </div>
            </div>

            <div class="product-card__controls flex flex-wrap items-end gap-4 desktop:justify-end desktop:gap-6">
                @if($isAuthenticated && ($store['favorites_enabled'] ?? true))
                    @storefrontForm('favorite-toggle', ['class' => 'shrink-0 self-center'])
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="product_name" value="{{ $product->name }}">
                        <input type="hidden" name="product_sku" value="{{ $product->sku }}">
                        <input type="hidden" name="product_slug" value="{{ data_get($product, 'seo.slug') ?: $product->id }}">
                        @if($redirect)
                            <input type="hidden" name="redirect" value="{{ $redirect }}">
                        @endif
                        <button
                            type="submit"
                            data-favorite-button
                            data-favorited="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? 'true' : 'false' }}"
                            class="product-card__favorite flex h-9 w-9 items-center justify-center rounded-full border border-border-subtle bg-surface-card {{ collect($favoriteIds ?? [])->contains((int) $product->id) ? 'text-primary' : 'text-body' }} transition-colors hover:text-primary cursor-pointer"
                            aria-label="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? t('Remove from favorites') : t('Add to favorites') }}"
                            title="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? t('Remove from favorites') : t('Add to favorites') }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    @endstorefrontForm
                @endif

                @if($canSeePrices && $canAddToCart)
                    @if(data_get($product, 'product_type') === 'configurable')
                        {{-- A configurable product is bought from its parent's
                             product page (variant picker / bulk-variant table).
                             The listing row can't add a specific variant, so it
                             links there instead of a direct add (Nexus parity). --}}
                        <a
                            href="@routeUrl('store.product', ['identifier' => data_get($product, 'seo.slug') ?: $product->id])"
                            class="product-card__options inline-flex h-9 items-center justify-center gap-2 self-center rounded-lg bg-primary px-4 font-medium text-primary-content transition-colors hover:bg-primary-600 hover:no-underline"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <line x1="4" y1="21" x2="4" y2="14"></line>
                                <line x1="4" y1="10" x2="4" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12" y2="3"></line>
                                <line x1="20" y1="21" x2="20" y2="16"></line>
                                <line x1="20" y1="12" x2="20" y2="3"></line>
                                <line x1="1" y1="14" x2="7" y2="14"></line>
                                <line x1="9" y1="8" x2="15" y2="8"></line>
                                <line x1="17" y1="16" x2="23" y2="16"></line>
                            </svg>
                            @t('Choose options')
                        </a>
                    @else
                    @storefrontForm('cart-add', ['class' => 'flex flex-wrap items-end gap-4 desktop:gap-6'])
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        @if($redirect)
                            <input type="hidden" name="redirect" value="{{ $redirect }}">
                        @endif

                        <label class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-body">@t('Quantity')</span>
                            @include('components.quantity-selector', ['value' => 1, 'min' => 1, 'name' => 'quantity'])
                        </label>

                        @if($showStock && data_get($product, 'stock') && filter_var($product->manage_stock ?? false, FILTER_VALIDATE_BOOLEAN))
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium text-body">@t('Stock')</span>
                                <span @class([
                                    'inline-flex h-9 items-center gap-1.5 text-sm font-semibold',
                                    'text-success' => data_get($product, 'stock.stock_availability'),
                                    'text-error' => !data_get($product, 'stock.stock_availability'),
                                ])>
                                    <span @class([
                                        'h-2 w-2 shrink-0 rounded-full',
                                        'bg-success' => data_get($product, 'stock.stock_availability'),
                                        'bg-error' => !data_get($product, 'stock.stock_availability'),
                                    ])></span>
                                    {{ (int) data_get($product, 'stock.stock_quantity', 0) }}
                                </span>
                            </div>
                        @endif

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-medium text-body">@t('Total Price')</span>
                            <span class="inline-flex h-9 items-center text-sm font-semibold text-headings" data-line-total>{{ formatCurrency(data_get($product, 'finalPrice.price') ?? data_get($product, 'specialPrice.price') ?? data_get($product, 'price.price') ?? 0) }}</span>
                        </div>

                        <button
                            type="submit"
                            class="product-card__add inline-flex h-9 items-center justify-center gap-2 self-end rounded-lg bg-primary px-4 font-medium text-primary-content transition-colors cursor-pointer hover:bg-primary-600"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            @t('Add to cart')
                        </button>
                    @endstorefrontForm
                    @storefrontError('product_id')
                    @endif
                @elseif($canSeePrices)
                    <a
                        href="@routeUrl('store.login')"
                        class="inline-flex h-9 items-center justify-center self-center rounded-lg bg-primary px-4 font-medium text-primary-content transition-colors hover:bg-primary-600 hover:no-underline"
                    >@t('Log in to purchase')</a>
                @else
                    <a
                        href="@routeUrl('store.login')"
                        class="inline-flex h-9 items-center justify-center gap-2 self-center rounded-lg border border-primary bg-primary-subtle px-4 font-medium text-primary transition-colors hover:bg-primary-100 hover:no-underline"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        @t('Log in to see prices')
                    </a>
                @endif
            </div>
        </article>
    @else
        <article
            class="product-card product-card--grid relative flex flex-col border border-border-subtle rounded-xl bg-surface-card overflow-hidden transition-colors hover:border-primary"
            data-product-id="{{ $product->id ?? '' }}"
            data-analytics-item-id="{{ ($product->sku ?? '') ?: ($product->id ?? '') }}"
            data-analytics-item-name="{{ $product->name ?? '' }}"
            data-analytics-item-category="{{ data_get($product, 'categories.0.category.name') ?? data_get($product, 'category.name') ?? '' }}"
            @if($canSeePrices) data-analytics-item-price="{{ data_get($product, 'finalPrice.price') ?? data_get($product, 'specialPrice.price') ?? data_get($product, 'price.price') ?? '' }}"@endif
        >
            <a
                href="@routeUrl('store.product', ['identifier' => data_get($product, 'seo.slug') ?: $product->id])"
                class="product-card__image block aspect-square bg-surface overflow-hidden hover:no-underline"
            >
                @if(data_get($product, 'resolved_main_media.media_url') ?? data_get($product, 'main_media.media_url'))
                    <img
                        src="@storefrontImage(data_get($product, 'resolved_main_media.media_url') ?? data_get($product, 'main_media.media_url'), 400, 400, 85)"
                        alt="{{ $product->name ?? '' }}"
                        class="w-full h-full object-cover"
                        loading="lazy"
                    >
                @elseif(!empty($store['product_placeholder']))
                    <img
                        src="{{ $store['product_placeholder'] }}"
                        alt="{{ $product->name ?? '' }}"
                        class="w-full h-full object-cover"
                        loading="lazy"
                    >
                @else
                    @include('partials.__image-placeholder', ['size' => 'h-8 w-8'])
                @endif
            </a>

            @if($isAuthenticated && ($store['favorites_enabled'] ?? true))
                @storefrontForm('favorite-toggle', ['class' => 'absolute right-3 top-3'])
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="product_name" value="{{ $product->name }}">
                    <input type="hidden" name="product_sku" value="{{ $product->sku }}">
                    <input type="hidden" name="product_slug" value="{{ data_get($product, 'seo.slug') ?: $product->id }}">
                    @if($redirect)
                        <input type="hidden" name="redirect" value="{{ $redirect }}">
                    @endif
                    <button
                        type="submit"
                        data-favorite-button
                        data-favorited="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? 'true' : 'false' }}"
                        class="product-card__favorite flex h-8 w-8 items-center justify-center rounded-full bg-surface-card {{ collect($favoriteIds ?? [])->contains((int) $product->id) ? 'text-primary' : 'text-body' }} shadow transition-colors hover:text-primary cursor-pointer"
                        aria-label="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? t('Remove from favorites') : t('Add to favorites') }}"
                        title="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? t('Remove from favorites') : t('Add to favorites') }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="{{ collect($favoriteIds ?? [])->contains((int) $product->id) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </button>
                @endstorefrontForm
            @endif

            <div class="product-card__body flex flex-1 flex-col gap-3 p-4">
                <div class="flex flex-col gap-1">
                    <h3 class="product-card__title font-primary text-base font-medium m-0 line-clamp-2">
                        <a
                            href="@routeUrl('store.product', ['identifier' => data_get($product, 'seo.slug') ?: $product->id])"
                            class="text-headings hover:text-primary hover:no-underline"
                        >{{ $product->name ?? '' }}</a>
                    </h3>
                    @if($product->sku ?? '')
                        <span class="product-card__sku text-xs text-body">{{ $product->sku }}</span>
                    @endif
                    @storefrontSlot('product.card.meta', [
                        'context' => [
                            'product' => 'product',
                        ],
                    ])
                </div>

                <div class="mt-auto flex flex-col gap-3 border-t border-border-subtle pt-3">
                    @include('components.price', [
                        'priceView' => $priceView ?? data_get($priceViews ?? [], (string) ($product->id ?? '')),
                        'amount'    => data_get($product, 'finalPrice.price')
                            ?? data_get($product, 'specialPrice.price')
                            ?? data_get($product, 'price.price')
                            ?? 0,
                        'compareAt' => data_get($product, 'specialPrice.price') !== null
                            ? data_get($product, 'price.price')
                            : null,
                    ])
                    @if($canSeePrices)
                        @include('components.add-to-cart-button', [
                            'product'  => $product,
                            'redirect' => $redirect,
                            'label'    => t('Add to cart'),
                            'block'    => true,
                        ])
                    @endif
                </div>
            </div>
        </article>
    @endif
@endif
