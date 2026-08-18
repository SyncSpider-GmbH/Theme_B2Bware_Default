@extends('layouts.shop')

@section('title', t('Shipping addresses'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    @include('partials.account-addresses-page', [
        'pageTitle'        => t('Shipping addresses'),
        'tab'              => 'shipping',
        'addresses'        => $addresses,
        'defaultAddress'   => $defaultShippingAddress ?? null,
        'companyAddresses' => $companyAddresses ?? collect(),
        'shippingCount'    => $shippingCount ?? $addresses->count(),
        'billingCount'     => $billingCount ?? 0,
        'createForm'       => 'address-shipping-create',
        'updateForm'       => 'address-shipping-update',
        'deleteForm'       => 'address-shipping-delete',
        'defaultForm'      => 'address-shipping-default',
    ])
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
