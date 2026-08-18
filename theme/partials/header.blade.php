<header class="storefront-header border-b border-border-subtle bg-surface-card" role="banner">
    {{-- Row 0 — dark contact banner: store contact details + language switcher --}}
    @section('header.banner')
        {{-- Always rendered so the light/dark theme toggle stays reachable even
             when the store has no contact details and only one locale. --}}
        <div class="storefront-header__banner bg-secondary-800 text-neutral-white">
            <div class="mx-auto flex w-full max-w-desktop items-center justify-between gap-4 px-4 py-1.5">
                    <div class="storefront-header__contact flex min-w-0 items-center gap-3 text-sm text-neutral-white sm:gap-5">
                        @if(!empty($store['contact_phone']))
                            <a href="tel:{{ $store['contact_phone'] }}" class="flex min-w-0 items-center gap-1.5 text-neutral-white hover:no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 shrink-0" aria-hidden="true">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <span class="truncate">{{ $store['contact_phone'] }}</span>
                            </a>
                        @endif
                        @if(!empty($store['contact_email']))
                            <a href="mailto:{{ $store['contact_email'] }}" class="hidden min-w-0 items-center gap-1.5 text-neutral-white hover:no-underline sm:flex">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 shrink-0" aria-hidden="true">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <span class="truncate">{{ $store['contact_email'] }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="storefront-header__banner-actions flex shrink-0 items-center gap-1">
                        {{-- Light/dark theme toggle. The no-JS form posts to the
                             colour-scheme endpoint and returns to this page; with
                             JS the inline module below flips the scheme live. The
                             visible button (sun/dark) follows the active scheme via
                             CSS, so it is correct even with JS off + OS preference. --}}
                        @storefrontForm('color-scheme', ['class' => 'theme-toggle inline-flex items-center', 'data-theme-toggle' => true])
                            <input type="hidden" name="redirect" value="{{ $currentPath ?? '/' }}">
                            <button type="submit" name="scheme" value="dark"
                                class="theme-toggle__to-dark inline-flex h-8 w-8 items-center justify-center rounded text-neutral-white/80 transition-colors hover:bg-neutral-white/10 hover:text-neutral-white"
                                aria-label="@t('Switch to dark mode')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                </svg>
                            </button>
                            <button type="submit" name="scheme" value="light"
                                class="theme-toggle__to-light inline-flex h-8 w-8 items-center justify-center rounded text-neutral-white/80 transition-colors hover:bg-neutral-white/10 hover:text-neutral-white"
                                aria-label="@t('Switch to light mode')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                    <circle cx="12" cy="12" r="5"></circle>
                                    <line x1="12" y1="1" x2="12" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="23"></line>
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                    <line x1="1" y1="12" x2="3" y2="12"></line>
                                    <line x1="21" y1="12" x2="23" y2="12"></line>
                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                </svg>
                            </button>
                        @endstorefrontForm
                        @include('partials.locale-switcher')
                    </div>
                </div>
            </div>
    @show

    {{-- Row 1 — main bar: logo, search, and the account/cart/favorites cluster --}}
    <div class="storefront-header__bar mx-auto flex w-full max-w-desktop items-center gap-4 px-4 py-3 lg:gap-8">
        @section('header.brand')
            <a class="storefront-header__brand flex shrink-0 items-center hover:no-underline" href="@routeUrl('store.home')" aria-label="{{ $store['name'] }}">
                @if(!empty($store['logo']))
                    <img src="{{ $store['logo'] }}" alt="{{ $store['name'] }}" class="storefront-logo {{ !empty($store['logo_dark']) ? 'storefront-logo--light' : '' }} max-h-9 w-auto">
                    @if(!empty($store['logo_dark']))
                        <img src="{{ $store['logo_dark'] }}" alt="{{ $store['name'] }}" class="storefront-logo storefront-logo--dark max-h-9 w-auto">
                    @endif
                @else
                    <span class="font-primary text-lg font-semibold text-headings">{{ $store['name'] }}</span>
                @endif
            </a>
        @show

        @section('header.search')
            <form class="storefront-header__search hidden min-w-0 flex-1 md:flex" method="get" action="@routeUrl('store.products')" role="search" data-live-search-form>
                <div class="relative w-full">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-placeholder">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <input type="search" name="q" value="{{ $searchQuery ?? '' }}" autocomplete="off" data-live-search-input
                        placeholder="@t('Search products, SKU, brand…')"
                        aria-label="@t('Search products, SKU, brand…')"
                        class="w-full rounded border border-surface-input-stroke bg-surface-input py-2 pl-9 pr-8 text-body outline-none focus:border-primary">
                    <button type="button" data-live-search-clear
                        class="absolute inset-y-0 right-0 flex items-center pr-2 text-placeholder hover:text-body{{ ($searchQuery ?? '') !== '' ? '' : ' hidden' }}"
                        aria-label="@t('Clear search')">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </form>
        @show

        @section('header.actions')
            <div class="storefront-header__actions ml-auto flex shrink-0 items-center gap-2 lg:gap-3">
                @if($store['favorites_enabled'] ?? true)
                    <a href="@routeUrl('store.favorites')" class="storefront-header__action inline-flex items-center gap-2 rounded px-2 py-2 text-headings hover:text-primary hover:no-underline">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        <span class="hidden lg:inline">@t('Favorites')</span>
                    </a>
                @endif

                @storefrontSection('mini-cart')

                @if($isAuthenticated)
                    <a href="@routeUrl('store.account.index')" class="storefront-header__action inline-flex items-center gap-2 rounded px-2 py-2 text-headings hover:text-primary hover:no-underline">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span class="hidden lg:inline">@t('My account')</span>
                    </a>
                    {{-- Admin access: shown only for admin-operator customers whose
                         role grants `admin.access` (Iteration 6D). Links to the
                         separate b2bware back-office SPA. --}}
                    @if(count($canAccessAdmin) && count($adminUrl))
                        <a href="{{ $adminUrl }}" class="storefront-header__admin-access inline-flex items-center gap-2 rounded border border-primary px-3 py-2 font-medium text-primary hover:bg-primary hover:text-primary-content hover:no-underline">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="12" rx="2"></rect>
                                <path d="M8 20h8M12 16v4"></path>
                            </svg>
                            <span class="hidden lg:inline">@t('Admin access')</span>
                        </a>
                    @endif
                @else
                    <a href="@routeUrl('store.login')" class="storefront-header__signin inline-flex items-center gap-2 rounded bg-primary px-3 py-2 font-medium text-primary-content hover:bg-primary-600 hover:no-underline">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>@t('Log in')</span>
                    </a>
                    @if(empty($store['restrict_registration']))
                        <a href="@routeUrl('store.register')" class="storefront-header__action hidden items-center rounded px-2 py-2 text-headings hover:text-primary hover:no-underline lg:inline-flex">@t('Register')</a>
                    @endif
                @endif
            </div>
        @show
    </div>

    {{-- Row 1b — mobile search: full-width search on its own row on small screens --}}
    @section('header.search-mobile')
        <form class="storefront-header__search-mobile mx-auto w-full max-w-desktop px-4 pb-3 md:hidden" method="get" action="@routeUrl('store.products')" role="search" data-live-search-form>
            <div class="relative w-full">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input type="search" name="q" value="{{ $searchQuery ?? '' }}" autocomplete="off" data-live-search-input
                    placeholder="@t('Search products, SKU, brand…')"
                    aria-label="@t('Search products, SKU, brand…')"
                    class="w-full rounded border border-surface-input-stroke bg-surface-input py-2 pl-9 pr-8 text-body outline-none focus:border-primary">
                <button type="button" data-live-search-clear
                    class="absolute inset-y-0 right-0 flex items-center pr-2 text-placeholder hover:text-body{{ ($searchQuery ?? '') !== '' ? '' : ' hidden' }}"
                    aria-label="@t('Clear search')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </form>
    @show

    {{-- Row 2 — category bar: All Categories menu + top-level category links --}}
    @section('header.nav')
        @include('partials.nav')
    @show

    {{-- Progressive enhancement: with the JS runtime present, the theme toggle
         flips the scheme live (no reload) instead of a full-page POST. With JS
         off, the form submits normally and the server re-renders the page. --}}
    <script type="module">
        for (const form of document.querySelectorAll('[data-theme-toggle]')) {
            form.addEventListener('submit', (event) => {
                const api = window.Storefront;
                if (api && typeof api.toggleColorScheme === 'function') {
                    event.preventDefault();
                    api.toggleColorScheme();
                }
            });
        }

        const LIVE_SEARCH_DEBOUNCE_MS = 350;
        const normalizeSearchQuery = (value) => value.trim();
        const normalizePath = (value) => {
            try {
                const url = new URL(value, window.location.origin);
                const path = url.pathname.replace(/\/+$/, '');
                return path === '' ? '/' : path;
            } catch {
                return '';
            }
        };

        const currentPath = normalizePath(window.location.href);

        for (const form of document.querySelectorAll('[data-live-search-form]')) {
            const input = form.querySelector('[data-live-search-input]');
            if (!(input instanceof HTMLInputElement)) {
                continue;
            }

            // Clear button — show when input has a value, hide when empty.
            const clearBtn = form.querySelector('[data-live-search-clear]');
            if (clearBtn) {
                input.addEventListener('input', () => {
                    clearBtn.classList.toggle('hidden', input.value.trim() === '');
                });
                clearBtn.addEventListener('click', () => {
                    input.value = '';
                    clearBtn.classList.add('hidden');
                    form.requestSubmit();
                });
            }

            // Auto-search only on the products listing itself; elsewhere keep
            // the manual submit behavior to avoid unexpected page jumps.
            const actionPath = normalizePath(form.getAttribute('action') ?? '');
            if (currentPath === '' || actionPath === '' || currentPath !== actionPath) {
                continue;
            }

            let debounceTimer = 0;
            let isComposing = false;
            let lastSubmittedQuery = normalizeSearchQuery(input.value);

            const clearPendingSubmit = () => {
                if (debounceTimer > 0) {
                    window.clearTimeout(debounceTimer);
                    debounceTimer = 0;
                }
            };

            const submitLiveSearch = () => {
                const nextQuery = normalizeSearchQuery(input.value);
                if (nextQuery === lastSubmittedQuery) {
                    return;
                }
                lastSubmittedQuery = nextQuery;
                form.requestSubmit();
            };

            const scheduleLiveSearch = () => {
                if (isComposing) {
                    return;
                }
                clearPendingSubmit();
                debounceTimer = window.setTimeout(() => {
                    debounceTimer = 0;
                    submitLiveSearch();
                }, LIVE_SEARCH_DEBOUNCE_MS);
            };

            input.addEventListener('input', scheduleLiveSearch);
            input.addEventListener('search', scheduleLiveSearch);

            input.addEventListener('compositionstart', () => {
                isComposing = true;
                clearPendingSubmit();
            });

            input.addEventListener('compositionend', () => {
                isComposing = false;
                scheduleLiveSearch();
            });
        }
    </script>
    <style>[data-live-search-input]::-webkit-search-cancel-button{display:none}</style>
</header>
