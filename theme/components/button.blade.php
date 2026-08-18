@props(['type' => 'button', 'variant' => 'primary', 'href' => null, 'label' => null])

@if($href)
    <a
        href="{{ $href }}"
        @class([
            'btn inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 font-medium transition-colors hover:no-underline disabled:cursor-not-allowed disabled:bg-surface-disabled disabled:text-surface-disabled-subtle-content',
            'btn--primary bg-primary text-primary-content hover:bg-primary-600 active:bg-primary-700' => $variant === 'primary',
            'btn--secondary bg-transparent text-headings border border-border-subtle hover:border-primary hover:text-primary' => $variant === 'secondary',
        ])
    >{!! (string) ($slot ?? '') !== '' ? (string) $slot : ($label ?? '') !!}</a>
@else
    <button
        type="{{ $type }}"
        @class([
            'btn inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 font-medium transition-colors cursor-pointer disabled:cursor-not-allowed disabled:bg-surface-disabled disabled:text-surface-disabled-subtle-content',
            'btn--primary bg-primary text-primary-content hover:bg-primary-600 active:bg-primary-700' => $variant === 'primary',
            'btn--secondary bg-transparent text-headings border border-border-subtle hover:border-primary hover:text-primary' => $variant === 'secondary',
        ])
    >{!! (string) ($slot ?? '') !== '' ? (string) $slot : ($label ?? '') !!}</button>
@endif
