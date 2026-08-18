{{--
    Forgot password page — split-screen layout (mirrors login.blade.php).

    Left panel  = brand / marketing (hidden below `lg`); the tokenized navy
                  `.auth__brand--gradient` panel, or the tenant's
                  `login_background_image` layered over it when set.
    Right panel = the "request a reset code" form.

    POSTs to `store.forgot-password.post` via `@storefrontForm`. The server
    response is intentionally identical whether or not the email belongs to a
    real account (no enumeration), so the only UI state surfaced here is a
    generic info flash. Every color / font / spacing value resolves through a
    design token (docs/styling.md, docs/tokens.md) — no hardcoded hex / px / font names.
--}}
@extends('layouts.auth')

@section('title', t('Forgot password'))

{{--
    Tokenized navy→blue gradient + brand glow for the left panel (identical to
    the login page). `isolation:isolate` makes the panel its own stacking-context
    floor so an optional background image (z-0) and the content (z-10) layer over
    it. Inline <style> so the whole page is self-contained; references ONLY
    design-token CSS variables, so re-skinning the ramps re-skins this panel.
--}}
@push('head')
    <style>
        .auth__brand--gradient {
            isolation: isolate;
            background-color: var(--color-secondary-800);
            background-image:
                radial-gradient(
                    circle at top right,
                    color-mix(in srgb, var(--color-neutral-white) 15%, transparent),
                    transparent 45%
                ),
                linear-gradient(
                    142deg,
                    var(--color-secondary-800) 0%,
                    var(--color-secondary-800) 50%,
                    var(--color-primary-700) 100%
                );
        }
    </style>
@endpush

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="auth-split flex min-h-dvh w-full">
        {{-- ── Left: brand / marketing panel (hidden below lg) ─────────────── --}}
        <aside class="auth__brand auth__brand--gradient relative hidden flex-col overflow-hidden p-8 lg:flex lg:flex-1 lg:p-10">
            @if(!empty($store['login_background']))
                <div class="auth__brand-bg absolute inset-0 z-0">
                    <img src="{{ $store['login_background'] }}" alt="" class="auth__brand-image h-full w-full object-cover">
                    <div class="auth__brand-scrim absolute inset-0 bg-secondary-800/70"></div>
                </div>
            @endif

            {{-- Logo (top): store logo (if uploaded) + store name --}}
            <a href="/" class="auth__brand-logo relative z-10 inline-flex items-center gap-2 text-neutral-white">
                @if(!empty($store['logo']))
                    <img src="{{ $store['logo'] }}" alt="{{ $store['name'] }}" class="auth__brand-logo-image max-h-8 object-contain">
                @endif
                <span class="auth__brand-logo-text font-primary text-lg font-bold">{{ $store['name'] }}</span>
            </a>

            {{-- Value proposition (vertically centered) --}}
            <div class="auth__brand-pitch relative z-10 my-auto max-w-420">
                <h2 class="auth__brand-title font-primary text-3xl font-semibold tracking-tight text-neutral-white">
                    @t('Welcome back')
                </h2>
                <p class="auth__brand-subtitle mt-3 text-sm leading-relaxed text-neutral-white/70">
                    @t('Sign in to access your account, orders and saved details.')
                </p>

                <ul class="auth__brand-benefits mt-8 flex flex-col gap-5">
                    <li class="auth__brand-benefit flex items-center gap-3">
                        <span class="auth__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                        </span>
                        <span class="auth__brand-benefit-label text-sm font-medium text-neutral-white">@t('Personalized prices and offers')</span>
                    </li>
                    <li class="auth__brand-benefit flex items-center gap-3">
                        <span class="auth__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <rect x="1" y="3" width="15" height="13"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                        </span>
                        <span class="auth__brand-benefit-label text-sm font-medium text-neutral-white">@t('Fast order tracking and reordering')</span>
                    </li>
                    <li class="auth__brand-benefit flex items-center gap-3">
                        <span class="auth__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 11 11 13 15 9"></polyline>
                            </svg>
                        </span>
                        <span class="auth__brand-benefit-label text-sm font-medium text-neutral-white">@t('Secure, private account access')</span>
                    </li>
                </ul>
            </div>

            {{-- Footer meta (bottom): store attribution --}}
            <p class="auth__brand-meta relative z-10 text-xs tracking-wide text-neutral-white/70">
                &copy; {{ now()->year }} {{ $store['name'] }}
            </p>
        </aside>

        {{-- ── Right: form panel ────────────────────────────────────────────── --}}
        <section class="auth__panel flex flex-1 flex-col overflow-y-auto bg-surface-page p-6 lg:p-10">
            <div class="auth__form-wrap m-auto w-full max-w-420">
                {{-- Logo on mobile (the brand panel that normally carries it is hidden) --}}
                <a href="/" class="auth__mobile-logo mb-6 inline-flex items-center gap-2 lg:hidden">
                    @if(!empty($store['logo']))
                        <img src="{{ $store['logo'] }}" alt="{{ $store['name'] }}" class="auth__mobile-logo-image max-h-10 object-contain">
                    @endif
                    <span class="auth__mobile-logo-text font-primary text-lg font-bold text-headings">{{ $store['name'] }}</span>
                </a>

                {{-- Header: badge + heading + subheading --}}
                <span class="auth__badge inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary-subtle text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </span>
                <h1 class="auth__title mt-4 font-primary text-2xl font-semibold tracking-tight text-headings">
                    @t('Forgot password')
                </h1>
                <p class="auth__subtitle mt-1.5 text-sm text-placeholder">
                    @t('Enter the email associated with your account and we will send you a reset code.')
                </p>

                {{-- Message (generic; no account enumeration) --}}
                @include('components.banner', ['type' => 'info', 'message' => $messages['info'] ?? null, 'class' => 'mt-6'])

                {{-- Form --}}
                @storefrontForm('forgot-password', ['class' => 'auth__form mt-6 flex flex-col gap-4'])
                    <div class="auth__field flex flex-col gap-1.5">
                        <label for="forgot-email" class="auth__label text-sm font-medium text-body">@t('Email address')</label>
                        <div class="auth__input flex items-center gap-2 rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 focus-within:border-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="auth__input-icon h-5 w-5 shrink-0 text-placeholder" aria-hidden="true">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <input
                                id="forgot-email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="@t('name@surname.com')"
                                class="w-full border-0 bg-transparent text-body outline-none placeholder:text-placeholder"
                            >
                        </div>
                        @storefrontError('email')
                    </div>

                    <button
                        type="submit"
                        class="auth__submit inline-flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 font-semibold text-primary-content transition-colors hover:bg-primary-600 active:bg-primary-700 cursor-pointer"
                    >
                        @t('Send reset code')
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                @endstorefrontForm

                {{-- Back to login --}}
                <a href="@routeUrl('store.login')" class="auth__back mt-6 inline-flex items-center justify-center gap-1.5 text-sm font-medium text-body hover:text-headings hover:no-underline">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    @t('Back to sign in')
                </a>
            </div>
        </section>
    </div>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection

