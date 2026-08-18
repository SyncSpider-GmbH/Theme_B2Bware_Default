{{--
    Register page — split-screen layout (mirrors login.blade.php).

    Left panel  = brand / marketing (hidden below `lg`); tokenized navy→blue
                  `.register__brand--gradient`.
    Right panel = the registration form. The company fieldset (salutation,
                  first/last name + the company block) renders only when the
                  tenant's `register.company.required` flag is truthy
                  (`$store['company_required']`); on B2C the form collapses to
                  email + phone + password + terms. Passwords are NEVER
                  re-populated from `old()`.

    POSTs to `store.register.post` via `@storefrontForm`. Every input keeps its
    exact `name=""` so validation (`@storefrontError`) and `old()` repopulation
    are unchanged. Password show/hide toggles + the password-requirements
    checklist (`$passwordRequirements`, see docs/view-data.md §9.6) are progressive
    enhancements — the form works without JS. Every color / font / spacing value
    resolves through a design token (docs/styling.md, docs/tokens.md) — no hardcoded hex / px /
    font names.
--}}
@extends('layouts.auth')

@section('title', t('Register'))

{{--
    Tokenized navy→blue gradient + brand glow for the left panel (identical to
    the login page). Inline <style> so the whole register design lives in one
    file; references ONLY design-token CSS variables, so re-skinning the
    `secondary` / `primary` ramps re-skins this panel automatically. Pushed to
    `head` so it loads after `base.css` (which defines the `--color-*` tokens).
    Tailwind gradient utilities are not compiled into `base.css`, which is why
    this one rule is hand-written CSS (docs/styling.md §10.4).
--}}
@push('head')
    <style>
        .register__brand--gradient {
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

        /* Tint the native terms checkbox with the primary token (no Tailwind
           accent-* utility is compiled into base.css; this keeps it token-based
           without a rebuild). */
        .register__checkbox {
            accent-color: var(--color-primary);
        }

        /* Wizard step indicator (only rendered when a company is required). Geometry
           is fixed chrome; colors resolve through design tokens. */
        .register__step-dot {
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 9999px;
            background-color: var(--color-surface-input-stroke);
            transition: background-color 150ms ease;
        }
        .register__step-dot[data-active="true"] {
            background-color: var(--color-primary);
        }
        .register__step-line {
            height: 2px;
            flex: 1 1 0%;
            border-radius: 9999px;
            background-color: var(--color-surface-input-stroke);
        }

        /* Live password-requirements checklist — ticked by the inline script
           further down as the visitor types. Kept inline (not in the theme
           stylesheet) because the theme CSS is served with an immutable 1-year
           cache and no version query, so a returning browser would never see a
           freshly-added rule there. */
        .password-req {
            color: var(--color-placeholder);
            transition: color 150ms ease;
        }
        .password-req__badge {
            display: inline-grid;
            place-items: center;
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
            border-radius: 9999px;
            background-color: var(--color-surface-input-stroke);
            color: var(--color-neutral-white);
            transition: background-color 150ms ease;
        }
        .password-req__badge svg {
            width: 0.625rem;
            height: 0.625rem;
        }
        .password-req[data-met="true"] {
            color: var(--color-primary);
        }
        .password-req[data-met="true"] .password-req__badge {
            background-color: var(--color-primary);
        }
    </style>
@endpush

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="register flex min-h-dvh w-full">
        {{-- ── Left: brand / marketing panel (hidden below lg) ─────────────── --}}
        <aside class="register__brand register__brand--gradient relative hidden flex-col overflow-hidden p-8 lg:flex lg:flex-1 lg:p-10">
            @if(!empty($store['login_background']))
                <div class="register__brand-bg absolute inset-0 z-0">
                    <img src="{{ $store['login_background'] }}" alt="" class="register__brand-image h-full w-full object-cover">
                    <div class="register__brand-scrim absolute inset-0 bg-secondary-800/70"></div>
                </div>
            @endif

            {{-- Logo (top): store logo (if uploaded) + store name --}}
            <a href="/" class="register__brand-logo relative z-10 inline-flex items-center gap-2 text-neutral-white">
                @if(!empty($store['logo']))
                    <img
                        src="{{ $store['logo'] }}"
                        alt="{{ $store['name'] }}"
                        class="register__brand-logo-image max-h-8 object-contain"
                    >
                @endif
                <span class="register__brand-logo-text font-primary text-lg font-bold">{{ $store['name'] }}</span>
            </a>

            {{-- Value proposition (vertically centered) --}}
            <div class="register__brand-pitch relative z-10 my-auto max-w-420">
                <h2 class="register__brand-title font-primary text-3xl font-semibold tracking-tight text-neutral-white">
                    @t('Create your account')
                </h2>
                <p class="register__brand-subtitle mt-3 text-sm leading-relaxed text-neutral-white/70">
                    @t('Set up your account to access pricing, place orders and manage your details.')
                </p>

                <ul class="register__brand-benefits mt-8 flex flex-col gap-5">
                    <li class="register__brand-benefit flex items-center gap-3">
                        <span class="register__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                        </span>
                        <span class="register__brand-benefit-label text-sm font-medium text-neutral-white">@t('Trade pricing and volume discounts')</span>
                    </li>
                    <li class="register__brand-benefit flex items-center gap-3">
                        <span class="register__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <rect x="1" y="3" width="15" height="13"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                        </span>
                        <span class="register__brand-benefit-label text-sm font-medium text-neutral-white">@t('Fast dispatch and order tracking')</span>
                    </li>
                    <li class="register__brand-benefit flex items-center gap-3">
                        <span class="register__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 11 11 13 15 9"></polyline>
                            </svg>
                        </span>
                        <span class="register__brand-benefit-label text-sm font-medium text-neutral-white">@t('Invoices and addresses in one place')</span>
                    </li>
                </ul>
            </div>

            {{-- Footer meta (bottom): store attribution --}}
            <p class="register__brand-meta relative z-10 text-xs tracking-wide text-neutral-white/70">
                &copy; {{ now()->year }} {{ $store['name'] }}
            </p>
        </aside>

        {{-- ── Right: registration form panel ──────────────────────────────── --}}
        <section class="register__auth flex flex-1 flex-col overflow-y-auto bg-surface-page p-6 lg:p-10">
            <div class="register__form-wrap m-auto w-full max-w-420">
                {{-- Logo on mobile (the brand panel that normally carries it is hidden) --}}
                <a href="/" class="register__mobile-logo mb-6 inline-flex items-center gap-2 lg:hidden">
                    @if(!empty($store['logo']))
                        <img src="{{ $store['logo'] }}" alt="{{ $store['name'] }}" class="register__mobile-logo-image max-h-10 object-contain">
                    @endif
                    <span class="register__mobile-logo-text font-primary text-lg font-bold text-headings">{{ $store['name'] }}</span>
                </a>

                {{-- Header: badge + heading + subheading --}}
                <span class="register__badge inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary-subtle text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <line x1="19" y1="8" x2="19" y2="14"></line>
                        <line x1="22" y1="11" x2="16" y2="11"></line>
                    </svg>
                </span>
                <h1 class="register__title mt-4 font-primary text-2xl font-semibold tracking-tight text-headings">
                    @t('Create an account')
                </h1>
                @if($store['company_required'])
                    <p class="register__subtitle mt-1.5 text-sm text-placeholder">
                        <span data-step-subtitle="1">@t('Step 1 of 2: your details')</span>
                        <span data-step-subtitle="2" class="hidden">@t('Step 2 of 2: company details')</span>
                    </p>
                @else
                    <p class="register__subtitle mt-1.5 text-sm text-placeholder">@t('Enter your details to get started.')</p>
                @endif

                {{-- Messages --}}
                @include('components.banner', ['type' => 'info', 'message' => $messages['info'] ?? null, 'class' => 'mt-6'])

                {{-- Form --}}
                @storefrontForm('register', ['class' => 'register__form mt-6 flex flex-col gap-4'])
                    @if($store['company_required'])
                        {{-- Step indicator (also the wizard's on/off switch in the script below) --}}
                        <div
                            class="register__stepper flex items-center gap-2"
                            data-register-stepper
                            data-initial-step="{{ $errors->hasAny(['company.name', 'company.phone', 'company.address_line_1', 'company.zip', 'company.city', 'company.country', 'company.website', 'company.vat_number', 'company.registration_number', 'terms_and_conditions']) ? '2' : '1' }}"
                        >
                            <span class="register__step-dot" data-step-dot="1" data-active="true" aria-hidden="true"></span>
                            <span class="register__step-line"></span>
                            <span class="register__step-dot" data-step-dot="2" data-active="false" aria-hidden="true"></span>
                        </div>
                    @endif

                    {{-- Step 1 — customer details (a plain block on B2C; the first wizard step on B2B) --}}
                    <div class="register__step flex flex-col gap-4" data-step="1">
                    {{-- Email --}}
                    <div class="register__field flex flex-col gap-1.5">
                        <label for="register-email" class="register__label text-sm font-medium text-body">@t('Email address')</label>
                        <div class="register__input flex items-center gap-2 rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 focus-within:border-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="register__input-icon h-5 w-5 shrink-0 text-placeholder" aria-hidden="true">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                            <input
                                id="register-email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="email"
                                placeholder="@t('Email address')"
                                class="w-full border-0 bg-transparent text-body outline-none placeholder:text-placeholder"
                            >
                        </div>
                        @storefrontError('email')
                    </div>

                    @if($store['company_required'])
                        {{-- Salutation --}}
                        <div class="register__field flex flex-col gap-1.5">
                            <label for="register-salutation" class="register__label text-sm font-medium text-body">@t('Salutation')</label>
                            <select
                                id="register-salutation"
                                name="salutation"
                                required
                                class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary"
                            >
                                <option value="">@t('Select')</option>
                                <option value="Mr." @selected(old('salutation') === 'Mr.')>@t('Mr.')</option>
                                <option value="Ms." @selected(old('salutation') === 'Ms.')>@t('Ms.')</option>
                                <option value="Other" @selected(old('salutation') === 'Other')>@t('Other')</option>
                            </select>
                            @storefrontError('salutation')
                        </div>

                        {{-- First / last name --}}
                        <div class="register__row grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="register__field flex flex-col gap-1.5">
                                <label for="register-first-name" class="register__label text-sm font-medium text-body">@t('First name')</label>
                                <input
                                    id="register-first-name"
                                    type="text"
                                    name="first_name"
                                    value="{{ old('first_name') }}"
                                    required
                                    autocomplete="given-name"
                                    placeholder="@t('e.g. John')"
                                    class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                >
                            </div>
                            <div class="register__field flex flex-col gap-1.5">
                                <label for="register-last-name" class="register__label text-sm font-medium text-body">@t('Last name')</label>
                                <input
                                    id="register-last-name"
                                    type="text"
                                    name="last_name"
                                    value="{{ old('last_name') }}"
                                    required
                                    autocomplete="family-name"
                                    placeholder="@t('e.g. Smith')"
                                    class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                >
                            </div>
                        </div>
                        @storefrontError('first_name')
                        @storefrontError('last_name')
                    @endif

                    {{-- Phone (always; optional) --}}
                    <div class="register__field flex flex-col gap-1.5">
                        <label for="register-phone" class="register__label text-sm font-medium text-body">@t('Phone')</label>
                        <input
                            id="register-phone"
                            type="tel"
                            name="phone"
                            value="{{ old('phone') }}"
                            autocomplete="tel"
                            placeholder="@t('e.g. +1 555 123 4567')"
                            class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                        >
                    </div>

                    {{-- Password --}}
                    <div class="register__field flex flex-col gap-1.5">
                        <label for="register-password" class="register__label text-sm font-medium text-body">@t('Password')</label>
                        <div class="register__input flex items-center gap-2 rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 focus-within:border-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="register__input-icon h-5 w-5 shrink-0 text-placeholder" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input
                                id="register-password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="@t('Password')"
                                class="w-full border-0 bg-transparent text-body outline-none placeholder:text-placeholder"
                            >
                            <button
                                type="button"
                                data-password-toggle="register-password"
                                aria-label="@t('Show password')"
                                aria-pressed="false"
                                class="register__password-toggle hidden shrink-0 text-placeholder transition-colors hover:text-body"
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
                        @if(!empty($passwordRules))
                            <ul class="register__password-hints mt-2 flex flex-col gap-1.5" data-password-checklist="register-password">
                                @foreach($passwordRules as $rule)
                                    <li class="password-req flex items-center gap-2 text-xs" data-password-rule="{{ $rule['rule'] }}" data-min="{{ $rule['length'] ?? '' }}" data-met="false">
                                        <span class="password-req__badge">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </span>
                                        <span>@t($rule['label'])</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- Confirm password --}}
                    <div class="register__field flex flex-col gap-1.5">
                        <label for="register-password-confirm" class="register__label text-sm font-medium text-body">@t('Confirm password')</label>
                        <div class="register__input flex items-center gap-2 rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 focus-within:border-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="register__input-icon h-5 w-5 shrink-0 text-placeholder" aria-hidden="true">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <input
                                id="register-password-confirm"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="@t('Confirm password')"
                                class="w-full border-0 bg-transparent text-body outline-none placeholder:text-placeholder"
                            >
                            <button
                                type="button"
                                data-password-toggle="register-password-confirm"
                                aria-label="@t('Show password')"
                                aria-pressed="false"
                                class="register__password-toggle hidden shrink-0 text-placeholder transition-colors hover:text-body"
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
                    </div>

                        @if($store['company_required'])
                            {{-- Continue to step 2 (revealed by JS; without JS step 2 renders stacked below) --}}
                            <button
                                type="button"
                                data-register-next
                                class="register__next hidden mt-2 inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2.5 font-semibold text-primary-content transition-colors hover:bg-primary-600 active:bg-primary-700"
                            >
                                @t('Continue')
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </button>
                        @endif
                    </div>{{-- /step 1 --}}

                    {{-- Step 2 — company details (B2B only) + terms + submit --}}
                    <div class="register__step flex flex-col gap-4" data-step="2">
                        @if($store['company_required'])
                            {{-- Company --}}
                            <fieldset class="register__fieldset flex flex-col gap-3 rounded-lg border border-surface-input-stroke p-3">
                                <legend class="register__legend px-1 text-sm font-medium text-headings">@t('Company')</legend>

                                <div class="register__field flex flex-col gap-1.5">
                                    <label for="register-company-name" class="register__label text-sm font-medium text-body">
                                        @t('Company name')@if($companyRequiredFields['name'] ?? false)<span aria-hidden="true" class="text-error ml-0.5">*</span>@endif
                                    </label>
                                    <input
                                        id="register-company-name"
                                        type="text"
                                        name="company[name]"
                                        value="{{ old('company.name') }}"
                                        {{ ($companyRequiredFields['name'] ?? false) ? 'required' : '' }}
                                        autocomplete="organization"
                                        placeholder="@t('e.g. Acme Industries Ltd')"
                                        class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                    >
                                    @storefrontError('company.name')
                                </div>

                                <div class="register__field flex flex-col gap-1.5">
                                    <label for="register-company-phone" class="register__label text-sm font-medium text-body">
                                        @t('Company phone')@if($companyRequiredFields['phone'] ?? false)<span aria-hidden="true" class="text-error ml-0.5">*</span>@endif
                                    </label>
                                    <input
                                        id="register-company-phone"
                                        type="tel"
                                        name="company[phone]"
                                        value="{{ old('company.phone') }}"
                                        {{ ($companyRequiredFields['phone'] ?? false) ? 'required' : '' }}
                                        autocomplete="tel"
                                        placeholder="@t('e.g. +1 555 123 4567')"
                                        class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                    >
                                    @storefrontError('company.phone')
                                </div>

                                <div class="register__field flex flex-col gap-1.5">
                                    <label for="register-company-address-1" class="register__label text-sm font-medium text-body">
                                        @t('Address')@if($companyRequiredFields['address'] ?? false)<span aria-hidden="true" class="text-error ml-0.5">*</span>@endif
                                    </label>
                                    <input
                                        id="register-company-address-1"
                                        type="text"
                                        name="company[address_line_1]"
                                        value="{{ old('company.address_line_1') }}"
                                        {{ ($companyRequiredFields['address'] ?? false) ? 'required' : '' }}
                                        autocomplete="address-line1"
                                        placeholder="@t('e.g. 123 Market Street')"
                                        class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                    >
                                    @storefrontError('company.address_line_1')
                                </div>

                                <div class="register__field flex flex-col gap-1.5">
                                    <label for="register-company-address-2" class="register__label text-sm font-medium text-body">@t('Address line 2')</label>
                                    <input
                                        id="register-company-address-2"
                                        type="text"
                                        name="company[address_line_2]"
                                        value="{{ old('company.address_line_2') }}"
                                        autocomplete="address-line2"
                                        placeholder="@t('e.g. Suite 400')"
                                        class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                    >
                                </div>

                                <div class="register__row grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="register__field flex flex-col gap-1.5">
                                        <label for="register-company-zip" class="register__label text-sm font-medium text-body">
                                            @t('ZIP / Postal code')@if($companyRequiredFields['address'] ?? false)<span aria-hidden="true" class="text-error ml-0.5">*</span>@endif
                                        </label>
                                        <input
                                            id="register-company-zip"
                                            type="text"
                                            name="company[zip]"
                                            value="{{ old('company.zip') }}"
                                            {{ ($companyRequiredFields['address'] ?? false) ? 'required' : '' }}
                                            autocomplete="postal-code"
                                            placeholder="@t('e.g. 10115')"
                                            class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                        >
                                    </div>
                                    <div class="register__field flex flex-col gap-1.5">
                                        <label for="register-company-city" class="register__label text-sm font-medium text-body">
                                            @t('City')@if($companyRequiredFields['address'] ?? false)<span aria-hidden="true" class="text-error ml-0.5">*</span>@endif
                                        </label>
                                        <input
                                            id="register-company-city"
                                            type="text"
                                            name="company[city]"
                                            value="{{ old('company.city') }}"
                                            {{ ($companyRequiredFields['address'] ?? false) ? 'required' : '' }}
                                            autocomplete="address-level2"
                                            placeholder="@t('e.g. Berlin')"
                                            class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                        >
                                    </div>
                                </div>
                                @storefrontError('company.zip')
                                @storefrontError('company.city')

                                <div class="register__field flex flex-col gap-1.5">
                                    <label for="register-company-state" class="register__label text-sm font-medium text-body">@t('State / Region')</label>
                                    <input
                                        id="register-company-state"
                                        type="text"
                                        name="company[state]"
                                        value="{{ old('company.state') }}"
                                        autocomplete="address-level1"
                                        placeholder="@t('e.g. California')"
                                        class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                    >
                                </div>

                                <div class="register__field flex flex-col gap-1.5">
                                    <label for="register-company-country" class="register__label text-sm font-medium text-body">
                                        @t('Country')@if($companyRequiredFields['address'] ?? false)<span aria-hidden="true" class="text-error ml-0.5">*</span>@endif
                                    </label>
                                    <input
                                        id="register-company-country"
                                        type="text"
                                        name="company[country]"
                                        value="{{ old('company.country') }}"
                                        {{ ($companyRequiredFields['address'] ?? false) ? 'required' : '' }}
                                        autocomplete="country-name"
                                        placeholder="@t('e.g. Germany')"
                                        class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                    >
                                    @storefrontError('company.country')
                                </div>

                                <div class="register__field flex flex-col gap-1.5">
                                    <label for="register-company-vat" class="register__label text-sm font-medium text-body">
                                        @t('VAT number')@if($companyRequiredFields['vat_number'] ?? false)<span aria-hidden="true" class="text-error ml-0.5">*</span>@endif
                                    </label>
                                    <input
                                        id="register-company-vat"
                                        type="text"
                                        name="company[vat_number]"
                                        value="{{ old('company.vat_number') }}"
                                        {{ ($companyRequiredFields['vat_number'] ?? false) ? 'required' : '' }}
                                        placeholder="@t('e.g. DE123456789')"
                                        class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                    >
                                    @storefrontError('company.vat_number')
                                </div>

                                <div class="register__field flex flex-col gap-1.5">
                                    <label for="register-company-website" class="register__label text-sm font-medium text-body">
                                        @t('Website')@if($companyRequiredFields['website'] ?? false)<span aria-hidden="true" class="text-error ml-0.5">*</span>@endif
                                    </label>
                                    <input
                                        id="register-company-website"
                                        type="url"
                                        name="company[website]"
                                        value="{{ old('company.website') }}"
                                        {{ ($companyRequiredFields['website'] ?? false) ? 'required' : '' }}
                                        autocomplete="url"
                                        placeholder="https://"
                                        class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                    >
                                    @storefrontError('company.website')
                                </div>

                                <div class="register__field flex flex-col gap-1.5">
                                    <label for="register-company-registration" class="register__label text-sm font-medium text-body">
                                        @t('Registration number')@if($companyRequiredFields['registration_number'] ?? false)<span aria-hidden="true" class="text-error ml-0.5">*</span>@endif
                                    </label>
                                    <input
                                        id="register-company-registration"
                                        type="text"
                                        name="company[registration_number]"
                                        value="{{ old('company.registration_number') }}"
                                        {{ ($companyRequiredFields['registration_number'] ?? false) ? 'required' : '' }}
                                        placeholder="@t('e.g. HRB 12345')"
                                        class="register__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 text-body outline-none focus:border-primary placeholder:text-placeholder"
                                    >
                                    @storefrontError('company.registration_number')
                                </div>
                            </fieldset>
                        @endif

                    {{-- Terms --}}
                    <label class="register__terms flex items-start gap-2 text-sm text-body">
                        <input
                            type="checkbox"
                            name="terms_and_conditions"
                            value="1"
                            required
                            @checked(old('terms_and_conditions'))
                            class="register__checkbox mt-0.5 h-4 w-4 shrink-0"
                        >
                        <span>@t('I accept the terms and conditions')</span>
                    </label>
                    @storefrontError('terms_and_conditions')

                    {{-- Actions: Back (wizard, revealed by JS) + submit --}}
                    <div class="register__actions mt-2 flex gap-3">
                        @if($store['company_required'])
                            <button
                                type="button"
                                data-register-back
                                class="register__back hidden inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg border border-surface-input-stroke bg-surface-input px-4 py-2.5 font-semibold text-body transition-colors hover:text-primary"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                    <line x1="19" y1="12" x2="5" y2="12"></line>
                                    <polyline points="12 19 5 12 12 5"></polyline>
                                </svg>
                                @t('Back')
                            </button>
                        @endif
                        <button
                            type="submit"
                            class="register__submit inline-flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2.5 font-semibold text-primary-content transition-colors hover:bg-primary-600 active:bg-primary-700 disabled:cursor-not-allowed disabled:bg-surface-disabled disabled:text-surface-disabled-content"
                        >
                            @t('Create account')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>
                    </div>{{-- /step 2 --}}
                @endstorefrontForm

                {{-- Sign in --}}
                <p class="register__signin mt-6 text-center text-sm text-placeholder">
                    @t('Already have an account?')
                    <a href="@routeUrl('store.login')" class="register__signin-link font-semibold text-primary hover:text-primary-600">@t('Log in')</a>
                </p>
            </div>
        </section>
    </div>

    {{-- Password show/hide toggles (progressive enhancement; each field works
         as a plain password input when JS is unavailable). Pushed to the
         layout's `scripts` stack so it runs after the page renders. --}}
    @push('scripts')
        <script type="module">
            // Password show/hide toggles.
            const wirePasswordToggles = () => {
                for (const toggle of document.querySelectorAll('[data-password-toggle]')) {
                    const input = document.getElementById(toggle.dataset.passwordToggle);
                    if (!input) {
                        continue;
                    }
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
            };

            // Register 2-step wizard — only active when the step indicator is present
            // (i.e. the tenant requires a company). Without JS both steps render
            // stacked and submit together, so no functionality is lost.
            const wireRegisterWizard = () => {
                const stepper = document.querySelector('[data-register-stepper]');
                const form = stepper?.closest('form');
                if (!stepper || !form) {
                    return;
                }
                const steps = form.querySelectorAll('[data-step]');
                const nextButton = form.querySelector('[data-register-next]');
                const backButton = form.querySelector('[data-register-back]');
                const dots = stepper.querySelectorAll('[data-step-dot]');
                const subtitles = document.querySelectorAll('[data-step-subtitle]');
                let current = stepper.dataset.initialStep === '2' ? 2 : 1;

                const render = () => {
                    for (const section of steps) {
                        section.classList.toggle('hidden', Number(section.dataset.step) !== current);
                    }
                    for (const dot of dots) {
                        dot.setAttribute('data-active', Number(dot.dataset.stepDot) <= current ? 'true' : 'false');
                    }
                    for (const sub of subtitles) {
                        sub.classList.toggle('hidden', Number(sub.dataset.stepSubtitle) !== current);
                    }
                };

                if (nextButton) {
                    nextButton.classList.remove('hidden');
                    nextButton.addEventListener('click', () => {
                        const step1 = form.querySelector('[data-step="1"]');
                        const fields = step1 ? step1.querySelectorAll('input, select, textarea') : [];
                        for (const fieldEl of fields) {
                            if (!fieldEl.checkValidity()) {
                                fieldEl.reportValidity();
                                return;
                            }
                        }
                        current = 2;
                        render();
                    });
                }
                if (backButton) {
                    backButton.classList.remove('hidden');
                    backButton.addEventListener('click', () => {
                        current = 1;
                        render();
                    });
                }

                render();
            };

            // Live password-requirements checklist — ticks each rule on/off as
            // the visitor types, mirroring the server-enforced rules. Without JS
            // the checklist still renders (unticked) and the server validates.
            const wirePasswordChecklist = () => {
                for (const list of document.querySelectorAll('[data-password-checklist]')) {
                    const input = document.getElementById(list.dataset.passwordChecklist);
                    if (!input) {
                        continue;
                    }
                    const items = list.querySelectorAll('[data-password-rule]');
                    const evaluate = () => {
                        const value = input.value ?? '';
                        for (const item of items) {
                            let met = false;
                            switch (item.dataset.passwordRule) {
                                case 'min':
                                    met = value.length >= Number(item.dataset.min || 8);
                                    break;
                                case 'uppercase':
                                    met = /[A-Z]/.test(value);
                                    break;
                                case 'lowercase':
                                    met = /[a-z]/.test(value);
                                    break;
                                case 'number':
                                    met = /[0-9]/.test(value);
                                    break;
                                case 'special':
                                    met = /[^A-Za-z0-9]/.test(value);
                                    break;
                            }
                            item.dataset.met = met ? 'true' : 'false';
                        }
                    };
                    input.addEventListener('input', evaluate);
                    evaluate();
                }
            };

            wirePasswordToggles();
            wireRegisterWizard();
            wirePasswordChecklist();
        </script>
    @endpush
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection

