@extends('layouts.shop')

@section('title', t('My account'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="page page--account flex flex-col gap-5">
        <div class="flex flex-col md:flex-row gap-6">
            @include('partials.account-nav', ['active' => 'dashboard'])

            <div class="flex-1 flex flex-col gap-5 min-w-0">
                {{-- Stat cards --}}
                <section class="account__stats grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    <div class="account__stat flex flex-col p-4 bg-surface-card border border-border-subtle rounded-lg">
                        <span class="flex items-center justify-center size-9 rounded-md bg-primary-subtle text-primary mb-3">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 3h6a1 1 0 0 1 1 1v1h2a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h2V4a1 1 0 0 1 1-1z" /><path d="M9 12h6M9 16h4" />
                            </svg>
                        </span>
                        <span class="text-xl font-semibold text-headings leading-tight">{{ (int) ($accountOverview['orders_count'] ?? 0) }}</span>
                        <span class="text-xs font-medium text-body mt-1">@t('Total orders')</span>
                        <span class="text-xs text-body/70 mt-0.5">{{ (int) ($accountOverview['open_orders'] ?? 0) }} @t('open')</span>
                    </div>

                    <div class="account__stat flex flex-col p-4 bg-surface-card border border-border-subtle rounded-lg">
                        <span class="flex items-center justify-center size-9 rounded-md bg-primary-subtle text-primary mb-3">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="6" width="18" height="13" rx="2" /><path d="M3 10h18" /><path d="M16 14h2" />
                            </svg>
                        </span>
                        <span class="text-xl font-semibold text-headings leading-tight">{{ formatCurrency($accountOverview['lifetime_spend'] ?? 0) }}</span>
                        <span class="text-xs font-medium text-body mt-1">@t('Lifetime spend')</span>
                        <span class="text-xs text-body/70 mt-0.5">@t('Across all orders')</span>
                    </div>

                    <div class="account__stat flex flex-col p-4 bg-surface-card border border-border-subtle rounded-lg">
                        <span class="flex items-center justify-center size-9 rounded-md bg-primary-subtle text-primary mb-3">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="9" cy="20" r="1.4" /><circle cx="18" cy="20" r="1.4" /><path d="M2 3h3l2.4 12.2a1 1 0 0 0 1 .8h8.8a1 1 0 0 0 1-.8L21 7H6" />
                            </svg>
                        </span>
                        <span class="text-xl font-semibold text-headings leading-tight">{{ (int) ($cartCount ?? 0) }}</span>
                        <span class="text-xs font-medium text-body mt-1">@t('Items in cart')</span>
                        <span class="text-xs text-body/70 mt-0.5">{{ (int) ($cartCount ?? 0) > 0 ? t('In your cart') : t('Cart is empty') }}</span>
                    </div>

                    <div class="account__stat flex flex-col p-4 bg-surface-card border border-border-subtle rounded-lg">
                        <span class="flex items-center justify-center size-9 rounded-md bg-primary-subtle text-primary mb-3">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 20s-7-4.5-7-9.5A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 7 3.5C19 15.5 12 20 12 20z" />
                            </svg>
                        </span>
                        <span class="text-xl font-semibold text-headings leading-tight">{{ (int) ($accountOverview['favorites_count'] ?? 0) }}</span>
                        <span class="text-xs font-medium text-body mt-1">@t('Saved products')</span>
                        <span class="text-xs text-body/70 mt-0.5">@t('Across all lists')</span>
                    </div>
                </section>

                {{-- Recent orders + quick actions --}}
                <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2 flex flex-col bg-surface-card border border-border-subtle rounded-lg overflow-hidden">
                        <header class="flex items-center justify-between gap-3 px-4 py-3.5 border-b border-border-subtle">
                            <div class="flex flex-col">
                                <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Recent orders')</h2>
                                <span class="text-xs text-body/70">@t('Your latest orders')</span>
                            </div>
                            <a href="@routeUrl('store.account.orders')" class="inline-flex items-center gap-1 text-sm font-medium text-primary hover:text-primary-600 no-underline">
                                @t('View all')
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6" /></svg>
                            </a>
                        </header>

                        @if($recentOrders->isEmpty())
                            <div class="p-4">
                                @include('components.empty-state', ['title' => t('No orders yet'), 'message' => t('Once you place an order it will show up here.')])
                            </div>
                        @else
                            <ul class="flex flex-col list-none m-0 p-0">
                                @foreach($recentOrders as $order)
                                    <li class="border-b border-border-subtle last:border-b-0 flex items-stretch">
                                        <a href="@routeUrl('store.account.order', ['order' => $order->id ?? ''])"
                                           class="flex items-center gap-3 px-4 py-3 no-underline hover:bg-surface-hover transition-colors flex-1 min-w-0">
                                            <span class="flex items-center justify-center size-9 rounded-md bg-surface text-body flex-shrink-0">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M9 3h6a1 1 0 0 1 1 1v1h2a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h2V4a1 1 0 0 1 1-1z" /><path d="M9 12h6M9 16h4" />
                                                </svg>
                                            </span>
                                            <span class="flex flex-col min-w-0 flex-1">
                                                <span class="text-sm font-semibold text-headings truncate">#{{ $order->order_number ?? $order->id }}</span>
                                                <span class="text-xs text-body/70 truncate">{{ formatDate($order->_created_at ?? null) }} · {{ (int) ($order->orderLineItems?->count() ?? 0) }} @t('items')</span>
                                            </span>
                                            <span @class([
                                                'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium flex-shrink-0',
                                                'bg-surface-success-subtle text-surface-success-subtle-content' => orderStatusTone($order->status ?? null) === 'success',
                                                'bg-surface-info-subtle text-surface-info-subtle-content' => orderStatusTone($order->status ?? null) === 'info',
                                                'bg-surface-warning-subtle text-surface-warning-subtle-content' => orderStatusTone($order->status ?? null) === 'warning',
                                                'bg-surface-error-subtle text-surface-error-subtle-content' => orderStatusTone($order->status ?? null) === 'danger',
                                                'bg-surface text-body' => orderStatusTone($order->status ?? null) === 'neutral',
                                            ])>{{ t(orderStatusLabel($order->status ?? null)) }}</span>
                                            <span class="text-sm font-semibold text-headings text-right flex-shrink-0">{{ formatCurrency($order->total_price ?? 0) }}</span>
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
                        @endif
                    </div>

                    @if($store['reorder_enabled'] ?? true)
                        <div class="flex flex-col bg-surface-card border border-border-subtle rounded-lg p-4">
                            <h2 class="font-primary text-base font-semibold text-headings m-0 mb-3">@t('Quick actions')</h2>
                            <a href="@routeUrl('store.account.orders')" class="flex items-center gap-3 p-3 border border-border-subtle rounded-md text-headings no-underline hover:bg-surface-hover hover:border-primary-subtle-stroke transition-colors">
                                <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8" /><path d="M3 4v4h4" /></svg>
                                <span class="text-xs font-medium">@t('Reorder')</span>
                            </a>
                        </div>
                    @endif
                </section>

                @if($isAgent ?? false)
                    <section class="account__agent flex flex-col gap-2 p-4 bg-surface-card border border-border-subtle rounded-lg">
                        <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Sales agent')</h2>
                        <p class="text-sm text-body m-0">@t('Place orders on behalf of one of your customers.')</p>
                        <a href="@routeUrl('store.customer-selection')" class="inline-flex items-center justify-center self-start rounded-md bg-primary text-primary-content font-medium text-sm px-4 py-2 mt-1 no-underline transition-colors hover:bg-primary-600">
                            @t('Select customer')
                        </a>
                    </section>
                @endif
            </div>
        </div>
    </div>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
