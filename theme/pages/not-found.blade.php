@extends('layouts.shop')

@section('title', t('Page not found'))

@section('content')
    <div class="page page--not-found flex flex-col items-center justify-center text-center gap-6 py-16">
        <p class="font-primary text-7xl font-bold text-primary m-0 leading-none">404</p>

        <div class="flex flex-col gap-2 max-w-640">
            <h1 class="font-primary text-2xl text-headings m-0">@t('Page not found')</h1>
            <p class="text-body m-0">@t('The page you are looking for does not exist, has been moved, or is no longer available.')</p>
        </div>

        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="@routeUrl('store.home')"
               class="bg-primary text-primary-content hover:bg-primary-600 rounded px-4 py-2 transition-colors no-underline">
                @t('Back to home')
            </a>
            <a href="@routeUrl('store.products')"
               class="bg-surface-card text-primary border border-surface-input-stroke hover:bg-surface-hover rounded px-4 py-2 transition-colors no-underline">
                @t('Browse products')
            </a>
        </div>
    </div>
@endsection
