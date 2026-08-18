{{--
    Verify email page — split-screen layout (mirrors login.blade.php).

    Left panel  = brand / marketing (hidden below `lg`); tokenized navy→blue
                  `.verify__brand--gradient`.
    Right panel = the verification form. The visitor lands here either from the
                  login flow (email pending verification) or via the link in the
                  verification email. They enter the 6-digit code and POST it to
                  `store.verify-email.post`; on success they are auto-logged in
                  and redirected to `redirect` (or `/`).

    Email handling: when the address is known (`$email`, from `?email=` / the
    login bounce / `old()`) it is shown as a confirmation line and submitted as a
    hidden field in BOTH forms; on direct navigation with no email an editable
    email input is rendered instead so the visitor can still verify.

    A separate `@storefrontForm('resend-verification')` form re-sends the code
    without revealing whether the email exists. Every input keeps its exact
    `name=""` so validation (`@storefrontError`) and `old()` repopulation are
    unchanged. Every color / font / spacing value resolves through a design
    token (docs/styling.md, docs/tokens.md) — no hardcoded hex / px / font names.
--}}
@extends('layouts.auth')

@section('title', t('Verify email'))

{{--
    Tokenized navy→blue gradient + brand glow for the left panel (identical to
    the login / register pages). Inline <style> so the whole verify design lives
    in one file; references ONLY design-token CSS variables, so re-skinning the
    `secondary` / `primary` ramps re-skins this panel automatically. Pushed to
    `head` so it loads after `base.css` (which defines the `--color-*` tokens).
    Tailwind gradient utilities are not compiled into `base.css`, which is why
    this one rule is hand-written CSS (docs/styling.md §10.4).
--}}
@push('head')
    <style>
        .verify__brand--gradient {
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

        /* Six-box OTP code entry. Hidden until the script activates it (so the
           single fallback field still works without JS); geometry only — the
           colors come from the cell utility classes / design tokens. */
        .verify__otp {
            display: none;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 0.5rem;
        }
        .verify__otp[data-active="true"] {
            display: grid;
        }
    </style>
@endpush

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="verify flex min-h-dvh w-full">
        {{-- ── Left: brand / marketing panel (hidden below lg) ─────────────── --}}
        <aside class="verify__brand verify__brand--gradient relative hidden flex-col overflow-hidden p-8 lg:flex lg:flex-1 lg:p-10">
            {{-- Logo (top): store logo (if uploaded) + store name --}}
            <a href="/" class="verify__brand-logo inline-flex items-center gap-2 text-neutral-white">
                @if(!empty($store['logo']))
                    <img
                        src="{{ $store['logo'] }}"
                        alt="{{ $store['name'] }}"
                        class="verify__brand-logo-image max-h-8 object-contain"
                    >
                @endif
                <span class="verify__brand-logo-text font-primary text-lg font-bold">{{ $store['name'] }}</span>
            </a>

            {{-- Value proposition (vertically centered) --}}
            <div class="verify__brand-pitch my-auto max-w-420">
                <h2 class="verify__brand-title font-primary text-3xl font-semibold tracking-tight text-neutral-white">
                    @t('Almost there')
                </h2>
                <p class="verify__brand-subtitle mt-3 text-sm leading-relaxed text-neutral-white/70">
                    @t('Confirm your email to activate your account and start ordering.')
                </p>

                <ul class="verify__brand-benefits mt-8 flex flex-col gap-5">
                    <li class="verify__brand-benefit flex items-center gap-3">
                        <span class="verify__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                        </span>
                        <span class="verify__brand-benefit-label text-sm font-medium text-neutral-white">@t('Personalized prices and offers')</span>
                    </li>
                    <li class="verify__brand-benefit flex items-center gap-3">
                        <span class="verify__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <rect x="1" y="3" width="15" height="13"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                        </span>
                        <span class="verify__brand-benefit-label text-sm font-medium text-neutral-white">@t('Fast order tracking and reordering')</span>
                    </li>
                    <li class="verify__brand-benefit flex items-center gap-3">
                        <span class="verify__brand-benefit-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-neutral-white/10 bg-neutral-white/5 text-neutral-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 11 11 13 15 9"></polyline>
                            </svg>
                        </span>
                        <span class="verify__brand-benefit-label text-sm font-medium text-neutral-white">@t('Secure, private account access')</span>
                    </li>
                </ul>
            </div>

            {{-- Footer meta (bottom): store attribution --}}
            <p class="verify__brand-meta text-xs tracking-wide text-neutral-white/70">
                &copy; {{ now()->year }} {{ $store['name'] }}
            </p>
        </aside>

        {{-- ── Right: verification form panel ──────────────────────────────── --}}
        <section class="verify__auth flex flex-1 flex-col overflow-y-auto bg-surface-page p-6 lg:p-10">
            <div class="verify__form-wrap m-auto w-full max-w-420">
                {{-- Logo on mobile (the brand panel that normally carries it is hidden) --}}
                <a href="/" class="verify__mobile-logo mb-6 inline-flex items-center gap-2 lg:hidden">
                    @if(!empty($store['logo']))
                        <img src="{{ $store['logo'] }}" alt="{{ $store['name'] }}" class="verify__mobile-logo-image max-h-10 object-contain">
                    @endif
                    <span class="verify__mobile-logo-text font-primary text-lg font-bold text-headings">{{ $store['name'] }}</span>
                </a>

                {{-- Header: badge + heading + subheading --}}
                <span class="verify__badge inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary-subtle text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </span>
                <h1 class="verify__title mt-4 font-primary text-2xl font-semibold tracking-tight text-headings">
                    @t('Verify your email')
                </h1>
                <p class="verify__subtitle mt-1.5 text-sm text-placeholder">
                    @t('Enter the 6-digit code we just sent.')
                </p>
                @if(!empty($email))
                    <p class="verify__sent-to mt-1 text-sm text-placeholder">
                        @t('Code sent to') <span class="verify__sent-to-email font-medium text-body">{{ $email }}</span>
                    </p>
                @endif

                {{-- Messages --}}
                @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null, 'class' => 'mt-6'])
                @include('components.banner', ['type' => 'info', 'message' => $messages['info'] ?? null, 'class' => 'mt-6'])
                @include('components.banner', ['type' => 'error', 'message' => $messages['error'] ?? null, 'class' => 'mt-6'])

                {{-- Form --}}
                @storefrontForm('verify-email', ['class' => 'verify__form mt-6 flex flex-col gap-4'])
                    @if(!empty($email))
                        {{-- Known email: submit it silently + surface any email-level error --}}
                        <input type="hidden" name="email" value="{{ old('email', $email) }}">
                        @storefrontError('email')
                    @else
                        {{-- Unknown email (direct navigation): let the visitor enter it --}}
                        <div class="verify__field flex flex-col gap-1.5">
                            <label for="verify-email-input" class="verify__label text-sm font-medium text-body">@t('Email address')</label>
                            <div class="verify__input flex items-center gap-2 rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2 focus-within:border-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="verify__input-icon h-5 w-5 shrink-0 text-placeholder" aria-hidden="true">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <input
                                    id="verify-email-input"
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
                    @endif

                    {{-- Verification code. The single field below is the no-JS
                         fallback AND the single source of truth that actually
                         posts `code`; when JS is present the 6-box OTP is
                         revealed, the single field is hidden + un-constrained,
                         and the script mirrors the boxes into it. --}}
                    <div class="verify__field flex flex-col gap-1.5" data-otp-field>
                        <label for="verify-code" class="verify__label text-sm font-medium text-body">@t('Verification code')</label>

                        {{-- 6-box OTP (revealed by JS). The boxes are name-less so
                             ONLY the single field below ever posts. --}}
                        <div class="verify__otp" data-otp aria-hidden="true">
                            @for ($i = 0; $i < 6; $i++)
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    maxlength="1"
                                    aria-label="@t('Verification code digit')"
                                    data-otp-cell
                                    class="verify__otp-cell h-12 w-full min-w-0 rounded-lg border border-surface-input-stroke bg-surface-input text-center text-lg font-semibold text-body outline-none focus:border-primary"
                                >
                            @endfor
                        </div>

                        {{-- Single field — the real input that posts `code`. --}}
                        <input
                            id="verify-code"
                            type="text"
                            name="code"
                            value="{{ old('code') }}"
                            required
                            inputmode="numeric"
                            pattern="[0-9]{6}"
                            minlength="6"
                            maxlength="6"
                            autocomplete="one-time-code"
                            autofocus
                            data-otp-input
                            class="verify__input rounded-lg border border-surface-input-stroke bg-surface-input px-3 py-2.5 text-center text-lg tracking-widest text-body outline-none focus:border-primary placeholder:text-placeholder"
                        >
                        @storefrontError('code')
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="verify__submit mt-2 inline-flex w-full items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2.5 font-semibold text-primary-content transition-colors hover:bg-primary-600 active:bg-primary-700 disabled:cursor-not-allowed disabled:bg-surface-disabled disabled:text-surface-disabled-content"
                    >
                        @t('Verify')
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                @endstorefrontForm

                {{-- Resend --}}
                @storefrontForm('resend-verification', ['class' => 'verify__resend mt-6 flex items-center justify-center gap-1.5 text-sm'])
                    <input type="hidden" name="email" value="{{ old('email', $email ?? '') }}">
                    <span class="verify__resend-text text-placeholder">@t("Didn't receive the code?")</span>
                    <button type="submit" class="verify__resend-link font-semibold text-primary hover:text-primary-600">
                        @t('Resend code')
                    </button>
                @endstorefrontForm

                {{-- Back to login --}}
                <p class="verify__back mt-6 text-center text-sm">
                    <a href="@routeUrl('store.login')" class="verify__back-link inline-flex items-center justify-center gap-1.5 font-medium text-placeholder hover:text-body">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        @t('Back to sign in')
                    </a>
                </p>
            </div>
        </section>
    </div>

    {{-- Six-box OTP progressive enhancement. Without JS the single `code`
         field + the "Verify" button submit exactly as before — nothing is
         lost. With JS, the boxes are revealed, the single field is hidden and
         un-constrained (so a partial manual submit is caught server-side
         rather than throwing a non-focusable validation error), the boxes are
         mirrored into it, and the form auto-submits once all six digits are
         entered. --}}
    @push('scripts')
        <script type="module">
            const otp = document.querySelector('[data-otp]');
            const field = document.querySelector('[data-otp-input]');
            const cells = otp ? [...otp.querySelectorAll('[data-otp-cell]')] : [];

            if (otp && field && cells.length > 0) {
                // Activate the boxes; demote the single field to a hidden mirror
                // that still posts `code`. Drop its native constraints so a
                // display:none required field can never block submit.
                otp.setAttribute('data-active', 'true');
                otp.removeAttribute('aria-hidden');
                field.classList.add('hidden');
                field.setAttribute('tabindex', '-1');
                field.setAttribute('aria-hidden', 'true');
                field.removeAttribute('required');
                field.removeAttribute('pattern');
                field.removeAttribute('minlength');
                field.removeAttribute('autofocus');

                const form = field.form;
                let submitted = false;

                const digits = (value) => (value ?? '').replace(/\D/g, '');
                const collect = () => cells.map((cell) => digits(cell.value).slice(0, 1)).join('');

                const focusCell = (index) => {
                    if (index >= 0 && index < cells.length) {
                        cells[index].focus();
                        cells[index].select();
                    }
                };

                const sync = (allowSubmit) => {
                    const value = collect();
                    field.value = value;
                    if (allowSubmit && value.length === cells.length && !submitted && form) {
                        submitted = true;
                        form.requestSubmit();
                    }
                };

                cells.forEach((cell, index) => {
                    cell.addEventListener('input', () => {
                        cell.value = digits(cell.value).slice(0, 1);
                        if (cell.value && index < cells.length - 1) {
                            focusCell(index + 1);
                        }
                        sync(true);
                    });

                    cell.addEventListener('keydown', (event) => {
                        if (event.key === 'Backspace' && !cell.value && index > 0) {
                            event.preventDefault();
                            cells[index - 1].value = '';
                            focusCell(index - 1);
                            sync(false);
                        } else if (event.key === 'ArrowLeft' && index > 0) {
                            event.preventDefault();
                            focusCell(index - 1);
                        } else if (event.key === 'ArrowRight' && index < cells.length - 1) {
                            event.preventDefault();
                            focusCell(index + 1);
                        }
                    });

                    cell.addEventListener('paste', (event) => {
                        event.preventDefault();
                        const pasted = digits(event.clipboardData?.getData('text') ?? '').slice(0, cells.length);
                        if (!pasted) {
                            return;
                        }
                        cells.forEach((target, i) => {
                            target.value = pasted.charAt(i) || '';
                        });
                        focusCell(Math.min(pasted.length, cells.length - 1));
                        sync(true);
                    });
                });

                // Repopulate from any server-side old('code') value (e.g. after a
                // failed attempt) WITHOUT auto-submitting, then focus the first
                // empty box.
                const initial = digits(field.value).slice(0, cells.length);
                if (initial) {
                    cells.forEach((cell, index) => {
                        cell.value = initial.charAt(index) || '';
                    });
                }
                sync(false);
                const firstEmpty = cells.findIndex((cell) => !cell.value);
                focusCell(firstEmpty === -1 ? cells.length - 1 : firstEmpty);
            }
        </script>
    @endpush
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
