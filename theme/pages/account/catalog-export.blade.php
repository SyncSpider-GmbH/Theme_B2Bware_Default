@extends('layouts.shop')

@section('title', t('Catalog export'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="page page--account page--account-catalog-export flex flex-col gap-5">
        <div class="flex flex-col md:flex-row gap-6">
            @include('partials.account-nav', ['active' => 'catalog-export'])

            <div class="flex-1 flex flex-col gap-5 min-w-0">
                <div class="bg-surface-card border border-border-subtle rounded-lg overflow-hidden" data-catalog-export>
                    <header class="px-5 py-4 border-b border-border-subtle">
                        <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Catalog export')</h2>
                        <p class="text-xs text-body/70 m-0 mt-0.5">@t('Pick the columns, filters and format, then generate the catalog file. You can download it to your device or copy a link to share it. Only storefront-visible products are included.')</p>
                    </header>

                    <div class="flex flex-col gap-6 p-5">
                        @storefrontError('catalog_export')

                        {{-- Stored export: shown once the customer has generated a file.
                             It is a frozen snapshot they can download or share by link.
                             Generating again replaces it, so there is only ever one. --}}
                        @if ($export)
                            <div class="flex flex-col gap-3 rounded-md border border-border-subtle  px-4 py-3"
                                data-export-card>
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <span
                                            class="flex items-center justify-center w-10 h-10 rounded-md bg-primary/10 text-primary shrink-0">
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"
                                                aria-hidden="true">
                                                <path d="M14 3v5h5" />
                                                <path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            </svg>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-headings m-0 truncate">
                                                {{ data_get($export, 'file_name') }}</p>
                                            <p class="text-xs text-body/60 m-0 mt-0.5">
                                                <span class="uppercase">{{ data_get($export, 'format') }}</span>
                                                · {{ data_get($export, 'file_size_human') }}
                                                @if (data_get($export, 'generated_at'))
                                                    · @t('Generated') @formatDate(data_get($export, 'generated_at'), 'L LT')
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <a href="@routeUrl('store.account.catalog-export.download')"
                                        data-download-btn
                                        class="inline-flex items-center gap-2 h-9 px-4 rounded-md bg-primary text-primary-content text-sm font-medium hover:bg-primary-600 transition-colors cursor-pointer whitespace-nowrap border-0">
                                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                            stroke-linejoin="round" aria-hidden="true">
                                            <path d="M12 3v12M8 11l4 4 4-4" />
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
                                        </svg>
                                        @t('Download')
                                    </a>
                                </div>

                                <label class="text-xs font-semibold text-headings"
                                    for="catalog-export-url">@t('Share link')</label>
                                <div class="flex items-stretch gap-2">
                                    <input id="catalog-export-url" type="text" readonly value="@routeUrl('store.feed', ['token' => data_get($export, 'token'), 'format' => data_get($export, 'format', 'csv')])"
                                        data-export-url
                                        class="flex-1 min-w-0 px-3 py-2 text-sm font-mono border border-surface-input-stroke rounded-md bg-surface-input text-body outline-none">
                                    <button type="button" data-export-copy
                                        class="inline-flex items-center gap-1.5 h-10 px-4 rounded-md border border-border-subtle bg-surface-card text-body text-sm font-medium hover:bg-surface-hover transition-colors cursor-pointer whitespace-nowrap">
                                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                            stroke-linejoin="round" aria-hidden="true">
                                            <rect x="9" y="9" width="11" height="11" rx="2" />
                                            <path d="M5 15V5a2 2 0 0 1 2-2h10" />
                                        </svg>
                                        <span data-export-copy-label>@t('Copy')</span>
                                    </button>
                                </div>
                                <p class="text-xs text-body/60 m-0">@t('Anyone with this link can download this file. Regenerate the link to revoke access, or delete the file to remove it.')</p>

                                <div class="flex flex-wrap items-center gap-3 pt-1">
                                    @storefrontForm('catalog-export-rotate', ['class' => 'contents'])
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 h-9 px-4 rounded-md border border-border-subtle bg-surface-card text-body text-sm font-medium hover:bg-surface-hover transition-colors cursor-pointer">
                                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                                stroke-linejoin="round" aria-hidden="true">
                                                <path d="M21 12a9 9 0 1 1-2.64-6.36" />
                                                <path d="M21 3v6h-6" />
                                            </svg>
                                            @t('Regenerate link')
                                        </button>
                                    @endstorefrontForm
                                    @storefrontForm('catalog-export-delete', ['class' => 'contents'])
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 h-9 px-4 rounded-md border border-error/40 bg-transparent text-error text-sm font-medium hover:bg-error/10 transition-colors cursor-pointer">
                                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                                stroke-linejoin="round" aria-hidden="true">
                                                <path
                                                    d="M4 7h16M10 11v6M14 11v6M5 7l1 13a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-13M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                            @t('Delete file')
                                        </button>
                                    @endstorefrontForm
                                </div>
                            </div>

                            <hr class="border-0 border-t border-border-subtle m-0">
                        @endif

                        {{-- Configuration: the same columns/filters/format drive the
                             generated file. Submitting generates (or regenerates) the
                             single stored export. --}}
                        @storefrontForm('catalog-export', [
                            'class' => 'catalog-export-form flex flex-col gap-6',
                            'data-storefront-loading' => 'off'
                        ])
                            @include('partials.catalog-export-fields', [
                                'exportFields' => $exportFields,
                                'exportFormats' => $exportFormats,
                                'defaultColumns' => $defaultColumns,
                                'selectedColumns' => old(
                                    'columns',
                                    data_get($export, 'columns', $defaultColumns)),
                                'selectedFormat' => old('format', data_get($export, 'format', 'csv')),
                                'selectedSearch' => old('q', data_get($export, 'filters.search', '')),
                                'selectedCategory' => old('category', data_get($export, 'filters.category', '')),
                                'selectedPriceMin' => old('price_min', data_get($export, 'filters.price_min', '')),
                                'selectedPriceMax' => old('price_max', data_get($export, 'filters.price_max', '')),
                                'selectedInStock' => (bool) old(
                                    'in_stock',
                                    data_get($export, 'filters.in_stock', false)),
                                'selectedSort' => old('sort', data_get($export, 'filters.sort', 'relevance')),
                            ])

                            {{-- Live preview: server-rendered initial table (no-JS
                                 friendly), refreshed via the Section Rendering route
                                 as the columns/filters change. --}}
                            <div data-catalog-preview data-endpoint="@routeUrl('store.sections')">
                                @storefrontSection('catalog-export-preview')
                            </div>

                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                <button type="submit"
                                    data-generate-btn
                                    class="inline-flex items-center gap-2 h-10 px-5 rounded-md bg-primary text-primary-content font-medium text-sm hover:bg-primary-600 transition-colors border-0 cursor-pointer">
                                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M13 2L4 14h7l-1 8 9-12h-7z" />
                                    </svg>
                                    @if ($export)
                                        @t('Regenerate export')
                                    @else
                                        @t('Generate export')
                                    @endif
                                </button>
                                <span class="text-xs text-body/70">@t('Generating replaces your current file and keeps a single export you can download or share.')</span>
                            </div>
                        @endstorefrontForm
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        const root = document.querySelector('[data-catalog-export]');
        if (root) {
            const copyButton = root.querySelector('[data-export-copy]');
            const urlField = root.querySelector('[data-export-url]');
            const label = root.querySelector('[data-export-copy-label]');

            copyButton?.addEventListener('click', async () => {
                if (!urlField) {
                    return;
                }
                const value = urlField.value;
                try {
                    if (navigator.clipboard?.writeText) {
                        await navigator.clipboard.writeText(value);
                    } else {
                        urlField.focus();
                        urlField.select();
                    }
                    if (label) {
                        const previous = label.textContent;
                        label.textContent = '@t('Copied')';
                        window.setTimeout(() => {
                            label.textContent = previous;
                        }, 1500);
                    }
                } catch {
                    urlField.focus();
                    urlField.select();
                }
            });

            urlField?.addEventListener('focus', () => urlField.select());
        }
    </script>

    <script type="module">
        // Replicates the platform markBusy() to show the .storefront-busy
        // spinner (defined in base.css) without depending on window.Storefront,
        // which is not yet defined when inline content scripts run before the
        // platform scripts asset loads in the layout.
        const markBusy = (el, busy = true) => {
            if (!el?.classList) return;
            if (busy) {
                if (!el.classList.contains('storefront-busy')) {
                    const color = getComputedStyle(el).color;
                    if (color) el.style.setProperty('--storefront-spinner-color', color);
                    el.classList.add('storefront-busy');
                }
            } else {
                el.classList.remove('storefront-busy');
                el.style.removeProperty('--storefront-spinner-color');
            }
        };

        const exportRoot = document.querySelector('[data-catalog-export]');

        // Download anchor: apply spinner on click and clear after 3 s — the page
        // does not navigate so the spinner would otherwise stick indefinitely.
        const downloadBtn = exportRoot?.querySelector('[data-download-btn]');
        downloadBtn?.addEventListener('click', () => {
            if (downloadBtn.classList.contains('storefront-busy')) return;
            markBusy(downloadBtn, true);
            window.setTimeout(() => markBusy(downloadBtn, false), 3000);
        });

        // Generate / Regenerate export: the form uses data-storefront-loading="off"
        // so the platform skips its built-in spinner — replicate it here.
        const generateBtn = exportRoot?.querySelector('[data-generate-btn]');
        generateBtn?.closest('form')?.addEventListener('submit', () => {
            markBusy(generateBtn, true);
            window.setTimeout(() => { generateBtn.disabled = true; }, 0);
        });
    </script>

    <script type="module">
        const previewMount = document.querySelector('[data-catalog-preview]');
        const form = document.querySelector('[data-storefront-form="catalog-export"]');
        if (previewMount && form) {
            const endpoint = previewMount.dataset.endpoint || '';
            const target = previewMount.querySelector('[data-storefront-section="catalog-export-preview"]');

            const buildUrl = () => {
                const url = new URL(endpoint, window.location.origin);
                url.searchParams.set('sections', 'catalog-export-preview');
                const data = new FormData(form);
                for (const key of ['q', 'category', 'price_min', 'price_max', 'sort']) {
                    const value = data.get(key);
                    if (value !== null && value !== '') {
                        url.searchParams.set(key, value);
                    }
                }
                if (data.get('in_stock')) {
                    url.searchParams.set('in_stock', '1');
                }
                for (const value of data.getAll('columns[]')) {
                    url.searchParams.append('columns[]', value);
                }
                return url;
            };

            let timer = null;
            let inFlight = null;

            const refresh = async () => {
                if (!endpoint || !target) {
                    return;
                }
                inFlight?.abort();
                inFlight = new AbortController();
                previewMount.setAttribute('aria-busy', 'true');
                try {
                    const response = await window.fetch(buildUrl(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        signal: inFlight.signal,
                    });
                    if (!response.ok) {
                        return;
                    }
                    const payload = await response.json();
                    const html = payload?.sections?.['catalog-export-preview'];
                    if (typeof html === 'string') {
                        target.innerHTML = html;
                    }
                } catch {
                    // Network error or aborted request — keep the last good preview.
                } finally {
                    previewMount.removeAttribute('aria-busy');
                }
            };

            const schedule = () => {
                window.clearTimeout(timer);
                timer = window.setTimeout(refresh, 350);
            };

            form.addEventListener('input', schedule);
            form.addEventListener('change', schedule);
            // Initial sync so the table reflects any pre-filled (saved) values.
            refresh();
        }
    </script>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
