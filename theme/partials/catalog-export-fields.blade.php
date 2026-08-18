{{--
    Shared catalog-export field set: column toggle switches (drag to reorder),
    a format select and an optional keyword filter. Reused by both the
    instant-download form and the public-feed configuration form on the
    catalog-export account page.

    Expected data:
      $exportFields    list<array{key:string,label:string}>  available columns
      $exportFormats   list<string>                          available formats
      $defaultColumns  list<string>                          columns pre-checked when $selectedColumns is absent
      $selectedColumns list<string>                          enabled columns (in order)
      $selectedFormat  string                                pre-selected format
      $selectedSearch  string                                pre-filled keyword (native `q`)
      $selectedCategory string|int|null                      pre-selected category id
      $selectedPriceMin string|float|null                    pre-filled min price
      $selectedPriceMax string|float|null                    pre-filled max price
      $selectedInStock  bool                                 in-stock-only toggle state
      $selectedSort     string                               pre-selected sort key
    Category options come from the global $rootCategories (StorefrontComposer).
--}}
<fieldset class="flex flex-col gap-3 border-0 p-0 m-0">
    <legend class="text-sm font-semibold text-headings p-0">@t('Columns')</legend>
    <p class="text-xs text-body/70 m-0">@t('Toggle the fields to include and drag to reorder them. If none are on, a default set is used.')</p>
    <div data-export-columns class="flex flex-col gap-1.5">
        @foreach (collect($selectedColumns ?? $defaultColumns ?? ['sku', 'name', 'price'])->intersect(collect($exportFields)->pluck('key'))->map(fn($key) => collect($exportFields)->firstWhere('key', $key))->concat(collect($exportFields)->whereNotIn('key', $selectedColumns ?? $defaultColumns ?? ['sku', 'name', 'price']))->values() as $field)
            <div data-col-row draggable="true"
                class="flex items-center justify-between gap-3 px-3 py-2 border border-border-subtle rounded-md bg-surface-card hover:bg-surface-hover transition-colors cursor-move select-none">
                <div class="flex items-center gap-2">
                    <span aria-hidden="true" class="shrink-0 text-body/70">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <circle cx="9" cy="5" r="1.4" />
                            <circle cx="15" cy="5" r="1.4" />
                            <circle cx="9" cy="12" r="1.4" />
                            <circle cx="15" cy="12" r="1.4" />
                            <circle cx="9" cy="19" r="1.4" />
                            <circle cx="15" cy="19" r="1.4" />
                        </svg>
                    </span>
                    <span class="text-sm text-body truncate">@t($field['label'])</span>
                </div>
                <label class="sf-switch">
                    <input type="checkbox" name="columns[]" value="{{ $field['key'] }}" @checked(in_array($field['key'], $selectedColumns ?? $defaultColumns ?? ['sku', 'name', 'price'], true))
                        class="sr-only">
                    <span class="sf-switch__track"></span>
                </label>

            </div>
        @endforeach
    </div>
</fieldset>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <label class="flex flex-col gap-1 text-sm text-body">
        <span class="font-semibold text-headings">@t('Format')</span>
        <select name="format"
            class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
            @foreach ($exportFormats as $format)
                <option value="{{ $format }}" @selected(($selectedFormat ?? 'csv') === $format)>{{ strtoupper($format) }}</option>
            @endforeach
        </select>
    </label>

    <label class="flex flex-col gap-1 text-sm text-body">
        <span class="font-semibold text-headings">@t('Sort')</span>
        <select name="sort"
            class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
            <option value="relevance" @selected(($selectedSort ?? 'relevance') === 'relevance')>@t('Relevance')</option>
            <option value="newest" @selected(($selectedSort ?? '') === 'newest')>@t('Newest')</option>
            <option value="oldest" @selected(($selectedSort ?? '') === 'oldest')>@t('Oldest')</option>
            <option value="name_asc" @selected(($selectedSort ?? '') === 'name_asc')>@t('Name (A–Z)')</option>
            <option value="name_desc" @selected(($selectedSort ?? '') === 'name_desc')>@t('Name (Z–A)')</option>
        </select>
    </label>
</div>

<fieldset class="flex flex-col gap-3 border-0 p-0 m-0">
    <legend class="text-sm font-semibold text-headings p-0">@t('Filters')</legend>
    <p class="text-xs text-body/70 m-0">@t('Narrow the catalog exactly like the product listing. Leave a field empty to skip it.')</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <label class="flex flex-col gap-1 text-sm text-body">
            <span class="font-semibold text-headings">@t('Search')</span>
            <input type="text" name="q" value="{{ $selectedSearch ?? '' }}" placeholder="@t('Filter by name or SKU (optional)')"
                class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
        </label>

        <label class="flex flex-col gap-1 text-sm text-body">
            <span class="font-semibold text-headings">@t('Category')</span>
            <select name="category"
                class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
                <option value="">@t('All categories')</option>
                @foreach ($rootCategories ?? [] as $rootCategory)
                    <option value="{{ $rootCategory->id }}" @selected((string) ($selectedCategory ?? '') === (string) $rootCategory->id)>{{ $rootCategory->name }}
                    </option>
                    @foreach ($rootCategory->children ?? [] as $childCategory)
                        <option value="{{ $childCategory->id }}" @selected((string) ($selectedCategory ?? '') === (string) $childCategory->id)>
                            &nbsp;&nbsp;{{ $childCategory->name }}</option>
                    @endforeach
                @endforeach
            </select>
        </label>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="flex flex-col gap-1 text-sm text-body">
            <span class="font-semibold text-headings">@t('Price range')</span>
            <div class="flex items-center gap-2">
                <input type="number" name="price_min" value="{{ $selectedPriceMin ?? '' }}" min="0"
                    step="0.01" placeholder="@t('Min')" aria-label="@t('Minimum price')"
                    class="w-full px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
                <span aria-hidden="true" class="text-body/70">–</span>
                <input type="number" name="price_max" value="{{ $selectedPriceMax ?? '' }}" min="0"
                    step="0.01" placeholder="@t('Max')" aria-label="@t('Maximum price')"
                    class="w-full px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
            </div>
        </div>

        <div class="flex flex-col gap-1 text-sm text-body">
            <span class="font-semibold text-headings">@t('Availability')</span>
            <label
                class="flex items-center gap-3 px-3 py-2 border border-border-subtle rounded-md bg-surface-card cursor-pointer select-none">
                <span class="sf-switch">
                    <input type="checkbox" name="in_stock" value="1" @checked(!empty($selectedInStock)) class="sr-only">
                    <span class="sf-switch__track"></span>
                </span>
                <span class="text-sm text-body">@t('In stock only')</span>
            </label>
        </div>
    </div>
</fieldset>

{{--
    Drag-to-reorder for the column switches. Native HTML5 drag-and-drop, no
    library. The submitted order of the checked `columns[]` inputs is the
    export/preview column order. Idempotent + scoped so it is safe even if this
    partial is included more than once on a page. Progressive enhancement: with
    JS off the switches still submit; only reordering is unavailable.
--}}
<script type="module">
    for (const list of document.querySelectorAll('[data-export-columns]')) {
        if (list.dataset.reorderReady === '1') {
            continue;
        }
        list.dataset.reorderReady = '1';

        let dragged = null;
        const rowOf = (node) => node?.closest?.('[data-col-row]');

        list.addEventListener('dragstart', (event) => {
            const row = rowOf(event.target);
            if (!row || !list.contains(row)) {
                return;
            }
            dragged = row;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', '');
            window.requestAnimationFrame(() => row.classList.add('opacity-50'));
        });

        list.addEventListener('dragover', (event) => {
            if (!dragged) {
                return;
            }
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            const target = rowOf(event.target);
            if (!target || target === dragged || !list.contains(target)) {
                return;
            }
            const rect = target.getBoundingClientRect();
            const after = (event.clientY - rect.top) > rect.height / 2;
            list.insertBefore(dragged, after ? target.nextElementSibling : target);
        });

        const reset = () => {
            if (dragged) {
                dragged.classList.remove('opacity-50');
                dragged = null;
            }
        };

        list.addEventListener('dragend', reset);
        list.addEventListener('drop', (event) => {
            event.preventDefault();
            reset();
            // Let listeners (e.g. the live preview) react to a new column order.
            list.dispatchEvent(new Event('input', {
                bubbles: true
            }));
        });
    }
</script>
