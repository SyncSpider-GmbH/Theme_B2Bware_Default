@if(count($localeOptions ?? []) > 1)
    <details class="locale-switcher group relative" data-storefront-dropdown="locale" data-locale-switcher data-endpoint="{{ route('store.locale.set') }}">
        <summary class="locale-switcher__trigger flex cursor-pointer list-none items-center gap-1.5 rounded px-1.5 py-1 text-neutral-white transition-colors hover:text-neutral-white">
            @foreach($localeOptions as $option)
                @if($option['active'])
                    <img src="{{ $option['flag'] }}" alt="" class="h-4 w-6 shrink-0 rounded-sm object-cover" width="24" height="16" loading="lazy">
                    <span class="text-sm font-medium">{{ $option['short'] }}</span>
                @endif
            @endforeach
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 shrink-0 transition-transform group-open:rotate-180" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
            <span class="sr-only">@t('Language')</span>
        </summary>
        <div class="locale-switcher__panel absolute right-0 top-full z-30 mt-2 min-w-44 overflow-hidden rounded-lg border border-border-subtle bg-surface-card py-1 shadow-lg">
            @foreach($localeOptions as $option)
                <a
                    href="{{ $option['url'] }}"
                    data-locale-option
                    data-locale="{{ $option['code'] }}"
                    @class([
                        'flex items-center gap-2.5 px-3 py-2 text-sm hover:no-underline',
                        'bg-primary-subtle text-primary font-medium' => $option['active'],
                        'text-headings hover:bg-surface-hover' => !$option['active'],
                    ])
                    @if($option['active']) aria-current="true" @endif
                >
                    <img src="{{ $option['flag'] }}" alt="" class="h-4 w-6 shrink-0 rounded-sm object-cover" width="24" height="16" loading="lazy">
                    <span class="truncate">{{ $option['label'] }}</span>
                </a>
            @endforeach
        </div>
    </details>

    <script type="module">
        for (const root of document.querySelectorAll('[data-locale-switcher]')) {
            const endpoint = root.dataset.endpoint || '/locale';

            for (const link of root.querySelectorAll('[data-locale-option]')) {
                link.addEventListener('click', () => {
                    // Fire-and-forget cookie persistence. `keepalive` lets the
                    // request survive the navigation the link triggers next, so
                    // a later visit to a locale-less URL remembers this choice.
                    try {
                        fetch(endpoint, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                            body: JSON.stringify({ locale: link.dataset.locale }),
                            credentials: 'same-origin',
                            keepalive: true,
                        });
                    } catch {
                        // Cookie write is best-effort; navigation still proceeds.
                    }
                });
            }
        }
    </script>
@endif
