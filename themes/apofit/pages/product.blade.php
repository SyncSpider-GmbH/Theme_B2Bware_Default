@extends('layouts.shop')

@section('title', data_get($product, 'seo.meta_title') ?: data_get($product, 'name') ?: t('Product'))

{{-- Owner-editable, full-width catalog hero — rendered above the breadcrumbs
     via the layout's @yield('hero'). Renders nothing until a banner is assigned. --}}
@section('hero')
    @storefrontSlot('hero')
@endsection

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')

    {{-- The whole product detail lives in a re-renderable section so the variant
         picker can swap it server-side (title, price, stock, gallery, table) via
         GET /{locale}/sections?sections=product-details&product=<id>&variant=<id>.
         `transition-opacity` lets the runtime dim it while the swap is in flight. --}}
    <div data-storefront-section="product-details" class="transition-opacity duration-150">
        @include('partials.product-details')
    </div>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
