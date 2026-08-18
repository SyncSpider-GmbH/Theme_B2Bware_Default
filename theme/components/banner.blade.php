@props(['type' => 'info', 'message' => null, 'class' => ''])

{{--
    Page-level flash message (success / info / warning / error) — the single
    banner for everything the platform injects into $messages (§9.6).

    Variables:
      $type     — success | info | warning | error (anything else renders as info)
      $message  — the already-localized string; renders nothing when empty
      $class    — optional extra utility classes, e.g. spacing for this context

    Usage:
      @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])

    The message is always escaped: $messages values can carry user-supplied
    input, such as a submitted email address.

    Never set role="banner" here — that is the site-header ARIA landmark, not a
    status message. Errors get role="alert" (assertive), everything else
    role="status" (polite).

    For per-field validation errors use @storefrontError instead.
--}}
@if((string) ($message ?? '') !== '')
    <div class="banner banner--{{ $type }} rounded-lg border px-4 py-3 text-sm {{ $class ?? '' }} {{ data_get([
            'success' => 'border-surface-success-subtle-stroke bg-surface-success-subtle text-surface-success-subtle-content',
            'warning' => 'border-surface-warning-subtle-stroke bg-surface-warning-subtle text-surface-warning-subtle-content',
            'error'   => 'border-surface-error-subtle-stroke bg-surface-error-subtle text-surface-error-subtle-content',
        ], $type, 'border-surface-info-subtle-stroke bg-surface-info-subtle text-surface-info-subtle-content') }}"
        role="{{ $type === 'error' ? 'alert' : 'status' }}">{{ $message }}</div>
@endif
