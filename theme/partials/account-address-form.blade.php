{{--
    Reusable address form fragment. Renders the standard fields for
    both shipping and billing addresses; billing-only fields render
    when $prefix === 'billing'. The caller wraps it in the matching
    @storefrontForm.

    Vars:
      $address  - existing address model or null (for create)
      $prefix   - 'shipping' or 'billing'
--}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
    <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
        @t('Address label')
        <input type="text" name="name" required maxlength="255"
               value="{{ old('name', optional($address ?? null)->name ?? '') }}"
               placeholder="@t('e.g. Headquarters, Warehouse')"
               class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
        @storefrontError('name')
    </label>

    <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
        @t('Company name')
        <input type="text" name="company_name" maxlength="255"
               value="{{ old('company_name', optional($address ?? null)->company_name ?? '') }}"
               placeholder="@t('e.g. Acme Industries Ltd')"
               class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
        @storefrontError('company_name')
    </label>

    @if(($prefix ?? 'shipping') === 'billing')
        <label class="flex flex-col gap-1 text-sm text-body">
            @t('Company number')
            <input type="text" name="company_number" maxlength="255"
                   value="{{ old('company_number', optional($address ?? null)->company_number ?? '') }}"
                   placeholder="@t('e.g. HRB 12345')"
                   class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
            @storefrontError('company_number')
        </label>

        <label class="flex flex-col gap-1 text-sm text-body">
            @t('VAT number')
            <input type="text" name="vat_number" maxlength="255"
                   value="{{ old('vat_number', optional($address ?? null)->vat_number ?? '') }}"
                   placeholder="@t('e.g. DE123456789')"
                   class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
            @storefrontError('vat_number')
        </label>

        <label class="flex flex-col gap-1 text-sm text-body">
            @t('Registration number')
            <input type="text" name="registration_number" maxlength="255"
                   value="{{ old('registration_number', optional($address ?? null)->registration_number ?? '') }}"
                   placeholder="@t('e.g. 12-3456789')"
                   class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
            @storefrontError('registration_number')
        </label>

        <label class="flex flex-col gap-1 text-sm text-body">
            @t('Email')
            <input type="email" name="email" maxlength="255"
                   value="{{ old('email', optional($address ?? null)->email ?? '') }}"
                   placeholder="@t('e.g. billing@acme.com')"
                   class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
            @storefrontError('email')
        </label>
    @endif

    <label class="flex flex-col gap-1 text-sm text-body">
        @t('First name')
        <input type="text" name="first_name" maxlength="255"
               value="{{ old('first_name', optional($address ?? null)->first_name ?? '') }}"
               placeholder="@t('e.g. John')"
               class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
        @storefrontError('first_name')
    </label>

    <label class="flex flex-col gap-1 text-sm text-body">
        @t('Last name')
        <input type="text" name="last_name" maxlength="255"
               value="{{ old('last_name', optional($address ?? null)->last_name ?? '') }}"
               placeholder="@t('e.g. Smith')"
               class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
        @storefrontError('last_name')
    </label>

    <label @class(['flex flex-col gap-1 text-sm text-body', 'sm:col-span-2' => ($prefix ?? 'shipping') !== 'billing'])>
        @t('Phone')
        <input type="tel" name="phone" maxlength="50"
               value="{{ old('phone', optional($address ?? null)->phone ?? '') }}"
               placeholder="@t('e.g. +1 555 123 4567')"
               class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
        @storefrontError('phone')
    </label>

    @if(($prefix ?? 'shipping') === 'billing')
        <label class="flex flex-col gap-1 text-sm text-body">
            @t('Website')
            <input type="text" name="website" maxlength="255"
                   value="{{ old('website', optional($address ?? null)->website ?? '') }}"
                   placeholder="@t('e.g. https://acme.com')"
                   class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
            @storefrontError('website')
        </label>
    @endif

    <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
        @t('Address line 1')
        <input type="text" name="address_line_1" required maxlength="255"
               value="{{ old('address_line_1', optional($address ?? null)->address_line_1 ?? '') }}"
               placeholder="@t('e.g. 123 Market Street')"
               class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
        @storefrontError('address_line_1')
    </label>

    <label class="flex flex-col gap-1 text-sm text-body sm:col-span-2">
        @t('Address line 2')
        <input type="text" name="address_line_2" maxlength="255"
               value="{{ old('address_line_2', optional($address ?? null)->address_line_2 ?? '') }}"
               placeholder="@t('e.g. Suite 400')"
               class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
        @storefrontError('address_line_2')
    </label>

    <label class="flex flex-col gap-1 text-sm text-body">
        @t('City')
        <input type="text" name="city" required maxlength="100"
               value="{{ old('city', optional($address ?? null)->city ?? '') }}"
               placeholder="@t('e.g. Berlin')"
               class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
        @storefrontError('city')
    </label>

    <label class="flex flex-col gap-1 text-sm text-body">
        @t('Postal code')
        <input type="text" name="zip" required maxlength="20"
               value="{{ old('zip', optional($address ?? null)->zip ?? '') }}"
               placeholder="@t('e.g. 10115')"
               class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
        @storefrontError('zip')
    </label>

    <label class="flex flex-col gap-1 text-sm text-body">
        @t('State')
        <input type="text" name="state" maxlength="100"
               value="{{ old('state', optional($address ?? null)->state ?? '') }}"
               placeholder="@t('e.g. California')"
               class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
        @storefrontError('state')
    </label>

    <label class="flex flex-col gap-1 text-sm text-body">
        @t('Country')
        <input type="text" name="country" required maxlength="100"
               value="{{ old('country', optional($address ?? null)->country ?? '') }}"
               placeholder="@t('e.g. Germany')"
               class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
        @storefrontError('country')
    </label>

    <label class="flex items-center gap-2 text-sm text-body sm:col-span-2">
        <input type="checkbox" name="default" value="1"
               @checked(old('default', (bool) (optional($address ?? null)->default ?? false)))>
        @t('Use as default')
    </label>
</div>
