{{--
    Quick Order — product-search combobox partial (JS-only enhancement).

    Mirrors the SelectInput / SelectRemote behaviour from the b2bware admin lib:
    • Leading search icon swaps to a spinner while a fetch is in-progress
    • Clear button (×) appears on the right whenever the input has text
    • Click the input while the dropdown is open → toggles it closed
    • Click outside the combobox (anywhere else on the page) → closes
    • Click the dropdown background (not a result row) → closes
    • Keyboard: ↑ ↓ navigate, Enter confirms, Escape / Tab closes
    • AbortController cancels in-flight requests when a new query starts
    • Initial results are fetched the moment the field is focused (empty query)
    • Progressive enhancement: the section is hidden until the module runs,
      so the CSV block remains the functional no-JS path.

    Communication with the host page:
      Dispatches a `qo:product-selected` CustomEvent (bubbles: true) with
        detail: { sku: string, name: string, qty: number }
      when the user picks a product. The host page's module listens for this
      event and merges the row into the shared review table — no coupling
      to this partial's internals required.

    Included by quick-order.blade.php:
        @include('partials.quick-order-search')
--}}
<style>
    @@keyframes qo-spin { to { transform: rotate(360deg); } }
    [data-qo-spinner] { animation: qo-spin .7s linear infinite; transform-origin: center; }
    /* Input border transition when focused-and-open */
    [data-qo-combobox] [data-qo-input]:focus { border-color: var(--color-primary, #4f46e5); }
</style>

<section
    class="quick-order-search bg-surface-card border border-border-subtle rounded-lg p-5"
    data-qo-search
    data-search-url="@routeUrl('store.account.quick-order.search')"
    hidden>

    <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Search products')</h2>
    <p class="text-xs text-body/70 m-0 mt-0.5 mb-4">
        @t('Start typing a SKU or product name, then pick a result to add it to your order review below.')
    </p>

    <div class="flex items-end gap-3">

        {{-- ── Combobox ──────────────────────────────────────────────────────── --}}
        <div class="relative flex-1 min-w-0" data-qo-combobox>

            {{-- Input row --}}
            <div class="relative flex items-center">

                {{-- Leading icon: search (idle) / spinner (loading) --}}
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-body/40"
                    data-qo-icon-search aria-hidden="true">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                </span>
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-primary"
                    data-qo-icon-loading aria-hidden="true" hidden>
                    <svg data-qo-spinner class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9"
                            stroke="currentColor" stroke-width="2.5"
                            stroke-dasharray="40 22" stroke-linecap="round"/>
                    </svg>
                </span>

                {{-- Text input (type="text" to suppress the browser's native × on search inputs;
                     our custom data-qo-clear button handles clearing instead) --}}
                <input
                    type="text"
                    autocomplete="off"
                    spellcheck="false"
                    role="combobox"
                    aria-haspopup="listbox"
                    aria-expanded="false"
                    aria-autocomplete="list"
                    aria-controls="qo-search-listbox"
                    placeholder="{{ t('Search by SKU or name…') }}"
                    data-qo-input
                    class="w-full rounded-lg border border-border-subtle bg-surface-card pl-9 pr-9 py-2 text-sm text-headings outline-none transition-colors">

                {{-- Clear button (visible only when input has text) --}}
                <button type="button"
                    aria-label="{{ t('Clear search') }}"
                    data-qo-clear
                    hidden
                    class="absolute inset-y-0 right-0 flex items-center pr-2 text-body/40 hover:text-body bg-transparent border-0 cursor-pointer transition-colors">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Dropdown listbox --}}
            <ul
                id="qo-search-listbox"
                role="listbox"
                aria-label="{{ t('Product search results') }}"
                data-qo-results
                class="absolute left-0 right-0 top-full z-30 mt-1 max-h-72 overflow-y-auto list-none m-0 p-1 bg-surface-card border border-border-subtle rounded-lg shadow-lg"
                hidden>
            </ul>
        </div>

        {{-- ── Quantity beside the combobox ──────────────────────────────────── --}}
        <label class="flex flex-col gap-1 text-xs font-medium text-body/70 shrink-0">
            <span>@t('Quantity')</span>
            <input type="number" min="1" step="1" value="1" inputmode="numeric"
                data-qo-qty
                class="w-20 rounded-lg border border-border-subtle bg-surface-card px-2 py-2 text-center text-sm font-semibold text-headings outline-none focus:border-primary">
        </label>

    </div>

    <script type="module">
        const section  = document.querySelector('[data-qo-search]');
        if (section) {

        // ── Reveal (JS-only; stays hidden with JS off) ──────────────────────────
        section.hidden = false;

        // ── DOM refs ────────────────────────────────────────────────────────────
        const combobox     = section.querySelector('[data-qo-combobox]');
        const input        = section.querySelector('[data-qo-input]');
        const results      = section.querySelector('[data-qo-results]');
        const clearBtn     = section.querySelector('[data-qo-clear]');
        const iconSearch   = section.querySelector('[data-qo-icon-search]');
        const iconLoading  = section.querySelector('[data-qo-icon-loading]');
        const qtyInput     = section.querySelector('[data-qo-qty]');
        const searchUrl    = section.dataset.searchUrl;

        // ── Storefront runtime globals ──────────────────────────────────────────
        const currencyCode = window.__STOREFRONT__?.currency ?? 'EUR';
        const localeTag    = document.documentElement.lang || undefined;

        // ── Constants ───────────────────────────────────────────────────────────
        const MIN_CHARS  = 2;   // chars before a non-empty query fires
        const DEBOUNCE   = 250; // ms

        // ── State ───────────────────────────────────────────────────────────────
        let debounceTimer = null;
        let controller    = null; // AbortController for the active fetch
        let isOpen        = false;
        let items         = [];   // current result set (for keyboard nav)
        let activeIndex   = -1;

        // ── Translated strings (baked in server-side) ───────────────────────────
        const i18n = {
            noResults:    @json(t('No matching products.')),
            loadingText:  @json(t('Searching…')),
        };

        // ── Money formatting ────────────────────────────────────────────────────
        const formatMoney = (value) => {
            const n = Number(value);
            if (!Number.isFinite(n)) return '';
            try {
                return new Intl.NumberFormat(localeTag, {
                    style: 'currency',
                    currency: currencyCode,
                }).format(n);
            } catch {
                return n.toFixed(2);
            }
        };

        const priceLabel = (item, canSee) => {
            if (!canSee || !item.price) return '';
            const amount = item.price.current_excl ?? item.price.current ?? item.price.current_incl;
            return amount != null ? formatMoney(amount) : '';
        };

        // ── Loading state ───────────────────────────────────────────────────────
        const setLoading = (on) => {
            iconSearch.hidden  = on;
            iconLoading.hidden = !on;
            input.setAttribute('aria-busy', on ? 'true' : 'false');
        };

        // ── Clear button sync ───────────────────────────────────────────────────
        const syncClearBtn = () => {
            clearBtn.hidden = input.value === '';
        };

        // ── Open / close ────────────────────────────────────────────────────────
        const openDropdown = () => {
            results.hidden = false;
            isOpen = true;
            input.setAttribute('aria-expanded', 'true');
        };

        const closeDropdown = () => {
            results.hidden = true;
            isOpen = false;
            activeIndex = -1;
            items = [];
            input.removeAttribute('aria-activedescendant');
            input.setAttribute('aria-expanded', 'false');
        };

        // ── Keyboard active item ────────────────────────────────────────────────
        const setActive = (index) => {
            const opts = [...results.querySelectorAll('[data-qo-opt]')];
            opts.forEach((el, i) => {
                const on = i === index;
                el.classList.toggle('bg-surface', on);
                el.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            activeIndex = index;
            const active = opts[index];
            if (active) {
                input.setAttribute('aria-activedescendant', active.id);
                active.scrollIntoView({ block: 'nearest' });
            }
        };

        // ── Status rows (loading / no-results) ──────────────────────────────────
        const buildStatusRow = (text) => {
            const li = document.createElement('li');
            li.setAttribute('role', 'presentation');
            li.className = 'px-3 py-2.5 text-sm text-body/60 select-none';
            li.textContent = text;
            return li;
        };

        // ── Dispatch: selected product → host page ──────────────────────────────
        const dispatchSelected = (item) => {
            const qty = Math.max(1, Number.parseInt(qtyInput?.value ?? '1', 10) || 1);
            section.dispatchEvent(new CustomEvent('qo:product-selected', {
                bubbles: true,
                detail: { sku: item.sku, name: item.name ?? item.sku, qty },
            }));
            closeDropdown();
            input.value = '';
            syncClearBtn();
            input.focus();
        };

        // ── Render results ──────────────────────────────────────────────────────
        const renderResults = (payload) => {
            const canSee = payload.can_see_prices !== false;
            items = Array.isArray(payload.results) ? payload.results : [];
            results.replaceChildren();

            if (items.length === 0) {
                results.appendChild(buildStatusRow(i18n.noResults));
                openDropdown();
                return;
            }

            items.forEach((item, index) => {
                const li = document.createElement('li');
                li.id = `qo-opt-${index}`;
                li.setAttribute('role', 'option');
                li.setAttribute('aria-selected', 'false');
                li.dataset.qoOpt = String(index);
                li.className =
                    'flex items-center justify-between gap-3 px-3 py-2 rounded-md cursor-pointer ' +
                    'hover:bg-surface-hover transition-colors select-none';

                // Left: name + SKU
                const info = document.createElement('span');
                info.className = 'flex flex-col min-w-0';

                const name = document.createElement('span');
                name.className = 'text-sm font-medium text-headings truncate';
                name.textContent = item.name ?? '';
                info.appendChild(name);

                const sku = document.createElement('span');
                sku.className = 'text-xs text-body/60 truncate font-mono';
                sku.textContent = item.sku ?? '';
                info.appendChild(sku);
                li.appendChild(info);

                // Right: price (when visible)
                const price = priceLabel(item, canSee);
                if (price) {
                    const priceEl = document.createElement('span');
                    priceEl.className = 'text-sm font-semibold text-headings whitespace-nowrap shrink-0';
                    priceEl.textContent = price;
                    li.appendChild(priceEl);
                }

                li.addEventListener('click', () => dispatchSelected(item));
                // Track the hovered item so keyboard nav stays in sync
                li.addEventListener('mousemove', () => { activeIndex = index; });
                results.appendChild(li);
            });

            openDropdown();
            activeIndex = -1;
        };

        // ── Fetch ───────────────────────────────────────────────────────────────
        const runSearch = async (query) => {
            // Cancel any previous in-flight request
            controller?.abort();
            controller = new AbortController();

            // Show spinner + "Searching…" row immediately
            setLoading(true);
            results.replaceChildren(buildStatusRow(i18n.loadingText));
            openDropdown();

            const url = new URL(searchUrl, window.location.origin);
            url.searchParams.set('q', query);

            try {
                const response = await fetch(url, {
                    signal: controller.signal,
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                setLoading(false);
                if (!response.ok) { closeDropdown(); return; }
                const payload = await response.json();
                renderResults(payload);
            } catch (err) {
                if (err?.name === 'AbortError') return; // cancelled — keep spinner from previous call
                setLoading(false);
                closeDropdown();
            }
        };

        // ── Debounce helper ─────────────────────────────────────────────────────
        const scheduleSearch = (query) => {
            if (debounceTimer) window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(() => runSearch(query), DEBOUNCE);
        };

        // ── Input: typing ───────────────────────────────────────────────────────
        input.addEventListener('input', () => {
            const q = input.value.trim();
            syncClearBtn();
            // Require MIN_CHARS before searching (except empty = browse list)
            if (q.length > 0 && q.length < MIN_CHARS) {
                closeDropdown();
                return;
            }
            scheduleSearch(q);
        });

        // ── Input: click — toggle dropdown when already open ────────────────────
        input.addEventListener('click', () => {
            if (isOpen) {
                closeDropdown();
            } else if (input.value.trim() === '') {
                // Show browse list on first click into an empty field
                scheduleSearch('');
            }
            // Non-empty + closed: input event already handles it
        });

        // ── Input: focus — prefetch on first focus ──────────────────────────────
        input.addEventListener('focus', () => {
            if (!isOpen && input.value.trim() === '') {
                scheduleSearch('');
            }
        });

        // ── Keyboard navigation ─────────────────────────────────────────────────
        input.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                event.preventDefault();
                closeDropdown();
                return;
            }
            if (event.key === 'Tab') {
                closeDropdown();
                return;
            }
            if (!isOpen || items.length === 0) return;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                setActive((activeIndex + 1) % items.length);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                setActive((activeIndex - 1 + items.length) % items.length);
            } else if (event.key === 'Enter' && activeIndex >= 0) {
                event.preventDefault();
                dispatchSelected(items[activeIndex]);
            }
        });

        // ── Clear button ────────────────────────────────────────────────────────
        clearBtn.addEventListener('click', () => {
            input.value = '';
            syncClearBtn();
            // Cancel any pending fetch
            controller?.abort();
            setLoading(false);
            closeDropdown();
            input.focus();
        });

        // ── Click outside: use pointerdown so it fires before focus events ──────
        document.addEventListener('pointerdown', (event) => {
            if (isOpen && !combobox.contains(event.target)) {
                closeDropdown();
            }
        }, { passive: true });

        // ── Click on the dropdown background (not a result row) → close ─────────
        // mousedown (not click) so we can preventDefault and keep input focus
        results.addEventListener('mousedown', (event) => {
            if (event.target === results) {
                event.preventDefault(); // prevent input losing focus
                closeDropdown();
            }
        });
        } // end if (section)
    </script>

</section>
