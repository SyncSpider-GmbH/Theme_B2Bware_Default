@if($isImpersonating)
    <div class="impersonation-banner w-full bg-warning text-on-warning text-sm">
        <div class="max-w-desktop mx-auto px-4 py-2 flex items-center justify-between gap-4 flex-wrap">
            <span>
                @t('You are shopping on behalf of a customer.')
                @if($me)
                    <strong>{{ trim(($me->first_name ?? '') . ' ' . ($me->last_name ?? '')) ?: ($me->email ?? '') }}</strong>
                @endif
            </span>
            <div class="flex gap-2">
                <a href="@routeUrl('store.customer-selection')" class="px-3 py-1 rounded border border-on-warning/40 bg-transparent hover:bg-on-warning/10">
                    @t('Switch customer')
                </a>
                @storefrontForm('impersonation-leave', ['class' => 'inline'])
                    <button type="submit" class="px-3 py-1 rounded bg-on-warning text-warning font-medium cursor-pointer">
                        @t('Stop impersonating')
                    </button>
                @endstorefrontForm
            </div>
        </div>
    </div>
@endif
