@props(['product' => null, 'variant' => null, 'quantity' => 1, 'label' => null, 'redirect' => null, 'block' => false, 'icon' => true])

@if($product && !empty($product->id))
    @if($canAddToCart)
        @if(data_get($product, 'product_type') === 'configurable')
            {{-- A configurable product is bought from its parent's product page,
                 where the variant picker / bulk-variant table lives. A listing
                 card can't add a specific variant, so it links to that page
                 instead of offering a direct add-to-cart (Nexus parity). --}}
            <a
                href="@routeUrl('store.product', ['identifier' => data_get($product, 'seo.slug') ?: $product->id])"
                @class([
                    'add-to-cart add-to-cart--options inline-flex items-center justify-center gap-2 rounded bg-primary text-primary-content font-medium px-4 py-2 transition-colors hover:bg-primary-600 hover:no-underline',
                    'w-full' => $block,
                    'min-w-40' => !$block,
                ])
            >
                @if($icon)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                        <line x1="4" y1="21" x2="4" y2="14"></line>
                        <line x1="4" y1="10" x2="4" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12" y2="3"></line>
                        <line x1="20" y1="21" x2="20" y2="16"></line>
                        <line x1="20" y1="12" x2="20" y2="3"></line>
                        <line x1="1" y1="14" x2="7" y2="14"></line>
                        <line x1="9" y1="8" x2="15" y2="8"></line>
                        <line x1="17" y1="16" x2="23" y2="16"></line>
                    </svg>
                @endif
                @t('Choose options')
            </a>
        @else
        @storefrontForm('cart-add', ['class' => $block ? 'add-to-cart flex w-full items-center gap-3' : 'add-to-cart flex items-center gap-3'])
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            @if($variant && !empty($variant->id))
                <input type="hidden" name="variant_id" value="{{ $variant->id }}">
            @endif
            <input type="hidden" name="quantity" value="{{ $quantity }}">
            @if($redirect)
                <input type="hidden" name="redirect" value="{{ $redirect }}">
            @endif
            <button
                type="submit"
                @class([
                    'add-to-cart__button inline-flex items-center justify-center gap-2 rounded bg-primary text-primary-content font-medium px-4 py-2 transition-colors hover:bg-primary-600 active:bg-primary-700 cursor-pointer',
                    'w-full' => $block,
                    'min-w-40' => !$block,
                ])
            >
                @if($icon)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                @endif
                {{ $label ?? t('Add to cart') }}
            </button>
        @endstorefrontForm
        @storefrontError('product_id')
        @endif
    @else
        {{-- Guests cannot add to cart when guest checkout is disabled
             ($canAddToCart, set globally from the `allow_guest_checkout`
             flag). Offer a sign-in path instead. --}}
        <a
            href="@routeUrl('store.login')"
            class="add-to-cart add-to-cart--login inline-flex items-center justify-center rounded bg-primary text-primary-content font-medium px-4 py-2 min-w-40 transition-colors hover:bg-primary-600 hover:no-underline"
        >
            @t('Log in to purchase')
        </a>
    @endif
@endif
