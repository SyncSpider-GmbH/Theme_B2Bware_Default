@extends('layouts.shop')

@section('title', t('Shopping cart'))

@section('content')
    <div class="page page--cart flex flex-col gap-6">
        {{-- Owner-editable content region (top of page). --}}
        @storefrontSlot('content-top')
        {{-- Owner-editable cart promo (e.g. free-shipping threshold banner).
             Renders nothing until a widget is assigned to the slot. --}}
        @storefrontSlot('cart.promo')
        <header class="cart__header flex flex-col gap-1">
            <h1 class="cart__title font-primary text-2xl desktop:text-3xl font-semibold text-headings m-0 tracking-tight">@t('Shopping cart')</h1>
            {{-- Live item count: the runtime keeps [data-storefront-cart-count]
                 in sync with the cart on every AJAX mutation (mirrors the
                 mini-cart badge), and toggles visibility via
                 [data-storefront-cart-filled] when the cart empties. --}}
            <p class="cart__count text-sm text-body m-0" data-storefront-cart-filled @if(($cartCount ?? 0) < 1) hidden @endif>
                <span data-storefront-cart-count="{{ $cartCount }}">{{ $cartCount }}</span> {{ $cartCount === 1 ? t('item') : t('items') }}
            </p>
        </header>

        @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])
        @include('components.banner', ['type' => 'info', 'message' => $messages['info'] ?? null])

        {{--
            The cart body is composed of AJAX-refreshable sections (see
            ThemeSections). Each @storefrontSection emits a
            [data-storefront-section] wrapper the runtime swaps in place after a
            cart mutation, so quantity changes, removals and coupons update
            without a full reload. With JS disabled every form falls back to a
            normal POST → redirect, so the page still works.
        --}}
        @if($cartCount === 0)
            @storefrontSection('cart-line-items')
        @else
            <div class="cart__layout flex flex-col gap-6 desktop:flex-row desktop:items-start">
                {{-- Left: rewards + line items + actions --}}
                <div class="cart__main flex flex-col gap-4 min-w-0 desktop:flex-1">
                    {{-- Rewards progress is meaningless for an empty cart, so the
                         runtime hides it (with the sidebar below) when the cart
                         is emptied via AJAX — this keeps the empty state from
                         inheriting a stray gap and matches a full-reload cart. --}}
                    <div data-storefront-cart-filled>@storefrontSection('cart-rewards')</div>
                    @storefrontSection('cart-line-items')

                    {{-- Hidden by the runtime when the cart becomes empty via AJAX. --}}
                    <div class="cart__actions flex gap-3 flex-wrap items-center" data-storefront-cart-filled>
                        <a href="@routeUrl('store.products')" class="inline-flex items-center justify-center rounded-lg bg-transparent text-headings border border-border-subtle font-medium px-4 py-2 transition-colors hover:border-primary hover:text-primary hover:no-underline">@t('Continue shopping')</a>

                        @storefrontForm('cart-clear', ['class' => 'inline'])
                            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-transparent text-body border border-border-subtle font-medium px-4 py-2 transition-colors hover:border-primary hover:text-primary cursor-pointer">
                                @t('Clear cart')
                            </button>
                        @endstorefrontForm
                    </div>
                </div>

                {{-- Right: sticky sidebar (coupon + order summary + free-delivery
                     banner). Hidden by the runtime when the cart empties via
                     AJAX so the left column expands to the full page width — the
                     same single-column empty state a full reload renders. --}}
                <aside class="cart__sidebar flex flex-col gap-4 w-full desktop:w-96 desktop:sticky desktop:top-6" data-storefront-cart-filled>
                    @storefrontSection('cart-discounts')
                    @storefrontSection('cart-summary')
                </aside>
            </div>
        @endif
        {{-- Owner-editable content region (bottom of page). --}}
        @storefrontSlot('content-bottom')
    </div>
@endsection

