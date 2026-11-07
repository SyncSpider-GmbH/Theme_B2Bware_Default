@extends('layouts.shop')

@section('title', $store['name'])

@section('hero')
    <section class="apofit-hero">
        <div class="mx-auto flex w-full max-w-desktop flex-col gap-4 px-4 py-10 md:py-16">
            <p class="apofit-hero__kicker">@t('Natur & Wohlbefinden')</p>
            <h1 class="font-primary m-0 text-3xl font-semibold text-headings md:text-5xl">{{ $store['name'] }}</h1>
            <p class="m-0 max-w-640 text-base text-body">@t('Ausgewählte Produkte für Pflege, Aroma und Gesundheit. Öffentliche Preise, Bestellung auch als Gast. Staffelpreise sehen nur angemeldete, berechtigte Kunden.')</p>
            <div class="flex flex-wrap gap-3">
                <a href="@routeUrl('store.products')" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-primary px-5 py-3 font-medium text-primary-content hover:bg-primary-600 hover:no-underline">@t('Sortiment entdecken')</a>
                @if(!$isAuthenticated)
                    <a href="@routeUrl('store.login')" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-primary px-5 py-3 font-medium text-primary hover:bg-primary-subtle hover:no-underline">@t('Anmelden für Kundenpreise')</a>
                @else
                    <a href="@routeUrl('store.account.index')" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-primary px-5 py-3 font-medium text-primary hover:bg-primary-subtle hover:no-underline">@t('My account')</a>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('breadcrumbs')
@endsection

@section('content')
    <div class="page page--home flex flex-col gap-8">
        @storefrontSlot('content-top')

        <section class="flex flex-col gap-4" aria-labelledby="apofit-home-categories">
            <div class="flex items-end justify-between gap-3">
                <h2 id="apofit-home-categories" class="font-primary m-0 text-2xl font-semibold text-headings">@t('Sortiment')</h2>
                <a href="@routeUrl('store.categories')" class="text-sm font-medium text-primary hover:text-primary-600 hover:no-underline">@t('Categories')</a>
            </div>
            @if(($store['category_ui_type'] ?? 'list') === 'mega_menu' && $megaMenuCategories->isNotEmpty())
                <ul class="m-0 grid list-none grid-cols-2 gap-3 p-0 lg:grid-cols-4">
                    @foreach($megaMenuCategories as $category)
                        @if(data_get($category, 'menu_path'))
                            <li>
                                <a href="@routeUrl('store.category', ['slug' => data_get($category, 'menu_path')])" class="flex h-full min-h-11 flex-col justify-between gap-2 rounded-xl border border-border-subtle bg-surface-card p-4 text-headings hover:border-primary hover:no-underline">
                                    <span class="font-primary text-lg font-semibold">{{ $category->name }}</span>
                                    @if(count($category->children ?? []) > 0)
                                        <span class="text-sm text-body">@t('%d Unterkategorien', count($category->children))</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @elseif($rootCategories->isNotEmpty())
                <ul class="m-0 grid list-none grid-cols-2 gap-3 p-0 lg:grid-cols-4">
                    @foreach($rootCategories as $category)
                        <li>
                            <a href="@routeUrl('store.category', ['slug' => data_get($category, 'menu_path') ?: data_get($category, 'seo.slug') ?: data_get($category, 'slug', '')])" class="flex h-full min-h-11 flex-col justify-between gap-2 rounded-xl border border-border-subtle bg-surface-card p-4 text-headings hover:border-primary hover:no-underline">
                                <span class="font-primary text-lg font-semibold">{{ $category->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                @include('components.empty-state', [
                    'title' => t('No categories'),
                    'message' => t('Sortiment entdecken'),
                ])
            @endif
        </section>

        <section class="grid grid-cols-1 gap-3 md:grid-cols-3" aria-label="@t('Shop')">
            <article class="rounded-xl border border-border-subtle bg-surface-card p-5">
                <h2 class="font-primary m-0 text-lg font-semibold text-headings">@t('Öffentliche Preise')</h2>
                <p class="mb-0 mt-2 text-sm text-body">@t('Gäste sehen die aktuellen Shop-Preise und können ohne Konto bestellen.')</p>
            </article>
            <article class="rounded-xl border border-border-subtle bg-surface-card p-5">
                <h2 class="font-primary m-0 text-lg font-semibold text-headings">@t('Kundenpreise')</h2>
                <p class="mb-0 mt-2 text-sm text-body">@t('Staffel- und Sonderpreise erscheinen nur nach Anmeldung und nur, wenn der Server sie für dieses Konto liefert.')</p>
            </article>
            <article class="rounded-xl border border-border-subtle bg-surface-card p-5">
                <h2 class="font-primary m-0 text-lg font-semibold text-headings">@t('Beratung')</h2>
                <p class="mb-0 mt-2 text-sm text-body">
                    @if(!empty($store['contact_email']))
                        <a href="mailto:{{ $store['contact_email'] }}" class="text-primary hover:text-primary-600">{{ $store['contact_email'] }}</a>
                    @else
                        @t('Kontakt über den Shop.')
                    @endif
                </p>
            </article>
        </section>

        @storefrontSlot('content-bottom')
    </div>
@endsection
