{{-- In-stock filter. Same icon chip as a boolean_icon facet. Query stays in_stock=1. --}}
@if($inStockAvailable ?? false)
    <section class="catalog-filter flex flex-col gap-2 border-t border-border-subtle pt-4" data-facet-display="boolean_icon">
        <h2 class="text-sm font-semibold text-headings m-0">@t('Availability')</h2>
        <label class="flex items-center gap-2 text-sm text-body cursor-pointer">
            <input
                type="checkbox"
                name="in_stock"
                value="1"
                class="sr-only"
                @checked($inStockSelected ?? false)
            >
            <span @class([
                'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border text-body',
                'border-primary bg-primary-subtle text-primary' => $inStockSelected ?? false,
                'border-border-subtle' => !($inStockSelected ?? false),
            ])>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm4.7 8.2a1 1 0 0 0-1.4-1.4L11 13.1l-2.3-2.3a1 1 0 0 0-1.4 1.4l3 3a1 1 0 0 0 1.4 0l5-5Z" clip-rule="evenodd"/></svg>
            </span>
            <span>@t('In stock only')</span>
        </label>
    </section>
@endif
