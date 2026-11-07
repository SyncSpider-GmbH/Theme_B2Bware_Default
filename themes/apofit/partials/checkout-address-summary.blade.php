{{--
  Compact address row inside the checkout "Manage addresses" modals.

  Expects:
    - $address     address model (company- or customer-owned)
    - $defaultForm form type for the "Set as default" action
    - $locale      active locale, for the relative redirect back to checkout

  Company addresses are read-only here, matching the account address pages:
  they are managed by the company, not by the individual customer.
--}}
<li class="flex items-start justify-between gap-3 p-3 border border-border-subtle rounded-lg">
    <div class="flex flex-col gap-0.5 text-sm min-w-0">
        <span class="flex items-center gap-2 flex-wrap">
            <span class="font-medium text-headings">{{ $address->name }}</span>
            @if(data_get($address, 'type') === 'company')
                <span class="inline-flex items-center rounded-full border border-border-subtle bg-surface-page text-body text-xs font-medium px-2 py-0.5">@t('Company')</span>
            @endif
            @if($address->default)
                <span class="inline-flex items-center rounded-full bg-primary-subtle text-primary text-xs font-medium px-2 py-0.5">@t('Default')</span>
            @endif
        </span>
        <span class="text-body">{{ $address->address_line_1 }}, {{ trim($address->zip . ' ' . $address->city) }}</span>
    </div>
    @if(!$address->default && data_get($address, 'type') !== 'company')
        @storefrontForm($defaultForm, ['_params' => ['id' => $address->id], 'class' => 'shrink-0'])
            <input type="hidden" name="redirect" value="/{{ $locale }}/checkout">
            <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-border-subtle text-body font-medium px-3 py-1.5 text-sm transition-colors hover:border-primary hover:text-primary cursor-pointer">@t('Set as default')</button>
        @endstorefrontForm
    @endif
</li>
