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
<body class="storefront storefront--auth bg-surface-page text-body" data-locale="{{ $locale }}">
    <main>
        @yield('content')
    </main>

    @storefrontScripts
    @stack('scripts')
</body>
</html>
