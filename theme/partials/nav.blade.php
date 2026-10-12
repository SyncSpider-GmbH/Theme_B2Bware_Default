{{-- Header category navigation follows $store['category_ui_type'].
     list (default): a link to the categories index.
     mega_menu: a disclosure of the depth window in $megaMenuCategories. --}}
@if(($store['category_ui_type'] ?? 'list') === 'mega_menu')
    <nav class="storefront-nav border-t border-border-subtle" aria-label="@t('Categories')" data-category-ui-type="mega_menu">
        <div class="storefront-nav__bar relative mx-auto flex w-full max-w-desktop items-center px-4">
            <details class="storefront-nav__mega group" data-storefront-dropdown="categories">
                <summary class="flex cursor-pointer list-none items-center gap-2 rounded px-3 py-2 font-medium text-headings hover:bg-surface-hover">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>@t('Categories')</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-transform group-open:rotate-180" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </summary>
                <div class="storefront-nav__panel absolute inset-x-0 top-full z-20 mt-1 max-h-96 overflow-auto rounded border border-border-subtle bg-surface-card p-4 shadow" data-mega-menu>
                    @if($megaMenuCategories->isEmpty())
                        <p class="m-0 text-sm text-body">@t('No categories')</p>
                    @else
                        <input type="search" data-mega-menu-search autocomplete="off"
                            placeholder="@t('Search categories')"
                            aria-label="@t('Search categories')"
                            class="mb-4 w-full rounded border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary">
                        <div class="columns-1 gap-6 lg:columns-3">
                            @foreach($megaMenuCategories as $category)
                                <div class="mb-3 break-inside-avoid" data-mega-menu-group>
                                    @if(data_get($category, 'menu_path'))
                                        <a href="@routeUrl('store.category', ['slug' => data_get($category, 'menu_path')])"
                                            data-mega-menu-item
                                            data-mega-menu-name="{{ mb_strtolower((string) $category->name) }}"
                                            class="mb-1 block rounded px-2 py-1 font-semibold text-headings hover:bg-surface-hover hover:no-underline">{{ $category->name }}</a>
                                    @endif
                                    @foreach($category->children as $child)
                                        @if(data_get($child, 'menu_path'))
                                            <a href="@routeUrl('store.category', ['slug' => data_get($child, 'menu_path')])"
                                                data-mega-menu-item
                                                data-mega-menu-name="{{ mb_strtolower((string) $child->name) }}"
                                                class="ml-3 block rounded px-2 py-1 text-sm text-body hover:bg-surface-hover hover:no-underline">{{ $child->name }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                        <p hidden data-mega-menu-no-matches class="m-0 text-sm text-body">@t('No categories')</p>
                    @endif
                </div>
            </details>
        </div>
    </nav>

    <script type="module">
        for (const panel of document.querySelectorAll('[data-mega-menu]')) {
            const input = panel.querySelector('[data-mega-menu-search]');
            const empty = panel.querySelector('[data-mega-menu-no-matches]');
            if (!(input instanceof HTMLInputElement)) {
                continue;
            }

            const apply = () => {
                const query = input.value.trim().toLowerCase();
                let visible = 0;

                for (const group of panel.querySelectorAll('[data-mega-menu-group]')) {
                    let groupVisible = 0;
                    for (const item of group.querySelectorAll('[data-mega-menu-item]')) {
                        const name = item.getAttribute('data-mega-menu-name') || '';
                        const show = query === '' || name.includes(query);
                        item.hidden = !show;
                        if (show) {
                            groupVisible += 1;
                        }
                    }
                    group.hidden = groupVisible === 0;
                    visible += groupVisible;
                }

                if (empty) {
                    empty.hidden = visible !== 0;
                }
            };

            input.addEventListener('input', apply);
        }
    </script>
@else
    <nav class="storefront-nav border-t border-border-subtle" aria-label="@t('Categories')" data-category-ui-type="list">
        <div class="storefront-nav__bar mx-auto flex w-full max-w-desktop items-center px-4">
            <a href="@routeUrl('store.categories')"
                class="inline-flex items-center gap-2 rounded px-3 py-2 font-medium text-headings hover:bg-surface-hover hover:no-underline">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                    <line x1="8" y1="6" x2="21" y2="6"></line>
                    <line x1="8" y1="12" x2="21" y2="12"></line>
                    <line x1="8" y1="18" x2="21" y2="18"></line>
                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
                <span>@t('Categories')</span>
            </a>
        </div>
    </nav>
@endif
