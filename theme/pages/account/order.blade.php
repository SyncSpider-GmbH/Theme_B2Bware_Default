@extends('layouts.shop')

@section('title', t('Order'))

{{--
    Printable order document — strip the storefront chrome when printing so the
    output reads as a clean order. Kept inline (not in the theme stylesheet)
    because the theme CSS is served with an immutable 1-year cache and no
    version query, so a returning browser would never pick up a freshly-added
    rule there.
--}}
@push('head')
    <style>
        @media print {
            .storefront-header,
            .storefront-footer,
            .impersonation-banner,
            .breadcrumbs,
            .account__nav,
            .order__back,
            .order__actions {
                display: none !important;
            }

            body {
                background: #fff !important;
            }
        }
    </style>
@endpush

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="page page--account page--account-order flex flex-col gap-5">
        <div class="flex flex-col md:flex-row gap-6">
            @include('partials.account-nav', ['active' => 'orders'])

            <div class="flex-1 flex flex-col gap-4 min-w-0">
                <a href="@routeUrl('store.account.orders')" class="order__back inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary-600 no-underline self-start">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6" /></svg>
                    @t('Back to orders')
                </a>

                @if(!$order)
                    @include('components.empty-state', [
                        'title' => t('Order not found'),
                        'message' => t('We could not find this order on your account.'),
                    ])
                @else
                    {{-- Order summary --}}
                    <div class="bg-surface-card border border-border-subtle rounded-lg p-5 flex flex-col gap-4">
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <div class="flex flex-col gap-2">
                                <h1 class="font-primary text-lg font-semibold text-headings m-0">@t('Order') #{{ $order->order_number ?? $orderId }}</h1>
                                <span @class([
                                    'inline-flex items-center self-start rounded-md px-2 py-0.5 text-xs font-medium',
                                    'bg-surface-success-subtle text-surface-success-subtle-content' => orderStatusTone($order->status ?? null) === 'success',
                                    'bg-surface-info-subtle text-surface-info-subtle-content' => orderStatusTone($order->status ?? null) === 'info',
                                    'bg-surface-warning-subtle text-surface-warning-subtle-content' => orderStatusTone($order->status ?? null) === 'warning',
                                    'bg-surface-error-subtle text-surface-error-subtle-content' => orderStatusTone($order->status ?? null) === 'danger',
                                    'bg-surface text-body' => orderStatusTone($order->status ?? null) === 'neutral',
                                ])>{{ t(orderStatusLabel($order->status ?? null)) }}</span>
                            </div>
                            <div class="order__actions flex items-center gap-2 flex-wrap">
                                @if($store['order_documents_enabled'] ?? false)
                                    <button type="button" data-print-order class="inline-flex items-center gap-2 rounded-md border border-border-subtle bg-surface-card text-headings font-medium text-sm px-4 py-2 transition-colors hover:bg-surface-hover cursor-pointer">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V2h12v7" /><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" /><rect x="6" y="14" width="12" height="8" /></svg>
                                        @t('Print')
                                    </button>
                                @endif
                                @if(($order->status ?? '') === 'completed' && ($store['reorder_enabled'] ?? true))
                                    @storefrontForm('reorder', ['_params' => ['order' => $order->id], 'class' => 'inline'])
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-primary text-primary-content font-medium text-sm px-4 py-2 transition-colors hover:bg-primary-600 cursor-pointer">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" /><path d="M3 4v4h4" /></svg>
                                            @t('Re-order')
                                        </button>
                                    @endstorefrontForm
                                @endif
                            </div>
                        </div>

                        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 m-0 pt-1 border-t border-border-subtle">
                            <div class="flex flex-col gap-0.5 pt-3">
                                <dt class="text-xs font-medium text-body/70 uppercase tracking-wide">@t('Date')</dt>
                                <dd class="text-sm text-headings m-0">{{ formatDate($order->_created_at ?? null) }}</dd>
                            </div>
                            <div class="flex flex-col gap-0.5 pt-3">
                                <dt class="text-xs font-medium text-body/70 uppercase tracking-wide">@t('Status')</dt>
                                <dd class="text-sm text-headings m-0">{{ t(orderStatusLabel($order->status ?? null)) }}</dd>
                            </div>
                            <div class="flex flex-col gap-0.5 pt-3">
                                <dt class="text-xs font-medium text-body/70 uppercase tracking-wide">@t('Payment')</dt>
                                <dd class="text-sm text-headings m-0">{{ optional($order->billingMethod)->name ?: '—' }}</dd>
                            </div>
                            <div class="flex flex-col gap-0.5 pt-3">
                                <dt class="text-xs font-medium text-body/70 uppercase tracking-wide">@t('Shipping')</dt>
                                <dd class="text-sm text-headings m-0">{{ optional($order->shippingMethod)->name ?: '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Line items + totals --}}
                    <div class="bg-surface-card border border-border-subtle rounded-lg overflow-hidden">
                        <header class="px-5 py-4 border-b border-border-subtle">
                            <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Items')</h2>
                        </header>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left border-collapse">
                                <thead>
                                    <tr class="text-xs uppercase tracking-wide text-body/70 border-b border-border-subtle">
                                        <th class="px-5 py-3 font-medium">@t('SKU')</th>
                                        <th class="px-3 py-3 font-medium">@t('Product')</th>
                                        <th class="px-3 py-3 font-medium text-center">@t('Qty')</th>
                                        <th class="px-3 py-3 font-medium text-right">@t('Unit price')</th>
                                        <th class="px-5 py-3 font-medium text-right">@t('Total')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderItems as $item)
                                        <tr class="border-b border-border-subtle last:border-b-0">
                                            <td class="px-5 py-3 text-body whitespace-nowrap">
                                                {{ $item->sku ?? optional($item->product)->sku ?? $item->product_id }}
                                                @storefrontSlot('order.line.meta')
                                            </td>
                                            <td class="px-3 py-3 text-headings">
                                                <div class="flex items-center gap-3">
                                                    @if(data_get($item, 'product.resolved_main_media.media_url') ?? data_get($item, 'product.main_media.media_url'))
                                                        <span class="block h-10 w-10 shrink-0 overflow-hidden rounded bg-surface">
                                                            <img src="@storefrontImage(data_get($item, 'product.resolved_main_media.media_url') ?? data_get($item, 'product.main_media.media_url'), 80, 80, 85)" alt="{{ $item->name ?? optional($item->product)->name ?? '' }}" class="h-full w-full object-cover" loading="lazy">
                                                        </span>
                                                    @else
                                                        <span class="block h-10 w-10 shrink-0 overflow-hidden rounded">
                                                            @include('partials.__image-placeholder', ['size' => 'h-5 w-5'])
                                                        </span>
                                                    @endif
                                                    <span>{{ $item->name ?? optional($item->product)->name ?? '' }}</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 text-body text-center">{{ (int) ($item->quantity ?? 0) }}</td>
                                            <td class="px-3 py-3 text-body text-right whitespace-nowrap">{{ formatCurrency($item->price ?? 0) }}</td>
                                            <td class="px-5 py-3 text-headings font-medium text-right whitespace-nowrap">{{ formatCurrency($item->total_price ?? (($item->price ?? 0) * ($item->quantity ?? 0))) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="border-t border-border-subtle p-5 flex justify-end">
                            <dl class="grid grid-cols-[1fr_auto] gap-x-8 gap-y-1.5 m-0 w-full max-w-xs">
                                <dt class="text-sm text-body">@t('Subtotal')</dt>
                                <dd class="text-sm text-headings m-0 text-right">{{ formatCurrency($orderTotals['subtotal'] ?? 0) }}</dd>
                                @if(($orderTotals['discount'] ?? 0) > 0)
                                    <dt class="text-sm text-body">@t('Discount')</dt>
                                    <dd class="text-sm text-success m-0 text-right">-{{ formatCurrency($orderTotals['discount'] ?? 0) }}</dd>
                                @endif
                                <dt class="text-sm text-body">@t('Shipping')</dt>
                                <dd class="text-sm text-headings m-0 text-right">{{ formatCurrency($orderTotals['shipping'] ?? 0) }}</dd>
                                <dt class="text-sm text-body">@t('Tax')</dt>
                                <dd class="text-sm text-headings m-0 text-right">{{ formatCurrency($orderTotals['tax'] ?? 0) }}</dd>
                                <dt class="text-base font-semibold text-headings pt-2 mt-1 border-t border-border-subtle">@t('Total')</dt>
                                <dd class="text-base font-semibold text-headings m-0 text-right pt-2 mt-1 border-t border-border-subtle">{{ formatCurrency($orderTotals['grand_total'] ?? 0) }}</dd>
                            </dl>
                        </div>
                    </div>

                    {{-- Addresses --}}
                    @if($order->shippingAddress || $order->billingAddress)
                        <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if($order->shippingAddress)
                                <div class="p-5 bg-surface-card border border-border-subtle rounded-lg">
                                    <h3 class="text-xs font-medium text-body/70 uppercase tracking-wide m-0 mb-2">@t('Shipping address')</h3>
                                    <address class="text-sm text-headings not-italic leading-relaxed">
                                        @if($order->shippingAddress->company_name){{ $order->shippingAddress->company_name }}<br>@endif
                                        {{ $order->shippingAddress->name }}<br>
                                        {{ $order->shippingAddress->address_line_1 }}<br>
                                        @if($order->shippingAddress->address_line_2){{ $order->shippingAddress->address_line_2 }}<br>@endif
                                        {{ trim($order->shippingAddress->zip . ' ' . $order->shippingAddress->city) }}<br>
                                        {{ $order->shippingAddress->country }}
                                    </address>
                                </div>
                            @endif
                            @if($order->billingAddress)
                                <div class="p-5 bg-surface-card border border-border-subtle rounded-lg">
                                    <h3 class="text-xs font-medium text-body/70 uppercase tracking-wide m-0 mb-2">@t('Billing address')</h3>
                                    <address class="text-sm text-headings not-italic leading-relaxed">
                                        @if($order->billingAddress->company_name){{ $order->billingAddress->company_name }}<br>@endif
                                        {{ $order->billingAddress->name }}<br>
                                        {{ $order->billingAddress->address_line_1 }}<br>
                                        @if($order->billingAddress->address_line_2){{ $order->billingAddress->address_line_2 }}<br>@endif
                                        {{ trim($order->billingAddress->zip . ' ' . $order->billingAddress->city) }}<br>
                                        {{ $order->billingAddress->country }}
                                    </address>
                                </div>
                            @endif
                        </section>
                    @endif

                    {{-- Order note --}}
                    @if(!empty($order->order_note))
                        <section class="p-5 bg-surface-card border border-border-subtle rounded-lg flex flex-col gap-2">
                            <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Your comment')</h2>
                            <p class="text-sm text-body m-0 whitespace-pre-line">{{ $order->order_note }}</p>
                        </section>
                    @endif
                @endif
            </div>
        </div>
    </div>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection

@push('scripts')
    <script type="module">
        // Print order documents: open the browser print dialog (Save as PDF).
        for (const btn of document.querySelectorAll('[data-print-order]')) {
            btn.addEventListener('click', () => window.print());
        }
    </script>
@endpush
