<aside class="mini-cart" aria-label="@t('Cart')">
    <a class="mini-cart__link inline-flex items-center gap-2 rounded px-2 py-2 text-headings hover:text-primary hover:no-underline" href="@routeUrl('store.cart')">
        <span class="relative inline-flex">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <span class="mini-cart__count absolute -right-2 -top-2 min-w-5 rounded-full bg-primary px-1 text-center text-xs leading-5 text-primary-content"
                data-storefront-cart-count="{{ $cartCount }}" data-storefront-cart-filled @if(($cartCount ?? 0) < 1) hidden @endif>{{ $cartCount }}</span>
        </span>
        <span class="hidden lg:inline">@t('Cart')</span>
    </a>
</aside>
