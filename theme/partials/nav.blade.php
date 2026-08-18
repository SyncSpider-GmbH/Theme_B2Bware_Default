@if(count($rootCategories ?? []) > 0)
    <nav class="storefront-nav border-t border-border-subtle" aria-label="@t('Categories')">
        <div class="storefront-nav__bar mx-auto flex w-full max-w-desktop items-center gap-1 px-4">
            <details class="storefront-nav__all group relative shrink-0" data-storefront-dropdown="categories">
                <summary class="flex cursor-pointer list-none items-center gap-2 rounded px-3 py-2 font-medium text-headings hover:bg-surface-hover">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>@t('All Categories')</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-transform group-open:rotate-180" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </summary>
                <div class="storefront-nav__panel absolute left-0 top-full z-20 mt-1 max-h-96 w-72 overflow-auto rounded border border-border-subtle bg-surface-card p-2 shadow-lg">
                    <ul class="m-0 flex list-none flex-col gap-1 p-0">
                        @foreach($rootCategories as $category)
                            <li>
                                <a href="@routeUrl('store.category', ['slug' => data_get($category, 'seo.slug') ?: data_get($category, 'slug', '')])"
                                    class="block rounded px-2 py-1 font-medium text-headings hover:bg-surface-hover hover:no-underline">{{ $category->name }}</a>
                                @if(count($category->children ?? []) > 0)
                                    <ul class="m-0 mb-2 mt-1 flex list-none flex-col gap-1 border-l border-border-subtle p-0 pl-3">
                                        @foreach($category->children as $child)
                                            <li>
                                                <a href="@routeUrl('store.category', ['slug' => data_get($child, 'seo.slug') ?: data_get($child, 'slug', '')])"
                                                    class="block rounded px-2 py-1 text-sm text-body hover:bg-surface-hover hover:no-underline">{{ $child->name }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </details>

            <span class="storefront-nav__divider mx-1 h-5 w-px shrink-0 bg-border-subtle" aria-hidden="true"></span>

            {{-- Scrollable row of top-level categories. The scrollbar itself is hidden (CSS,
                 assets/css/storefront.css) and replaced by the two arrows below, which the
                 inline module only reveals when the row actually overflows. `tabindex` keeps
                 it keyboard/no-JS scrollable even when the arrows never appear. --}}
            <div class="storefront-nav__scroll relative flex min-w-0 flex-1 items-center gap-1" data-nav-scroll>
                <button type="button" data-nav-scroll-prev hidden
                    class="storefront-nav__arrow inline-flex h-8 w-8 shrink-0 items-center justify-center rounded text-headings hover:bg-surface-hover"
                    aria-label="@t('Scroll categories left')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <ul class="storefront-nav__links m-0 flex list-none items-center gap-1 overflow-x-auto p-0" data-nav-scroll-track tabindex="0">
                    @foreach($rootCategories as $category)
                        <li class="shrink-0">
                            <a href="@routeUrl('store.category', ['slug' => data_get($category, 'seo.slug') ?: data_get($category, 'slug', '')])"
                                class="block whitespace-nowrap rounded px-3 py-2 font-medium text-body hover:text-primary hover:no-underline">{{ $category->name }}</a>
                        </li>
                    @endforeach
                </ul>

                <button type="button" data-nav-scroll-next hidden
                    class="storefront-nav__arrow inline-flex h-8 w-8 shrink-0 items-center justify-center rounded text-headings hover:bg-surface-hover"
                    aria-label="@t('Scroll categories right')">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <script type="module">
        // Progressive enhancement only: arrows stay hidden (and the row falls back to native
        // touch/trackpad/keyboard scroll) unless this runs and finds real overflow.
        for (const scroller of document.querySelectorAll('[data-nav-scroll]')) {
            const track = scroller.querySelector('[data-nav-scroll-track]');
            const prevBtn = scroller.querySelector('[data-nav-scroll-prev]');
            const nextBtn = scroller.querySelector('[data-nav-scroll-next]');
            if (!track || !prevBtn || !nextBtn) {
                continue;
            }

            const update = () => {
                const hasOverflow = track.scrollWidth > track.clientWidth + 1;
                prevBtn.hidden = !hasOverflow || track.scrollLeft <= 0;
                nextBtn.hidden = !hasOverflow || track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;
            };

            prevBtn.addEventListener('click', () => {
                track.scrollBy({ left: -track.clientWidth * 0.8, behavior: 'smooth' });
            });
            nextBtn.addEventListener('click', () => {
                track.scrollBy({ left: track.clientWidth * 0.8, behavior: 'smooth' });
            });

            track.addEventListener('scroll', update);
            window.addEventListener('resize', update);
            update();
        }
    </script>
@endif
