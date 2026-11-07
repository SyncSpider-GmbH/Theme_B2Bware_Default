<!DOCTYPE html>
<html lang="{{ $locale }}" @class(['dark' => ($colorScheme ?? null) === 'dark'])>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- No-flash light/dark bootstrap: applies the saved cookie / OS preference
         to <html> before first paint. Must come before the stylesheets. --}}
    @storefrontColorScheme
    {{-- Consent-gated analytics loader (GTM / GA4 / Clarity). Emits nothing when
         the tenant has configured no tracking. --}}
    @storefrontAnalytics
    <title>@yield('title', $store['name'])</title>
    @if(!empty($store['favicon']))
        <link rel="icon" href="{{ $store['favicon'] }}">
    @endif
    @storefrontSeo
    @yield('head')
    {{-- 1. Global platform utility CSS --}}
    <link rel="stylesheet" href="@storefrontAsset('base.css')">
    @if(!empty($tenantBrandCss))
        {{-- 2. Tenant brand colours for both schemes (:root + :root.dark) --}}
        <style>{!! $tenantBrandCss !!}</style>
    @endif
    @if(!empty($tenantFontsHtml))
        {{-- 2b. Tenant-selected Storefront (new) Google Fonts --}}
        {!! $tenantFontsHtml !!}
    @endif
    {{-- 3. Theme override CSS — after Appearance, so a theme that deliberately
         redeclares a token owns it. Defaults a theme wants Appearance to keep
         overriding go in a @layer (see docs/tokens.md §11). --}}
    <link rel="stylesheet" href="@themeAsset('css/storefront.css')">
    @if(!empty($brandingCss))
        {{-- 4. Per-customer category branding (overrides Appearance and the theme) --}}
        <style>{!! $brandingCss !!}</style>
    @endif
    @stack('head')
</head>
<body class="storefront storefront--shop bg-surface-page text-body font-secondary flex flex-col min-h-screen" data-locale="{{ $locale }}" data-currency="{{ $currency }}">
    {{-- Standalone documents (e.g. the public proposal share page) opt out of
         the shop chrome: no header/footer/cart UI, just the page content. --}}
    @unless($hideChrome ?? false)
        {{-- Owner-editable announcement bar (topmost, site-wide). Renders nothing
             until a shop manager assigns content to the 'announcement' slot. --}}
        @storefrontSlot('announcement')
        @include('partials.impersonation-banner')
        @include('partials.header')
    @endunless

    {{-- Owner-editable, full-width hero region, rendered above the breadcrumbs.
         Page-scoped: each page fills its own 'hero' section (see the catalog
         pages). Renders nothing until a page provides one. --}}
    @yield('hero')

    <main class="storefront__main flex-1 w-full mx-auto max-w-desktop px-4 py-6 flex flex-col gap-4">
        @unless($hideChrome ?? false)
            @section('breadcrumbs')
                @include('partials.breadcrumbs')
            @show
        @endunless

        @yield('content')
    </main>

    @unless($hideChrome ?? false)
        @include('partials.footer')

        {{-- Added-to-cart confirmation (optional UX — see docs/ajax-and-runtime.md §9.7).
             Override `partials/added-to-cart-modal.blade.php` in your theme for a
             custom drawer/toast, or omit this @include to suppress it entirely. --}}
        @include('partials.added-to-cart-modal')
    @endunless

    {{-- Storefront JS runtime: progressive-enhancement AJAX for cart forms +
         section swapping. Themes that omit it still work (forms POST normally). --}}
    @storefrontScripts
    @stack('scripts')
</body>
</html>
