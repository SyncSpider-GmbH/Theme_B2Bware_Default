{{--
    Cart discounts section: coupon control + applied price-rule breakdown.

    AJAX-refreshable render unit — rendered inline via
    `@storefrontSection('cart-discounts')` and re-rendered after a cart mutation
    (apply/remove coupon, quantity change that crosses a rule threshold, …).

    The coupon apply/remove forms are SIBLINGS of the cart's other forms, never
    nested — HTML forbids nested <form> elements. The runtime enhances them to
    AJAX automatically (cart-coupon-apply / cart-coupon-remove are eligible
    form types); with JS off they POST → redirect back to the cart.

    Renders nothing for an empty cart so an AJAX-emptied cart (last item
    removed, cart cleared) collapses cleanly — the coupon control never lingers
    once there is nothing to discount, matching cart-summary.

    Data: $cartPricing (supplied by ThemeSections), $cartCount, $store, $locale
    (composer).
--}}
@if(($cartCount ?? 0) > 0 && ($store['coupons_enabled'] || !empty($cartPricing['applied_rules'])))
    <div class="cart__discounts flex flex-col gap-3 p-5 bg-surface-card border border-border-subtle rounded-lg">
        @if($store['coupons_enabled'])
            @if($cartPricing['coupon'] ?? null)
                <div class="cart__coupon-applied flex items-center justify-between gap-2 rounded-lg border border-primary-subtle-stroke bg-primary-subtle px-3 py-2">
                    <span class="flex items-center gap-2 text-sm text-headings">
                        <span class="font-mono font-medium">{{ $cartPricing['coupon']['code'] ?? '' }}</span>
                        <span class="text-body">— @t('Coupon applied')</span>
                    </span>
                    @storefrontForm('cart-coupon-remove', ['class' => 'inline'])
                        <input type="hidden" name="redirect" value="/{{ $locale }}/cart">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-transparent text-body border border-border-subtle font-medium px-3 py-1 text-sm transition-colors hover:border-primary hover:text-primary cursor-pointer">
                            @t('Remove')
                        </button>
                    @endstorefrontForm
                </div>
            @else
                @storefrontForm('cart-coupon-apply', ['class' => 'cart__coupon-form flex items-stretch gap-2'])
                    <input type="hidden" name="redirect" value="/{{ $locale }}/cart">
                    <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="@t('Coupon code')" autocomplete="off" class="flex-1 min-w-0 px-3 py-2 border border-border-subtle rounded-lg bg-surface-card text-headings text-sm focus:border-primary outline-none">
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary text-primary-content font-medium px-4 py-2 text-sm transition-colors hover:bg-primary-600 cursor-pointer">@t('Apply')</button>
                @endstorefrontForm
                @if($cartPricing['coupon_invalid'] ?? false)
                    <p class="text-sm text-error m-0">@t('That coupon code is no longer valid.')</p>
                @endif
                @storefrontError('coupon_code')
            @endif
        @endif

        @if(!empty($cartPricing['applied_rules']))
            <ul class="cart__rules list-none m-0 p-0 flex flex-col gap-1">
                @foreach($cartPricing['applied_rules'] as $rule)
                    <li class="flex items-center justify-between gap-2 text-sm text-body">
                        <span>{{ $rule['rule_name'] ?? t('Discount') }}</span>
                        @if(!empty($rule['free_shipping']))
                            <span class="text-success font-medium">@t('Free shipping')</span>
                        @elseif(!empty($rule['discount']))
                            <span class="text-success font-medium">-{{ formatCurrency($rule['discount']) }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif

