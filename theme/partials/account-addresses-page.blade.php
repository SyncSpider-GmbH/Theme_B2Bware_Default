{{--
    Shared template for both shipping + billing address index pages.
    Renders the saved-address cards with modal add/edit, set-default
    and delete, the Shipping|Billing tabs, and a read-only company
    addresses section.

    Required vars:
      $pageTitle, $tab ('shipping'|'billing'), $addresses,
      $defaultAddress, $companyAddresses, $shippingCount, $billingCount,
      $createForm, $updateForm, $deleteForm, $defaultForm
--}}
<div class="page page--account page--account-addresses flex flex-col gap-5">
    <div class="flex flex-col md:flex-row gap-6">
        @include('partials.account-nav', ['active' => 'addresses'])

        <div class="flex-1 min-w-0 flex flex-col gap-4">
            @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])
            @storefrontError('address')

            {{-- Header --}}
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div class="flex flex-col gap-0.5">
                    <h1 class="font-primary text-xl font-semibold text-headings m-0">@t('Saved addresses')</h1>
                    <p class="text-sm text-body/70 m-0">@t('Manage where your orders are shipped and invoiced.')</p>
                </div>
                <button type="button" data-storefront-modal-open="address-add"
                        class="inline-flex items-center gap-2 rounded-md bg-primary text-primary-content font-medium text-sm px-4 py-2 cursor-pointer hover:bg-primary-600 transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
                    {{ ($tab ?? 'shipping') === 'billing' ? t('Add billing') : t('Add shipping') }}
                </button>
            </div>

            {{-- Tabs --}}
            <div class="flex gap-1 border-b border-border-subtle -mb-px">
                <a href="@routeUrl('store.account.shipping-addresses')"
                   @class([
                       'inline-flex items-center gap-2 px-4 py-2.5 text-sm border-b-2 no-underline transition-colors',
                       'border-primary text-primary font-semibold' => ($tab ?? 'shipping') === 'shipping',
                       'border-transparent text-body font-medium hover:text-headings' => ($tab ?? 'shipping') !== 'shipping',
                   ])>
                    @t('Shipping addresses')
                    <span class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-surface-card text-xs text-body">{{ (int) ($shippingCount ?? 0) }}</span>
                </a>
                <a href="@routeUrl('store.account.billing-addresses')"
                   @class([
                       'inline-flex items-center gap-2 px-4 py-2.5 text-sm border-b-2 no-underline transition-colors',
                       'border-primary text-primary font-semibold' => ($tab ?? 'shipping') === 'billing',
                       'border-transparent text-body font-medium hover:text-headings' => ($tab ?? 'shipping') !== 'billing',
                   ])>
                    @t('Billing addresses')
                    <span class="inline-flex items-center justify-center min-w-5 h-5 px-1.5 rounded-full bg-surface-card text-xs text-body">{{ (int) ($billingCount ?? 0) }}</span>
                </a>
            </div>

            {{-- My addresses --}}
            @if($addresses->isEmpty())
                @include('components.empty-state', [
                    'title' => t('No saved addresses yet'),
                    'message' => t('Add an address to speed up checkout.'),
                ])
            @else
                <ul class="addresses grid grid-cols-1 lg:grid-cols-2 gap-4 list-none m-0 p-0">
                    @foreach($addresses as $address)
                        <li @class([
                            'address p-4 bg-surface-card border rounded-lg flex flex-col',
                            'border-border-subtle' => optional($defaultAddress)->id !== ($address->id ?? null),
                            'address--default border-primary' => optional($defaultAddress)->id === ($address->id ?? null),
                        ])>
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <strong class="text-sm font-semibold text-headings">{{ $address->name }}</strong>
                                    @if(optional($defaultAddress)->id === ($address->id ?? null))
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-primary-subtle text-primary">@t('Default')</span>
                                    @endif
                                </div>
                                <button type="button" data-storefront-modal-open="address-edit-{{ $address->id }}"
                                        class="inline-flex h-8 w-8 items-center justify-center text-body hover:text-primary cursor-pointer bg-transparent border-0" aria-label="@t('Edit address')">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9" /><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z" /></svg>
                                </button>
                            </div>

                            <div class="flex flex-col gap-0.5 text-sm text-body mt-2">
                                @if($address->company_name)<span>{{ $address->company_name }}</span>@endif
                                @if(trim(($address->first_name ?? '') . ' ' . ($address->last_name ?? '')) !== '')<span>{{ trim(($address->first_name ?? '') . ' ' . ($address->last_name ?? '')) }}</span>@endif
                                <span>{{ $address->address_line_1 }}</span>
                                @if($address->address_line_2)<span>{{ $address->address_line_2 }}</span>@endif
                                <span>{{ $address->zip }} {{ $address->city }}{{ $address->state ? ', ' . $address->state : '' }}</span>
                                <span>{{ $address->country }}</span>
                                @if($address->phone)<span>{{ $address->phone }}</span>@endif
                                @if(($tab ?? 'shipping') === 'billing' && $address->vat_number)<span class="text-body/70">@t('VAT'): {{ $address->vat_number }}</span>@endif
                            </div>

                            <div class="flex items-center gap-4 mt-auto pt-3 border-t border-border-subtle">
                                @if(optional($defaultAddress)->id !== ($address->id ?? null))
                                    @storefrontForm($defaultForm, ['_params' => ['id' => $address->id], 'class' => 'inline'])
                                        <button type="submit" class="text-sm font-medium text-primary hover:text-primary-600 bg-transparent border-0 px-2 py-1 cursor-pointer">
                                            @t('Set as default')
                                        </button>
                                    @endstorefrontForm
                                @endif
                                @storefrontForm($deleteForm, ['_params' => ['id' => $address->id], 'class' => 'inline'])
                                    <button type="submit" class="text-sm font-medium text-error hover:text-error/80 bg-transparent border-0 px-2 py-1 cursor-pointer"
                                            data-confirm="{{ t('Delete this address?') }}">
                                        @t('Delete')
                                    </button>
                                @endstorefrontForm
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- Company addresses (read-only) --}}
            @if(!$companyAddresses->isEmpty())
                <div class="flex flex-col gap-3 mt-3">
                    <div class="flex flex-col gap-0.5">
                        <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Company addresses')</h2>
                        <p class="text-xs text-body/70 m-0">@t('Shared addresses managed by your company.')</p>
                    </div>
                    <ul class="grid grid-cols-1 lg:grid-cols-2 gap-4 list-none m-0 p-0">
                        @foreach($companyAddresses as $address)
                            <li class="address p-4 bg-surface-card border border-border-subtle rounded-lg flex flex-col">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <strong class="text-sm font-semibold text-headings">{{ $address->name }}</strong>
                                    @if($address->default)
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-primary-subtle text-primary">@t('Default')</span>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-0.5 text-sm text-body mt-2">
                                    @if($address->company_name)<span>{{ $address->company_name }}</span>@endif
                                    <span>{{ $address->address_line_1 }}</span>
                                    @if($address->address_line_2)<span>{{ $address->address_line_2 }}</span>@endif
                                    <span>{{ $address->zip }} {{ $address->city }}{{ $address->state ? ', ' . $address->state : '' }}</span>
                                    <span>{{ $address->country }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Add address modal --}}
<dialog id="address-add" class="modal bg-surface-card text-body rounded-lg shadow-xl p-0 border-0 backdrop:bg-backdrop/40 max-w-xl">
    <div class="flex flex-col gap-4 p-6 min-w-80 max-h-[85vh] overflow-y-auto">
        <header class="flex items-center justify-between gap-3 pb-3 border-b border-border-subtle">
            <h2 class="font-primary text-lg text-headings m-0">{{ ($tab ?? 'shipping') === 'billing' ? t('Add billing address') : t('Add shipping address') }}</h2>
            <button type="button" class="inline-flex h-8 w-8 items-center justify-center text-2xl leading-none text-body hover:text-headings cursor-pointer bg-transparent border-0" data-modal-close aria-label="@t('Close')">&times;</button>
        </header>
        @storefrontForm($createForm, ['class' => 'flex flex-col gap-4'])
            @include('partials.account-address-form', ['address' => null, 'prefix' => ($tab ?? 'shipping')])
            <div class="flex items-center justify-end gap-2">
                <button type="button" data-modal-close class="inline-flex items-center justify-center rounded-md border border-border-subtle bg-transparent text-headings px-4 py-2 cursor-pointer hover:bg-surface-hover text-sm">@t('Cancel')</button>
                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-primary text-primary-content font-medium px-4 py-2 cursor-pointer hover:bg-primary-600 text-sm">@t('Save address')</button>
            </div>
        @endstorefrontForm
    </div>
</dialog>

{{-- Edit address modals --}}
@foreach($addresses as $address)
    <dialog id="address-edit-{{ $address->id }}" class="modal bg-surface-card text-body rounded-lg shadow-xl p-0 border-0 backdrop:bg-backdrop/40 max-w-xl">
        <div class="flex flex-col gap-4 p-6 min-w-80 max-h-[85vh] overflow-y-auto">
            <header class="flex items-center justify-between gap-3 pb-3 border-b border-border-subtle">
                <h2 class="font-primary text-lg text-headings m-0">@t('Edit address')</h2>
                <button type="button" class="inline-flex h-8 w-8 items-center justify-center text-2xl leading-none text-body hover:text-headings cursor-pointer bg-transparent border-0" data-modal-close aria-label="@t('Close')">&times;</button>
            </header>
            @storefrontForm($updateForm, ['_params' => ['id' => $address->id], 'class' => 'flex flex-col gap-4'])
                @include('partials.account-address-form', ['address' => $address, 'prefix' => ($tab ?? 'shipping')])
                <div class="flex items-center justify-end gap-2">
                    <button type="button" data-modal-close class="inline-flex items-center justify-center rounded-md border border-border-subtle bg-transparent text-headings px-4 py-2 cursor-pointer hover:bg-surface-hover text-sm">@t('Cancel')</button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-primary text-primary-content font-medium px-4 py-2 cursor-pointer hover:bg-primary-600 text-sm">@t('Update address')</button>
                </div>
            @endstorefrontForm
        </div>
    </dialog>
@endforeach

<script type="module">
    // Confirm before destructive submits.
    for (const el of document.querySelectorAll('[data-confirm]')) {
        el.addEventListener('click', (event) => {
            if (!window.confirm(el.dataset.confirm)) {
                event.preventDefault();
            }
        });
    }
    // Re-open a modal whose form failed server-side validation so the
    // visitor sees the inline errors after the redirect.
    for (const dialog of document.querySelectorAll('dialog')) {
        if (dialog.querySelector('.storefront-form-error')) {
            dialog.showModal?.();
            break;
        }
    }
</script>
