@extends('layouts.shop')

@section('title', t('Profile'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="page page--account page--account-profile flex flex-col gap-5">
        <div class="flex flex-col md:flex-row gap-6">
            @include('partials.account-nav', ['active' => 'profile'])

            <div class="flex-1 flex flex-col gap-5 min-w-0">
                @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])

                {{-- Editable profile --}}
                <div class="bg-surface-card border border-border-subtle rounded-lg overflow-hidden">
                    <header class="px-5 py-4 border-b border-border-subtle">
                        <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Profile')</h2>
                        <p class="text-xs text-body/70 m-0 mt-0.5">@t('Your personal contact details.')</p>
                    </header>

                    @storefrontForm('profile', ['class' => 'account-form flex flex-col gap-4 p-5'])
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="flex flex-col gap-1 text-sm text-body">
                                @t('Salutation')
                                <select name="salutation"
                                        class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
                                    <option value="">@t('Select')</option>
                                    <option value="Mr." @selected(old('salutation', optional($customer)->salutation) === 'Mr.')>@t('Mr.')</option>
                                    <option value="Ms." @selected(old('salutation', optional($customer)->salutation) === 'Ms.')>@t('Ms.')</option>
                                    <option value="Other" @selected(old('salutation', optional($customer)->salutation) === 'Other')>@t('Other')</option>
                                </select>
                                @storefrontError('salutation')
                            </label>

                            <label class="flex flex-col gap-1 text-sm text-body">
                                @t('Phone')
                                <input type="tel" name="phone" value="{{ old('phone', optional($customer)->phone ?? '') }}"
                                       class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
                                @storefrontError('phone')
                            </label>

                            <label class="flex flex-col gap-1 text-sm text-body">
                                @t('First name')
                                <input type="text" name="first_name" value="{{ old('first_name', optional($customer)->first_name ?? '') }}" required
                                       class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
                                @storefrontError('first_name')
                            </label>

                            <label class="flex flex-col gap-1 text-sm text-body">
                                @t('Last name')
                                <input type="text" name="last_name" value="{{ old('last_name', optional($customer)->last_name ?? '') }}" required
                                       class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
                                @storefrontError('last_name')
                            </label>

                            <label class="flex flex-col gap-1 text-sm text-body">
                                @t('Email')
                                <input type="email" value="{{ optional($customer)->email ?? '' }}" disabled
                                       class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface text-body opacity-70">
                            </label>

                            @if(optional($customer)->customer_number)
                                <label class="flex flex-col gap-1 text-sm text-body">
                                    @t('Customer number')
                                    <input type="text" value="{{ optional($customer)->customer_number }}" disabled
                                           class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface text-body opacity-70">
                                </label>
                            @endif
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-primary text-primary-content font-medium text-sm px-5 py-2 self-end transition-colors hover:bg-primary-600 cursor-pointer">
                            @t('Save')
                        </button>
                    @endstorefrontForm
                </div>

                {{-- Email address change --}}
                <div class="bg-surface-card border border-border-subtle rounded-lg overflow-hidden">
                    <header class="px-5 py-4 border-b border-border-subtle">
                        <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Email address')</h2>
                        <p class="text-xs text-body/70 m-0 mt-0.5">
                            @t('Current email:') <strong class="text-headings">{{ optional($customer)->email ?? '' }}</strong>
                        </p>
                    </header>

                    <div class="p-5">
                        @if(optional($customer)->pending_email)
                            <div class="p-3 rounded-md bg-surface-info-subtle text-surface-info-subtle-content text-sm mb-4">
                                @t('Pending verification:') <strong>{{ optional($customer)->pending_email }}</strong>
                                <div class="opacity-80 mt-1">@t('We sent a 6-digit code to that address. Enter it below to confirm the change.')</div>
                            </div>

                            @storefrontForm('profile-email-confirm', ['class' => 'flex flex-col gap-3'])
                                <label class="flex flex-col gap-1 text-sm text-body max-w-xs">
                                    @t('Verification code')
                                    <input type="text" name="code" inputmode="numeric" pattern="\d{6}" maxlength="6" autocomplete="one-time-code" required
                                           class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none tracking-widest font-mono">
                                    @storefrontError('code')
                                    @storefrontError('profile_email')
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-primary text-primary-content font-medium text-sm px-4 py-2 transition-colors hover:bg-primary-600 cursor-pointer">
                                        @t('Confirm new email')
                                    </button>
                                </div>
                            @endstorefrontForm

                            <div class="mt-2">
                                @storefrontForm('profile-email-cancel', ['class' => 'inline'])
                                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-border-subtle bg-transparent text-headings px-4 py-2 cursor-pointer hover:bg-surface-hover text-sm">
                                        @t('Cancel email change')
                                    </button>
                                @endstorefrontForm
                            </div>
                        @else
                            @storefrontForm('profile-email-request', ['class' => 'flex flex-col gap-4 max-w-md'])
                                <label class="flex flex-col gap-1 text-sm text-body">
                                    @t('New email')
                                    <input type="email" name="new_email" value="{{ old('new_email', '') }}" required
                                           class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
                                    @storefrontError('new_email')
                                </label>
                                <label class="flex flex-col gap-1 text-sm text-body">
                                    @t('Current password')
                                    <input type="password" name="current_password" autocomplete="current-password" required
                                           class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
                                    @storefrontError('current_password')
                                    @storefrontError('profile_email')
                                </label>
                                <button type="submit" class="inline-flex items-center justify-center rounded-md border border-border-subtle bg-transparent text-headings font-medium text-sm px-4 py-2 self-start cursor-pointer hover:bg-surface-hover">
                                    @t('Send verification code')
                                </button>
                            @endstorefrontForm
                        @endif
                    </div>
                </div>

                {{-- B2B additional information (only for company customers) --}}
                @if($company)
                    <div class="bg-surface-card border border-border-subtle rounded-lg overflow-hidden">
                        <header class="px-5 py-4 border-b border-border-subtle">
                            <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Additional information')</h2>
                            <p class="text-xs text-body/70 m-0 mt-0.5">@t('Your company details, default addresses and trade conditions.')</p>
                        </header>

                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium text-body/70 uppercase tracking-wide">@t('Company')</span>
                                <span class="text-sm text-headings">{{ optional($company)->name ?? '—' }}</span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium text-body/70 uppercase tracking-wide">@t('Phone')</span>
                                <span class="text-sm text-headings">{{ optional($customer)->phone ?: '—' }}</span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium text-body/70 uppercase tracking-wide">@t('Default shipping address')</span>
                                @if($defaultShipping)
                                    <address class="text-sm text-headings not-italic leading-relaxed">
                                        @if($defaultShipping->company_name){{ $defaultShipping->company_name }}<br>@endif
                                        @if($defaultShipping->first_name || $defaultShipping->last_name){{ trim($defaultShipping->first_name . ' ' . $defaultShipping->last_name) }}<br>@endif
                                        {{ $defaultShipping->address_line_1 }}<br>
                                        @if($defaultShipping->address_line_2){{ $defaultShipping->address_line_2 }}<br>@endif
                                        {{ trim($defaultShipping->zip . ' ' . $defaultShipping->city) }}<br>
                                        {{ $defaultShipping->country }}
                                    </address>
                                @else
                                    <span class="text-sm text-body/70">@t('No default shipping address.')</span>
                                @endif
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium text-body/70 uppercase tracking-wide">@t('Default billing address')</span>
                                @if($defaultBilling)
                                    <address class="text-sm text-headings not-italic leading-relaxed">
                                        @if($defaultBilling->company_name){{ $defaultBilling->company_name }}<br>@endif
                                        @if($defaultBilling->first_name || $defaultBilling->last_name){{ trim($defaultBilling->first_name . ' ' . $defaultBilling->last_name) }}<br>@endif
                                        {{ $defaultBilling->address_line_1 }}<br>
                                        @if($defaultBilling->address_line_2){{ $defaultBilling->address_line_2 }}<br>@endif
                                        {{ trim($defaultBilling->zip . ' ' . $defaultBilling->city) }}<br>
                                        {{ $defaultBilling->country }}
                                    </address>
                                @else
                                    <span class="text-sm text-body/70">@t('No default billing address.')</span>
                                @endif
                            </div>

                            @if(optional($company)->conditions_1)
                                <div class="flex flex-col gap-1 sm:col-span-2">
                                    <span class="text-xs font-medium text-body/70 uppercase tracking-wide">@t('Payment conditions')</span>
                                    <div class="text-sm text-body storefront-richtext">{!! $company->conditions_1 !!}</div>
                                </div>
                            @endif

                            @if(optional($company)->conditions_2)
                                <div class="flex flex-col gap-1 sm:col-span-2">
                                    <span class="text-xs font-medium text-body/70 uppercase tracking-wide">@t('Shipping conditions')</span>
                                    <div class="text-sm text-body storefront-richtext">{!! $company->conditions_2 !!}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Danger zone: delete account --}}
                <div class="bg-surface-card border border-error/30 rounded-lg overflow-hidden">
                    <div class="p-5 flex flex-col gap-3">
                        <div class="flex flex-col gap-1">
                            <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Delete account')</h2>
                            <p class="text-sm text-body m-0">@t('Permanently close your account. This signs you out and cannot be undone.')</p>
                        </div>
                        @storefrontError('account_delete_password')
                        @storefrontError('account_delete')
                        <button type="button" data-storefront-modal-open="delete-account-modal"
                                class="inline-flex items-center justify-center self-start rounded-md border border-error text-error font-medium text-sm px-4 py-2 cursor-pointer hover:bg-error/10 transition-colors bg-transparent">
                            @t('Delete account')
                        </button>
                    </div>
                </div>

                <dialog id="delete-account-modal" class="modal bg-surface-card text-body rounded-lg shadow-xl p-0 border-0 backdrop:bg-backdrop/40">
                    <div class="flex flex-col gap-3 p-6 min-w-80 max-w-md">
                        <header class="flex items-center justify-between gap-3 pb-3 border-b border-border-subtle">
                            <h2 class="font-primary text-lg text-headings m-0">@t('Delete account')</h2>
                            <button type="button" class="inline-flex h-8 w-8 items-center justify-center text-2xl leading-none text-body hover:text-headings cursor-pointer bg-transparent border-0" data-modal-close aria-label="@t('Close')">&times;</button>
                        </header>
                        <p class="text-sm text-body m-0">@t('This permanently closes your account and signs you out. This action cannot be undone.')</p>
                        @storefrontForm('profile-delete', ['class' => 'flex flex-col gap-3'])
                            <label class="flex flex-col gap-1 text-sm text-body">
                                @t('Confirm your password')
                                <input type="password" name="current_password" autocomplete="current-password" required
                                       class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
                            </label>
                            <div class="flex items-center justify-end gap-2 pt-1">
                                <button type="button" data-modal-close class="inline-flex items-center justify-center rounded-md border border-border-subtle bg-transparent text-headings px-4 py-2 cursor-pointer hover:bg-surface-hover text-sm">@t('Cancel')</button>
                                <button type="submit" class="inline-flex items-center justify-center rounded-md bg-error text-neutral-white font-medium px-4 py-2 cursor-pointer hover:bg-error/90 text-sm">@t('Delete account')</button>
                            </div>
                        @endstorefrontForm
                    </div>
                </dialog>
            </div>
        </div>
    </div>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
