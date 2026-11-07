{{-- Header category navigation follows $store['category_ui_type'].
     list: a link to the categories index.
     mega_menu: one open department at a time, fed by $megaMenuCategories.
     Links use menu_path. Old shop URLs are never hardcoded. --}}
@if(($store['category_ui_type'] ?? 'list') === 'mega_menu')
    <nav class="apofit-nav border-t border-border-subtle" aria-label="@t('Categories')" data-category-ui-type="mega_menu" data-apofit-nav>
        <div class="apofit-nav__desktop mx-auto w-full max-w-desktop px-4">
            @if($megaMenuCategories->isEmpty())
                <p class="m-0 py-3 text-sm text-body">@t('No categories')</p>
            @else
                {{-- One control. The live depth window is about 18 parents and
                     their children. Putting every parent in the header wrapped
                     onto two rows, including Unkategorisiert and both
                     Wärmeflaschen names. Those names stay in the panel. --}}
                <div class="apofit-nav__toplinks">
                    <details class="apofit-nav__details" data-apofit-details>
                        <summary class="apofit-nav__summary">@t('Products')</summary>
                        <div class="apofit-nav__flyout apofit-nav__flyout--board">
                            <div class="apofit-nav__board">
                                @foreach($megaMenuCategories as $category)
                                    <section class="apofit-nav__col" data-apofit-item data-name="{{ mb_strtolower((string) $category->name) }}">
                                        @if(data_get($category, 'menu_path'))
                                            <a class="apofit-nav__heading" href="@routeUrl('store.category', ['slug' => data_get($category, 'menu_path')])">{{ $category->name }}</a>
                                        @else
                                            <p class="apofit-nav__heading">{{ $category->name }}</p>
                                        @endif
                                        @if(count($category->children ?? []) > 0)
                                            <ul>
                                                @foreach($category->children as $child)
                                                    @if(data_get($child, 'menu_path'))
                                                        <li data-apofit-item data-name="{{ mb_strtolower((string) $child->name) }}"><a href="@routeUrl('store.category', ['slug' => data_get($child, 'menu_path')])">{{ $child->name }}</a></li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </section>
                                @endforeach
                            </div>
                        </div>
                    </details>
                    @foreach($storefrontContentNav ?? [] as $item)
                        @if(data_get($item, 'href'))
                            <a class="apofit-nav__direct" href="{{ data_get($item, 'href') }}">{{ data_get($item, 'label') }}</a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <dialog id="apofit-nav-dialog" class="apofit-nav-dialog" aria-labelledby="apofit-nav-title">
            <div class="apofit-nav-dialog__bar">
                <button type="button" data-apofit-back hidden>@t('Zurück')</button>
                <h2 id="apofit-nav-title" class="font-primary" data-apofit-title data-root-label="@t('Sortiment')">@t('Sortiment')</h2>
                <button type="button" data-apofit-nav-close>@t('Close')</button>
            </div>
            <div class="apofit-nav-dialog__search">
                <input type="search" data-apofit-mobile-search autocomplete="off"
                    placeholder="@t('Search categories')"
                    aria-label="@t('Search categories')">
            </div>
            <div data-apofit-mpane="root" data-pane-title="@t('Sortiment')">
                <ul class="apofit-nav-dialog__list">
                    <li><a href="@routeUrl('store.categories')">@t('Categories')</a></li>
                    <li><a href="@routeUrl('store.products')">@t('All products')</a></li>
                    @foreach($megaMenuCategories as $category)
                        <li data-apofit-item data-name="{{ mb_strtolower((string) $category->name) }}">
                            @if(count($category->children ?? []) > 0)
                                <button type="button" data-apofit-drill="p-{{ $loop->index }}">
                                    <span>{{ $category->name }}</span>
                                    <span aria-hidden="true">›</span>
                                </button>
                            @elseif(data_get($category, 'menu_path'))
                                <a href="@routeUrl('store.category', ['slug' => data_get($category, 'menu_path')])">{{ $category->name }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
            @foreach($megaMenuCategories as $category)
                @if(count($category->children ?? []) > 0)
                    <div data-apofit-mpane="p-{{ $loop->index }}" data-pane-title="{{ $category->name }}" hidden>
                        <ul class="apofit-nav-dialog__list">
                            @if(data_get($category, 'menu_path'))
                                <li data-apofit-item data-name="{{ mb_strtolower((string) $category->name) }}">
                                    <a class="apofit-nav__all" href="@routeUrl('store.category', ['slug' => data_get($category, 'menu_path')])">@t('Alle anzeigen')</a>
                                </li>
                            @endif
                            @foreach($category->children as $child)
                                <li data-apofit-item data-name="{{ mb_strtolower((string) $child->name) }}">
                                    @if(count(data_get($child, 'children') ?? []) > 0)
                                        <button type="button" data-apofit-drill="p-{{ $loop->parent->index }}-{{ $loop->index }}">
                                            <span>{{ $child->name }}</span>
                                            <span aria-hidden="true">›</span>
                                        </button>
                                    @elseif(data_get($child, 'menu_path'))
                                        <a href="@routeUrl('store.category', ['slug' => data_get($child, 'menu_path')])">{{ $child->name }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @foreach($category->children as $child)
                        @if(count(data_get($child, 'children') ?? []) > 0)
                            <div data-apofit-mpane="p-{{ $loop->parent->index }}-{{ $loop->index }}" data-pane-title="{{ $child->name }}" hidden>
                                <ul class="apofit-nav-dialog__list">
                                    @if(data_get($child, 'menu_path'))
                                        <li>
                                            <a class="apofit-nav__all" href="@routeUrl('store.category', ['slug' => data_get($child, 'menu_path')])">@t('Alle anzeigen')</a>
                                        </li>
                                    @endif
                                    @foreach(data_get($child, 'children') as $grandchild)
                                        @if(data_get($grandchild, 'menu_path'))
                                            <li data-apofit-item data-name="{{ mb_strtolower((string) data_get($grandchild, 'name')) }}">
                                                <a href="@routeUrl('store.category', ['slug' => data_get($grandchild, 'menu_path')])">{{ data_get($grandchild, 'name') }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
                @endif
            @endforeach
            <p hidden data-apofit-mobile-empty class="px-4 text-sm text-body">@t('No categories')</p>
        </dialog>
    </nav>

    <script type="module">
        const nav = document.querySelector('[data-apofit-nav]');
        if (nav) {
            nav.classList.add('apofit-nav--enhanced');
            const groups = [...nav.querySelectorAll('[data-apofit-details]')];
            const tabs = [...nav.querySelectorAll('[data-apofit-tab]')];
            const panes = [...nav.querySelectorAll('[data-apofit-pane]')];

            const showPane = (id) => {
                for (const pane of panes) {
                    pane.hidden = pane.getAttribute('data-apofit-pane') !== id;
                }
                for (const tab of tabs) {
                    tab.setAttribute('aria-expanded', tab.getAttribute('data-apofit-tab') === id ? 'true' : 'false');
                }
            };

            for (const tab of tabs) {
                tab.addEventListener('click', () => {
                    const id = tab.getAttribute('data-apofit-tab');
                    if (id) {
                        showPane(id);
                    }
                });
            }

            const closeGroups = () => {
                for (const group of groups) {
                    group.open = false;
                }
            };

            for (const group of groups) {
                const input = group.querySelector('[data-apofit-flyout-search]');
                const empty = group.querySelector('[data-apofit-none]');
                if (input instanceof HTMLInputElement) {
                    input.addEventListener('input', () => {
                        const query = input.value.trim().toLowerCase();
                        let visible = 0;
                        for (const parent of group.querySelectorAll('[data-apofit-parent]')) {
                            const name = parent.getAttribute('data-name') || '';
                            const show = query === '' || name.includes(query);
                            parent.hidden = !show;
                            if (show) {
                                visible += 1;
                            }
                        }
                        for (const item of group.querySelectorAll('[data-apofit-item]')) {
                            const name = item.getAttribute('data-name') || '';
                            item.hidden = query !== '' && !name.includes(query);
                        }
                        if (empty) {
                            empty.hidden = visible !== 0;
                        }
                    });
                }
            }

            const summary = nav.querySelector('.apofit-nav__summary');
            summary?.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeGroups();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeGroups();
                }
            });

            document.addEventListener('click', (event) => {
                if (!(event.target instanceof Node) || nav.contains(event.target)) {
                    return;
                }
                closeGroups();
            });

            const dialog = document.getElementById('apofit-nav-dialog');
            const openButton = document.querySelector('[data-apofit-nav-open]');
            if (dialog instanceof HTMLDialogElement && openButton instanceof HTMLButtonElement) {
                const title = dialog.querySelector('[data-apofit-title]');
                const back = dialog.querySelector('[data-apofit-back]');
                const closer = dialog.querySelector('[data-apofit-nav-close]');
                const search = dialog.querySelector('[data-apofit-mobile-search]');
                const empty = dialog.querySelector('[data-apofit-mobile-empty]');
                const stack = ['root'];

                const pane = () => dialog.querySelector('[data-apofit-mpane="' + stack[stack.length - 1] + '"]');

                const render = () => {
                    const current = stack[stack.length - 1];
                    for (const node of dialog.querySelectorAll('[data-apofit-mpane]')) {
                        node.hidden = node.getAttribute('data-apofit-mpane') !== current;
                    }
                    if (back instanceof HTMLButtonElement) {
                        back.hidden = current === 'root';
                    }
                    const active = pane();
                    if (title) {
                        const rootLabel = title.getAttribute('data-root-label') || '';
                        title.textContent = current === 'root' ? rootLabel : (active?.getAttribute('data-pane-title') || rootLabel);
                    }
                    if (search instanceof HTMLInputElement) {
                        search.value = '';
                    }
                    if (empty) {
                        empty.hidden = true;
                    }
                    for (const item of dialog.querySelectorAll('[data-apofit-item]')) {
                        item.hidden = false;
                    }
                };

                const filterPane = () => {
                    const active = pane();
                    if (!active || !(search instanceof HTMLInputElement)) {
                        return;
                    }
                    const query = search.value.trim().toLowerCase();
                    let visible = 0;
                    for (const item of active.querySelectorAll('[data-apofit-item]')) {
                        const name = item.getAttribute('data-name') || '';
                        const show = query === '' || name.includes(query);
                        item.hidden = !show;
                        if (show) {
                            visible += 1;
                        }
                    }
                    if (empty) {
                        empty.hidden = visible !== 0;
                    }
                };

                openButton.addEventListener('click', () => {
                    stack.splice(0, stack.length, 'root');
                    render();
                    dialog.showModal();
                    openButton.setAttribute('aria-expanded', 'true');
                    if (search instanceof HTMLInputElement) {
                        search.focus();
                    }
                });

                const shut = () => {
                    dialog.close();
                    openButton.setAttribute('aria-expanded', 'false');
                    openButton.focus();
                };

                closer?.addEventListener('click', shut);
                dialog.addEventListener('close', () => {
                    openButton.setAttribute('aria-expanded', 'false');
                });
                dialog.addEventListener('cancel', (event) => {
                    if (stack.length > 1) {
                        event.preventDefault();
                        stack.pop();
                        render();
                    }
                });
                back?.addEventListener('click', () => {
                    if (stack.length > 1) {
                        stack.pop();
                        render();
                    }
                });
                dialog.addEventListener('click', (event) => {
                    const trigger = event.target instanceof Element ? event.target.closest('[data-apofit-drill]') : null;
                    if (!(trigger instanceof HTMLButtonElement)) {
                        return;
                    }
                    const next = trigger.getAttribute('data-apofit-drill');
                    if (!next) {
                        return;
                    }
                    stack.push(next);
                    render();
                });
                search?.addEventListener('input', filterPane);
            }
        }
    </script>
@else
    <nav class="storefront-nav border-t border-border-subtle" aria-label="@t('Categories')" data-category-ui-type="list">
        <div class="storefront-nav__bar mx-auto flex w-full max-w-desktop items-center px-4">
            <a href="@routeUrl('store.categories')"
                class="inline-flex min-h-11 items-center gap-2 rounded px-3 py-2 font-medium text-headings hover:bg-surface-hover hover:no-underline">
                <span>@t('Categories')</span>
            </a>
        </div>
    </nav>
@endif
