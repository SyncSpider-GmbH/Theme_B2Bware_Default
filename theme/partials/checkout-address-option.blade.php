{{--
  Selectable address card in the checkout billing / delivery pickers.

  Expects:
    - $address    address model (company- or customer-owned)
    - $field      'billing_address_id' | 'shipping_address_id'
    - $selectedId the pre-checked address id resolved by the controller
    - $required   whether the radio is required

  Pre-selection compares against $selectedId. The platform also collapses
  $address->default down to that same entry, so pre-checking on either works.
--}}
<label class="checkout__address relative flex gap-3 p-4 border border-border-subtle rounded-lg cursor-pointer transition-colors hover:border-primary/60 has-[:checked]:border-primary has-[:checked]:bg-primary-subtle">
    <input type="radio" name="{{ $field }}" value="{{ $address->id }}" class="mt-0.5 accent-primary shrink-0" @checked(old($field, $selectedId) == $address->id) @if($required ?? false) required @endif>
    <span class="flex flex-col gap-0.5 min-w-0">
        <span class="flex items-center gap-2 flex-wrap">
            <span class="font-medium text-headings text-sm">{{ $address->name }}</span>
            @if(data_get($address, 'type') === 'company')
                <span class="inline-flex items-center rounded-full border border-border-subtle bg-surface-page text-body text-xs font-medium px-2 py-0.5">@t('Company')</span>
            @endif
            @if($address->default)
                <span class="inline-flex items-center rounded-full bg-primary-subtle text-primary text-xs font-medium px-2 py-0.5">@t('Default')</span>
            @endif
        </span>
        @if($address->first_name || $address->last_name)
            <span class="text-sm text-body">{{ trim($address->first_name . ' ' . $address->last_name) }}</span>
        @endif
        @if($address->company_name)
            <span class="text-sm text-body">{{ $address->company_name }}</span>
        @endif
        <span class="text-sm text-body">{{ $address->address_line_1 }}@if($address->address_line_2), {{ $address->address_line_2 }}@endif</span>
        <span class="text-sm text-body">{{ trim($address->zip . ' ' . $address->city) }}@if($address->country), {{ $address->country }}@endif</span>
        @if($address->phone)
            <span class="text-sm text-body">{{ $address->phone }}</span>
        @endif
    </span>
</label>
