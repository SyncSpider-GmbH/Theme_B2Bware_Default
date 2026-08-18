@props(['inStock' => false, 'quantity' => null, 'low' => false])

{{--
    Availability badge with three states driven by the controller's
    `stockInfo`: in stock (success / green), low stock (warning / amber,
    shown with the remaining quantity) and out of stock (error / red).
    `low` only applies while the item is still in stock.
--}}
<span @class([
    'stock-badge inline-flex items-center gap-1.5 text-xs font-medium',
    'stock-badge--in text-success' => $inStock && !$low,
    'stock-badge--low text-warning' => $inStock && $low,
    'stock-badge--out text-error' => !$inStock,
])>
    <span @class([
        'stock-badge__dot inline-block w-2 h-2 rounded-full shrink-0',
        'bg-success' => $inStock && !$low,
        'bg-warning' => $inStock && $low,
        'bg-error' => !$inStock,
    ])></span>
    @if($inStock)
        @if($low)
            @if(!is_null($quantity))
                @t('Only') {{ $quantity }} @t('left')
            @else
                @t('Low stock')
            @endif
        @elseif(!is_null($quantity))
            {{ $quantity }} @t('in stock')
        @else
            @t('In stock')
        @endif
    @else
        @t('Out of stock')
    @endif
</span>
