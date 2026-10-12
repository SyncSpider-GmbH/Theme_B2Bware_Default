@props([
    'product' => null,
    'source' => null,
    'priceView' => null,
    'hideWhenConfigurableParentWithoutPrice' => false,
    'includeLineUnit' => false,
])

@if($product)
    @if($canSeePrices)
        data-analytics-item-price="{{
            (
                $hideWhenConfigurableParentWithoutPrice &&
                data_get($product, 'product_type') === 'configurable' &&
                (
                    (
                        data_get($priceView, 'current_excl')
                            ?? data_get($source ?: $product, 'finalPrice.price')
                            ?? data_get($source ?: $product, 'specialPrice.price')
                            ?? data_get($source ?: $product, 'price.price')
                    ) === null
                    || (
                        data_get($priceView, 'current_excl')
                            ?? data_get($source ?: $product, 'finalPrice.price')
                            ?? data_get($source ?: $product, 'specialPrice.price')
                            ?? data_get($source ?: $product, 'price.price')
                    ) === ''
                )
            )
                ? ''
                : (
                    data_get($priceView, 'current_excl')
                        ?? data_get($source ?: $product, 'finalPrice.price')
                        ?? data_get($source ?: $product, 'specialPrice.price')
                        ?? data_get($source ?: $product, 'price.price')
                        ?? ''
                )
        }}"
    @endif

    @if($includeLineUnit)
        data-line-unit="{{
            (
                ! $canSeePrices ||
                (
                    $hideWhenConfigurableParentWithoutPrice &&
                    data_get($product, 'product_type') === 'configurable' &&
                    (
                        (
                            data_get($priceView, 'current_excl')
                                ?? data_get($source ?: $product, 'finalPrice.price')
                                ?? data_get($source ?: $product, 'specialPrice.price')
                                ?? data_get($source ?: $product, 'price.price')
                        ) === null
                        || (
                            data_get($priceView, 'current_excl')
                                ?? data_get($source ?: $product, 'finalPrice.price')
                                ?? data_get($source ?: $product, 'specialPrice.price')
                                ?? data_get($source ?: $product, 'price.price')
                        ) === ''
                    )
                )
            )
                ? ''
                : (
                    data_get($priceView, 'current_excl')
                        ?? data_get($source ?: $product, 'finalPrice.price')
                        ?? data_get($source ?: $product, 'specialPrice.price')
                        ?? data_get($source ?: $product, 'price.price')
                        ?? ''
                )
        }}"
        {{-- Live-total inputs (docs/pricing.md): the struck baseline at qty 1 and
             the quantity tiers, so the list-view total mirrors the cart charge. --}}
        data-line-original-unit="{{ $canSeePrices ? (data_get($priceView, 'compare_excl') ?? '') : '' }}"
        data-tier-prices="{{ $canSeePrices
            ? collect(data_get($source ?: $product, 'finalTierPrices') ?? [])
                ->filter(fn ($t) => (int) data_get($t, 'min_quantity', 0) > 1)
                ->map(fn ($t) => ['min' => (int) data_get($t, 'min_quantity', 0), 'price' => (float) data_get($t, 'price', 0)])
                ->sortBy('min')
                ->values()
                ->toJson()
            : '[]' }}"
    @endif
@endif
