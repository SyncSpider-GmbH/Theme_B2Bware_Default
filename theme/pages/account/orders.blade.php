@extends('layouts.shop')

@section('title', t('Orders'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="page page--account page--account-orders flex flex-col gap-5">
        <div class="flex flex-col md:flex-row gap-6">
            @include('partials.account-nav', ['active' => 'orders'])

            <div class="flex-1 flex flex-col gap-4 min-w-0">
                @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])
                @include('components.banner', ['type' => 'info', 'message' => $messages['info'] ?? null])

                @if($orders->total() === 0)
                    @include('components.empty-state', [
                        'title' => t('No orders yet'),
                        'message' => t('Your past orders will appear here once you place one.'),
                    ])
                @else
                    <div class="bg-surface-card border border-border-subtle rounded-lg overflow-hidden">
                        <header class="px-5 py-4 border-b border-border-subtle">
                            <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Order history')</h2>
                            <p class="text-xs text-body/70 m-0 mt-0.5">@t('Review your past orders.')</p>
                        </header>

                        {{-- Desktop table --}}
                        <div class="hidden md:block overflow-x-auto">
                            <table class="orders w-full text-sm text-left border-collapse">
                                <thead>
                                    <tr class="text-xs uppercase tracking-wide text-body/70 border-b border-border-subtle">
                                        <th class="px-5 py-3 font-medium">@t('Order ID')</th>
                                        <th class="px-3 py-3 font-medium whitespace-nowrap">@t('Date')</th>
                                        <th class="px-3 py-3 font-medium text-center">@t('Items')</th>
                                        <th class="px-3 py-3 font-medium">@t('Shipping')</th>
                                        <th class="px-3 py-3 font-medium">@t('Billing')</th>
                                        <th class="px-3 py-3 font-medium">@t('Status')</th>
                                        <th class="px-3 py-3 font-medium text-right">@t('Total')</th>
                                        <th class="px-5 py-3 font-medium text-right">@t('Actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr class="border-b border-border-subtle last:border-b-0 hover:bg-surface-hover transition-colors">
                                            <td class="px-5 py-3">
                                                <a href="@routeUrl('store.account.order', ['order' => $order->id])" class="font-semibold text-headings hover:text-primary no-underline">#{{ $order->order_number ?? $order->id }}</a>
                                            </td>
                                            <td class="px-3 py-3 text-body whitespace-nowrap">{{ formatDate($order->_created_at ?? null) }}</td>
                                            <td class="px-3 py-3 text-body text-center">{{ (int) ($order->orderLineItems?->count() ?? 0) }}</td>
                                            <td class="px-3 py-3 text-body">{{ optional($order->shippingMethod)->name ?: '/' }}</td>
                                            <td class="px-3 py-3 text-body">{{ optional($order->billingMethod)->name ?: '/' }}</td>
                                            <td class="px-3 py-3">
                                                <span @class([
                                                    'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium',
                                                    'bg-surface-success-subtle text-surface-success-subtle-content' => orderStatusTone($order->status ?? null) === 'success',
                                                    'bg-surface-info-subtle text-surface-info-subtle-content' => orderStatusTone($order->status ?? null) === 'info',
                                                    'bg-surface-warning-subtle text-surface-warning-subtle-content' => orderStatusTone($order->status ?? null) === 'warning',
                                                    'bg-surface-error-subtle text-surface-error-subtle-content' => orderStatusTone($order->status ?? null) === 'danger',
                                                    'bg-surface text-body' => orderStatusTone($order->status ?? null) === 'neutral',
                                                ])>{{ t(orderStatusLabel($order->status ?? null)) }}</span>
                                            </td>
                                            <td class="px-3 py-3 text-right font-semibold text-headings whitespace-nowrap">{{ formatCurrency($order->total_price ?? 0) }}</td>
                                            <td class="px-5 py-3 text-right">
                                                <div class="inline-flex items-center gap-1 justify-end">
                                                    @if(($order->status ?? '') === 'completed' && ($store['reorder_enabled'] ?? true))
                                                        @storefrontForm('reorder', ['_params' => ['order' => $order->id], 'class' => 'inline'])
                                                            <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded text-body/60 hover:text-primary hover:bg-surface-hover transition-colors" title="@t('Re-order')" aria-label="@t('Re-order')">
                                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" /><path d="M3 4v4h4" /></svg>
                                                            </button>
                                                        @endstorefrontForm
                                                    @endif
                                                    <a href="@routeUrl('store.account.order', ['order' => $order->id])" class="inline-flex h-8 w-8 items-center justify-center text-primary hover:text-primary-600" aria-label="@t('View order')">
                                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6" /></svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile stacked rows --}}
                        <ul class="md:hidden flex flex-col list-none m-0 p-0">
                            @foreach($orders as $order)
                                <li class="border-b border-border-subtle last:border-b-0 flex items-stretch">
                                    <a href="@routeUrl('store.account.order', ['order' => $order->id])" class="flex flex-col gap-2 p-4 no-underline hover:bg-surface-hover transition-colors flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="font-semibold text-headings">#{{ $order->order_number ?? $order->id }}</span>
                                            <span @class([
                                                'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium',
                                                'bg-surface-success-subtle text-surface-success-subtle-content' => orderStatusTone($order->status ?? null) === 'success',
                                                'bg-surface-info-subtle text-surface-info-subtle-content' => orderStatusTone($order->status ?? null) === 'info',
                                                'bg-surface-warning-subtle text-surface-warning-subtle-content' => orderStatusTone($order->status ?? null) === 'warning',
                                                'bg-surface-error-subtle text-surface-error-subtle-content' => orderStatusTone($order->status ?? null) === 'danger',
                                                'bg-surface text-body' => orderStatusTone($order->status ?? null) === 'neutral',
                                            ])>{{ t(orderStatusLabel($order->status ?? null)) }}</span>
                                        </div>
                                        <div class="flex items-center justify-between gap-3 text-xs text-body/70">
                                            <span>{{ formatDate($order->_created_at ?? null) }} · {{ (int) ($order->orderLineItems?->count() ?? 0) }} @t('items')</span>
                                            <span class="font-semibold text-headings text-sm">{{ formatCurrency($order->total_price ?? 0) }}</span>
                                        </div>
                                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-body/70">
                                            <span>@t('Shipping'): {{ optional($order->shippingMethod)->name ?: '/' }}</span>
                                            <span>@t('Billing'): {{ optional($order->billingMethod)->name ?: '/' }}</span>
                                        </div>
                                    </a>
                                    @if(($order->status ?? '') === 'completed' && ($store['reorder_enabled'] ?? true))
                                        <div class="flex items-center pr-3 pl-1 border-l border-border-subtle">
                                            @storefrontForm('reorder', ['_params' => ['order' => $order->id]])
                                                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded text-body/60 hover:text-primary hover:bg-surface-hover transition-colors" title="@t('Re-order')" aria-label="@t('Re-order')">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" /><path d="M3 4v4h4" /></svg>
                                                </button>
                                            @endstorefrontForm
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @include('components.pagination', ['paginator' => $orders])
                @endif
            </div>
        </div>
    </div>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
