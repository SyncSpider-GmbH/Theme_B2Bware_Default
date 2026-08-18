@extends('layouts.shop')

@section('title', t('API Keys'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="page page--account page--account-api-keys flex flex-col gap-5">
        <div class="flex flex-col md:flex-row gap-6">
            @include('partials.account-nav', ['active' => 'api-keys'])

            <div class="flex-1 flex flex-col gap-5 min-w-0">
                @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])

                @storefrontError('api_keys')

                {{-- One-time reveal: the plaintext token is only ever
                     available right after creation (session-flashed by the
                     controller). It is never shown again after this render. --}}
                @if ($newApiKeyToken)
                    <div class="flex flex-col gap-4 rounded-lg border border-warning/40 bg-warning/10 p-4" data-api-key-reveal>
                        <div class="flex items-start gap-2 text-sm text-warning">
                            <svg class="w-5 h-5 mt-0.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M12 9v4m0 4h.01M10.29 3.86l-8.18 14.18A2 2 0 0 0 3.94 21h16.12a2 2 0 0 0 1.83-2.96L13.71 3.86a2 2 0 0 0-3.42 0z" />
                            </svg>
                            <p class="m-0">@t('Copy this key now — you won\'t be able to see it again.')</p>
                        </div>
                        <div class="flex items-stretch gap-2">
                            <input type="text" readonly value="{{ $newApiKeyToken }}" data-api-key-value
                                   class="flex-1 min-w-0 px-3 py-2 text-sm font-mono border border-surface-input-stroke rounded-md bg-surface-input text-body outline-none">
                            <button type="button" data-api-key-copy
                                    class="inline-flex items-center gap-1.5 h-10 px-4 rounded-md border border-border-subtle bg-surface-card text-body text-sm font-medium hover:bg-surface-hover transition-colors cursor-pointer whitespace-nowrap">
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="9" y="9" width="11" height="11" rx="2" />
                                    <path d="M5 15V5a2 2 0 0 1 2-2h10" />
                                </svg>
                                <span data-api-key-copy-label>@t('Copy')</span>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="bg-surface-card border border-border-subtle rounded-lg overflow-hidden">
                    <header class="px-5 py-4 border-b border-border-subtle">
                        <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Create Key')</h2>
                        <p class="text-xs text-body/70 m-0 mt-0.5">@t('Create keys to access the API programmatically as yourself. Send a key as the x-auth-token header.')</p>
                    </header>

                    @storefrontForm('api-key-create', ['class' => 'flex flex-col gap-4 p-5'])
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="flex flex-col gap-1 text-sm text-body">
                                @t('Name')
                                <input type="text" name="name" value="{{ old('name') }}" required
                                       placeholder="@t('e.g. Production integration')"
                                       class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
                                @storefrontError('name')
                            </label>

                            <label class="flex flex-col gap-1 text-sm text-body">
                                @t('Expiry date (optional)')
                                <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                                       class="px-3 py-2 border border-surface-input-stroke rounded-md bg-surface-input text-body focus:border-primary outline-none">
                                @storefrontError('expires_at')
                            </label>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-primary text-primary-content font-medium text-sm px-5 py-2 self-end transition-colors hover:bg-primary-600 cursor-pointer">
                            @t('Create Key')
                        </button>
                    @endstorefrontForm
                </div>

                <div class="bg-surface-card border border-border-subtle rounded-lg overflow-hidden">
                    <header class="px-5 py-4 border-b border-border-subtle">
                        <h2 class="font-primary text-base font-semibold text-headings m-0">@t('Your API Keys')</h2>
                    </header>

                    @if (count($apiKeys) === 0)
                        <div class="p-5">
                            @include('components.empty-state', ['title' => t('No API keys yet'), 'message' => t('You have no API keys yet.')])
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left border-collapse">
                                <thead>
                                    <tr class="text-xs uppercase tracking-wide text-body/70 border-b border-border-subtle">
                                        <th class="px-5 py-3 font-medium">@t('Name')</th>
                                        <th class="px-3 py-3 font-medium whitespace-nowrap">@t('Created')</th>
                                        <th class="px-3 py-3 font-medium whitespace-nowrap">@t('Last used')</th>
                                        <th class="px-3 py-3 font-medium whitespace-nowrap">@t('Expires')</th>
                                        <th class="px-5 py-3 font-medium text-right">@t('Actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($apiKeys as $key)
                                        <tr class="border-b border-border-subtle last:border-b-0">
                                            <td class="px-5 py-3 font-medium text-headings">{{ data_get($key, 'name') }}</td>
                                            <td class="px-3 py-3 text-body whitespace-nowrap">{{ formatDate(data_get($key, 'created_at')) }}</td>
                                            <td class="px-3 py-3 text-body whitespace-nowrap">
                                                @if (data_get($key, 'last_used_at'))
                                                    {{ formatDate(data_get($key, 'last_used_at')) }}
                                                @else
                                                    @t('Never')
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 text-body whitespace-nowrap">
                                                @if (data_get($key, 'expires_at'))
                                                    {{ formatDate(data_get($key, 'expires_at')) }}
                                                @else
                                                    @t('Never')
                                                @endif
                                            </td>
                                            <td class="px-5 py-3 text-right">
                                                @storefrontForm('api-key-revoke', ['_params' => ['id' => data_get($key, 'id')], 'class' => 'inline'])
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md border border-error/40 bg-transparent text-error text-xs font-medium hover:bg-error/10 transition-colors cursor-pointer">
                                                        @t('Revoke')
                                                    </button>
                                                @endstorefrontForm
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        const reveal = document.querySelector('[data-api-key-reveal]');
        if (reveal) {
            const copyButton = reveal.querySelector('[data-api-key-copy]');
            const valueField = reveal.querySelector('[data-api-key-value]');
            const label = reveal.querySelector('[data-api-key-copy-label]');

            copyButton?.addEventListener('click', async () => {
                if (!valueField) {
                    return;
                }
                try {
                    if (navigator.clipboard?.writeText) {
                        await navigator.clipboard.writeText(valueField.value);
                    } else {
                        valueField.focus();
                        valueField.select();
                    }
                    if (label) {
                        const previous = label.textContent;
                        label.textContent = '@t('Copied')';
                        window.setTimeout(() => {
                            label.textContent = previous;
                        }, 1500);
                    }
                } catch {
                    valueField.focus();
                    valueField.select();
                }
            });

            valueField?.addEventListener('focus', () => valueField.select());
        }
    </script>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
