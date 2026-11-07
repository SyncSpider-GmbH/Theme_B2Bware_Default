{{--
    Added-to-cart confirmation (optional UX — see docs/ajax-and-runtime.md §9.7). The
    platform emits `storefront:cart:added` with `event.detail.added` after an
    AJAX add; the inline module below fills this single native <dialog> and
    opens it (one instance for the whole shop layout — no per-card markup).
    Degrades to nothing with JS off (the add still works); a session-scoped
    "Don't show this again" suppresses it. Centered by the `dialog { margin:
    auto }` rule in assets/css/storefront.css.

    Custom themes may override this partial with a drawer, toast, or any other
    UX by shipping their own `partials/added-to-cart-modal.blade.php`, or omit
    the @include entirely to suppress the confirmation altogether.
--}}
<dialog
    id="added-to-cart-modal"
    class="added-to-cart-modal w-full bg-surface-card text-body rounded-lg shadow-xl p-0 border-0 backdrop:bg-backdrop/40"
    style="max-width:28rem"
    data-title-one="@t('Added to cart')"
    data-title-many="@t('Items added to cart')"
    data-qty-label="@t('qty')"
    data-placeholder="@themeAsset('img/placeholder.svg')"
    aria-label="@t('Added to cart')"
>
    <article class="flex flex-col gap-4 p-6">
        <header class="flex items-center justify-between gap-3">
            <h2 class="flex items-center gap-2 font-primary text-lg font-semibold text-headings m-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 text-primary" aria-hidden="true">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <span data-added-title>@t('Added to cart')</span>
            </h2>
            <button type="button" class="inline-flex h-8 w-8 items-center justify-center text-2xl leading-none text-body hover:text-headings cursor-pointer bg-transparent border-0" data-modal-close aria-label="@t('Close')">&times;</button>
        </header>

        <ul data-added-items class="flex flex-col gap-3 list-none m-0 p-0" style="max-height:20rem;overflow-y:auto"></ul>

        <label class="flex items-center gap-2 text-sm text-body cursor-pointer">
            <input type="checkbox" data-added-skip class="shrink-0">
            <span>@t("Don't show this again")</span>
        </label>

        <footer class="flex flex-wrap items-center justify-end gap-3">
            <button type="button" data-modal-close class="inline-flex items-center justify-center rounded-lg border border-border-subtle bg-transparent text-headings font-medium px-4 py-2 transition-colors hover:border-primary hover:text-primary cursor-pointer">@t('Continue shopping')</button>
            <a href="@routeUrl('store.cart')" data-storefront-loading-link class="inline-flex items-center justify-center rounded-lg bg-primary text-primary-content font-medium px-4 py-2 transition-colors hover:bg-primary-600 hover:no-underline">@t('View cart')</a>
        </footer>
    </article>
</dialog>

{{-- Fills + opens the confirmation from the platform `storefront:cart:added`
     event. Colocated with its markup (single source of truth) per the theme JS rules. --}}
<script type="module">
    const dialog = document.getElementById('added-to-cart-modal');
    if (dialog) {
        const SKIP_KEY = 'storefront:added-to-cart:skip';
        const titleEl = dialog.querySelector('[data-added-title]');
        const itemsEl = dialog.querySelector('[data-added-items]');
        const skipEl = dialog.querySelector('[data-added-skip]');
        const titleOne = dialog.dataset.titleOne || 'Added to cart';
        const titleMany = dialog.dataset.titleMany || 'Items added to cart';
        const qtyLabel = dialog.dataset.qtyLabel || '';
        const placeholder = dialog.dataset.placeholder || '';

        const isSkipped = () => {
            try {
                return sessionStorage.getItem(SKIP_KEY) === '1';
            } catch {
                return false;
            }
        };

        skipEl?.addEventListener('change', () => {
            try {
                sessionStorage.setItem(SKIP_KEY, skipEl.checked ? '1' : '0');
            } catch {
                // Storage unavailable (e.g. private mode) — non-fatal.
            }
        });

        const rowFor = (item) => {
            const li = document.createElement('li');
            li.className = 'flex items-center gap-3 rounded-lg border border-border-subtle p-2';

            const figure = document.createElement('span');
            figure.className = 'flex items-center justify-center shrink-0 overflow-hidden rounded bg-surface';
            figure.style.width = '3.5rem';
            figure.style.height = '3.5rem';
            const img = document.createElement('img');
            img.className = 'h-full w-full object-contain';
            img.loading = 'lazy';
            img.alt = item.name ?? '';
            img.src = item.image || placeholder;
            figure.appendChild(img);

            const meta = document.createElement('span');
            meta.className = 'flex-1 min-w-0';
            const name = document.createElement('span');
            name.className = 'block font-medium text-headings truncate';
            name.textContent = item.name ?? '';
            meta.appendChild(name);
            if (item.sku) {
                const sku = document.createElement('span');
                sku.className = 'block text-sm text-body truncate';
                sku.textContent = item.sku;
                meta.appendChild(sku);
            }

            const qty = document.createElement('span');
            qty.className = 'shrink-0 text-sm text-headings';
            const strong = document.createElement('strong');
            strong.textContent = String(item.quantity ?? 1);
            qty.append(strong, qtyLabel ? ' ' + qtyLabel : '');

            li.append(figure, meta, qty);
            return li;
        };

        document.addEventListener('storefront:cart:added', (event) => {
            const added = event.detail?.added;
            if (!Array.isArray(added) || added.length === 0 || isSkipped() || dialog.open) {
                return;
            }

            itemsEl.replaceChildren(...added.map(rowFor));

            const total = added.reduce((sum, item) => sum + (Number(item.quantity) || 1), 0);
            if (titleEl) {
                titleEl.textContent = total === 1 ? titleOne : titleMany;
            }
            if (skipEl) {
                skipEl.checked = false;
            }
            if (typeof dialog.showModal === 'function') {
                dialog.showModal();
            }
        });
    }
</script>
