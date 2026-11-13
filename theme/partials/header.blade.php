<header class="storefront-header border-b border-border-subtle bg-surface-card" role="banner">
    {{-- Row 0 — dark contact banner: store contact details + language switcher --}}
    @section('header.banner')
        {{-- Always rendered so the light/dark theme toggle stays reachable even
             when the store has no contact details and only one locale. --}}
        <div class="storefront-header__banner bg-secondary text-secondary-content">
            <div class="mx-auto flex w-full max-w-desktop items-center justify-between gap-4 px-4 py-1.5">
                    <div class="storefront-header__contact flex min-w-0 items-center gap-3 text-sm text-secondary-content sm:gap-5">
                        @if(!empty($store['contact_phone']))
                            <a href="tel:{{ $store['contact_phone'] }}" class="flex min-w-0 items-center gap-1.5 text-secondary-content hover:no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 shrink-0" aria-hidden="true">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                                <span class="truncate">{{ $store['contact_phone'] }}</span>
                            </a>
                        @endif
                        @if(!empty($store['contact_email']))
                            <a href="mailto:{{ $store['contact_email'] }}" class="hidden min-w-0 items-center gap-1.5 text-secondary-content hover:no-underline sm:flex">
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
                                class="theme-toggle__to-dark inline-flex h-8 w-8 items-center justify-center rounded text-secondary-content/80 transition-colors hover:bg-secondary-content/10 hover:text-secondary-content"
                                aria-label="@t('Switch to dark mode')">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                </svg>
                            </button>
                            <button type="submit" name="scheme" value="light"
                                class="theme-toggle__to-light inline-flex h-8 w-8 items-center justify-center rounded text-secondary-content/80 transition-colors hover:bg-secondary-content/10 hover:text-secondary-content"
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
            <form class="storefront-header__search hidden min-w-0 flex-1 md:flex" method="get" action="@routeUrl('store.products')" role="search" data-live-search-form
            data-label-suggestions="@t('Search suggestions')" data-label-filters="@t('Filters')"
            data-label-empty="@t('No products found')" data-label-all="@t('Show all results')">
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
            @storefrontSlot('storefront.header.search')
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
        <form class="storefront-header__search-mobile mx-auto w-full max-w-desktop px-4 pb-3 md:hidden" method="get" action="@routeUrl('store.products')" role="search" data-live-search-form
            data-label-suggestions="@t('Search suggestions')" data-label-filters="@t('Filters')"
            data-label-empty="@t('No products found')" data-label-all="@t('Show all results')">
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

    {{-- Row 2 — category nav: list link or mega menu, from category_ui_type --}}
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

        // Header search. Typing opens a suggestion panel under the field: products
        // (image, name, price) and the filters that apply to the current matches.
        // The page content only changes when the search is confirmed: Enter, the
        // "show all" button, or a click on a suggestion. The panel reads the normal
        // products listing for the typed query, so it follows the same visibility
        // and price rules as the catalog and needs no extra endpoint.
        const SEARCH_DEBOUNCE_MS = 220;
        const SEARCH_MIN_CHARS = 2;
        const SEARCH_MAX_RESULTS = 8;
        const SEARCH_MAX_OPTIONS = 8;
        let searchPanelCount = 0;
        for (const form of document.querySelectorAll('[data-live-search-form]')) {
            const input = form.querySelector('[data-live-search-input]');
            const header = form.closest('header');
            if (!(input instanceof HTMLInputElement) || !header) {
                continue;
            }

            const labels = form.dataset;
            const initialQuery = input.value.trim();
            const filters = new URLSearchParams();
            let controller = null;
            let debounceTimer = 0;
            let isComposing = false;
            let shownKey = '';

            const panel = document.createElement('div');
            searchPanelCount += 1;
            panel.id = `search-panel-${searchPanelCount}`;
            panel.className = 'search-panel';
            panel.hidden = true;
            panel.setAttribute('role', 'dialog');
            panel.setAttribute('aria-label', labels.labelSuggestions || '');
            header.append(panel);
            if (window.getComputedStyle(header).position === 'static') {
                header.style.position = 'relative';
            }
            input.setAttribute('role', 'combobox');
            input.setAttribute('aria-haspopup', 'dialog');
            input.setAttribute('aria-controls', panel.id);
            input.setAttribute('aria-expanded', 'false');

            const element = (tag, className, text) => {
                const node = document.createElement(tag);
                if (className) node.className = className;
                if (text !== undefined) node.textContent = text;
                return node;
            };
            const clean = (value) => (value || '').replace(/\s+/g, ' ').trim();

            const place = () => {
                const top = input.getBoundingClientRect().bottom - header.getBoundingClientRect().top + 8;
                panel.style.top = `${Math.round(top)}px`;
            };
            const open = () => {
                place();
                panel.hidden = false;
                input.setAttribute('aria-expanded', 'true');
            };
            const close = () => {
                window.clearTimeout(debounceTimer);
                controller?.abort();
                panel.hidden = true;
                shownKey = '';
                input.setAttribute('aria-expanded', 'false');
            };

            const listingUrl = (query) => {
                const url = new URL(form.action, window.location.href);
                url.searchParams.set('q', query);
                for (const [name, value] of filters) {
                    url.searchParams.append(name, value);
                }
                return url;
            };

            // Read products and facets out of the listing markup.
            const readListing = (html) => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const products = [...doc.querySelectorAll('.product-card')].map((card) => {
                    const link = card.querySelector('.product-card__title a');
                    return {
                        href: link?.getAttribute('href') || '',
                        name: clean(link?.textContent),
                        sku: clean(card.querySelector('.product-card__sku')?.textContent),
                        image: card.querySelector('.product-card__image img')?.getAttribute('src') || '',
                        price: clean((card.querySelector('.price__current') ?? card.querySelector('.price'))?.textContent),
                    };
                }).filter((product) => product.href !== '' && product.name !== '');

                const groups = [];
                const seen = new Set();
                for (const section of doc.querySelectorAll('.catalog-filter')) {
                    const title = clean(section.querySelector('h2')?.textContent);
                    if (title === '' || seen.has(title)) continue;
                    const options = [];
                    for (const link of section.querySelectorAll('a[href]')) {
                        const parts = [...link.children].map((child) => clean(child.textContent));
                        options.push({ href: link.getAttribute('href'), label: parts[0] || clean(link.textContent), count: parts[1] || '' });
                    }
                    for (const label of section.querySelectorAll('label')) {
                        const field = label.querySelector('input[type="checkbox"], input[type="radio"]');
                        if (!field || !field.name) continue;
                        const count = clean(label.querySelector('[data-facet-count]')?.textContent);
                        const swatch = label.querySelector('[data-swatch], [style*="background-color"]');
                        let text = label.getAttribute('data-facet-value') || clean(label.textContent);
                        if (count !== '' && text.endsWith(count)) text = clean(text.slice(0, -count.length));
                        options.push({
                            name: field.name,
                            value: field.value,
                            multiple: field.type === 'checkbox',
                            checked: field.checked,
                            label: text || title,
                            count,
                            swatch: swatch instanceof HTMLElement ? swatch.style.backgroundColor : '',
                        });
                    }
                    if (options.length > 0) {
                        seen.add(title);
                        groups.push({ title, options });
                    }
                }
                return { products, groups, count: clean(doc.querySelector('.catalog-toolbar__count')?.textContent) };
            };

            const submitSearch = () => {
                window.clearTimeout(debounceTimer);
                form.requestSubmit();
            };

            const toggleFilter = (option) => {
                const kept = [...filters].filter(([name, value]) => !(name === option.name && (!option.multiple || value === option.value)));
                const wasOn = filters.getAll(option.name).includes(option.value);
                for (const name of [...filters.keys()]) filters.delete(name);
                for (const [name, value] of kept) filters.append(name, value);
                if (!wasOn) filters.append(option.name, option.value);
                input.focus();
                load();
            };

            const render = (query, listing) => {
                panel.replaceChildren();

                const head = element('div', 'search-panel__head');
                head.append(element('strong', 'search-panel__query', `„${query}“`), element('span', 'search-panel__count', listing.count));
                panel.append(head);

                const body = element('div', 'search-panel__body');
                if (listing.groups.length > 0) {
                    const aside = element('div', 'search-panel__facets');
                    aside.setAttribute('aria-label', labels.labelFilters || '');
                    for (const group of listing.groups) {
                        const block = element('div', 'search-panel__facet');
                        block.append(element('span', 'search-panel__facet-title', group.title));
                        const list = element('div', 'search-panel__options');
                        for (const option of group.options.slice(0, SEARCH_MAX_OPTIONS)) {
                            const control = option.href
                                ? element('a', 'search-panel__option')
                                : element('button', 'search-panel__option');
                            if (option.href) {
                                control.href = option.href;
                            } else {
                                control.type = 'button';
                                control.setAttribute('aria-pressed', option.checked ? 'true' : 'false');
                                control.addEventListener('click', () => toggleFilter(option));
                            }
                            if (option.swatch) {
                                const dot = element('span', 'search-panel__swatch');
                                dot.style.backgroundColor = option.swatch;
                                control.append(dot);
                            }
                            control.append(element('span', 'search-panel__option-label', option.label));
                            if (option.count) control.append(element('span', 'search-panel__option-count', option.count));
                            list.append(control);
                        }
                        block.append(list);
                        aside.append(block);
                    }
                    body.append(aside);
                }

                const main = element('div', 'search-panel__main');
                if (listing.products.length === 0) {
                    main.append(element('p', 'search-panel__empty', labels.labelEmpty || ''));
                } else {
                    const list = element('ul', 'search-panel__results');
                    for (const product of listing.products.slice(0, SEARCH_MAX_RESULTS)) {
                        const item = element('li');
                        const link = element('a', 'search-panel__item');
                        link.href = product.href;
                        const frame = element('span', 'search-panel__image');
                        if (product.image) {
                            const image = element('img');
                            image.src = product.image;
                            image.alt = '';
                            image.loading = 'lazy';
                            frame.append(image);
                        }
                        const text = element('span', 'search-panel__text');
                        text.append(element('span', 'search-panel__name', product.name));
                        if (product.sku) text.append(element('span', 'search-panel__sku', product.sku));
                        if (product.price) text.append(element('span', 'search-panel__price', product.price));
                        link.append(frame, text);
                        item.append(link);
                        list.append(item);
                    }
                    main.append(list);
                    const all = element('button', 'search-panel__all', labels.labelAll || '');
                    all.type = 'button';
                    all.addEventListener('click', submitSearch);
                    main.append(all);
                }
                body.append(main);
                panel.append(body);
            };

            const load = async () => {
                const query = input.value.trim();
                if (query.length < SEARCH_MIN_CHARS) {
                    close();
                    return;
                }
                const url = listingUrl(query);
                if (url.search === shownKey && !panel.hidden) {
                    return;
                }
                controller?.abort();
                controller = new AbortController();
                panel.classList.add('search-panel--loading');
                try {
                    const response = await fetch(url, {
                        signal: controller.signal,
                        credentials: 'same-origin',
                        headers: { Accept: 'text/html' },
                    });
                    if (!response.ok) throw new Error(String(response.status));
                    const listing = readListing(await response.text());
                    if (input.value.trim() !== query) return;
                    render(query, listing);
                    shownKey = url.search;
                    open();
                } catch (error) {
                    if (error?.name !== 'AbortError') close();
                } finally {
                    panel.classList.remove('search-panel--loading');
                }
            };

            const schedule = () => {
                if (isComposing) return;
                window.clearTimeout(debounceTimer);
                debounceTimer = window.setTimeout(load, SEARCH_DEBOUNCE_MS);
            };

            // Clear button: show when the field has a value. Clearing only reloads
            // the listing when a search is currently applied to the page.
            const clearBtn = form.querySelector('[data-live-search-clear]');
            const syncClear = () => clearBtn?.classList.toggle('hidden', input.value.trim() === '');
            clearBtn?.addEventListener('click', () => {
                input.value = '';
                for (const name of [...filters.keys()]) filters.delete(name);
                syncClear();
                close();
                if (initialQuery !== '') {
                    form.requestSubmit();
                } else {
                    input.focus();
                }
            });

            input.addEventListener('input', () => {
                syncClear();
                schedule();
            });
            input.addEventListener('focus', () => {
                if (panel.hidden && input.value.trim().length >= SEARCH_MIN_CHARS) schedule();
            });
            input.addEventListener('compositionstart', () => {
                isComposing = true;
                window.clearTimeout(debounceTimer);
            });
            input.addEventListener('compositionend', () => {
                isComposing = false;
                schedule();
            });

            // Confirmed search: carry the filters chosen in the panel into the listing.
            form.addEventListener('submit', () => {
                form.querySelectorAll('[data-search-filter]').forEach((field) => field.remove());
                for (const [name, value] of filters) {
                    const field = element('input');
                    field.type = 'hidden';
                    field.name = name;
                    field.value = value;
                    field.setAttribute('data-search-filter', '');
                    form.append(field);
                }
                close();
            });

            // Keyboard: arrow keys walk the suggestions, Escape closes the panel.
            const items = () => [...panel.querySelectorAll('.search-panel__item, .search-panel__all')];
            input.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !panel.hidden) {
                    event.preventDefault();
                    close();
                } else if (event.key === 'ArrowDown' && !panel.hidden) {
                    event.preventDefault();
                    items()[0]?.focus();
                }
            });
            panel.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    close();
                    input.focus();
                    return;
                }
                if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;
                const list = items();
                const index = list.indexOf(document.activeElement);
                if (index === -1) return;
                event.preventDefault();
                const next = index + (event.key === 'ArrowDown' ? 1 : -1);
                if (next < 0) {
                    input.focus();
                } else {
                    list[Math.min(next, list.length - 1)]?.focus();
                }
            });
            document.addEventListener('pointerdown', (event) => {
                if (panel.hidden || !(event.target instanceof Node)) return;
                if (!panel.contains(event.target) && !form.contains(event.target)) close();
            });
            window.addEventListener('resize', () => {
                if (!panel.hidden) place();
            });
        }
    </script>
    <style>[data-live-search-input]::-webkit-search-cancel-button{display:none}</style>
</header>
