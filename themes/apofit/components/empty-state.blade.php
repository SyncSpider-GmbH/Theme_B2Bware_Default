@props(['title' => null, 'message' => null, 'icon' => null])

<div class="empty-state text-center py-12 px-6 text-body flex flex-col items-center gap-3 rounded-lg border border-border-subtle bg-surface-card" role="status">
    @if($icon)
        <div class="empty-state__icon text-4xl opacity-50 text-headings">{!! $icon !!}</div>
    @endif
    @if($title)
        <h3 class="empty-state__title text-headings font-primary text-lg font-semibold m-0">{{ $title }}</h3>
    @endif
    @if($message)
        <p class="empty-state__message m-0 text-sm">{{ $message }}</p>
    @endif
    @if((string) ($slot ?? '') !== '')
        <div class="empty-state__actions mt-2 flex gap-2 justify-center flex-wrap">{!! $slot !!}</div>
    @endif
</div>
