@props(['value' => 1, 'min' => 1, 'max' => null, 'name' => 'quantity'])

<div class="quantity-selector inline-flex items-stretch rounded-lg border border-border-subtle bg-surface-card overflow-hidden">
    <button
        type="button"
        class="quantity-selector__btn flex items-center justify-center w-9 bg-surface-card border-0 cursor-pointer text-headings font-medium text-base leading-none transition-colors hover:bg-surface-hover disabled:opacity-40 disabled:cursor-not-allowed"
        data-action="dec"
        aria-label="{{ t('Decrease quantity') }}"
    >&minus;</button>
    <input
        type="number"
        name="{{ $name }}"
        value="{{ $value }}"
        min="{{ $min }}"
        @if(!is_null($max)) max="{{ $max }}" @endif
        inputmode="numeric"
        class="quantity-selector__input w-12 text-center border-x border-border-subtle px-1 py-1.5 bg-surface-card text-headings text-sm font-semibold outline-none focus:bg-surface-page"
    >
    <button
        type="button"
        class="quantity-selector__btn flex items-center justify-center w-9 bg-surface-card border-0 cursor-pointer text-headings font-medium text-base leading-none transition-colors hover:bg-surface-hover disabled:opacity-40 disabled:cursor-not-allowed"
        data-action="inc"
        aria-label="{{ t('Increase quantity') }}"
    >+</button>
</div>
