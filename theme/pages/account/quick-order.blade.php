@extends('layouts.shop')

@section('title', t('Quick Order'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    {{--
        Quick Order (My Account → Quick Order). Two ways to fill one review
        table, then confirm it into the cart in a single step:
          1. Typeahead search (SKU / name) — a progressive enhancement wired by
             the inline module below. It calls store.account.quick-order.search
             for JSON results; picking one appends it to the review table.
          2. CSV bulk upload — download the template and upload a `SKU,Quantity`
             file. With JS on the upload is posted as JSON and its rows are
             merged into the same review table; with JS off it posts a full page
             and the server seeds that table (works with JavaScript disabled).
        Both feed one editable review table (adjust quantities, remove lines);
        the confirm button posts sku[]/quantity[] to store.account.quick-order.add.
        The page renders only when the store enables Quick Order; the nav link
        mirrors that flag via $store['quick_order_enabled'].
    --}}
    <div class="page page--account page--account-quick-order flex flex-col gap-5">
        <div class="flex flex-col md:flex-row gap-6">
            @include('partials.account-nav', ['active' => 'quick-order'])

            <div class="flex-1 flex flex-col gap-6 min-w-0">
                @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])
                @include('components.banner', ['type' => 'info', 'message' => $messages['info'] ?? null])
                @include('components.banner', ['type' => 'error', 'message' => $messages['error'] ?? null])

                <header class="flex flex-col gap-1">
                    <h1 class="font-primary text-xl font-semibold text-headings m-0">@t('Quick Order')</h1>
                    <p class="text-sm text-body/70 m-0">@t('Add products to your cart fast — search by SKU or name, or upload a CSV file.')</p>
                </header>

                {{-- 1. Typeahead product search (JS-only; the partial reveals itself when its module runs) --}}
                @include('partials.quick-order-search')

                {{-- 2. CSV bulk upload. With JS on the upload is intercepted and
                     its rows are merged into the shared review table below; with
                     JS off it posts a full page and the server seeds that same
                     table. Either way there is a single review table. --}}
                <section
                    class="quick-order-csv bg-surface-card border border-border-subtle rounded-lg p-5 flex flex-col gap-4">
                    <div class="flex flex-col gap-1">
                        <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Upload a CSV file')</h2>
                        <p class="text-xs text-body/70 m-0">
                            @t('Upload a comma-separated file with a SKU column and a Quantity column.')
                            @t('Up to %d rows per file.', $maxRows)
                        </p>
                    </div>

                    <div>
                        <a href="{{ $templateUrl }}"
                            class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:text-primary-600 no-underline">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14" />
                            </svg>
                            @t('Download CSV template')
                        </a>
                    </div>

                    @storefrontForm('quick-order-upload', [
                        'enctype' => 'multipart/form-data',
                        'data-qo-upload-form' => true,
                        'class' => 'flex flex-col sm:flex-row gap-3 sm:items-center'
                    ])
                        {{--
                            The file input is visually hidden — the styled
                            <label> IS the "Upload CSV file" button (clicking it
                            opens the file picker). The inline module below
                            auto-submits the form the moment a file is chosen,
                            so there is no separate submit button in the JS
                            flow. The no-JS submit button below is tagged
                            data-storefront-no-js and hidden by the runtime when
                            JS runs, so no-JS users still get a working
                            "Upload & review" control.
                        --}}
                        <label
                            data-qo-upload-btn
                            class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 font-medium bg-primary text-primary-content hover:bg-primary-600 cursor-pointer whitespace-nowrap transition-opacity">
                            {{-- Upload icon: visible when idle --}}
                            <span data-qo-upload-icon aria-hidden="true">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 16V4m0 0l-4 4m4-4l4 4M4 20h16" />
                                </svg>
                            </span>
                            {{-- Spinner: visible while the upload fetch is in progress.
                                 Uses the same @keyframes qo-spin defined in the
                                 quick-order-search partial (same page render). --}}
                            <span data-qo-upload-spinner aria-hidden="true" hidden>
                                <svg data-qo-spinner class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2.5"
                                        stroke-dasharray="40 22" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <span data-qo-upload-label>@t('Upload CSV file')</span>
                            <input type="file" name="file" accept=".csv,text/csv" required data-qo-upload-input
                                class="sr-only">
                        </label>
                        @storefrontError('file')
                        <button type="submit" data-storefront-no-js
                            class="btn btn--secondary inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 font-medium bg-transparent text-headings border border-border-subtle hover:border-primary hover:text-primary cursor-pointer whitespace-nowrap">
                            @t('Upload & review')
                        </button>
                    @endstorefrontForm
                </section>

                {{-- 3. Order review — the single table that both the typeahead search
                     and the CSV upload feed. Rows are editable (quantity) and
                     removable; the confirm button posts sku[]/quantity[] to the
                     shared add endpoint. With JS off the server seeds the valid
                     CSV rows here so the no-JS path still works (per-row remove
                     is a JS enhancement; quantities are still editable). --}}
                <section
                    class="quick-order-review bg-surface-card border border-border-subtle rounded-lg p-5 flex flex-col gap-4"
                    data-quick-order-review
                    {{ isset($review) && (int) data_get($review, 'valid_count') > 0 ? '' : 'hidden' }}>
                    <div class="flex flex-col gap-1">
                        <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Order review')</h2>
                        <p class="text-xs text-body/70 m-0">@t('Review the products from your search and CSV file, adjust quantities or remove lines, then add them all to your cart.')</p>
                    </div>

                    {{-- Over-limit / could-not-match notices. Server-rendered for the
                         no-JS path; the inline module re-renders these after an AJAX
                         upload. --}}
                    <div class="flex flex-col gap-2" data-qo-review-notices>
                        @isset($review)
                            @if (data_get($review, 'over_limit'))
                                <div class="p-3 rounded-md bg-surface-error-subtle text-surface-error-subtle-content text-sm">
                                    @t('Your file has %d rows and the limit is %d — please split it into smaller files.', (int) data_get($review, 'total_rows'), (int) data_get($review, 'max_rows'))
                                </div>
                            @elseif((int) data_get($review, 'invalid_count') > 0)
                                <div
                                    class="p-3 rounded-md bg-surface-error-subtle text-surface-error-subtle-content text-sm flex flex-col gap-1">
                                    <span
                                        class="font-medium">@t('%d row(s) from your file could not be added:', (int) data_get($review, 'invalid_count'))</span>
                                    <ul class="list-disc list-inside m-0">
                                        @foreach (data_get($review, 'rows', []) as $row)
                                            @if (data_get($row, 'status') !== 'ok')
                                                <li>@t('Line %d', (int) data_get($row, 'line')){{ data_get($row, 'sku') ? ' · ' . data_get($row, 'sku') : '' }}
                                                    —
                                                    @switch(data_get($row, 'status'))
                                                        @case('missing_sku')
                                                            @t('Missing SKU')
                                                        @break

                                                        @case('invalid_quantity')
                                                            @t('Invalid quantity')
                                                        @break

                                                        @case('not_found')
                                                            @t('Not found')
                                                        @break

                                                        @case('out_of_stock')
                                                            @t('Out of stock')
                                                        @break

                                                        @default
                                                            @t('Skipped')
                                                    @endswitch
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endisset
                    </div>

                    @storefrontForm('quick-order-add', ['data-qo-review-form' => true, 'class' => 'flex flex-col gap-4'])
                        <div class="overflow-x-auto border border-border-subtle rounded-lg">
                            <table class="quick-order-review__table w-full text-sm text-left border-collapse">
                                <thead>
                                    <tr class="text-xs uppercase tracking-wide text-body/70 border-b border-border-subtle">
                                        <th class="px-4 py-2.5 font-medium">@t('SKU')</th>
                                        <th class="px-4 py-2.5 font-medium">@t('Product')</th>
                                        <th class="px-4 py-2.5 font-medium text-right">@t('Quantity')</th>
                                        <th class="px-4 py-2.5 font-medium text-right"><span
                                                class="sr-only">@t('Remove')</span></th>
                                    </tr>
                                </thead>
                                <tbody data-qo-review-body>
                                    @isset($review)
                                        @foreach (data_get($review, 'rows', []) as $row)
                                            @if (data_get($row, 'status') === 'ok')
                                                <tr class="border-b border-border-subtle last:border-0" data-qo-review-row
                                                    data-sku="{{ data_get($row, 'sku') }}">
                                                    <td class="px-4 py-2.5 font-mono text-xs text-body/80 align-middle">
                                                        {{ data_get($row, 'sku') }}</td>
                                                    <td class="px-4 py-2.5 text-headings align-middle">
                                                        {{ data_get($row, 'name') ?: data_get($row, 'sku') }}</td>
                                                    <td class="px-4 py-2.5 text-right align-middle">
                                                        <input type="hidden" name="sku[]" value="{{ data_get($row, 'sku') }}">
                                                        <input type="number" name="quantity[]" min="1" step="1"
                                                            inputmode="numeric"
                                                            value="{{ max(1, (int) data_get($row, 'quantity')) }}" data-qo-row-qty
                                                            class="w-20 rounded-lg border border-border-subtle bg-surface-card px-2 py-1.5 text-center text-sm font-semibold text-headings outline-none focus:border-primary">
                                                    </td>
                                                    <td class="px-4 py-2.5 text-right align-middle">
                                                        <button type="button" data-qo-row-remove aria-label="{{ t('Remove') }}"
                                                            class="text-body/60 hover:text-error bg-transparent border-0 p-1 leading-none text-lg cursor-pointer">&times;</button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endisset
                                </tbody>
                            </table>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <button type="button" data-qo-review-clear
                                class="text-sm font-medium text-body/70 hover:text-error bg-transparent border-0 p-0 cursor-pointer">@t('Clear all')</button>
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 font-medium bg-primary text-primary-content hover:bg-primary-600 cursor-pointer">
                                <span data-qo-review-submit-label>
                                    @isset($review)
                                        @t('Add %d products to cart', (int) data_get($review, 'valid_count'))@else@t('Add to cart')
                                    @endisset
                                </span>
                            </button>
                        </div>
                    @endstorefrontForm

                    <p class="text-xs text-body/60 m-0" data-qo-review-empty
                        {{ isset($review) && (int) data_get($review, 'valid_count') > 0 ? 'hidden' : '' }}>
                        @t('No products yet — search above or upload a CSV file to build your order.')</p>
                </section>

                {{-- 4. Suggested products (cross-sells seeded from the current cart). --}}
                @if (isset($suggestions) && $suggestions->isNotEmpty())
                    <section class="quick-order-suggestions flex flex-col gap-3">
                        <h2 class="font-primary text-base font-semibold text-headings m-0">@t('You might also need')</h2>
                        <ul class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 list-none m-0 p-0">
                            @foreach ($suggestions as $product)
                                <li>@include('components.product-card', [
                                    'product' => $product,
                                    'view' => 'grid',
                                    'redirect' => '/' . $locale . '/account/quick-order',
                                ])</li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </div>
        </div>
    </div>

    <script type="module">
        // ── Review table refs ──────────────────────────────────────────────────
        const reviewSection     = document.querySelector('[data-quick-order-review]');
        const reviewBody        = reviewSection?.querySelector('[data-qo-review-body]');
        const reviewSubmitLabel = reviewSection?.querySelector('[data-qo-review-submit-label]');
        const reviewClear       = reviewSection?.querySelector('[data-qo-review-clear]');
        const reviewNotices     = reviewSection?.querySelector('[data-qo-review-notices]');
        const reviewEmpty       = reviewSection?.querySelector('[data-qo-review-empty]');
        const uploadForm        = document.querySelector('[data-qo-upload-form]');
        const uploadInput       = uploadForm?.querySelector('[data-qo-upload-input]');
        const uploadBtn         = uploadForm?.querySelector('[data-qo-upload-btn]');
        const uploadBtnIcon     = uploadBtn?.querySelector('[data-qo-upload-icon]');
        const uploadBtnSpinner  = uploadBtn?.querySelector('[data-qo-upload-spinner]');
        const uploadBtnLabel    = uploadBtn?.querySelector('[data-qo-upload-label]');
        const uploadingText     = @json(t('Uploading…'));
        const uploadIdleText    = @json(t('Upload CSV file'));

        // ── Translated strings (baked in server-side) ──────────────────────────
        const addLabelTemplate  = @json(t('Add %d products to cart'));
        const removeLabel       = @json(t('Remove'));
        const statusLabels = {
            missing_sku:      @json(t('Missing SKU')),
            invalid_quantity: @json(t('Invalid quantity')),
            not_found:        @json(t('Not found')),
            out_of_stock:     @json(t('Out of stock')),
            skipped:          @json(t('Skipped')),
        };
        const invalidHeading    = @json(t('%d row(s) from your file could not be added:'));
        const lineLabel         = @json(t('Line %d'));
        const overLimitTemplate = @json(t('Your file has %d rows and the limit is %d — please split it into smaller files.'));
        const uploadFailed      = @json(t('We could not read that file — please check the format and try again.'));

        // ── Review table helpers ───────────────────────────────────────────────
        const findReviewRow = (sku) => {
            if (!reviewBody) return null;
            const key = String(sku).toLowerCase();
            return [...reviewBody.querySelectorAll('[data-qo-review-row]')]
                .find((row) => (row.dataset.sku || '').toLowerCase() === key) ?? null;
        };

        const refreshReview = () => {
            if (!reviewSection || !reviewBody) return;
            const count = reviewBody.querySelectorAll('[data-qo-review-row]').length;
            if (reviewSubmitLabel) {
                reviewSubmitLabel.textContent = addLabelTemplate.replace('%d', String(count));
            }
            if (reviewEmpty) reviewEmpty.hidden = count > 0;
            const hasNotices = !!(reviewNotices && reviewNotices.childElementCount > 0);
            reviewSection.hidden = count === 0 && !hasNotices;
        };

        const buildReviewRow = (sku, name, qty) => {
            const row = document.createElement('tr');
            row.dataset.qoReviewRow = '';
            row.dataset.sku = sku;
            row.className = 'border-b border-border-subtle last:border-0';

            const skuCell = document.createElement('td');
            skuCell.className = 'px-4 py-2.5 font-mono text-xs text-body/80 align-middle';
            skuCell.textContent = sku;

            const nameCell = document.createElement('td');
            nameCell.className = 'px-4 py-2.5 text-headings align-middle';
            nameCell.textContent = name || sku;

            const qtyCell = document.createElement('td');
            qtyCell.className = 'px-4 py-2.5 text-right align-middle';
            const skuField = document.createElement('input');
            skuField.type = 'hidden';
            skuField.name = 'sku[]';
            skuField.value = sku;
            const qtyField = document.createElement('input');
            qtyField.type = 'number';
            qtyField.name = 'quantity[]';
            qtyField.min = '1';
            qtyField.step = '1';
            qtyField.inputMode = 'numeric';
            qtyField.value = String(qty);
            qtyField.dataset.qoRowQty = '';
            qtyField.className =
                'w-20 rounded-lg border border-border-subtle bg-surface-card px-2 py-1.5 text-center text-sm font-semibold text-headings outline-none focus:border-primary';
            qtyCell.append(skuField, qtyField);

            const removeCell = document.createElement('td');
            removeCell.className = 'px-4 py-2.5 text-right align-middle';
            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.dataset.qoRowRemove = '';
            removeButton.setAttribute('aria-label', removeLabel);
            removeButton.className =
                'text-body/60 hover:text-error bg-transparent border-0 p-1 leading-none text-lg cursor-pointer';
            removeButton.textContent = '×';
            removeCell.appendChild(removeButton);

            row.append(skuCell, nameCell, qtyCell, removeCell);
            return row;
        };

        // Add a product row to the review table, merging by SKU: an existing
        // line has its quantity bumped; otherwise a new row is appended.
        const mergeRow = (sku, name, qty) => {
            if (!reviewBody || !sku) return;
            const quantity = Math.max(1, Number.parseInt(qty, 10) || 1);
            const existing = findReviewRow(sku);
            if (existing) {
                const field = existing.querySelector('[data-qo-row-qty]');
                const current = Math.max(0, Number.parseInt(field?.value ?? '0', 10) || 0);
                if (field) field.value = String(current + quantity);
            } else {
                reviewBody.appendChild(buildReviewRow(sku, name, quantity));
            }
        };

        // ── Product selected: event from the search partial ────────────────────
        // The quick-order-search partial dispatches `qo:product-selected`
        // (bubbles:true) so we can stay decoupled from its internals.
        document.addEventListener('qo:product-selected', (event) => {
            const { sku, name, qty } = event.detail;
            if (!sku) return;
            mergeRow(sku, name ?? sku, qty ?? 1);
            refreshReview();
        });

        // ── Review body: remove a row ──────────────────────────────────────────
        reviewBody?.addEventListener('click', (event) => {
            const remove = event.target.closest('[data-qo-row-remove]');
            if (!remove) return;
            remove.closest('[data-qo-review-row]')?.remove();
            refreshReview();
        });

        // ── Clear all ──────────────────────────────────────────────────────────
        reviewClear?.addEventListener('click', () => {
            reviewBody?.replaceChildren();
            if (reviewNotices) reviewNotices.replaceChildren();
            refreshReview();
        });

        // ── CSV upload (AJAX intercept; falls back to full-page POST without JS) ─

        // Show/hide the upload button's spinner and disable interaction while
        // the fetch is in-progress so users know something is happening and
        // can't trigger a second upload before the first completes.
        const setUploadLoading = (on) => {
            if (!uploadBtn) return;
            if (uploadBtnIcon)    uploadBtnIcon.hidden    = on;
            if (uploadBtnSpinner) uploadBtnSpinner.hidden = !on;
            if (uploadBtnLabel)   uploadBtnLabel.textContent = on ? uploadingText : uploadIdleText;
            uploadBtn.classList.toggle('opacity-75', on);
            uploadBtn.classList.toggle('pointer-events-none', on);
            uploadBtn.setAttribute('aria-disabled', on ? 'true' : 'false');
            // Disable the file input so keyboard activation of the label
            // (Space / Enter) cannot open the file picker while uploading.
            if (uploadInput) uploadInput.disabled = on;
        };

        const noticeBox = () => {
            const box = document.createElement('div');
            box.className =
                'p-3 rounded-md bg-surface-error-subtle text-surface-error-subtle-content text-sm flex flex-col gap-1';
            return box;
        };

        const showUploadError = (msg) => {
            if (!reviewNotices) return;
            reviewNotices.replaceChildren();
            const box = noticeBox();
            box.textContent = msg;
            reviewNotices.appendChild(box);
        };

        const renderUploadNotices = (payload) => {
            if (!reviewNotices) return;
            reviewNotices.replaceChildren();

            if (payload.over_limit) {
                const box = noticeBox();
                box.textContent = overLimitTemplate
                    .replace('%d', String(payload.total_rows ?? 0))
                    .replace('%d', String(payload.max_rows ?? 0));
                reviewNotices.appendChild(box);
                return;
            }

            const invalid = Array.isArray(payload.invalid) ? payload.invalid : [];
            if (invalid.length === 0) return;

            const box = noticeBox();
            const heading = document.createElement('span');
            heading.className = 'font-medium';
            heading.textContent = invalidHeading.replace('%d', String(invalid.length));
            box.appendChild(heading);

            const list = document.createElement('ul');
            list.className = 'list-disc list-inside m-0';
            invalid.forEach((row) => {
                const li = document.createElement('li');
                const parts = [lineLabel.replace('%d', String(row.line ?? 0))];
                if (row.sku) parts.push(String(row.sku));
                const reason = statusLabels[row.status] ?? statusLabels.skipped;
                li.textContent = parts.join(' · ') + ' — ' + reason;
                list.appendChild(li);
            });
            box.appendChild(list);
            reviewNotices.appendChild(box);
        };

        const onUpload = async (event) => {
            event.preventDefault();
            if (!uploadForm) return;
            // Snapshot FormData BEFORE disabling the input (disabled fields are
            // excluded from FormData, so order matters).
            const body = new FormData(uploadForm);
            setUploadLoading(true);
            try {
                const response = await fetch(uploadForm.action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body,
                });
                const payload = await response.json().catch(() => null);
                if (!response.ok || !payload) {
                    showUploadError(
                        (payload && (payload.errors?.file?.[0] ?? payload.message)) ?? uploadFailed
                    );
                    refreshReview();
                    return;
                }
                renderUploadNotices(payload);
                (Array.isArray(payload.rows) ? payload.rows : []).forEach((row) => {
                    mergeRow(row.sku, row.name ?? row.sku, row.quantity);
                });
                refreshReview();
                if (uploadInput) uploadInput.value = '';
            } catch {
                showUploadError(uploadFailed);
                refreshReview();
            } finally {
                setUploadLoading(false);
            }
        };

        uploadForm?.addEventListener('submit', onUpload);

        // Auto-submit the moment a file is chosen (the styled <label> is the
        // only visible upload control in the JS flow).
        uploadInput?.addEventListener('change', () => {
            if (uploadInput.files && uploadInput.files.length > 0) {
                uploadForm?.requestSubmit();
            }
        });

        // Sync review table state for any rows the server seeded (no-JS CSV path).
        refreshReview();
    </script>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
