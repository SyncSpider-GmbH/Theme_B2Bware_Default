@foreach(($crossSellItems ?? []) as $cs)
    <li data-cross-sell-product>@include('components.product-card', [
        'product'   => $cs,
        'view'      => 'grid',
        'priceView' => data_get($crossSellItemPriceViews ?? [], (string) ($cs->id ?? '')),
        'redirect'  => '/' . $locale . '/products/' . ($productSlug ?? ''),
    ])</li>
@endforeach
@if(!empty($crossSellHasMore))
    <li hidden data-cross-sell-marker data-cross-sell-next="{{ (int) ($crossSellNextOffset ?? 0) }}"></li>
@endif
