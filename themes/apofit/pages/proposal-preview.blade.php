@extends('layouts.shop')

@section('title', $proposalTitle ?? t('Proposal'))

@section('content')
    @push('head')
        {{-- Shared proposals default to LIGHT for recipients who never visited
             the store (no colour-scheme cookie set). The no-FOUC head script
             otherwise follows the visitor's OS preference; force light here
             unless the visitor made an explicit choice. The on-page switch
             below lets them change it, persisting the cookie. --}}
        <script>
            try {
                if (!/(?:^|;\s*)storefront_color_scheme=/.test(document.cookie)) {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.style.colorScheme = 'light';
                }
            } catch (e) {}
        </script>
    @endpush
    @if(!empty($orderedId))
        {{-- Post-order thank-you state: the anonymous customer lands back on
             this public URL with ?ordered={id} (also the post-payment return
             target), so the confirmation must not depend on a login. --}}
        <div class="page page--proposal-ordered flex flex-col items-center justify-center gap-4 py-24 text-center">
            <svg class="h-16 w-16 text-success" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm4.7 8.2a1 1 0 0 0-1.4-1.4L11 13.1l-2.3-2.3a1 1 0 0 0-1.4 1.4l3 3a1 1 0 0 0 1.4 0l5-5Z" clip-rule="evenodd"/></svg>
            <h1 class="font-primary text-2xl text-headings m-0">@t('Thank you for your order')</h1>
            <p class="text-body m-0">@t('Your order has been placed successfully.')</p>
            <p class="text-headings font-semibold m-0">@t('Order number'): #{{ $orderedId }}</p>
        </div>
    @else
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="page page--proposal flex flex-col gap-6" data-proposal-id="{{ $proposalId }}">
        @if(!empty($hideChrome))
            {{-- Standalone share document: no shop chrome, so surface the store
                 identity as a quote letterhead and expose a light/dark switch
                 (the chrome toggle is absent here). Recipients may never have
                 visited the store, so the page defaults to light (see the head
                 script above) and this control lets them flip it. --}}
            <div class="proposal__letterhead relative flex min-h-16 items-center justify-center">
                @if(!empty($store['logo']))
                    <img src="{{ $store['logo'] }}" alt="{{ $store['name'] ?? '' }}"
                         class="proposal__logo storefront-logo {{ !empty($store['logo_dark']) ? 'storefront-logo--light' : '' }} h-16 w-auto">
                    @if(!empty($store['logo_dark']))
                        <img src="{{ $store['logo_dark'] }}" alt="{{ $store['name'] ?? '' }}"
                             class="proposal__logo storefront-logo storefront-logo--dark h-16 w-auto">
                    @endif
                @endif

                <div class="proposal__color-scheme absolute right-0 top-0">
                    @storefrontForm('color-scheme', ['class' => 'theme-toggle inline-flex items-center', 'data-theme-toggle' => true])
                        <input type="hidden" name="redirect" value="{{ $currentPath ?? '/' }}">
                        <button type="submit" name="scheme" value="dark"
                            class="theme-toggle__to-dark inline-flex h-8 w-8 items-center justify-center rounded text-body transition-colors hover:bg-surface-card hover:text-headings"
                            aria-label="@t('Switch to dark mode')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </button>
                        <button type="submit" name="scheme" value="light"
                            class="theme-toggle__to-light inline-flex h-8 w-8 items-center justify-center rounded text-body transition-colors hover:bg-surface-card hover:text-headings"
                            aria-label="@t('Switch to light mode')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                        </button>
                    @endstorefrontForm
                </div>
            </div>

            @push('scripts')
                {{-- Live (no-reload) toggle when the JS runtime is present; the
                     no-JS form POST still works and the server re-renders. --}}
                <script type="module">
                    for (const form of document.querySelectorAll('.page--proposal [data-theme-toggle]')) {
                        form.addEventListener('submit', (event) => {
                            const api = window.Storefront;
                            if (api && typeof api.toggleColorScheme === 'function') {
                                event.preventDefault();
                                api.toggleColorScheme();
                            }
                        });
                    }
                </script>
            @endpush
        @endif

        @section('proposal.header')
        <header class="page__head flex flex-col gap-2 text-center">
            @if($proposal->description)
                {{-- The admin composer preview shows the authored rich text as
                     the document head — no generic "Proposal" heading. --}}
                <div class="storefront-richtext text-body">{!! $proposal->description !!}</div>
            @else
                <h1 class="font-primary text-2xl text-headings m-0">@t('Proposal')</h1>
            @endif
        </header>
        @show

        @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])
        @storefrontError('proposal')
        @storefrontError('items')
        @storefrontError('accept_terms')

        @if($groups->isEmpty())
            @include('components.empty-state', [
                'title'   => t('This proposal is empty'),
                'message' => t('There are no items to review.'),
            ])
        @else
            @if($editable)
                {{-- One form wraps every line so the visitor can tune
                     quantities (0 drops the line) before ordering. --}}
                @storefrontForm('proposal-public-order', ['_params' => ['token' => $proposalId], 'class' => 'flex flex-col gap-6'])
            @endif

            @section('proposal.items')
            <div class="flex flex-col gap-6" data-proposal-items>
                @foreach($groups as $group)
                    <section class="proposal-group flex flex-col gap-3">
                        @if($group['name'] !== '')
                            {{-- Brand-colored banner, mirroring the admin
                                 composer preview so the shared page looks the
                                 same as what the tenant designed. --}}
                            <h2 class="font-primary text-xl font-bold text-primary-content m-0 text-center uppercase py-4 rounded bg-primary">{{ $group['name'] }}</h2>
                        @endif
                        @if($group['description'] !== '')
                            <div class="storefront-richtext text-sm text-body text-center">{!! $group['description'] !!}</div>
                        @endif
                        @if(($group['image'] ?? '') !== '')
                            <img src="{{ $group['image'] }}" alt="{{ $group['name'] !== '' ? $group['name'] : t('Proposal') }}" class="proposal-group__image mx-auto my-2 max-h-80 w-auto rounded-lg" loading="lazy">
                        @endif

                        <ul class="flex flex-col gap-2 list-none m-0 p-0">
                            @foreach($group['items'] as $item)
                                <li class="proposal-line flex flex-wrap items-center gap-3 p-3 bg-surface-card border border-border-subtle rounded-lg"
                                    @if($editable) data-proposal-line data-line-index="{{ data_get($item, 'line_index') }}" data-price="{{ (float) ($item->price ?? 0) }}" @endif>
                                    <div class="h-14 w-14 shrink-0 overflow-hidden rounded bg-surface-page">
                                        @if(data_get($item, 'product.resolved_main_media.media_url') ?? data_get($item, 'product.main_media.media_url'))
                                            <img src="@storefrontImage(data_get($item, 'product.resolved_main_media.media_url') ?? data_get($item, 'product.main_media.media_url'), 112, 112, 85)"
                                                 alt="{{ data_get($item, 'product.name') ?? '' }}" class="h-full w-full object-contain" loading="lazy">
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0 flex flex-col">
                                        <span class="text-headings font-medium">{{ data_get($item, 'product.name') ?: t('Product') }}</span>
                                        @if(data_get($item, 'product.sku'))
                                            <span class="text-body text-sm">@t('SKU'): {{ data_get($item, 'product.sku') }}</span>
                                        @endif
                                    </div>
                                    @if($editable)
                                        <input type="hidden" name="items[{{ data_get($item, 'line_index') }}][product_id]" value="{{ (int) $item->product_id }}">
                                        @include('components.quantity-selector', [
                                            'name'  => 'items[' . data_get($item, 'line_index') . '][quantity]',
                                            'value' => (int) ($item->quantity ?? 1),
                                            'min'   => 0,
                                        ])
                                        <div class="text-body text-sm shrink-0">&times; {{ formatCurrency($item->price ?? 0) }}</div>
                                    @else
                                        <div class="text-body text-sm shrink-0">
                                            {{ formatNumber($item->quantity ?? 0) }} &times; {{ formatCurrency($item->price ?? 0) }}
                                        </div>
                                    @endif
                                    <div class="text-headings font-medium shrink-0 w-28 text-right" @if($editable) data-proposal-line-total @endif>
                                        {{ formatCurrency($editable
                                            ? (float) ($item->price ?? 0) * (float) ($item->quantity ?? 0)
                                            : ($item->total_price ?? ($item->subtotal_price ?? (float) ($item->price ?? 0) * (float) ($item->quantity ?? 0)))) }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </div>
            @show

            {{-- Products Preview — flat summary table of every kept line,
                 mirroring the "Products Preview" grid on the admin composer
                 preview. When editable, rows track the quantity inputs above
                 (a row hides once its quantity is set to 0). --}}
            @section('proposal.products')
            <section class="proposal-products-preview flex flex-col gap-3">
                <h2 class="font-primary text-xl font-bold text-primary-content m-0 text-center uppercase py-4 rounded bg-primary">@t('Products Preview')</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="text-left text-body border-b border-border-subtle">
                                <th class="py-2 pr-3 font-medium">@t('Product number')</th>
                                <th class="py-2 pr-3 font-medium">@t('Product Name')</th>
                                <th class="py-2 pr-3 font-medium">@t('Brand')</th>
                                <th class="py-2 pr-3 font-medium text-right">@t('Quantity')</th>
                                <th class="py-2 pr-3 font-medium text-right">@t('List Price')</th>
                                <th class="py-2 font-medium text-right">@t('Total Price')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewLines as $item)
                                @if($editable || (int) ($item->quantity ?? 0) > 0)
                                    <tr class="border-b border-border-subtle {{ $editable && (int) ($item->quantity ?? 0) < 1 ? 'hidden' : '' }}"
                                        @if($editable) data-proposal-row="{{ data_get($item, 'line_index') }}" @endif>
                                        <td class="py-2 pr-3 text-body">{{ data_get($item, 'product.sku') ?: '—' }}</td>
                                        <td class="py-2 pr-3 text-headings font-medium">{{ data_get($item, 'product.name') ?: t('Product') }}</td>
                                        <td class="py-2 pr-3 text-body">{{ data_get($item, 'brand') ?: '—' }}</td>
                                        <td class="py-2 pr-3 text-right text-body" @if($editable) data-proposal-row-qty @endif>{{ (int) ($item->quantity ?? 0) }}</td>
                                        <td class="py-2 pr-3 text-right text-body">{{ formatCurrency($item->price ?? 0) }}</td>
                                        <td class="py-2 text-right text-headings font-medium" @if($editable) data-proposal-row-total @endif>
                                            {{ formatCurrency((float) ($item->price ?? 0) * (int) ($item->quantity ?? 0)) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            @show

            @section('proposal.summary')
            <aside class="proposal-summary self-end w-full sm:w-80 p-4 bg-surface-card border border-border-subtle rounded-lg flex flex-col gap-3"
                   @if($editable) data-proposal-extra="{{ (float) ($totals['grand_total'] - $totals['subtotal']) }}" @endif>
                <h2 class="font-primary text-lg text-headings m-0">@t('Summary')</h2>
                <dl class="grid grid-cols-[1fr_auto] gap-x-3 gap-y-1 m-0">
                    <dt class="text-body">@t('Subtotal')</dt>
                    <dd class="text-headings m-0" @if($editable) data-proposal-subtotal @endif>{{ formatCurrency($totals['subtotal']) }}</dd>
                    @if(!empty($totals['discount']))
                        <dt class="text-body">@t('Discount')</dt>
                        <dd class="text-headings m-0">-{{ formatCurrency($totals['discount']) }}</dd>
                    @endif
                    @if(!empty($totals['surcharge']))
                        <dt class="text-body">@t('Surcharge')</dt>
                        <dd class="text-headings m-0">{{ formatCurrency($totals['surcharge']) }}</dd>
                    @endif
                    {{-- Shipping is always shown (even at 0) so the summary
                         mirrors the admin composer preview; the customer sees
                         the same line the agent designed. --}}
                    <dt class="text-body">@t('Shipping')</dt>
                    <dd class="text-headings m-0">{{ formatCurrency($totals['shipping'] ?? 0) }}</dd>
                    @if(!empty($totals['tax']))
                        <dt class="text-body">@t('Tax')</dt>
                        <dd class="text-headings m-0">{{ formatCurrency($totals['tax']) }}</dd>
                    @endif
                    <dt class="font-semibold text-headings">@t('Total')</dt>
                    <dd class="font-semibold text-headings m-0" @if($editable) data-proposal-total @endif>{{ formatCurrency($totals['grand_total']) }}</dd>
                </dl>
                @if(empty($totals['tax']))
                    <p class="text-xs text-body m-0">@t('excl. VAT, plus shipping')</p>
                @endif
                @if($editable)
                    <p class="text-xs text-body m-0">@t('Final shipping and tax are calculated when the order is placed.')</p>
                @endif

                @if($editable)
                    <label class="proposal__terms flex items-start gap-2 text-sm text-body">
                        <input type="checkbox" name="accept_terms" value="1" required @checked(old('accept_terms')) class="shrink-0">
                        <span>
                            @t('I agree to the')
                            @if(data_get($store, 'legal.terms_and_conditions_url'))
                                <a href="{{ data_get($store, 'legal.terms_and_conditions_url') }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-primary-600 font-medium">@t('Terms of Sale')</a>
                            @else
                                <span class="text-headings">@t('Terms of Sale')</span>
                            @endif
                            @t('and')
                            @if(data_get($store, 'legal.privacy_policy_url'))
                                <a href="{{ data_get($store, 'legal.privacy_policy_url') }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-primary-600 font-medium">@t('Privacy Policy')</a>
                            @else
                                <span class="text-headings">@t('Privacy Policy')</span>
                            @endif.
                        </span>
                    </label>
                    <button type="submit" class="btn btn--primary inline-flex items-center justify-center rounded bg-primary text-primary-content font-medium px-4 py-2 transition-colors hover:bg-primary-600 cursor-pointer">
                        @t('Create Order')
                    </button>
                    <p class="text-xs text-body m-0">@t('The order is placed for the customer this proposal was prepared for.')</p>
                @elseif(empty($isPublic) && empty($readOnly))
                    @storefrontForm('proposal-accept', ['_params' => ['proposal' => $proposalId], 'class' => 'flex flex-col gap-2'])
                        <button type="submit" class="btn btn--primary inline-flex items-center justify-center rounded bg-primary text-primary-content font-medium px-4 py-2 transition-colors hover:bg-primary-600 cursor-pointer">
                            @t('Accept and add to cart')
                        </button>
                        <p class="text-xs text-body m-0">@t('Items will be added to your cart so you can complete checkout.')</p>
                    @endstorefrontForm
                @endif
            </aside>
            @show

            @if($editable)
                @endstorefrontForm
            @endif
        @endif
    </div>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
    @endif
@endsection

@if(!empty($canPublicOrder) && empty($orderedId))
    @push('scripts')
        <script type="module">
            {{-- Live line/summary totals while the visitor tunes quantities.
                 Server-side placement recomputes authoritatively, so this is
                 presentation-only progressive enhancement. --}}
            const locale = document.body.dataset.locale || 'en';
            const currency = document.body.dataset.currency || 'EUR';
            const fmt = new Intl.NumberFormat(locale, { style: 'currency', currency });
            const summary = document.querySelector('[data-proposal-extra]');
            const extra = summary ? Number(summary.dataset.proposalExtra) || 0 : 0;

            function recalc() {
                let subtotal = 0;
                document.querySelectorAll('[data-proposal-line]').forEach((line) => {
                    const price = Number(line.dataset.price) || 0;
                    const qty = Number(line.querySelector('input[type="number"]')?.value) || 0;
                    const total = price * qty;
                    subtotal += total;
                    const cell = line.querySelector('[data-proposal-line-total]');
                    if (cell) cell.textContent = fmt.format(total);
                    // Keep the Products Preview summary row in sync (hide at qty 0).
                    const row = document.querySelector(`[data-proposal-row="${line.dataset.lineIndex}"]`);
                    if (row) {
                        row.classList.toggle('hidden', qty < 1);
                        const qtyCell = row.querySelector('[data-proposal-row-qty]');
                        if (qtyCell) qtyCell.textContent = String(qty);
                        const totalCell = row.querySelector('[data-proposal-row-total]');
                        if (totalCell) totalCell.textContent = fmt.format(total);
                    }
                });
                const sub = document.querySelector('[data-proposal-subtotal]');
                if (sub) sub.textContent = fmt.format(subtotal);
                const grand = document.querySelector('[data-proposal-total]');
                if (grand) grand.textContent = fmt.format(subtotal + extra);
            }

            document.querySelectorAll('[data-proposal-line] input[type="number"]').forEach((input) => {
                input.addEventListener('change', recalc);
                input.addEventListener('input', recalc);
            });
        </script>
    @endpush
@endif
