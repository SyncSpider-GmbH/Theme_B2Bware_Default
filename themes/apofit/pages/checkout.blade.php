@extends('layouts.shop')

@section('title', t('Checkout'))

@section('breadcrumbs')
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ol class="flex flex-wrap gap-2 list-none m-0 p-0 text-sm text-body">
            <li class="breadcrumbs__item flex items-center gap-2">
                <a href="@routeUrl('store.home')" class="text-primary hover:text-primary-600">@t('Home')</a>
            </li>
            <li class="breadcrumbs__item flex items-center gap-2 before:content-['/'] before:text-border before:mr-1">
                <a href="@routeUrl('store.cart')" class="text-primary hover:text-primary-600">@t('Cart')</a>
            </li>
            <li class="breadcrumbs__item flex items-center gap-2 before:content-['/'] before:text-border before:mr-1 is-current text-headings">
                <span aria-current="page">@t('Checkout')</span>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="checkout apofit-checkout flex flex-col gap-5" data-checkout-root data-initial-step="@if($errors->hasAny(['shipping_method_id', 'billing_method_id', 'accept_terms', 'comment']))payment@elseaddress@endif">
        @section('checkout.header')
        <div class="checkout__head flex flex-wrap items-end justify-between gap-3">
            <div class="flex flex-col gap-1">
                <h1 class="font-primary text-2xl font-semibold text-headings m-0">@t('Checkout')</h1>
                <p class="text-sm text-body m-0">@t('Review your order details and confirm.')</p>
            </div>
            <a href="@routeUrl('store.cart')" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary-600 hover:no-underline">
                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" /></svg>
                @t('Back to cart')
            </a>
        </div>
        @show

        @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])

        @if(empty($cartItems) || (is_countable($cartItems) && count($cartItems) === 0))
            <div class="checkout__empty flex flex-col items-start gap-3 p-6 bg-surface-card border border-border-subtle rounded-lg">
                <p class="text-body m-0">@t('Your cart is empty.')</p>
                <a href="@routeUrl('store.products')" class="inline-flex items-center justify-center rounded-lg bg-primary text-primary-content font-medium px-4 py-2.5 transition-colors hover:bg-primary-600 hover:no-underline">@t('Continue shopping')</a>
            </div>
        @else
            @storefrontForm('checkout', ['class' => 'checkout__grid flex flex-col gap-5 desktop:flex-row desktop:items-start'])
                <input type="hidden" name="is_guest" value="{{ $isAuthenticated ? '0' : '1' }}">

                <div class="checkout__primary flex flex-col gap-5 min-w-0 desktop:flex-1">
                    <section class="checkout__progress flex items-center justify-between gap-3 rounded-lg border border-border-subtle bg-surface-card p-4 sm:p-5">
                        <div class="checkout__progress-step checkout__progress-step--active flex min-w-0 flex-1 flex-col items-center gap-1 text-center" data-checkout-step-indicator="address">
                            <span class="checkout__progress-badge inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary text-sm font-semibold text-primary-content">1</span>
                            <strong class="font-primary text-base text-headings">@t('Adresse')</strong>
                            <span class="text-xs text-body">@t('Lieferung und Rechnung')</span>
                        </div>
                        <div class="checkout__progress-line h-px flex-1 bg-border-subtle"></div>
                        <div class="checkout__progress-step flex min-w-0 flex-1 flex-col items-center gap-1 text-center text-body" data-checkout-step-indicator="payment">
                            <span class="checkout__progress-badge inline-flex h-8 w-8 items-center justify-center rounded-full border border-border-subtle bg-surface-card text-sm font-semibold text-border">2</span>
                            <strong class="font-primary text-base text-headings/60">@t('Abschluss')</strong>
                            <span class="text-xs text-body/70">@t('Versand- und Zahlungsart')</span>
                        </div>
                    </section>

                    <section class="checkout__mobile-summary flex flex-col gap-3 rounded-lg border border-border-subtle bg-surface-card p-0 desktop:hidden" data-checkout-mobile-summary>
                        <button
                            type="button"
                            class="checkout__summary-toggle flex w-full items-center justify-between gap-3 rounded-lg px-4 py-3 text-left text-headings"
                            data-checkout-summary-toggle
                            aria-expanded="false"
                            aria-controls="checkout-mobile-summary-panel"
                        >
                            <span class="flex min-w-0 items-center gap-2 text-sm font-medium">
                                <svg class="h-4 w-4 shrink-0 text-primary" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2.5 4A1.5 1.5 0 0 0 1 5.5V6h18v-.5A1.5 1.5 0 0 0 17.5 4h-15ZM19 8.5H1v6A1.5 1.5 0 0 0 2.5 16h15a1.5 1.5 0 0 0 1.5-1.5v-6ZM3 13.25a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75Zm4.75-.75a.75.75 0 0 0 0 1.5h3.5a.75.75 0 0 0 0-1.5h-3.5Z" /></svg>
                                <span class="truncate">@t('Bestellzusammenfassung')</span>
                            </span>
                            <span class="flex items-center gap-2 font-semibold">
                                <span data-checkout-total>{{ formatCurrency($cartPricing['grand_total'] ?? 0) }}</span>
                                <svg class="checkout__summary-chevron h-4 w-4 shrink-0 text-body" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08Z" clip-rule="evenodd" /></svg>
                            </span>
                        </button>
                        <div id="checkout-mobile-summary-panel" class="checkout__summary-panel hidden border-t border-border-subtle p-4" data-checkout-summary-panel>
                            @storefrontSection('checkout-summary')
                        </div>
                    </section>

                    <div class="checkout__flow flex flex-col gap-5">
                        <section class="checkout__panel checkout__panel--step1 flex flex-col gap-5 rounded-lg border border-border-subtle bg-surface-card p-5 sm:p-6">
                            <header class="checkout__panel-head flex items-center gap-2.5">
                                <span class="checkout__step inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-subtle text-xs font-semibold text-primary">1</span>
                                <div class="flex min-w-0 flex-col gap-0.5">
                                    <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Adresse')</h2>
                                    <p class="text-sm text-body m-0">@t('Lieferung und Rechnung')</p>
                                </div>
                            </header>

                            @if(!$isAuthenticated)
                                <p class="checkout__login-note text-sm text-body m-0">
                                    @t('Besitzen Sie bereits ein Kundenkonto?')
                                    <a href="@routeUrl('store.login')" class="text-primary font-medium hover:text-primary-600 hover:no-underline">@t('Klicken Sie hier, um sich anzumelden.')</a>
                                </p>
                            @else
                                <p class="checkout__login-note text-sm text-body m-0">{{ optional($customer ?? $me)->email ?? '' }}</p>
                            @endif

                            <div class="checkout__section flex flex-col gap-4">
                                <h3 class="font-primary text-sm font-semibold uppercase tracking-wide text-headings/70 m-0">@t('Kundendetails')</h3>
                                @if($isAuthenticated)
                                    <div class="rounded-lg border border-border-subtle bg-surface-muted px-4 py-3 text-sm text-body">
                                        {{ optional($customer ?? $me)->email ?? '' }}
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
                                            @t('Email')
                                            <input type="email" name="email" value="{{ old('email') }}" required class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                            @storefrontError('email')
                                        </label>
                                        <label class="flex flex-col gap-1 text-sm text-body">
                                            @t('Phone')
                                            <input type="text" name="phone" value="{{ old('phone') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                            @storefrontError('phone')
                                        </label>
                                        <label class="flex flex-col gap-1 text-sm text-body">
                                            @t('First name')
                                            <input type="text" name="first_name" value="{{ old('first_name') }}" required class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                            @storefrontError('first_name')
                                        </label>
                                        <label class="flex flex-col gap-1 text-sm text-body">
                                            @t('Last name')
                                            <input type="text" name="last_name" value="{{ old('last_name') }}" required class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                            @storefrontError('last_name')
                                        </label>
                                    </div>
                                @endif
                            </div>

                            <div class="checkout__section flex flex-col gap-4">
                                <h3 class="font-primary text-sm font-semibold uppercase tracking-wide text-headings/70 m-0">@t('Rechnungsadresse')</h3>
                                @if($isAuthenticated && isset($billingAddresses) && count($billingAddresses) > 0)
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        @foreach($billingAddresses as $address)
                                            @include('partials.checkout-address-option', [
                                                'address'    => $address,
                                                'field'      => 'billing_address_id',
                                                'selectedId' => $defaultBillingAddressId ?? null,
                                                'required'   => true,
                                            ])
                                        @endforeach
                                    </div>
                                    @storefrontError('billing_address_id')
                                    <button type="button" data-storefront-modal-open="billing-address-modal" class="checkout__manage inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary-600 cursor-pointer bg-transparent border-0 p-0 self-start">
                                        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" /><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" /></svg>
                                        @t('Manage addresses')
                                    </button>
                                @elseif($isAuthenticated)
                                    <div class="checkout__empty-addr flex flex-col items-start gap-3">
                                        <p class="text-sm text-body m-0">@t('You have no saved billing addresses yet.')</p>
                                        <button type="button" data-storefront-modal-open="billing-address-modal" class="inline-flex items-center justify-center rounded-lg border border-primary text-primary font-medium px-4 py-2 text-sm transition-colors hover:bg-primary-subtle cursor-pointer">@t('Add a billing address')</button>
                                    </div>
                                    @storefrontError('billing_address_id')
                                @else
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
                                            @t('Address label')
                                            <input type="text" name="billing_address[name]" value="{{ old('billing_address.name') }}" required class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                            @storefrontError('billing_address.name')
                                        </label>
                                        <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
                                            @t('Company name')
                                            <input type="text" name="billing_address[company_name]" value="{{ old('billing_address.company_name') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                        </label>
                                        <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
                                            @t('Address line 1')
                                            <input type="text" name="billing_address[address_line_1]" value="{{ old('billing_address.address_line_1') }}" required class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                            @storefrontError('billing_address.address_line_1')
                                        </label>
                                        <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
                                            @t('Address line 2')
                                            <input type="text" name="billing_address[address_line_2]" value="{{ old('billing_address.address_line_2') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                        </label>
                                        <label class="flex flex-col gap-1 text-sm text-body">
                                            @t('City')
                                            <input type="text" name="billing_address[city]" value="{{ old('billing_address.city') }}" required class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                            @storefrontError('billing_address.city')
                                        </label>
                                        <label class="flex flex-col gap-1 text-sm text-body">
                                            @t('Postal code')
                                            <input type="text" name="billing_address[zip]" value="{{ old('billing_address.zip') }}" required class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                            @storefrontError('billing_address.zip')
                                        </label>
                                        <label class="flex flex-col gap-1 text-sm text-body">
                                            @t('State / region')
                                            <input type="text" name="billing_address[state]" value="{{ old('billing_address.state') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                        </label>
                                        <label class="flex flex-col gap-1 text-sm text-body">
                                            @t('Country')
                                            @foreach ([storefront_country_name(old('billing_address.country'))] as $selectedCountry)
                                                <select name="billing_address[country]" required autocomplete="billing country-name" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                                    <option value="">@t('Select country')</option>
                                                    @foreach (storefront_countries() as $country)
                                                        <option value="{{ $country['name'] }}" @selected($selectedCountry === $country['name'])>{{ $country['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            @endforeach
                                            @storefrontError('billing_address.country')
                                        </label>
                                    </div>
                                @endif
                            </div>

                            <div class="checkout__section flex flex-col gap-4">
                                <h3 class="font-primary text-sm font-semibold uppercase tracking-wide text-headings/70 m-0">@t('Lieferadresse')</h3>
                                <input type="hidden" name="shipping_address_is_same_as_billing" value="0">
                                <label class="checkout__same-toggle inline-flex items-center gap-2 cursor-pointer select-none text-sm text-body">
                                    <input id="checkout-ship-same" type="checkbox" name="shipping_address_is_same_as_billing" value="1" class="checkout__same-cb shrink-0" @checked(old('shipping_address_is_same_as_billing', '1') == '1')>
                                    @t('Deliver to my billing address')
                                </label>

                                <div class="checkout__delivery-body flex flex-col gap-3">
                                    @if($isAuthenticated && isset($shippingAddresses) && count($shippingAddresses) > 0)
                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            @foreach($shippingAddresses as $address)
                                                @include('partials.checkout-address-option', [
                                                    'address'    => $address,
                                                    'field'      => 'shipping_address_id',
                                                    'selectedId' => $defaultShippingAddressId ?? null,
                                                    'required'   => false,
                                                ])
                                            @endforeach
                                        </div>
                                        @storefrontError('shipping_address_id')
                                        <button type="button" data-storefront-modal-open="shipping-address-modal" class="checkout__manage inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:text-primary-600 cursor-pointer bg-transparent border-0 p-0 self-start">
                                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" /><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" /></svg>
                                            @t('Manage addresses')
                                        </button>
                                    @elseif($isAuthenticated)
                                        <div class="checkout__empty-addr flex flex-col items-start gap-3">
                                            <p class="text-sm text-body m-0">@t('You have no saved delivery addresses yet.')</p>
                                            <button type="button" data-storefront-modal-open="shipping-address-modal" class="inline-flex items-center justify-center rounded-lg border border-primary text-primary font-medium px-4 py-2 text-sm transition-colors hover:bg-primary-subtle cursor-pointer">@t('Add a delivery address')</button>
                                        </div>
                                        @storefrontError('shipping_address_id')
                                    @else
                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
                                                @t('Address label')
                                                <input type="text" name="shipping_address[name]" value="{{ old('shipping_address.name') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                                @storefrontError('shipping_address.name')
                                            </label>
                                            <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
                                                @t('Address line 1')
                                                <input type="text" name="shipping_address[address_line_1]" value="{{ old('shipping_address.address_line_1') }}" required class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                                @storefrontError('shipping_address.address_line_1')
                                            </label>
                                            <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
                                                @t('Address line 2')
                                                <input type="text" name="shipping_address[address_line_2]" value="{{ old('shipping_address.address_line_2') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                            </label>
                                            <label class="flex flex-col gap-1 text-sm text-body">
                                                @t('City')
                                                <input type="text" name="shipping_address[city]" value="{{ old('shipping_address.city') }}" required class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                                @storefrontError('shipping_address.city')
                                            </label>
                                            <label class="flex flex-col gap-1 text-sm text-body">
                                                @t('Postal code')
                                                <input type="text" name="shipping_address[zip]" value="{{ old('shipping_address.zip') }}" required class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                                @storefrontError('shipping_address.zip')
                                            </label>
                                            <label class="flex flex-col gap-1 text-sm text-body">
                                                @t('State / region')
                                                <input type="text" name="shipping_address[state]" value="{{ old('shipping_address.state') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                            </label>
                                            <label class="flex flex-col gap-1 text-sm text-body">
                                                @t('Country')
                                                @foreach ([storefront_country_name(old('shipping_address.country'))] as $selectedCountry)
                                                    <select name="shipping_address[country]" required autocomplete="shipping country-name" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                                        <option value="">@t('Select country')</option>
                                                        @foreach (storefront_countries() as $country)
                                                            <option value="{{ $country['name'] }}" @selected($selectedCountry === $country['name'])>{{ $country['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                @endforeach
                                                @storefrontError('shipping_address.country')
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <button type="button" class="checkout__next inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-5 py-3 font-semibold text-primary-content transition-colors hover:bg-primary-600 self-start" data-checkout-next-step>
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.69L11.22 5.03a.75.75 0 0 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06-1.06l3.22-3.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" /></svg>
                                @t('Weiter zu Zahlung & Versand')
                            </button>
                        </section>

                        <section class="checkout__panel checkout__panel--step2 flex flex-col gap-5 rounded-lg border border-border-subtle bg-surface-card p-5 sm:p-6" data-checkout-step-panel="payment" @if(!$errors->hasAny(['shipping_method_id', 'billing_method_id', 'accept_terms', 'comment']))hidden@else aria-hidden="false"@endif>
                            <header class="checkout__panel-head flex items-center gap-2.5">
                                <span class="checkout__step inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-subtle text-xs font-semibold text-primary">2</span>
                                <div class="flex min-w-0 flex-col gap-0.5">
                                    <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Abschluss')</h2>
                                    <p class="text-sm text-body m-0">@t('Versand- und Zahlungsart')</p>
                                </div>
                            </header>

                            <button type="button" class="checkout__back inline-flex items-center gap-1.5 self-start rounded-lg border border-border-subtle px-4 py-2 text-sm font-medium text-body transition-colors hover:border-primary hover:text-primary desktop:hidden" data-checkout-prev-step>
                                <svg class="h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" /></svg>
                                @t('Zurück zu Adresse')
                            </button>

                            @if(count($shippingMethods) >= 2)
                                <section class="checkout__section flex flex-col gap-4">
                                    <header class="checkout__panel-head flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h.05a2.5 2.5 0 0 0 4.9 0h4.1a2.5 2.5 0 0 0 4.9 0H17a1 1 0 0 0 1-1v-3.382a1 1 0 0 0-.106-.448l-1.724-3.447A1 1 0 0 0 15.276 5H13V5a1 1 0 0 0-1-1H3Zm10 3h1.659l1.341 2.682V11H13V7Zm-7.5 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2Zm9 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2Z" /></svg>
                                        <h3 class="font-primary text-base font-semibold text-headings m-0">@t('Shipping method')</h3>
                                    </header>
                                    <div class="flex flex-col gap-3">
                                        @foreach($shippingMethods as $method)
                                            <label class="checkout__method relative flex gap-3 rounded-lg border border-border-subtle p-4 cursor-pointer transition-colors hover:border-primary/60 has-[:checked]:border-primary has-[:checked]:bg-primary-subtle">
                                                <input type="radio" name="shipping_method_id" value="{{ $method->id }}" class="mt-0.5 shrink-0 accent-primary" @checked(old('shipping_method_id', $defaultShippingMethodId) == $method->id) required>
                                                <span class="flex min-w-0 flex-col gap-0.5">
                                                    <span class="font-medium text-headings text-sm">{{ $method->name }}</span>
                                                    @if(!empty($method->details))
                                                        <button
                                                            type="button"
                                                            data-storefront-modal-open="shipping-method-{{ $method->id }}"
                                                            class="checkout__method-readmore inline-flex items-center gap-1 self-start bg-transparent border-0 p-0 text-sm font-medium text-primary hover:text-primary-600 cursor-pointer"
                                                        >
                                                            <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" /></svg>
                                                            @t('Read more')
                                                        </button>
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @storefrontError('shipping_method_id')
                                </section>
                            @elseif(count($shippingMethods) === 1)
                                <input type="hidden" name="shipping_method_id" value="{{ data_get($shippingMethods->first(), 'id') }}">
                            @endif

                            @if(!empty($store['hide_payment_method']) && !empty($defaultBillingMethodId))
                                <input type="hidden" name="billing_method_id" value="{{ $defaultBillingMethodId }}">
                            @else
                                <section class="checkout__section flex flex-col gap-4">
                                    <header class="checkout__panel-head flex items-center gap-2.5">
                                        <span class="checkout__step inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary-subtle text-xs font-semibold text-primary">3</span>
                                        <h3 class="font-primary text-base font-semibold text-headings m-0">@t('Payment method')</h3>
                                    </header>
                                    @if(empty($billingMethods) || count($billingMethods) === 0)
                                        <p class="text-sm text-body m-0">@t('No payment methods available.')</p>
                                    @else
                                        <div class="flex flex-col gap-3">
                                            @foreach($billingMethods as $method)
                                                <label class="checkout__method relative flex gap-3 rounded-lg border border-border-subtle p-4 cursor-pointer transition-colors hover:border-primary/60 has-[:checked]:border-primary has-[:checked]:bg-primary-subtle">
                                                    <input type="radio" name="billing_method_id" value="{{ $method->id }}" class="mt-0.5 shrink-0 accent-primary" @checked(old('billing_method_id', $defaultBillingMethodId) == $method->id) required>
                                                    <span class="flex min-w-0 flex-col gap-0.5">
                                                        <span class="font-medium text-headings text-sm">{{ $method->name }}</span>
                                                        @if(!empty($method->details))
                                                            <button
                                                                type="button"
                                                                data-storefront-modal-open="payment-method-{{ $method->id }}"
                                                                class="checkout__method-readmore inline-flex items-center gap-1 self-start bg-transparent border-0 p-0 text-sm font-medium text-primary hover:text-primary-600 cursor-pointer"
                                                            >
                                                                <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" /></svg>
                                                                @t('Read more')
                                                            </button>
                                                        @endif
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @storefrontError('billing_method_id')
                                    @endif
                                </section>
                            @endif

                            @foreach($checkoutAttributeGroups ?? [] as $attributeGroup)
                                <section class="checkout__panel flex flex-col gap-4 rounded-lg border border-border-subtle bg-surface-card p-4 sm:p-5" @if($attributeGroup['code']) data-collection="{{ $attributeGroup['code'] }}" @endif>
                                    <header class="checkout__panel-head flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M6 3.75A1.75 1.75 0 0 1 7.75 2h4.5A1.75 1.75 0 0 1 14 3.75v.5h.25A2.75 2.75 0 0 1 17 7v8.25A2.75 2.75 0 0 1 14.25 18h-8.5A2.75 2.75 0 0 1 3 15.25V7a2.75 2.75 0 0 1 2.75-2.75H6v-.5Zm1.5.5h5v-.5a.25.25 0 0 0-.25-.25h-4.5a.25.25 0 0 0-.25.25v.5Z" clip-rule="evenodd" /></svg>
                                        <h3 class="font-primary text-base font-semibold text-headings m-0">{{ $attributeGroup['name'] ?? t('Additional details') }}</h3>
                                    </header>
                                    <div class="flex flex-col gap-3">
                                        @foreach($attributeGroup['attributes'] as $field)
                                            @include('partials.checkout-attribute-field', ['attribute' => $field['attribute'], 'index' => $field['index']])
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach

                            <section class="checkout__panel flex flex-col gap-4 rounded-lg border border-border-subtle bg-surface-card p-4 sm:p-5">
                                <header class="checkout__panel-head flex items-center gap-2.5">
                                    <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 10.879 2H4.5Zm3.75 8.5a.75.75 0 0 0 0 1.5h3.5a.75.75 0 0 0 0-1.5h-3.5Zm0 3a.75.75 0 0 0 0 1.5h3.5a.75.75 0 0 0 0-1.5h-3.5Z" clip-rule="evenodd" /></svg>
                                    <h3 class="font-primary text-base font-semibold text-headings m-0">@t('Order notes')</h3>
                                </header>
                                <textarea name="comment" rows="3" placeholder="@t('Add a note to your order (optional)')" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">{{ old('comment') }}</textarea>
                                @storefrontError('comment')
                            </section>

                            <div class="checkout__mobile-submit flex flex-col gap-4 rounded-lg border border-border-subtle bg-surface-card p-4 desktop:hidden">
                                <label class="checkout__terms flex items-start gap-2 text-sm text-body">
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
                                @storefrontError('accept_terms')

                                <button type="submit" class="checkout__place-order inline-flex items-center justify-center gap-2 w-full rounded-lg bg-primary text-primary-content font-semibold px-4 py-3 transition-colors hover:bg-primary-600 cursor-pointer">
                                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" /></svg>
                                    @t('Place order') · <span data-checkout-total>{{ formatCurrency($cartPricing['grand_total'] ?? 0) }}</span>
                                </button>

                                <p class="checkout__secure flex items-center justify-center gap-1.5 text-xs text-body m-0">
                                    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" /></svg>
                                    @t('Secure encrypted checkout')
                                </p>
                            </div>
                        </section>
                    </div>
                </div>

                <aside class="checkout__sidebar hidden desktop:flex flex-col gap-4 w-full desktop:w-96 p-5 bg-surface-card border border-border-subtle rounded-lg desktop:sticky desktop:top-6 desktop:self-start">
                    @storefrontSection('checkout-summary')

                    <div class="checkout__sidebar-divider h-px w-full bg-border-subtle"></div>

                    <label class="checkout__terms flex items-start gap-2 text-sm text-body">
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
                    @storefrontError('accept_terms')

                    <button type="submit" class="checkout__place-order inline-flex items-center justify-center gap-2 w-full rounded-lg bg-primary text-primary-content font-semibold px-4 py-3 transition-colors hover:bg-primary-600 cursor-pointer">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" /></svg>
                        @t('Place order') · <span data-checkout-total>{{ formatCurrency($cartPricing['grand_total'] ?? 0) }}</span>
                    </button>

                    <p class="checkout__secure flex items-center justify-center gap-1.5 text-xs text-body m-0">
                        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" /></svg>
                        @t('Secure encrypted checkout')
                    </p>
                </aside>
            @endstorefrontForm

            @if($isAuthenticated)
                {{-- Manage-addresses modals. Rendered OUTSIDE the checkout form so
                     their address forms are never nested inside it. Opened by the
                     panel triggers (data-storefront-modal-open) via storefront.js
                     showModal(); each action is its own sibling <form> that POSTs
                     and redirects back to the checkout (relative redirect — accepted
                     by SafeRedirect). With JS off the triggers are inert, but the
                     account address pages remain the full-page fallback. --}}
                <dialog id="billing-address-modal" class="modal bg-surface-card text-body rounded-lg shadow-xl p-0 border-0 w-full max-w-640 backdrop:bg-backdrop/40">
                    <article class="modal__panel flex flex-col gap-4 p-6">
                        <header class="modal__head flex items-center justify-between gap-3 pb-3 border-b border-border-subtle">
                            <h2 class="font-primary text-lg font-semibold text-headings m-0">@t('Billing addresses')</h2>
                            <button type="button" class="modal__close inline-flex h-8 w-8 items-center justify-center text-2xl leading-none text-body hover:text-headings cursor-pointer bg-transparent border-0" data-modal-close aria-label="@t('Close')">&times;</button>
                        </header>
                        <div class="modal__body flex flex-col gap-4">
                            @if(count($billingAddresses) > 0)
                                <ul class="flex flex-col gap-2 list-none m-0 p-0">
                                    @foreach($billingAddresses as $address)
                                        @include('partials.checkout-address-summary', [
                                            'address'     => $address,
                                            'defaultForm' => 'address-billing-default',
                                        ])
                                    @endforeach
                                </ul>
                            @endif

                            <details class="checkout__add-address border border-border-subtle rounded-lg p-4">
                                <summary class="cursor-pointer text-headings font-medium text-sm">@t('Add a new address')</summary>
                                @storefrontForm('address-billing-create', ['class' => 'flex flex-col gap-3 mt-3'])
                                    <input type="hidden" name="redirect" value="/{{ $locale }}/checkout">
                                    @include('partials.account-address-form', ['address' => null])
                                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary text-primary-content font-medium px-4 py-2 transition-colors hover:bg-primary-600 cursor-pointer self-start">@t('Save address')</button>
                                @endstorefrontForm
                            </details>
                        </div>
                    </article>
                </dialog>

                <dialog id="shipping-address-modal" class="modal bg-surface-card text-body rounded-lg shadow-xl p-0 border-0 w-full max-w-640 backdrop:bg-backdrop/40">
                    <article class="modal__panel flex flex-col gap-4 p-6">
                        <header class="modal__head flex items-center justify-between gap-3 pb-3 border-b border-border-subtle">
                            <h2 class="font-primary text-lg font-semibold text-headings m-0">@t('Delivery addresses')</h2>
                            <button type="button" class="modal__close inline-flex h-8 w-8 items-center justify-center text-2xl leading-none text-body hover:text-headings cursor-pointer bg-transparent border-0" data-modal-close aria-label="@t('Close')">&times;</button>
                        </header>
                        <div class="modal__body flex flex-col gap-4">
                            @if(count($shippingAddresses) > 0)
                                <ul class="flex flex-col gap-2 list-none m-0 p-0">
                                    @foreach($shippingAddresses as $address)
                                        @include('partials.checkout-address-summary', [
                                            'address'     => $address,
                                            'defaultForm' => 'address-shipping-default',
                                        ])
                                    @endforeach
                                </ul>
                            @endif

                            <details class="checkout__add-address border border-border-subtle rounded-lg p-4">
                                <summary class="cursor-pointer text-headings font-medium text-sm">@t('Add a new address')</summary>
                                @storefrontForm('address-shipping-create', ['class' => 'flex flex-col gap-3 mt-3'])
                                    <input type="hidden" name="redirect" value="/{{ $locale }}/checkout">
                                    @include('partials.account-address-form', ['address' => null])
                                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary text-primary-content font-medium px-4 py-2 transition-colors hover:bg-primary-600 cursor-pointer self-start">@t('Save address')</button>
                                @endstorefrontForm
                            </details>
                        </div>
                    </article>
                </dialog>
            @endif

            @if((empty($store['hide_payment_method']) || empty($defaultBillingMethodId)) && !empty($billingMethods) && count($billingMethods) > 0)
                @foreach($billingMethods as $method)
                    @if(!empty($method->details))
                        <dialog id="payment-method-{{ $method->id }}" class="modal bg-surface-card text-body rounded-lg shadow-xl p-0 border-0 w-full max-w-640 backdrop:bg-backdrop/40">
                            <article class="modal__panel flex flex-col gap-4 p-6">
                                <header class="modal__head flex items-center justify-between gap-3 pb-3 border-b border-border-subtle">
                                    <h2 class="font-primary text-lg font-semibold text-headings m-0">{{ $method->name }}</h2>
                                    <button type="button" class="modal__close inline-flex h-8 w-8 items-center justify-center text-2xl leading-none text-body hover:text-headings cursor-pointer bg-transparent border-0" data-modal-close aria-label="@t('Close')">&times;</button>
                                </header>
                                <div class="modal__body storefront-richtext text-sm text-body overflow-y-auto" style="max-height:60vh">{!! $method->details !!}</div>
                            </article>
                        </dialog>
                    @endif
                @endforeach
            @endif

            @if(!empty($shippingMethods) && count($shippingMethods) > 0)
                @foreach($shippingMethods as $method)
                    @if(!empty($method->details))
                        <dialog id="shipping-method-{{ $method->id }}" class="modal bg-surface-card text-body rounded-lg shadow-xl p-0 border-0 w-full max-w-640 backdrop:bg-backdrop/40">
                            <article class="modal__panel flex flex-col gap-4 p-6">
                                <header class="modal__head flex items-center justify-between gap-3 pb-3 border-b border-border-subtle">
                                    <h2 class="font-primary text-lg font-semibold text-headings m-0">{{ $method->name }}</h2>
                                    <button type="button" class="modal__close inline-flex h-8 w-8 items-center justify-center text-2xl leading-none text-body hover:text-headings cursor-pointer bg-transparent border-0" data-modal-close aria-label="@t('Close')">&times;</button>
                                </header>
                                <div class="modal__body storefront-richtext text-sm text-body overflow-y-auto" style="max-height:60vh">{!! $method->details !!}</div>
                            </article>
                        </dialog>
                    @endif
                @endforeach
            @endif

            @if($store['coupons_enabled'])
                @storefrontForm('cart-coupon-apply', ['id' => 'checkout-coupon-apply', 'class' => 'hidden'])
                    <input type="hidden" name="redirect" value="/{{ $locale }}/checkout">
                @endstorefrontForm
                @storefrontForm('cart-coupon-remove', ['id' => 'checkout-coupon-remove', 'class' => 'hidden'])
                    <input type="hidden" name="redirect" value="/{{ $locale }}/checkout">
                @endstorefrontForm
            @endif
        @endif
    </div>

    {{-- Live order-summary recompute. Selecting a shipping method, a payment
         method, a delivery address, or toggling same-as-billing re-renders the `checkout-summary`
         section server-side WITH the real shipping charge. The checkout keeps the
         mirrored total in sync across the mobile summary toggle and the submit
         buttons. --}}
    <script type="module">
        const root = document.querySelector('[data-checkout-root]');
        const summarySections = [...document.querySelectorAll('[data-storefront-section="checkout-summary"]')];
        const totalOutputs = [...document.querySelectorAll('[data-checkout-total]')];
        const mobileSummaryToggle = document.querySelector('[data-checkout-summary-toggle]');
        const mobileSummaryPanel = document.querySelector('[data-checkout-summary-panel]');
        const nextStepButton = document.querySelector('[data-checkout-next-step]');
        const prevStepButton = document.querySelector('[data-checkout-prev-step]');
        const step1Panel = document.querySelector('[data-checkout-step-panel="address"]') ?? document.querySelector('.checkout__panel--step1');
        const step2Panel = document.querySelector('[data-checkout-step-panel="payment"]') ?? document.querySelector('.checkout__panel--step2');
        const stepIndicators = [...document.querySelectorAll('[data-checkout-step-indicator]')];
        const initialStep = root?.dataset.checkoutInitialStep === 'payment' ? 'payment' : 'address';
        const recomputeNames = new Set([
            'shipping_method_id',
            'billing_method_id',
            'shipping_address_id',
            'billing_address_id',
            'shipping_address_is_same_as_billing',
        ]);

        const currentSelection = () => {
            const method = document.querySelector('input[name="shipping_method_id"]:checked')
                ?? document.querySelector('input[type="hidden"][name="shipping_method_id"]');
            const billing = document.querySelector('input[name="billing_method_id"]:checked')
                ?? document.querySelector('input[type="hidden"][name="billing_method_id"]');
            const sameToggle = document.querySelector('input[type="checkbox"][name="shipping_address_is_same_as_billing"]');
            return {
                shipping_method_id: method?.value ?? null,
                billing_method_id: billing?.value ?? null,
                shipping_address_id: document.querySelector('input[name="shipping_address_id"]:checked')?.value ?? null,
                billing_address_id: document.querySelector('input[name="billing_address_id"]:checked')?.value ?? null,
                shipping_address_is_same_as_billing: sameToggle ? (sameToggle.checked ? 1 : 0) : null,
            };
        };

        const syncTotal = () => {
            const rendered = [...summarySections].find((section) => section.offsetParent !== null)
                ?? summarySections[0]
                ?? null;
            const source = rendered?.querySelector('[data-checkout-section-total]')
                ?? rendered?.querySelector('.checkout-summary__total span.text-lg');
            if (!source) {
                return;
            }
            const value = source.textContent?.trim() ?? '';
            totalOutputs.forEach((output) => {
                output.textContent = value;
            });
        };

        const waitForStorefront = async (retries = 20, delay = 100) => {
            for (let i = 0; i < retries; i += 1) {
                if (window.Storefront?.refresh) {
                    return window.Storefront;
                }
                await new Promise((resolve) => setTimeout(resolve, delay));
            }
            return null;
        };

        const toggleStepInputs = (enabled) => {
            if (!step2Panel) {
                return;
            }
            step2Panel.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach((field) => {
                field.disabled = !enabled;
            });
        };

        const updateStepper = (step) => {
            stepIndicators.forEach((indicator) => {
                const isAddress = indicator.dataset.checkoutStepIndicator === 'address';
                const isPayment = indicator.dataset.checkoutStepIndicator === 'payment';
                const active = (step === 'address' && isAddress) || (step === 'payment' && isPayment);
                const done = step === 'payment' && isAddress;
                indicator.classList.toggle('checkout__progress-step--active', active);
                indicator.classList.toggle('checkout__progress-step--done', done);
                const badge = indicator.querySelector('.checkout__progress-badge');
                if (badge) {
                    badge.classList.toggle('bg-primary', active || done);
                    badge.classList.toggle('text-primary-content', active || done);
                    badge.classList.toggle('border', !active && !done);
                    badge.classList.toggle('border-border-subtle', !active && !done);
                    badge.classList.toggle('bg-surface-card', !active && !done);
                    badge.classList.toggle('text-border', !active && !done);
                }
            });
        };

        const setStep = (step) => {
            const isPayment = step === 'payment';
            if (step2Panel) {
                step2Panel.hidden = !isPayment;
                step2Panel.setAttribute('aria-hidden', isPayment ? 'false' : 'true');
            }
            toggleStepInputs(isPayment);
            updateStepper(step);
            root?.setAttribute('data-active-checkout-step', step);
        };

        const validateAddressStep = () => {
            if (!step1Panel) {
                return true;
            }
            const fields = [...step1Panel.querySelectorAll('input, select, textarea')].filter((field) => {
                if (field.disabled || field.type === 'hidden') {
                    return false;
                }
                if (field.closest('[hidden]')) {
                    return false;
                }
                return field.offsetParent !== null;
            });
            for (const field of fields) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    field.focus({ preventScroll: false });
                    return false;
                }
            }
            return true;
        };

        syncTotal();
        updateStepper(initialStep);
        toggleStepInputs(initialStep === 'payment');
        if (step2Panel) {
            step2Panel.hidden = initialStep !== 'payment';
            step2Panel.setAttribute('aria-hidden', initialStep === 'payment' ? 'false' : 'true');
        }
        if (initialStep === 'payment') {
            root?.setAttribute('data-active-checkout-step', 'payment');
        }

        const summaryObserver = new MutationObserver(() => {
            syncTotal();
        });
        summarySections.forEach((section) => {
            summaryObserver.observe(section, { childList: true, subtree: true, characterData: true });
        });

        let busy = false;
        let selfTriggered = false;
        const recompute = async () => {
            if (busy) {
                return;
            }
            busy = true;
            selfTriggered = true;
            summarySections.forEach((section) => section.classList.add('opacity-50'));
            try {
                const api = window.Storefront?.refresh ? window.Storefront : await waitForStorefront();
                if (api?.refresh) {
                    await api.refresh(['checkout-summary'], currentSelection());
                }
            } finally {
                syncTotal();
                summarySections.forEach((section) => section.classList.remove('opacity-50'));
                busy = false;
            }
        };

        document.addEventListener('change', (event) => {
            if (recomputeNames.has(event.target?.name)) {
                recompute();
            }
        });

        document.addEventListener('storefront:sections:rendered', (event) => {
            if (selfTriggered) {
                selfTriggered = false;
                syncTotal();
                return;
            }
            if (event.detail?.sections?.['checkout-summary'] && currentSelection().shipping_method_id) {
                recompute();
            }
        });

        if (nextStepButton) {
            nextStepButton.addEventListener('click', () => {
                if (!validateAddressStep()) {
                    return;
                }
                setStep('payment');
                step2Panel?.scrollIntoView({ block: 'start', behavior: 'smooth' });
            });
        }

        if (prevStepButton) {
            prevStepButton.addEventListener('click', () => {
                setStep('address');
                step1Panel?.scrollIntoView({ block: 'start', behavior: 'smooth' });
            });
        }

        if (mobileSummaryToggle && mobileSummaryPanel) {
            mobileSummaryToggle.addEventListener('click', () => {
                const expanded = mobileSummaryToggle.getAttribute('aria-expanded') === 'true';
                mobileSummaryToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                mobileSummaryPanel.hidden = expanded;
            });
        }
    </script>

    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection

@push('scripts')
<script type="module">
    {{-- BFCache guard: if the browser restores this page from the back/forward
         cache (e.g. the visitor pressed Back after being sent to an external
         payment provider), force a fresh HTTP GET so the server-side redirect
         in StorefrontController::checkout() can detect the payment-pending
         session flag and send the visitor to their order history instead of
         showing a stale empty-cart checkout form. --}}
    window.addEventListener('pageshow', (e) => {
        if (e.persisted) {
            window.location.reload();
        }
    });
</script>
@endpush
