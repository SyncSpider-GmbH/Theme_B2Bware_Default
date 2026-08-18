{{--
    Cart price-rule reward progress.

    One progress bar per eligible active cart price rule toward its threshold,
    mirroring the Nexus storefront cart rewards. Data comes from
    StorefrontController@cart -> StorefrontCart::ruleProgress() (RuleHub
    getProgress). Visibility is decided per rule by its own `show_progress_bar`
    config (skipped below), so an empty/absent $cartProgress renders nothing.

    Free-shipping rules are intentionally NOT rendered here — the sidebar's
    "X away from free delivery" banner ($freeDelivery) already surfaces the
    nearest one, so this section shows only discount-type rewards and never
    duplicates it. The outer guard uses collect()->contains() so the section
    (and its heading) is omitted entirely when no discount reward qualifies.

    Each $reward entry carries: rule_name, action_type, action_amount,
    free_shipping, met (bool), blocked (bool), show_progress_bar (bool) and a
    `thresholds` list (each with current_value/target_value/remaining/
    percentage/met and the cart `attribute` being tracked).
--}}
@if(count($cartProgress ?? []) && collect($cartProgress)->contains(fn ($r) => ($r['free_shipping'] ?? 'no') === 'no' && !($r['blocked'] ?? false) && ($r['show_progress_bar'] ?? true) !== false && !empty($r['thresholds'])))
    <section class="cart__rewards flex flex-col gap-3 p-5 bg-surface-card border border-border-subtle rounded-lg" aria-label="{{ t('Rewards') }}">
        <h2 class="cart__rewards-title font-primary text-lg font-semibold text-headings m-0">@t('Rewards')</h2>

        @foreach($cartProgress as $reward)
            @continue(($reward['free_shipping'] ?? 'no') !== 'no' || ($reward['blocked'] ?? false) || ($reward['show_progress_bar'] ?? true) === false || empty($reward['thresholds']))

            @foreach($reward['thresholds'] as $threshold)
                <div class="cart__reward flex flex-col gap-2 p-3 rounded-lg border border-border-subtle">
                    <div class="cart__reward-head flex items-center justify-between gap-2">
                        <span class="cart__reward-label text-sm font-semibold text-headings">
                            @if(($reward['action_type'] ?? '') === 'by_percent')
                                {{ formatNumber($reward['action_amount'] ?? 0) }}% @t('off')
                            @elseif(in_array($reward['action_type'] ?? '', ['by_fixed', 'to_fixed', 'per_item_fixed']))
                                {{ formatCurrency($reward['action_amount'] ?? 0) }} @t('off')
                            @else
                                {{ $reward['rule_name'] ?? t('Discount') }}
                            @endif
                        </span>

                        @if($reward['met'] ?? false)
                            <span class="cart__reward-status text-sm font-medium text-success">@t('Unlocked')</span>
                        @endif
                    </div>

                    <div class="cart__reward-track h-2 w-full rounded-full bg-surface-page overflow-hidden">
                        <div
                            class="cart__reward-fill h-full rounded-full {{ ($reward['met'] ?? false) ? 'bg-success' : 'bg-primary' }} transition-all duration-300"
                            style="width: {{ max(0, min(100, (float) ($threshold['percentage'] ?? 0))) }}%"
                        ></div>
                    </div>

                    <div class="cart__reward-meta flex items-center justify-between gap-2 text-xs text-body">
                        @if($reward['met'] ?? false)
                            <span class="cart__reward-message">@t('You have unlocked this reward.')</span>
                        @else
                            <span class="cart__reward-message">
                                @t('Add')
                                @if(in_array($threshold['attribute'] ?? '', ['subtotal_price', 'total_price', 'price']))
                                    {{ formatCurrency($threshold['remaining'] ?? 0) }}
                                @else
                                    {{ formatNumber($threshold['remaining'] ?? 0) }}
                                @endif
                                @t('to unlock')
                            </span>
                        @endif

                        <span class="cart__reward-values">
                            @if(in_array($threshold['attribute'] ?? '', ['subtotal_price', 'total_price', 'price']))
                                {{ formatCurrency($threshold['current_value'] ?? 0) }} / {{ formatCurrency($threshold['target_value'] ?? 0) }}
                            @else
                                {{ formatNumber($threshold['current_value'] ?? 0) }} / {{ formatNumber($threshold['target_value'] ?? 0) }}
                            @endif
                        </span>
                    </div>
                </div>
            @endforeach
        @endforeach
    </section>
@endif

