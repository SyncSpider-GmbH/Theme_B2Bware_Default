{{--
    Cart order-summary section: totals card + checkout CTA + the rule-derived
    "X away from free delivery" banner.

    AJAX-refreshable render unit — rendered inline via
    `@storefrontSection('cart-summary')` and re-rendered after every cart
    mutation. Renders nothing for an empty cart so an AJAX-emptied cart
    collapses cleanly (the line-items section shows the empty message instead).

    On the cart page shipping/tax are not yet known (no address or method), so
    shipping reads "Calculated at checkout" (or "Free shipping" when a rule
    grants it) and the VAT line shows only when a tax amount is present. When
    the tenant hides prices from guests ($canSeePrices false) the totals are
    replaced by a sign-in call to action.

    Data: $cartPricing, $freeDelivery (ThemeSections) + $canSeePrices (composer).
--}}
@if(($cartCount ?? 0) > 0)
    <div class="cart__summary flex flex-col gap-4">
        <div class="cart__summary-card flex flex-col gap-4 p-5 bg-surface-card border border-border-subtle rounded-lg">
            <h2 class="cart__summary-title font-primary text-lg font-semibold text-headings m-0">@t('Order summary')</h2>

            @if($canSeePrices)
                <dl class="cart__totals flex flex-col gap-2 m-0">
                    <div class="cart__totals-row flex items-center justify-between gap-2">
                        <dt class="text-sm text-body">@t('Subtotal')</dt>
                        <dd class="text-sm text-headings m-0">{{ formatCurrency($cartPricing['subtotal'] ?? 0) }}</dd>
                    </div>

                    @if(!empty($cartPricing['discount']))
                        <div class="cart__totals-row flex items-center justify-between gap-2">
                            <dt class="text-sm text-body">@t('Discount')</dt>
                            <dd class="text-sm text-success m-0">-{{ formatCurrency($cartPricing['discount']) }}</dd>
                        </div>
                    @endif

                    <div class="cart__totals-row flex items-center justify-between gap-2">
                        <dt class="text-sm text-body">@t('Shipping')</dt>
                        <dd class="text-sm text-headings m-0">
                            @if(!empty($cartPricing['free_shipping']))
                                @t('Free shipping')
                            @else
                                @t('Calculated at checkout')
                            @endif
                        </dd>
                    </div>

                    @if(!empty($cartPricing['surcharge']))
                        <div class="cart__totals-row flex items-center justify-between gap-2">
                            <dt class="text-sm text-body">@t('Surcharge')</dt>
                            <dd class="text-sm text-headings m-0">{{ formatCurrency($cartPricing['surcharge']) }}</dd>
                        </div>
                    @endif

                    @if(!empty($cartPricing['tax']))
                        <div class="cart__totals-row flex items-center justify-between gap-2">
                            <dt class="text-sm text-body">@t('VAT')</dt>
                            <dd class="text-sm text-headings m-0">{{ formatCurrency($cartPricing['tax']) }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="cart__summary-divider h-px w-full bg-border-subtle"></div>

                <div class="cart__summary-total flex items-center justify-between gap-2">
                    <span class="text-sm font-semibold text-body">@t('Total')</span>
                    <span class="text-lg font-semibold text-headings">{{ formatCurrency($cartPricing['grand_total'] ?? 0) }}</span>
                </div>

                @if(!empty($cartPricing['tax']))
                    <p class="cart__summary-tax-note text-xs text-body m-0">
                        @if(($store['display_prices_with_tax'] ?? 'excluding_tax') === 'including_tax')
                            @t('Prices include VAT')
                        @else
                            @t('Prices exclude VAT')
                        @endif
                    </p>
                @endif

                <a
                    href="@routeUrl('store.checkout')"
                    data-storefront-loading-link
                    class="cart__checkout inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-content font-medium px-4 py-2.5 transition-colors hover:bg-primary-600 hover:no-underline"
                >@t('Proceed to checkout')</a>
            @else
                <p class="cart__summary-locked text-sm text-body m-0">@t('Sign in to see prices and continue to checkout.')</p>
                <a
                    href="@routeUrl('store.login')"
                    data-storefront-loading-link
                    class="cart__summary-login inline-flex items-center justify-center gap-2 rounded-lg bg-primary text-primary-content font-medium px-4 py-2.5 transition-colors hover:bg-primary-600 hover:no-underline"
                >@t('Login to see prices')</a>
            @endif
        </div>

        @if(count($freeDelivery ?? []))
            <div class="cart__free-delivery flex items-center gap-2 px-4 py-3 rounded-lg bg-primary-subtle border border-primary-subtle-stroke">
                <svg class="cart__free-delivery-icon shrink-0 w-4 h-4 text-primary-700" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M3 4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h.05a2.5 2.5 0 0 0 4.9 0h4.1a2.5 2.5 0 0 0 4.9 0H17a1 1 0 0 0 1-1v-3.382a1 1 0 0 0-.106-.448l-1.724-3.447A1 1 0 0 0 15.276 5H13V5a1 1 0 0 0-1-1H3Zm10 3h1.659l1.341 2.682V11H13V7Zm-7.5 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2Zm9 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2Z" />
                </svg>
                <p class="cart__free-delivery-text text-sm font-semibold text-primary-700 m-0">
                    @if($freeDelivery['met'] ?? false)
                        @t('You have unlocked free delivery')
                    @else
                        @if($freeDelivery['is_currency'] ?? false)
                            {{ formatCurrency($freeDelivery['remaining'] ?? 0) }}
                        @else
                            {{ formatNumber($freeDelivery['remaining'] ?? 0) }}
                        @endif
                        @t('away from free delivery')
                    @endif
                </p>
            </div>
        @endif
    </div>
@endif

