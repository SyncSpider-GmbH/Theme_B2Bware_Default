@extends('layouts.shop')

@section('title', t('Something went wrong'))

@section('content')
    <div class="page page--error flex flex-col items-center justify-center text-center gap-6 py-16">
        <p class="font-primary text-7xl font-bold text-error m-0 leading-none">
            {{ $errorStatus ?? 500 }}
        </p>

        <div class="flex flex-col gap-2 max-w-640">
            <h1 class="font-primary text-2xl text-headings m-0">@t('Something went wrong')</h1>
            <p class="text-body m-0">@t('An unexpected error occurred while processing your request. Our team has been notified.')</p>
        </div>

        @isset($errorReference)
            <div class="flex flex-col items-center gap-2 bg-surface-card border border-surface-input-stroke rounded-lg p-4 max-w-640 w-full">
                <p class="text-sm text-body m-0">@t('If you need help, please share this reference with our support team.')</p>

                <div class="flex items-center gap-2 w-full">
                    <code class="flex-1 font-mono text-base text-headings bg-surface-disabled rounded px-3 py-2 text-center select-all"
                          id="storefront-error-reference">{{ $errorReference }}</code>

                    <button type="button"
                            class="bg-primary text-primary-content hover:bg-primary-600 rounded px-3 py-2 transition-colors text-sm"
                            data-copy-target="storefront-error-reference"
                            data-copy-label-default="{{ t('Copy') }}"
                            data-copy-label-copied="{{ t('Copied') }}">
                        @t('Copy')
                    </button>
                </div>
            </div>
        @endisset

        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="@routeUrl('store.home')"
               class="bg-primary text-primary-content hover:bg-primary-600 rounded px-4 py-2 transition-colors no-underline">
                @t('Back to home')
            </a>
            <a href="{{ canonicalUrl() }}"
               class="bg-surface-card text-primary border border-surface-input-stroke hover:bg-surface-hover rounded px-4 py-2 transition-colors no-underline">
                @t('Try again')
            </a>
        </div>

        @isset($errorDebug)
            <details class="bg-surface-warning-subtle text-surface-warning-subtle-content border border-surface-warning-subtle-stroke rounded-lg p-4 max-w-1024 w-full text-left">
                <summary class="cursor-pointer font-semibold">
                    @t('Developer details') &mdash; {{ data_get($errorDebug, 'class') }}
                </summary>
                <div class="flex flex-col gap-3 mt-3">
                    <div>
                        <p class="text-sm font-semibold m-0 mb-1">@t('Message')</p>
                        <pre class="font-mono text-sm whitespace-pre-wrap break-words m-0">{{ data_get($errorDebug, 'message') }}</pre>
                    </div>
                    <div>
                        <p class="text-sm font-semibold m-0 mb-1">@t('Location')</p>
                        <pre class="font-mono text-sm whitespace-pre-wrap break-words m-0">{{ data_get($errorDebug, 'file') }}:{{ data_get($errorDebug, 'line') }}</pre>
                    </div>
                    <div>
                        <p class="text-sm font-semibold m-0 mb-1">@t('Stack trace')</p>
                        <pre class="font-mono text-xs whitespace-pre-wrap overflow-auto max-h-96 m-0">{{ data_get($errorDebug, 'trace') }}</pre>
                    </div>
                </div>
            </details>
        @endisset
    </div>

    <script type="module">
        const showCopied = (btn) => {
            const copiedLabel = btn.dataset.copyLabelCopied;
            if (!copiedLabel) return;
            const originalLabel = btn.dataset.copyLabelDefault ?? btn.textContent;
            btn.textContent = copiedLabel;
            setTimeout(() => { btn.textContent = originalLabel; }, 2000);
        };

        for (const btn of document.querySelectorAll('[data-copy-target]')) {
            btn.addEventListener('click', async () => {
                const target = document.getElementById(btn.dataset.copyTarget);
                if (!target || !navigator.clipboard) return;

                const text = (target.value ?? target.textContent ?? '').trim();
                try {
                    await navigator.clipboard.writeText(text);
                } catch {
                    // Copy unavailable/denied; the reference stays hand-selectable.
                    return;
                }
                showCopied(btn);
            });
        }
    </script>
@endsection
