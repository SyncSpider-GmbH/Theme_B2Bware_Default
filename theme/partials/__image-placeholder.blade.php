{{--
    Canonical product image placeholder icon.

    Variables:
      $size  — Tailwind size class for the SVG, e.g. 'h-8 w-8' (default), 'h-4 w-4', 'h-12 w-12'

    Usage:
      @include('partials.__image-placeholder', ['size' => 'h-8 w-8'])
--}}
<span class="flex h-full w-full items-center justify-center bg-surface-page text-placeholder" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="{{ $size ?? 'h-8 w-8' }}" aria-hidden="true">
        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
        <circle cx="9" cy="10" r="1.5"></circle>
        <path d="M21 15l-4.5-4.5a1 1 0 0 0-1.4 0L8 17"></path>
    </svg>
</span>
