@if($isImpersonating)
    <div class="impersonation-banner w-full bg-surface-card text-body text-sm border-b border-border-subtle">
        <div class="max-w-desktop mx-auto px-4 py-2 flex items-center justify-between gap-4 flex-wrap">
            <span>
                @t('You are shopping on behalf of a customer.')
                @if($me)
                    <strong class="text-headings font-medium">{{ trim(($me->first_name ?? '') . ' ' . ($me->last_name ?? '')) ?: ($me->email ?? '') }}</strong>
                @endif
            </span>
            <div class="flex gap-2">
                <a href="@routeUrl('store.customer-selection')" class="px-3 py-1 rounded border border-border-subtle bg-transparent text-headings no-underline hover:bg-surface-hover">
                    @t('Switch customer')
                </a>
                @storefrontForm('impersonation-leave', ['class' => 'inline'])
                    <button type="submit" class="px-3 py-1 rounded border border-border-subtle bg-transparent text-headings font-medium cursor-pointer hover:bg-surface-hover">
                        @t('Stop impersonating')
                    </button>
                @endstorefrontForm
            </div>
        </div>
    </div>
@endif
