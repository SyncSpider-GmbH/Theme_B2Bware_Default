{{--
    Login page — split-screen layout.

    Left panel  = brand / marketing (hidden below `lg`). Its background is the
                  tenant's `login_background_image` + a scrim when set, otherwise
                  the tokenized `.login__brand--gradient` navy panel defined in
                  `assets/css/storefront.css`.
    Right panel = the email + password sign-in form.

    Semantic `login__*` class hooks live alongside the Tailwind utilities so a
    custom theme can re-style individual parts from its own
    `assets/css/storefront.css` without forking this template. Every color,
    font and spacing value resolves through a design token (docs/styling.md, docs/tokens.md) —
    no hardcoded hex / px / font names.

    The form posts to `store.login.post` via `@storefrontForm('login')`, which
    emits the action URL + CSRF token + a hidden `redirect` field. The request
    accepts only `email`, `password`, `redirect`. Validation errors and old
    input are surfaced by the `web` middleware group (`$errors`, `old()`); the
    password is never repopulated. The show/hide toggle is a progressive
    enhancement — the field works as a plain password input without JS.
--}}
@extends('layouts.auth')

@section('title', t('Log in'))

{{--
    Tokenized navy gradient + brand glow for the left panel — shown only when the
    tenant has NOT set a `login_background_image` (otherwise the blade renders the
    tenant image + a scrim instead). This lives in an inline <style> rather than
    the theme's `assets/css/storefront.css` so the whole login design is contained
    in one file. It references ONLY design-token CSS variables (no hardcoded
    colors), so re-skinning the `secondary` / `primary` ramps re-skins this panel
    automatically. Pushed to `head` so it loads after `base.css` (which defines
    the `--color-*` tokens). Tailwind gradient utilities are not compiled into
    `base.css`, which is why this one rule is hand-written CSS (docs/styling.md §10.4).
--}}
@push('head')
    <style>
        .login__brand--gradient {
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
    <div class="login flex min-h-dvh w-full">
        {{-- ── Left: brand / marketing panel (hidden below lg) ─────────────── --}}
        <aside class="login__brand login__brand--gradient relative hidden flex-col overflow-hidden p-8 lg:flex lg:flex-1 lg:p-10">
            @if(!empty($store['login_background']))
                <div class="login__brand-bg absolute inset-0 z-0">
                    <img
                        src="{{ $store['login_background'] }}"
                        alt=""
                        class="login__brand-image h-full w-full object-cover"
                    >
                    <div class="login__brand-scrim absolute inset-0 bg-secondary-800/70"></div>
                </div>
            @endif

            {{-- Logo (top): store logo (if uploaded) on the left + store name --}}
            <a href="/" class="login__brand-logo relative z-10 inline-flex items-center gap-2 text-neutral-white">
                @if(!empty($store['logo']))
                    <img
                        src="{{ $store['logo'] }}"
                        alt="{{ $store['name'] }}"
                        class="login__brand-logo-image storefront-logo {{ !empty($store['logo_dark']) ? 'storefront-logo--light' : '' }} max-h-8 object-contain"
                    >
                    @if(!empty($store['logo_dark']))
                        <img
                            src="{{ $store['logo_dark'] }}"
                            alt="{{ $store['name'] }}"
                            class="login__brand-logo-image storefront-logo storefront-logo--dark max-h-8 object-contain"
                        >
                    @endif
                @endif
                <span class="login__brand-logo-text font-primary text-lg font-bold">{{ $store['name'] }}</span>
            </a>

            {{-- Value proposition (vertically centered) --}}
            <div class="login__brand-pitch relative z-10 my-auto max-w-420">
                <h2 class="login__brand-title font-primary text-3xl font-semibold tracking-tight text-neutral-white">
                    @t('Welcome back')
                </h2>
                <p class="login__brand-subtitle mt-3 text-sm leading-relaxed text-neutral-white/70">
                    @t('Sign in to access your account, orders and saved details.')
                </p>

                <ul class="login__brand-benefits mt-8 flex flex-col gap-5">
                    <li class="login__brand-benefit flex items-center gap-3">
                        <span class="login__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                        </span>
                        <span class="login__brand-benefit-label text-sm font-medium text-neutral-white">@t('Personalized prices and offers')</span>
                    </li>
                    <li class="login__brand-benefit flex items-center gap-3">
                        <span class="login__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <rect x="1" y="3" width="15" height="13"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                        </span>
                        <span class="login__brand-benefit-label text-sm font-medium text-neutral-white">@t('Fast order tracking and reordering')</span>
                    </li>
                    <li class="login__brand-benefit flex items-center gap-3">
                        <span class="login__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 11 11 13 15 9"></polyline>
                            </svg>
                        </span>
                        <span class="login__brand-benefit-label text-sm font-medium text-neutral-white">@t('Secure, private account access')</span>
                    </li>
                </ul>
            </div>

            {{-- Footer meta (bottom): store attribution --}}
            <p class="login__brand-meta relative z-10 text-xs tracking-wide text-neutral-white/70">
                &copy; {{ now()->year }} {{ $store['name'] }}
            </p>
        </aside>

        {{-- ── Right: auth form panel ──────────────────────────────────────── --}}
        <section class="login__auth flex flex-1 flex-col overflow-y-auto bg-surface-page p-6 lg:p-10">
            <div class="login__form-wrap m-auto w-full max-w-420">
                {{-- Logo on mobile (the brand panel that normally carries it is hidden):
                     store logo (if uploaded) + store name --}}
                <a href="/" class="login__mobile-logo mb-6 inline-flex items-center gap-2 lg:hidden">
                    @if(!empty($store['logo']))
                        <img src="{{ $store['logo'] }}" alt="{{ $store['name'] }}" class="login__mobile-logo-image max-h-10 object-contain">
                    @endif
                    <span class="login__mobile-logo-text font-primary text-lg font-bold text-headings">{{ $store['name'] }}</span>
                </a>

                {{-- Header: badge + heading + subheading --}}
                <span class="login__badge inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary-subtle text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </span>
                <h1 class="login__title mt-4 font-primary text-2xl font-semibold tracking-tight text-headings">
                    @t('Sign in to your account')
                </h1>
                <p class="login__subtitle mt-1.5 text-sm text-placeholder">
                    @t('Use your account credentials.')
                </p>

                {{-- Messages --}}
                @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null, 'class' => 'mt-6'])
                @include('components.banner', ['type' => 'info', 'message' => $messages['info'] ?? null, 'class' => 'mt-6'])
                @include('components.banner', ['type' => 'error', 'message' => $messages['error'] ?? null, 'class' => 'mt-6'])

                {{-- Form --}}
                @storefrontForm('login', ['class' => 'login__form mt-6 flex flex-col gap-4'])
                    {{-- Email --}}
                    <div class="login__field flex flex-col gap-1.5">
                        <label for="login-email" class="login__label text-sm font-medium text-body">@t('Email address')</label>
                        <div class="login__input flex items-center gap-2 rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 focus-within:border-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="login__input-icon h-5 w-5 shrink-0 text-placeholder" aria-hidden="true">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <input
                                id="login-email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="@t('Email address')"
                                class="w-full border-0 bg-transparent text-body outline-none placeholder:text-placeholder"
                            >
                        </div>
                        @storefrontError('email')
                    </div>

                    {{-- Password --}}
                    <div class="login__field flex flex-col gap-1.5">
                        <div class="login__label-row flex items-center justify-between gap-2">
                            <label for="login-password" class="login__label text-sm font-medium text-body">@t('Password')</label>
                            <a href="@routeUrl('store.forgot-password')" tabindex="-1" class="login__forgot-link text-sm font-medium text-primary hover:text-primary-600">
                                @t('Forgot password?')
                            </a>
                        </div>
                        <div class="login__input flex items-center gap-2 rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 focus-within:border-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="login__input-icon h-5 w-5 shrink-0 text-placeholder" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input
                                id="login-password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="@t('Password')"
                                class="w-full border-0 bg-transparent text-body outline-none placeholder:text-placeholder"
                            >
                            <button
                                type="button"
                                data-login-password-toggle
                                aria-label="@t('Show password')"
                                aria-pressed="false"
                                class="login__password-toggle hidden shrink-0 text-placeholder transition-colors hover:text-body"
                            >
                                <svg data-eye="show" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <svg data-eye="hide" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden h-5 w-5" aria-hidden="true">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                    <line x1="1" y1="1" x2="23" y2="23"></line>
                                </svg>
                            </button>
                        </div>
                        @storefrontError('password')
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="login__submit mt-2 inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2.5 font-semibold text-primary-content transition-colors hover:bg-primary-600 active:bg-primary-700 disabled:cursor-not-allowed disabled:bg-surface-disabled disabled:text-surface-disabled-content"
                    >
                        @t('Sign in')
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                @endstorefrontForm

                {{-- Sign up --}}
                @if(empty($store['restrict_registration']))
                    <p class="login__signup mt-6 text-center text-sm text-placeholder">
                        @t("Don't have an account?")
                        <a id="link-register" href="@routeUrl('store.register')" class="login__signup-link font-semibold text-primary hover:text-primary-600">
                            @t('Create account')
                        </a>
                    </p>
                @endif

                {{-- Legal --}}
                @if(!empty($store['privacy_policy_url']) || !empty($store['terms_and_conditions_url']))
                    <div class="login__legal mt-6 flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
                        @if(!empty($store['privacy_policy_url']))
                            <a href="{{ $store['privacy_policy_url'] }}" target="_blank" rel="noopener" class="login__legal-link text-xs text-placeholder hover:text-body">
                                @t('Privacy Policy')
                            </a>
                        @endif
                        @if(!empty($store['terms_and_conditions_url']))
                            <a href="{{ $store['terms_and_conditions_url'] }}" target="_blank" rel="noopener" class="login__legal-link text-xs text-placeholder hover:text-body">
                                @t('Terms & Conditions')
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </section>
    </div>

    {{-- Password show/hide toggle (progressive enhancement; the field works
         as a plain password input when JS is unavailable). Pushed to the
         layout's `scripts` stack so it runs after the page renders. --}}
    @push('scripts')
        <script type="module">
            const toggle = document.querySelector('[data-login-password-toggle]');
            const input = document.getElementById('login-password');
            if (toggle && input) {
                toggle.classList.remove('hidden');
                toggle.addEventListener('click', () => {
                    const reveal = input.type === 'password';
                    input.type = reveal ? 'text' : 'password';
                    toggle.setAttribute('aria-pressed', String(reveal));
                    for (const icon of toggle.querySelectorAll('[data-eye]')) {
                        icon.classList.toggle('hidden');
                    }
                });
            }
        </script>
    @endpush
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
