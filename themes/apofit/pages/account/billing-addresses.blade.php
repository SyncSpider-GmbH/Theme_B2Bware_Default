@extends('layouts.shop')

@section('title', t('Billing addresses'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    @include('partials.account-addresses-page', [
        'pageTitle'        => t('Billing addresses'),
        'tab'              => 'billing',
        'addresses'        => $addresses,
        'defaultAddress'   => $defaultBillingAddress ?? null,
        'companyAddresses' => $companyAddresses ?? collect(),
        'shippingCount'    => $shippingCount ?? 0,
        'billingCount'     => $billingCount ?? $addresses->count(),
        'createForm'       => 'address-billing-create',
        'updateForm'       => 'address-billing-update',
        'deleteForm'       => 'address-billing-delete',
        'defaultForm'      => 'address-billing-default',
    ])
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
