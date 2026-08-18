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
    <div class="checkout flex flex-col gap-5" data-mode="{{ $checkoutMode }}">
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

        @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])

        @if(empty($cartItems) || (is_countable($cartItems) && count($cartItems) === 0))
            <div class="checkout__empty flex flex-col items-start gap-3 p-6 bg-surface-card border border-border-subtle rounded-lg">
                <p class="text-body m-0">@t('Your cart is empty.')</p>
                <a href="@routeUrl('store.products')" class="inline-flex items-center justify-center rounded-lg bg-primary text-primary-content font-medium px-4 py-2.5 transition-colors hover:bg-primary-600 hover:no-underline">@t('Continue shopping')</a>
            </div>
        @else
            @storefrontForm('checkout', ['class' => 'checkout__grid flex flex-col gap-5 desktop:flex-row desktop:items-start'])
                <input type="hidden" name="is_guest" value="{{ $isAuthenticated ? '0' : '1' }}">

                <div class="checkout__main flex flex-col gap-5 min-w-0 desktop:flex-1">

                    {{-- Contact details --}}
                    <section class="checkout__panel flex flex-col gap-4 p-5 bg-surface-card border border-border-subtle rounded-lg">
                        <header class="checkout__panel-head flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm-7 9a7 7 0 1 1 14 0H3Z" clip-rule="evenodd" /></svg>
                            <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Contact details')</h2>
                        </header>
                        @if($isAuthenticated)
                            <p class="text-sm text-body m-0">{{ optional($customer ?? $me)->email ?? '' }}</p>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex flex-col gap-1 text-sm text-body">
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
                    </section>

                    {{-- 1. Billing address --}}
                    <section class="checkout__panel flex flex-col gap-4 p-5 bg-surface-card border border-border-subtle rounded-lg">
                        <header class="checkout__panel-head flex items-center gap-2.5">
                            <span class="checkout__step inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary-subtle text-primary text-xs font-semibold shrink-0">1</span>
                            <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M9.293 2.293a1 1 0 0 1 1.414 0l7 7A1 1 0 0 1 17 11h-1v6a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6H3a1 1 0 0 1-.707-1.707l7-7Z" clip-rule="evenodd" /></svg>
                            <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Billing address')</h2>
                        </header>

                        @if($isAuthenticated && isset($billingAddresses) && count($billingAddresses) > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                                    <input type="text" name="billing_address[country]" value="{{ old('billing_address.country') }}" required class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                    @storefrontError('billing_address.country')
                                </label>
                            </div>
                        @endif
                    </section>

                    {{-- 2. Delivery address --}}
                    <section class="checkout__panel flex flex-col gap-4 p-5 bg-surface-card border border-border-subtle rounded-lg">
                        <header class="checkout__panel-head flex items-center gap-2.5">
                            <span class="checkout__step inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary-subtle text-primary text-xs font-semibold shrink-0">2</span>
                            <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h.05a2.5 2.5 0 0 0 4.9 0h4.1a2.5 2.5 0 0 0 4.9 0H17a1 1 0 0 0 1-1v-3.382a1 1 0 0 0-.106-.448l-1.724-3.447A1 1 0 0 0 15.276 5H13V5a1 1 0 0 0-1-1H3Zm10 3h1.659l1.341 2.682V11H13V7Zm-7.5 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2Zm9 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2Z" /></svg>
                            <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Delivery address')</h2>
                        </header>

                        <input type="hidden" name="shipping_address_is_same_as_billing" value="0">
                        <label class="checkout__same-toggle inline-flex items-center gap-2 cursor-pointer select-none text-sm text-body">
                            <input id="checkout-ship-same" type="checkbox" name="shipping_address_is_same_as_billing" value="1" class="checkout__same-cb shrink-0" @checked(old('shipping_address_is_same_as_billing', '1') == '1')>
                            @t('Deliver to my billing address')
                        </label>

                        <div class="checkout__delivery-body flex flex-col gap-3">
                            @if($isAuthenticated && isset($shippingAddresses) && count($shippingAddresses) > 0)
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
                                        @t('Address label')
                                        <input type="text" name="shipping_address[name]" value="{{ old('shipping_address.name') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                        @storefrontError('shipping_address.name')
                                    </label>
                                    <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
                                        @t('Address line 1')
                                        <input type="text" name="shipping_address[address_line_1]" value="{{ old('shipping_address.address_line_1') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                        @storefrontError('shipping_address.address_line_1')
                                    </label>
                                    <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
                                        @t('Address line 2')
                                        <input type="text" name="shipping_address[address_line_2]" value="{{ old('shipping_address.address_line_2') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                    </label>
                                    <label class="flex flex-col gap-1 text-sm text-body">
                                        @t('City')
                                        <input type="text" name="shipping_address[city]" value="{{ old('shipping_address.city') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                        @storefrontError('shipping_address.city')
                                    </label>
                                    <label class="flex flex-col gap-1 text-sm text-body">
                                        @t('Postal code')
                                        <input type="text" name="shipping_address[zip]" value="{{ old('shipping_address.zip') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                        @storefrontError('shipping_address.zip')
                                    </label>
                                    <label class="flex flex-col gap-1 text-sm text-body">
                                        @t('State / region')
                                        <input type="text" name="shipping_address[state]" value="{{ old('shipping_address.state') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                    </label>
                                    <label class="flex flex-col gap-1 text-sm text-body">
                                        @t('Country')
                                        <input type="text" name="shipping_address[country]" value="{{ old('shipping_address.country') }}" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                        @storefrontError('shipping_address.country')
                                    </label>
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- Shipping method (shown only when the customer has a real choice) --}}
                    @if(count($shippingMethods) >= 2)
                        <section class="checkout__panel flex flex-col gap-4 p-5 bg-surface-card border border-border-subtle rounded-lg">
                            <header class="checkout__panel-head flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h.05a2.5 2.5 0 0 0 4.9 0h4.1a2.5 2.5 0 0 0 4.9 0H17a1 1 0 0 0 1-1v-3.382a1 1 0 0 0-.106-.448l-1.724-3.447A1 1 0 0 0 15.276 5H13V5a1 1 0 0 0-1-1H3Zm10 3h1.659l1.341 2.682V11H13V7Zm-7.5 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2Zm9 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2Z" /></svg>
                                <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Shipping method')</h2>
                            </header>
                            <div class="flex flex-col gap-3">
                                @foreach($shippingMethods as $method)
                                    <label class="checkout__method relative flex gap-3 p-4 border border-border-subtle rounded-lg cursor-pointer transition-colors hover:border-primary/60 has-[:checked]:border-primary has-[:checked]:bg-primary-subtle">
                                        <input type="radio" name="shipping_method_id" value="{{ $method->id }}" class="mt-0.5 accent-primary shrink-0" @checked(old('shipping_method_id', $defaultShippingMethodId) == $method->id) required>
                                        <span class="flex flex-col gap-0.5 min-w-0">
                                            <span class="font-medium text-headings text-sm">{{ $method->name }}</span>
                                            @if(!empty($method->details))
                                                <button
                                                    type="button"
                                                    data-storefront-modal-open="shipping-method-{{ $method->id }}"
                                                    class="checkout__method-readmore inline-flex items-center gap-1 self-start text-sm font-medium text-primary hover:text-primary-600 cursor-pointer bg-transparent border-0 p-0"
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

                    {{-- 3. Payment method --}}
                    <section class="checkout__panel flex flex-col gap-4 p-5 bg-surface-card border border-border-subtle rounded-lg">
                        <header class="checkout__panel-head flex items-center gap-2.5">
                            <span class="checkout__step inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary-subtle text-primary text-xs font-semibold shrink-0">3</span>
                            <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2.5 4A1.5 1.5 0 0 0 1 5.5V6h18v-.5A1.5 1.5 0 0 0 17.5 4h-15ZM19 8.5H1v6A1.5 1.5 0 0 0 2.5 16h15a1.5 1.5 0 0 0 1.5-1.5v-6ZM3 13.25a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-1.5a.75.75 0 0 1-.75-.75Zm4.75-.75a.75.75 0 0 0 0 1.5h3.5a.75.75 0 0 0 0-1.5h-3.5Z" /></svg>
                            <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Payment method')</h2>
                        </header>
                        @if(empty($billingMethods) || count($billingMethods) === 0)
                            <p class="text-sm text-body m-0">@t('No payment methods available.')</p>
                        @else
                            <div class="flex flex-col gap-3">
                                @foreach($billingMethods as $method)
                                    <label class="checkout__method relative flex gap-3 p-4 border border-border-subtle rounded-lg cursor-pointer transition-colors hover:border-primary/60 has-[:checked]:border-primary has-[:checked]:bg-primary-subtle">
                                        <input type="radio" name="billing_method_id" value="{{ $method->id }}" class="mt-0.5 accent-primary shrink-0" @checked(old('billing_method_id', $defaultBillingMethodId) == $method->id) required>
                                        <span class="flex flex-col gap-0.5 min-w-0">
                                            <span class="font-medium text-headings text-sm">{{ $method->name }}</span>
                                            @if(!empty($method->details))
                                                <button
                                                    type="button"
                                                    data-storefront-modal-open="payment-method-{{ $method->id }}"
                                                    class="checkout__method-readmore inline-flex items-center gap-1 self-start text-sm font-medium text-primary hover:text-primary-600 cursor-pointer bg-transparent border-0 p-0"
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

                    {{-- Additional details: tenant-defined custom order attributes
                         (group.type=order, use_in_checkout). Each renders a hidden
                         attributes[i][attribute_id] plus a value input mapped from
                         the attribute type. The `value` submitted for option types
                         is the option's raw value (CheckoutRepository looks it up by
                         value). `media` attributes are skipped — a file upload isn't
                         supported in this server-rendered flow. --}}
                    @if(!empty($checkoutAttributes) && count($checkoutAttributes) > 0)
                        <section class="checkout__panel flex flex-col gap-4 p-5 bg-surface-card border border-border-subtle rounded-lg">
                            <header class="checkout__panel-head flex items-center gap-2.5">
                                <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M6 3.75A1.75 1.75 0 0 1 7.75 2h4.5A1.75 1.75 0 0 1 14 3.75v.5h.25A2.75 2.75 0 0 1 17 7v8.25A2.75 2.75 0 0 1 14.25 18h-8.5A2.75 2.75 0 0 1 3 15.25V7a2.75 2.75 0 0 1 2.75-2.75H6v-.5Zm1.5.5h5v-.5a.25.25 0 0 0-.25-.25h-4.5a.25.25 0 0 0-.25.25v.5Z" clip-rule="evenodd" /></svg>
                                <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Additional details')</h2>
                            </header>
                            <div class="flex flex-col gap-3">
                                @foreach($checkoutAttributes as $attribute)
                                    @if(strtolower((string) $attribute->type) === 'media')
                                        @continue
                                    @endif
                                    <div class="checkout__attribute flex flex-col gap-1">
                                        <input type="hidden" name="attributes[{{ $loop->index }}][attribute_id]" value="{{ $attribute->id }}">
                                        @switch(strtolower((string) $attribute->type))
                                            @case('boolean')
                                                <label class="flex items-center gap-2 text-sm text-body">
                                                    <input type="hidden" name="attributes[{{ $loop->index }}][value]" value="0">
                                                    <input type="checkbox" name="attributes[{{ $loop->index }}][value]" value="1" @checked(old('attributes.' . $loop->index . '.value')) class="accent-primary shrink-0">
                                                    <span>{{ $attribute->name }}</span>
                                                </label>
                                                @break
                                            @case('select')
                                            @case('swatch')
                                                <label class="flex flex-col gap-1 text-sm text-body">
                                                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                                                    <select name="attributes[{{ $loop->index }}][value]" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                                        <option value="">@t('Select an option')</option>
                                                        @foreach($attribute->values as $opt)
                                                            <option value="{{ $opt->value }}" @selected(old('attributes.' . $loop->parent->index . '.value') == $opt->value)>{{ $opt->value }}</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                                @break
                                            @case('radio')
                                                <span class="text-sm text-body">{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                                                <div class="flex flex-col gap-2">
                                                    @foreach($attribute->values as $opt)
                                                        <label class="inline-flex items-center gap-2 text-sm text-body">
                                                            <input type="radio" name="attributes[{{ $loop->parent->index }}][value]" value="{{ $opt->value }}" @checked(old('attributes.' . $loop->parent->index . '.value') == $opt->value) {{ $attribute->required ? 'required' : '' }} class="accent-primary shrink-0">
                                                            <span>{{ $opt->value }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                @break
                                            @case('multiselect')
                                                <span class="text-sm text-body">{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                                                <div class="flex flex-col gap-2">
                                                    @foreach($attribute->values as $opt)
                                                        <label class="inline-flex items-center gap-2 text-sm text-body">
                                                            <input type="checkbox" name="attributes[{{ $loop->parent->index }}][value][]" value="{{ $opt->value }}" @checked(in_array($opt->value, (array) old('attributes.' . $loop->parent->index . '.value', []))) class="accent-primary shrink-0">
                                                            <span>{{ $opt->value }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                @break
                                            @case('textarea')
                                            @case('richtext')
                                                <label class="flex flex-col gap-1 text-sm text-body">
                                                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                                                    <textarea name="attributes[{{ $loop->index }}][value]" rows="3" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">{{ old('attributes.' . $loop->index . '.value') }}</textarea>
                                                </label>
                                                @break
                                            @case('number')
                                                <label class="flex flex-col gap-1 text-sm text-body">
                                                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                                                    <input type="number" name="attributes[{{ $loop->index }}][value]" value="{{ old('attributes.' . $loop->index . '.value') }}" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                                </label>
                                                @break
                                            @case('date')
                                                <label class="flex flex-col gap-1 text-sm text-body">
                                                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                                                    <input type="date" name="attributes[{{ $loop->index }}][value]" value="{{ old('attributes.' . $loop->index . '.value') }}" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                                </label>
                                                @break
                                            @case('datetime')
                                                <label class="flex flex-col gap-1 text-sm text-body">
                                                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                                                    <input type="datetime-local" name="attributes[{{ $loop->index }}][value]" value="{{ old('attributes.' . $loop->index . '.value') }}" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                                </label>
                                                @break
                                            @default
                                                <label class="flex flex-col gap-1 text-sm text-body">
                                                    <span>{{ $attribute->name }}@if($attribute->required) <span class="text-error">*</span>@endif</span>
                                                    <input type="text" name="attributes[{{ $loop->index }}][value]" value="{{ old('attributes.' . $loop->index . '.value') }}" {{ $attribute->required ? 'required' : '' }} class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">
                                                </label>
                                        @endswitch
                                        @storefrontError('attributes.' . $loop->index . '.value')
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    {{-- Order notes --}}
                    <section class="checkout__panel flex flex-col gap-4 p-5 bg-surface-card border border-border-subtle rounded-lg">
                        <header class="checkout__panel-head flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-primary shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 10.879 2H4.5Zm3.75 8.5a.75.75 0 0 0 0 1.5h3.5a.75.75 0 0 0 0-1.5h-3.5Zm0 3a.75.75 0 0 0 0 1.5h3.5a.75.75 0 0 0 0-1.5h-3.5Z" clip-rule="evenodd" /></svg>
                            <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Order notes')</h2>
                        </header>
                        <textarea name="comment" rows="3" placeholder="@t('Add a note to your order (optional)')" class="px-3 py-2 border border-surface-input-stroke rounded-lg bg-surface-input text-body focus:border-primary outline-none">{{ old('comment') }}</textarea>
                        @storefrontError('comment')
                    </section>

                </div>

                <aside class="checkout__sidebar flex flex-col gap-4 w-full desktop:w-96 p-5 bg-surface-card border border-border-subtle rounded-lg desktop:sticky desktop:top-6 desktop:self-start">
                    {{-- AJAX-refreshable order summary (coupon + totals). Swapped in
                         place after an AJAX coupon apply/remove; the terms +
                         place-order controls below live outside the section so
                         they survive the swap. --}}
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

                    <button type="submit" class="checkout__place-order inline-flex items-center justify-center gap-2 w-full rounded-lg bg-primary text-primary-content font-medium px-4 py-2.5 transition-colors hover:bg-primary-600 cursor-pointer">
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

            {{-- Payment-method description modals. Rendered OUTSIDE the checkout
                 <form> (HTML forbids nested forms; a stray </form> would orphan the
                 Place order button). Available to guests and customers alike, opened
                 by each method's "Read more" trigger (data-storefront-modal-open) via
                 storefront.js showModal(). With JS off the triggers are inert and the
                 method name still renders in the radio list. The body renders the
                 admin-configured description as HTML (storefront-richtext, mirroring
                 the product/category description blocks) and scrolls for long copy. --}}
            @if(!empty($billingMethods) && count($billingMethods) > 0)
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

            {{-- Shipping method detail modals — opened by the "Read more" triggers
                 in the shipping method radio list above. Renders the admin-configured
                 description as HTML (storefront-richtext), same pattern as payment methods. --}}
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

            {{-- Coupon apply/remove forms live OUTSIDE the checkout <form>: HTML
                 forbids nested forms, and the inner </form> would otherwise close
                 the checkout form early and orphan the "Place order" button. The
                 coupon controls in the summary above target these by id via `form`.
                 BOTH are always rendered (hidden) so those controls keep working
                 after an AJAX coupon apply/remove swaps the summary section in
                 place — the runtime never re-renders this block. --}}
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

    {{-- Live order-summary recompute. Selecting a shipping method, a delivery
         address, or toggling same-as-billing re-renders the `checkout-summary`
         section server-side WITH the real shipping charge (the platform runtime
         fetches GET /{locale}/sections?sections=checkout-summary&...). The
         "Place order" button total lives outside the swapped section, so its
         amount is mirrored from the freshly rendered summary. Progressive
         enhancement: with JS off the summary still shows the default method's
         shipping from the initial server render, and the order is priced at
         placement. --}}
    <script type="module">
        const summary = document.querySelector('[data-storefront-section="checkout-summary"]');
        if (summary) {
            const totalOut = document.querySelector('[data-checkout-total]')
                ?? document.querySelector('.checkout__place-order span:last-of-type');
            const recomputeNames = new Set([
                'shipping_method_id',
                'shipping_address_id',
                'billing_address_id',
                'shipping_address_is_same_as_billing',
            ]);

            const currentSelection = () => {
                const method = document.querySelector('input[name="shipping_method_id"]:checked')
                    ?? document.querySelector('input[type="hidden"][name="shipping_method_id"]');
                const sameToggle = document.querySelector('input[type="checkbox"][name="shipping_address_is_same_as_billing"]');
                return {
                    shipping_method_id: method?.value ?? null,
                    shipping_address_id: document.querySelector('input[name="shipping_address_id"]:checked')?.value ?? null,
                    billing_address_id: document.querySelector('input[name="billing_address_id"]:checked')?.value ?? null,
                    shipping_address_is_same_as_billing: sameToggle ? (sameToggle.checked ? 1 : 0) : null,
                };
            };

            const syncTotal = () => {
                const rendered = summary.querySelector('[data-checkout-section-total]')
                    ?? summary.querySelector('.checkout-summary__total span.text-lg');
                if (rendered && totalOut) {
                    totalOut.textContent = rendered.textContent?.trim() ?? '';
                }
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

            // Keep the button amount aligned with the currently rendered summary
            // immediately on page load before any interactive refresh happens.
            syncTotal();

            const summaryObserver = new MutationObserver(() => {
                syncTotal();
            });
            summaryObserver.observe(summary, { childList: true, subtree: true, characterData: true });

            let busy = false;
            let selfTriggered = false;
            const recompute = async () => {
                if (busy) {
                    return;
                }
                busy = true;
                selfTriggered = true;
                summary.classList.add('opacity-50');
                try {
                    const api = window.Storefront?.refresh ? window.Storefront : await waitForStorefront();
                    if (api?.refresh) {
                        await api.refresh(['checkout-summary'], currentSelection());
                    }
                } finally {
                    syncTotal();
                    summary.classList.remove('opacity-50');
                    busy = false;
                }
            };

            document.addEventListener('change', (event) => {
                if (recomputeNames.has(event.target?.name)) {
                    recompute();
                }
            });

            // A coupon apply/remove re-renders checkout-summary from its own POST,
            // which carries no shipping selection — restore the shipping charge
            // afterwards when a method is chosen. The guard stops our own refresh
            // from looping.
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
