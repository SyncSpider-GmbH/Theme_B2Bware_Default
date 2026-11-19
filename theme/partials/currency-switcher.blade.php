@if(count($currencyOptions ?? []) > 1)
    <details class="currency-switcher group relative" data-storefront-dropdown="currency" data-currency-switcher>
        <summary class="currency-switcher__trigger flex cursor-pointer list-none items-center gap-1.5 rounded px-1.5 py-1 text-inherit transition-colors hover:opacity-90">
            @foreach($currencyOptions as $option)
                @if($option['active'])
                    <span class="text-sm font-medium" data-currency-active>{{ $option['code'] }}</span>
                @endif
            @endforeach
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 shrink-0 transition-transform group-open:rotate-180" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
            <span class="sr-only">@t('Currency')</span>
        </summary>
        <div class="currency-switcher__panel absolute right-0 top-full z-30 mt-2 min-w-28 overflow-hidden rounded-lg border border-border-subtle bg-surface-card py-1 shadow-lg">
            @foreach($currencyOptions as $option)
                <button
                    type="button"
                    data-currency-option
                    data-currency="{{ $option['code'] }}"
                    @class([
                        'flex w-full items-center gap-2.5 px-3 py-2 text-left text-sm',
                        'bg-primary-subtle text-primary font-medium' => $option['active'],
                        'text-headings hover:bg-surface-hover' => !$option['active'],
                    ])
                    @if($option['active']) aria-current="true" @endif
                >
                    <span>{{ $option['label'] }}</span>
                </button>
            @endforeach
        </div>
    </details>

    <script type="module">
        for (const root of document.querySelectorAll('[data-currency-switcher]')) {
            for (const button of root.querySelectorAll('[data-currency-option]')) {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const code = button.dataset.currency;
                    if (!code || typeof window.Storefront?.setCurrency !== 'function') {
                        return;
                    }
                    window.Storefront.setCurrency(code);
                });
            }
        }
    </script>
@endif
