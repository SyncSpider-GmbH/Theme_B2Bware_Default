@extends('layouts.shop')

@section('title', $store['name'])

@section('breadcrumbs')
@endsection

@section('content')
    <div class="page page--home flex flex-col gap-4">
        @storefrontSlot('content-top')
        <h1 class="font-primary m-0 text-3xl text-headings">{{ $store['name'] }}</h1>
        <p class="m-0 max-w-640 text-body">@t('Die bearbeitbare Startseite kommt aus dem Storefront Builder. Dieses Theme zeigt sie, sobald die Seite veröffentlicht und als Startseite gesetzt ist.')</p>
        <a href="@routeUrl('store.products')" class="inline-flex min-h-11 items-center self-start rounded-lg bg-primary px-5 py-3 font-medium text-primary-content hover:bg-primary-600 hover:no-underline">@t('Sortiment entdecken')</a>
        @storefrontSlot('content-bottom')
    </div>
@endsection
