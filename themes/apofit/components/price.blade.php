@props(['amount' => 0, 'compareAt' => null, 'priceView' => null, 'size' => 'base'])

{{--
    Price display. Hidden from anonymous visitors when the tenant requires login
    to see prices ($canSeePrices, set globally by StorefrontComposer from the
    `require_login_for_prices` flag).

    Net vs gross follows the tenant's TaxHub setting
    ($store['display_prices_with_tax'] = excluding_tax | including_tax | both).
    The net/gross figures are PRE-COMPUTED in PHP (StorefrontTaxDisplay) and
    handed in as the optional $priceView view-model
    {current_excl, current_incl, compare_excl, compare_incl, has_tax} — the theme
    never does tax arithmetic (TaxHub is the source of truth). A "excl./incl.
    VAT" label is shown ONLY when the item actually carries tax ($priceView's
    has_tax); we never tax-qualify a price we can't. Callers without a $priceView
    (e.g. a raw line total) pass plain $amount and it renders unchanged.

    A zero/absent price reads "Request quote" when the tenant opts in
    ($store['zero_price_as_quote']), mirroring the Nexus ProductPriceDisplay.
--}}
@if($canSeePrices)
    @if(((float) (data_get($priceView, 'current_excl') ?? $amount)) <= 0 && data_get($store, 'zero_price_as_quote'))
        <span class="price price--quote inline-flex items-center text-sm font-medium text-primary">@t('Request quote')</span>
    @elseif(data_get($priceView, 'has_tax') && data_get($store, 'display_prices_with_tax') === 'including_tax')
        {{-- B2C: gross only --}}
        <span class="price inline-flex items-baseline gap-2 font-semibold" data-amount="{{ data_get($priceView, 'current_incl') }}">
            @if((data_get($priceView, 'compare_incl') ?? $compareAt) && (data_get($priceView, 'compare_incl') ?? $compareAt) > data_get($priceView, 'current_incl'))
                <span class="price__compare text-sm text-body font-normal line-through">{{ formatCurrency(data_get($priceView, 'compare_incl') ?? $compareAt) }}</span>
            @endif
            <span class="price__current {{ $size === 'lg' ? 'text-3xl' : 'text-lg' }} text-headings">{{ formatCurrency(data_get($priceView, 'current_incl')) }}</span>
            <span class="price__tax-note text-xs font-normal text-body">@t('incl. VAT')</span>
        </span>
    @elseif(data_get($priceView, 'has_tax') && data_get($store, 'display_prices_with_tax') === 'both')
        {{-- Mixed B2B/B2C: net primary + gross secondary --}}
        <span class="price inline-flex flex-col gap-0.5" data-amount="{{ data_get($priceView, 'current_excl') }}">
            <span class="inline-flex items-baseline gap-2 font-semibold">
                @if((data_get($priceView, 'compare_excl') ?? $compareAt) && (data_get($priceView, 'compare_excl') ?? $compareAt) > data_get($priceView, 'current_excl'))
                    <span class="price__compare text-sm text-body font-normal line-through">{{ formatCurrency(data_get($priceView, 'compare_excl') ?? $compareAt) }}</span>
                @endif
                <span class="price__current {{ $size === 'lg' ? 'text-3xl' : 'text-lg' }} text-headings">{{ formatCurrency(data_get($priceView, 'current_excl')) }}</span>
                <span class="price__tax-note text-xs font-normal text-body">@t('excl. VAT')</span>
            </span>
            <span class="price__secondary text-xs text-body">{{ formatCurrency(data_get($priceView, 'current_incl')) }} @t('incl. VAT')</span>
        </span>
    @else
        {{-- B2B net, or no tax: single figure. Label shown only when taxed. --}}
        <span class="price inline-flex items-baseline gap-2 font-semibold" data-amount="{{ data_get($priceView, 'current_excl') ?? $amount }}">
            @if((data_get($priceView, 'compare_excl') ?? $compareAt) && (data_get($priceView, 'compare_excl') ?? $compareAt) > (data_get($priceView, 'current_excl') ?? $amount))
                <span class="price__compare text-sm text-body font-normal line-through">{{ formatCurrency(data_get($priceView, 'compare_excl') ?? $compareAt) }}</span>
            @endif
            <span class="price__current {{ $size === 'lg' ? 'text-3xl' : 'text-lg' }} text-headings">{{ formatCurrency(data_get($priceView, 'current_excl') ?? $amount) }}</span>
            @if(data_get($priceView, 'has_tax'))
                <span class="price__tax-note text-xs font-normal text-body">@t('excl. VAT')</span>
            @endif
        </span>
    @endif
@else
    <a href="@routeUrl('store.login')" class="price price--login inline-flex items-center text-sm font-medium text-primary hover:text-primary-600 hover:no-underline">
        @t('Log in to see prices')
    </a>
@endif
