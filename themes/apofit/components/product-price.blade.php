@props([
    'product' => null,
    'source' => null,
    'priceView' => null,
    'size' => 'base',
    'hideWhenConfigurableParentWithoutPrice' => false,
])

@if($product)
    @unless(
        $canSeePrices &&
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
        @include('components.price', [
            'priceView' => $priceView,
            'amount'    => data_get($source ?: $product, 'finalPrice.price')
                ?? data_get($source ?: $product, 'specialPrice.price')
                ?? data_get($source ?: $product, 'price.price')
                ?? 0,
            'compareAt' => data_get($source ?: $product, 'specialPrice.price') !== null
                ? data_get($source ?: $product, 'price.price')
                : null,
            'size'      => $size,
        ])
    @endunless
@endif
