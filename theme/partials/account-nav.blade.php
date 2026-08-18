{{--
    Account sidebar. Pass `active` to highlight the current section:
      @include('partials.account-nav', ['active' => 'dashboard'])
    Accepted values: dashboard, profile, orders, quick-order, addresses, api-keys,
    favorites, catalog-export.
    The Favorites row is hidden when the tenant disables favorites
    ($store['favorites_enabled'] defaults to on when the flag is absent).
    The Quick Order row shows only when the tenant enables it
    ($store['quick_order_enabled'], defaults off when the flag is absent).
    The Catalog export row is shown only when the tenant enables the
    catalog-export module ($store['catalog_export_enabled'] defaults to off).
--}}
<nav class="account__nav md:w-64 flex-shrink-0" aria-label="@t('Account')">
    <div class="flex flex-col p-2 bg-surface-card border border-border-subtle rounded-lg">
        <a href="@routeUrl('store.account.index')" @class([
            'account__nav-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm no-underline transition-colors',
            'bg-primary-subtle text-primary font-semibold' =>
                ($active ?? '') === 'dashboard',
            'text-headings font-medium hover:bg-surface-hover' =>
                ($active ?? '') !== 'dashboard',
        ])
            @if (($active ?? '') === 'dashboard') aria-current="page" @endif>
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
            </svg>
            @t('Dashboard')
        </a>

        <a href="@routeUrl('store.account.profile')" @class([
            'account__nav-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm no-underline transition-colors',
            'bg-primary-subtle text-primary font-semibold' => ($active ?? '') === 'profile',
            'text-headings font-medium hover:bg-surface-hover' =>
                ($active ?? '') !== 'profile',
        ])
            @if (($active ?? '') === 'profile') aria-current="page" @endif>
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="8" r="4" />
                <path d="M4 21c0-4 4-6 8-6s8 2 8 6" />
            </svg>
            @t('My Profile')
        </a>

        <a href="@routeUrl('store.account.orders')" @class([
            'account__nav-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm no-underline transition-colors',
            'bg-primary-subtle text-primary font-semibold' => ($active ?? '') === 'orders',
            'text-headings font-medium hover:bg-surface-hover' =>
                ($active ?? '') !== 'orders',
        ])
            @if (($active ?? '') === 'orders') aria-current="page" @endif>
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path
                    d="M9 3h6a1 1 0 0 1 1 1v1h2a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h2V4a1 1 0 0 1 1-1z" />
                <path d="M9 12h6M9 16h4" />
            </svg>
            @t('Orders')
        </a>

        @if(($store['quick_order_enabled'] ?? false))
            <a href="@routeUrl('store.account.quick-order.index')"
               @class([
                   'account__nav-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm no-underline transition-colors',
                   'bg-primary-subtle text-primary font-semibold' => ($active ?? '') === 'quick-order',
                   'text-headings font-medium hover:bg-surface-hover' => ($active ?? '') !== 'quick-order',
               ])
               @if(($active ?? '') === 'quick-order') aria-current="page" @endif>
                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z" />
                </svg>
                @t('Quick Order')
            </a>
        @endif

        <a href="@routeUrl('store.account.shipping-addresses')" @class([
            'account__nav-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm no-underline transition-colors',
            'bg-primary-subtle text-primary font-semibold' =>
                ($active ?? '') === 'addresses',
            'text-headings font-medium hover:bg-surface-hover' =>
                ($active ?? '') !== 'addresses',
        ])
            @if (($active ?? '') === 'addresses') aria-current="page" @endif>
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11z" />
                <circle cx="12" cy="10" r="2.5" />
            </svg>
            @t('Addresses')
        </a>

        <a href="@routeUrl('store.account.api-keys')" @class([
            'account__nav-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm no-underline transition-colors',
            'bg-primary-subtle text-primary font-semibold' =>
                ($active ?? '') === 'api-keys',
            'text-headings font-medium hover:bg-surface-hover' =>
                ($active ?? '') !== 'api-keys',
        ])
            @if (($active ?? '') === 'api-keys') aria-current="page" @endif>
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="7.5" cy="15.5" r="3.5" />
                <path d="M10.5 12.5L19 4m-4 4l2 2m1-5l2 2" />
            </svg>
            @t('API Keys')
        </a>

        @if ($store['favorites_enabled'] ?? true)
            <a href="@routeUrl('store.favorites')" @class([
                'account__nav-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm no-underline transition-colors',
                'bg-primary-subtle text-primary font-semibold' =>
                    ($active ?? '') === 'favorites',
                'text-headings font-medium hover:bg-surface-hover' =>
                    ($active ?? '') !== 'favorites',
            ])
                @if (($active ?? '') === 'favorites') aria-current="page" @endif>
                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 20s-7-4.5-7-9.5A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 7 3.5C19 15.5 12 20 12 20z" />
                </svg>
                @t('Favorites')
            </a>
        @endif

        @if ($store['catalog_export_enabled'] ?? false)
            <a href="@routeUrl('store.account.catalog-export')" @class([
                'account__nav-link flex items-center gap-3 px-3 py-2.5 rounded-md text-sm no-underline transition-colors',
                'bg-primary-subtle text-primary font-semibold' =>
                    ($active ?? '') === 'catalog-export',
                'text-headings font-medium hover:bg-surface-hover' =>
                    ($active ?? '') !== 'catalog-export',
            ])
                @if (($active ?? '') === 'catalog-export') aria-current="page" @endif>
                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 3v12M8 11l4 4 4-4" />
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                </svg>
                @t('Catalog export')
            </a>
        @endif

        <div class="my-2 h-px bg-border-subtle" role="separator"></div>

        @storefrontForm('logout', ['class' => 'block'])
            <button type="submit"
                class="account__nav-link account__nav-logout w-full flex items-center gap-3 px-3 py-2.5 rounded-md text-sm font-medium text-error hover:bg-error/10 bg-transparent border-0 cursor-pointer transition-colors">
                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 17l5-5-5-5M20 12H9M9 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h3" />
                </svg>
                @t('Sign out')
            </button>
        @endstorefrontForm
    </div>
</nav>
