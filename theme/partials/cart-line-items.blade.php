{{--
    Cart line items section.

    AJAX-refreshable render unit — rendered inline via
    `@storefrontSection('cart-line-items')` and re-rendered in place after a
    cart mutation (see ThemeSections). It owns the empty-cart state too, so a
    cart emptied via AJAX (last item removed, cart cleared) shows the right
    message without a full reload.

    Each line is a self-contained card: image, name, SKU, optional unit label,
    a quantity stepper (cart-update form, auto-submitted by the runtime), an
    optional stock badge, the line price with per-line catalog-rule discount,
    an optional favourite toggle, a remove control and — when the tenant allows
    it — a preferred delivery-date picker.

    Every POST control is its own `@storefrontForm` (siblings, never nested).

    Data: $cartLines (normalised rows), $cartCount, $store, $locale,
    $isAuthenticated, $canSeePrices — all from the global composer.
--}}
@if(($cartCount ?? 0) === 0)
    @include('components.empty-state', [
        'title'   => t('Your cart is empty'),
        'message' => t('Browse our shop to find something you like.'),
    ])
@else
    <ul class="cart__items list-none m-0 p-0 flex flex-col gap-4">
        @foreach($cartLines as $line)
            <li
                class="cart__item flex flex-col desktop:flex-row desktop:items-center gap-4 desktop:gap-6 p-4 rounded-lg border border-border-subtle bg-surface-card"
                data-cart-item-id="{{ $line['id'] }}"
            >
                {{-- Product: image + name / SKU / unit / delivery date --}}
                <div class="cart__item-product flex gap-4 items-start flex-1 min-w-0">
                    <a
                        href="@routeUrl('store.product', ['identifier' => $line['slug'] !== '' ? $line['slug'] : $line['product_id']])"
                        class="cart__item-image shrink-0 block w-24 h-24 rounded-lg bg-surface-page overflow-hidden hover:no-underline"
                    >
                        @if($line['image_url'])
                            <img src="@storefrontImage($line['image_url'], 160, 160, 85)" alt="{{ $line['name'] }}" class="w-full h-full object-cover" loading="lazy">
                        @else
                            @include('partials.__image-placeholder', ['size' => 'h-8 w-8'])
                        @endif
                    </a>

                    <div class="cart__item-info flex-1 min-w-0 flex flex-col gap-1">
                        <a
                            href="@routeUrl('store.product', ['identifier' => $line['slug'] !== '' ? $line['slug'] : $line['product_id']])"
                            class="cart__item-name text-headings text-sm font-medium hover:text-primary hover:no-underline"
                        >{{ $line['name'] }}</a>

                        @if($line['sku'] !== '')
                            <span class="cart__item-sku text-xs text-body">{{ $line['sku'] }}</span>
                        @endif
                        @storefrontSlot('cart.line.meta')

                        @if($line['unit_label'] !== '')
                            <span class="cart__item-unit text-xs text-body">{{ $line['unit_label'] }}</span>
                        @endif

                        @if($store['delivery_date_enabled'] ?? false)
                            @storefrontForm('cart-line-delivery-date', [
                                '_params' => ['cartItem' => $line['id']],
                                'class'   => 'cart__item-delivery flex items-center gap-2 mt-1 flex-wrap',
                            ])
                                <input type="hidden" name="redirect" value="/{{ $locale }}/cart">
                                <label class="text-xs text-body" for="delivery-{{ $line['id'] }}">@t('Delivery date')</label>
                                <input
                                    id="delivery-{{ $line['id'] }}"
                                    type="date"
                                    name="delivery_date"
                                    value="{{ $line['delivery_date'] }}"
                                    class="px-2 py-1 text-xs border border-border-subtle rounded-lg bg-surface-card text-headings outline-none focus:border-primary"
                                >
                                <button type="submit" data-storefront-no-js class="cart__no-js text-xs font-medium text-primary hover:text-primary-600 cursor-pointer">@t('Save')</button>
                            @endstorefrontForm
                        @endif
                    </div>
                </div>

                {{-- Controls: quantity + stock + price + actions --}}
                <div class="cart__item-controls flex flex-wrap items-center justify-between gap-4 desktop:gap-6 desktop:justify-end">
                    <div class="cart__item-qty flex flex-col gap-1">
                        <span class="text-xs text-body">@t('Quantity')</span>
                        @storefrontForm('cart-update', [
                            '_params' => ['cartItem' => $line['id']],
                            'class'   => 'flex items-center gap-2',
                        ])
                            <input type="hidden" name="redirect" value="/{{ $locale }}/cart">
                            @include('components.quantity-selector', [
                                'name'  => 'quantity',
                                'value' => (int) $line['quantity'],
                                'min'   => $line['min_quantity'],
                                'max'   => $line['max_quantity'],
                            ])
                            <button type="submit" data-storefront-no-js class="cart__no-js text-xs font-medium text-primary hover:text-primary-600 cursor-pointer">@t('Update')</button>
                        @endstorefrontForm
                    </div>

                    @if(($line['manage_stock'] ?? false) && !is_null($line['stock_quantity']))
                        <div class="cart__item-stock flex flex-col gap-1">
                            <span class="text-xs text-body">@t('Stock')</span>
                            @include('components.stock-badge', [
                                'inStock'  => ($line['stock_quantity'] > 0),
                                'quantity' => $line['stock_quantity'],
                            ])
                        </div>
                    @endif

                    <div class="cart__item-price flex flex-col items-end gap-0.5 min-w-24">
                        @include('components.price', [
                            'amount'    => $line['line_total'],
                            'priceView' => [
                                'current_excl' => $line['line_total_excl'],
                                'current_incl' => $line['line_total_incl'],
                                'has_tax'      => $line['has_tax'],
                            ],
                        ])

                        @if($canSeePrices && $line['has_discount'])
                            <span class="cart__item-was text-xs text-body line-through">{{ formatCurrency((($store['display_prices_with_tax'] ?? 'excluding_tax') === 'including_tax' && $line['has_tax'] ? $line['original_price_incl'] : $line['original_price_excl']) * $line['quantity']) }}</span>
                            @foreach($line['applied_catalog_rules'] as $rule)
                                @if(($rule['rule_name'] ?? '') !== '')
                                    <span class="cart__item-rule text-xs text-success">{{ $rule['rule_name'] }}</span>
                                @endif
                            @endforeach
                        @elseif($canSeePrices && (float) $line['quantity'] > 1)
                            <span class="cart__item-each text-xs text-body">{{ formatCurrency(($store['display_prices_with_tax'] ?? 'excluding_tax') === 'including_tax' && $line['has_tax'] ? $line['unit_price_incl'] : $line['unit_price_excl']) }} @t('each')</span>
                        @endif
                    </div>

                    <div class="cart__item-actions flex items-center gap-1">
                        @if($isAuthenticated && ($store['favorites_enabled'] ?? true))
                            @storefrontForm('favorite-toggle', ['class' => 'inline'])
                                <input type="hidden" name="product_id" value="{{ $line['product_id'] }}">
                                <input type="hidden" name="product_name" value="{{ $line['name'] }}">
                                <input type="hidden" name="product_sku" value="{{ $line['sku'] }}">
                                <input type="hidden" name="product_slug" value="{{ $line['slug'] !== '' ? $line['slug'] : $line['product_id'] }}">
                                <input type="hidden" name="redirect" value="/{{ $locale }}/cart">
                                <button
                                    type="submit"
                                    class="cart__item-favorite h-9 w-9 flex items-center justify-center rounded-lg text-body transition-colors hover:text-primary hover:bg-surface-hover cursor-pointer"
                                    aria-label="{{ t('Move to favorites') }}"
                                    title="{{ t('Move to favorites') }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6" aria-hidden="true">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                </button>
                            @endstorefrontForm
                        @endif

                        @storefrontForm('cart-remove', [
                            '_params' => ['cartItem' => $line['id']],
                            'class'   => 'inline',
                        ])
                            <input type="hidden" name="redirect" value="/{{ $locale }}/cart">
                            <button
                                type="submit"
                                class="cart__item-remove h-9 w-9 flex items-center justify-center rounded-lg text-body transition-colors hover:text-error hover:bg-surface-hover cursor-pointer"
                                aria-label="{{ t('Remove') }}"
                                title="{{ t('Remove') }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6" aria-hidden="true">
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                    <line x1="6" y1="18" x2="18" y2="6"></line>
                                </svg>
                            </button>
                        @endstorefrontForm
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endif

