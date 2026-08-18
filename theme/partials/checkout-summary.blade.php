{{--
    Checkout order-summary section: coupon control + applied price-rule
    breakdown + totals.

    AJAX-refreshable render unit — rendered inline via
    `@storefrontSection('checkout-summary')` inside the checkout form and
    re-rendered after an AJAX coupon apply/remove, so the totals update without
    a full reload.

    The coupon controls target the hidden #checkout-coupon-apply /
    #checkout-coupon-remove sibling forms by `form=` id (HTML forbids nested
    <form> elements; the checkout page already wraps everything in one form).
    Both hidden forms are always present on the page so these controls keep
    working after the summary is swapped in place.

    Data: $cartPricing (supplied by ThemeSections), $store (composer).
--}}
<div class="checkout-summary flex flex-col gap-3">
    <h2 class="font-primary text-lg font-semibold text-headings m-0">@t('Order summary')</h2>

    @if($store['coupons_enabled'])
        @if($cartPricing['coupon'] ?? null)
            <div class="checkout-summary__coupon-applied flex items-center justify-between gap-2 rounded-lg border border-primary/30 bg-primary/5 px-3 py-2">
                <span class="flex items-center gap-2 text-sm text-headings">
                    <span class="font-mono">{{ $cartPricing['coupon']['code'] ?? '' }}</span>
                    <span class="text-body">— @t('Coupon applied')</span>
                </span>
                <button type="submit" form="checkout-coupon-remove" class="inline-flex items-center justify-center rounded-lg bg-transparent text-body border border-border-subtle font-medium px-3 py-1 text-sm transition-colors hover:border-primary hover:text-primary cursor-pointer">
                    @t('Remove')
                </button>
            </div>
        @else
            <div class="checkout-summary__coupon-form flex items-stretch gap-2">
                <input type="text" name="coupon_code" form="checkout-coupon-apply" value="{{ old('coupon_code') }}" placeholder="@t('Coupon code')" autocomplete="off" class="flex-1 min-w-0 px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                <button type="submit" form="checkout-coupon-apply" class="inline-flex items-center justify-center rounded-lg bg-primary text-primary-content font-medium px-4 py-2 transition-colors hover:bg-primary-600 cursor-pointer">@t('Apply')</button>
            </div>
            @if($cartPricing['coupon_invalid'] ?? false)
                <p class="text-sm text-error m-0">@t('That coupon code is no longer valid.')</p>
            @endif
            @storefrontError('coupon_code')
        @endif
    @endif

    @if(!empty($cartPricing['applied_rules']))
        <ul class="checkout-summary__rules list-none m-0 p-0 flex flex-col gap-1">
            @foreach($cartPricing['applied_rules'] as $rule)
                <li class="flex items-center justify-between gap-2 text-sm text-body">
                    <span>{{ $rule['rule_name'] ?? t('Discount') }}</span>
                    @if(!empty($rule['free_shipping']))
                        <span class="text-success">@t('Free shipping')</span>
                    @elseif(!empty($rule['discount']))
                        <span class="text-success">-{{ formatCurrency($rule['discount']) }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif

    <dl class="checkout-summary__totals flex flex-col gap-2 m-0">
        <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-body">@t('Subtotal')</dt>
            <dd class="text-sm text-headings m-0">{{ formatCurrency($cartPricing['subtotal'] ?? 0) }}</dd>
        </div>
        @if(!empty($cartPricing['discount']))
            <div class="flex items-center justify-between gap-2">
                <dt class="text-sm text-body">@t('Discount')</dt>
                <dd class="text-sm text-success m-0">-{{ formatCurrency($cartPricing['discount']) }}</dd>
            </div>
        @endif
        <div class="flex items-center justify-between gap-2">
            <dt class="text-sm text-body">@t('Shipping')</dt>
            <dd class="text-sm text-headings m-0">
                @if(!empty($cartPricing['free_shipping']))
                    @t('Free shipping')
                @elseif(!empty($cartPricing['shipping_known']))
                    {{ formatCurrency($cartPricing['shipping'] ?? 0) }}
                @else
                    @t('Calculated at checkout')
                @endif
            </dd>
        </div>
        @if(!empty($cartPricing['surcharge']))
            <div class="flex items-center justify-between gap-2">
                <dt class="text-sm text-body">@t('Surcharge')</dt>
                <dd class="text-sm text-headings m-0">{{ formatCurrency($cartPricing['surcharge']) }}</dd>
            </div>
        @endif
        @if(!empty($cartPricing['tax']))
            <div class="flex items-center justify-between gap-2">
                <dt class="text-sm text-body">@t('VAT')</dt>
                <dd class="text-sm text-headings m-0">{{ formatCurrency($cartPricing['tax']) }}</dd>
            </div>
        @endif
    </dl>

    <div class="checkout-summary__divider h-px w-full bg-border-subtle"></div>

    <div class="checkout-summary__total flex items-center justify-between gap-2">
        <span class="text-sm font-semibold text-body">@t('Total')</span>
        <span class="text-lg font-semibold text-headings">{{ formatCurrency($cartPricing['grand_total'] ?? 0) }}</span>
        {{-- Locale-formatted mirror of the total for the "Place order" button,
             which lives OUTSIDE this swapped section. The page's inline module
             copies this into the button after every AJAX summary refresh, so
             all currency formatting stays server-side. --}}
        <span data-checkout-section-total hidden>{{ formatCurrency($cartPricing['grand_total'] ?? 0) }}</span>
    </div>
    @if(!empty($cartPricing['tax']))
        <p class="checkout-summary__vat-note text-xs text-body text-right m-0">
            @if(($store['display_prices_with_tax'] ?? 'excluding_tax') === 'including_tax')
                @t('Prices include VAT')
            @else
                @t('Prices exclude VAT')
            @endif
        </p>
    @endif

    @storefrontError('cart')
</div>
