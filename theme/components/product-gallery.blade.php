@props(['product' => null, 'images' => []])

{{--
    Product gallery: a thumbnail rail (a vertical column left of the main image
    on tablet+, a horizontal strip below it on phones) plus a large main image.
    Thumbnails carry their url/alt on data hooks; the inline module swaps the
    main image and moves the active ring on click. With JavaScript off the first
    image shows and the page still works (the thumbnails are inert buttons).

    On tablet+ the rail is absolutely positioned inside the figure's left
    padding (`tablet:pl-24`), so the rail is exactly as tall as the main image's
    aspect-square box no matter how many thumbnails there are — the two scroll
    arrows plus the scroll track share that one height.

    `images` is the controller's `galleryImages` payload — an array of
    `{ url, alt }` rows, the main image first, de-duped.
--}}
<figure class="product-gallery m-0 flex flex-col-reverse gap-3 tablet:flex-row {{ count($images) > 1 ? 'tablet:relative tablet:pl-24' : '' }}" data-gallery>
    @if(count($images) > 0)
        @if(count($images) > 1)
            {{-- The scrollbar is hidden (CSS, assets/css/storefront.css) and replaced by
                 the two arrows, which the inline module only reveals when the track
                 actually overflows: up/down on tablet+, left/right on phones (each arrow
                 ships both glyphs and shows the one matching the current axis). Once
                 revealed both stay in the layout for as long as the track overflows and
                 are merely disabled at the ends, so a trackpad scroll never resizes the
                 track under the pointer. `tabindex` keeps the track keyboard/no-JS
                 scrollable either way. --}}
            <div class="product-gallery__thumbs-wrap flex min-w-0 items-center gap-1 tablet:absolute tablet:bottom-0 tablet:left-0 tablet:top-0 tablet:w-20 tablet:flex-col" data-gallery-thumbs-wrap>
                <button type="button" data-gallery-scroll-prev hidden
                    class="product-gallery__thumb-arrow inline-flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-lg border border-border-subtle bg-neutral-white text-neutral-800 transition duration-150 tablet:w-full hover:border-primary hover:text-primary"
                    aria-label="{{ t('Previous images') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 tablet:hidden" aria-hidden="true">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden h-4 w-4 tablet:block" aria-hidden="true">
                        <polyline points="18 15 12 9 6 15"></polyline>
                    </svg>
                </button>

                <div class="product-gallery__thumbs flex min-h-0 min-w-0 flex-1 gap-2 overflow-auto tablet:w-full tablet:flex-col" data-gallery-scroll-track tabindex="0">
                    @foreach($images as $img)
                        <button
                            type="button"
                            class="product-gallery__thumb h-20 w-20 shrink-0 cursor-pointer overflow-hidden rounded-lg border-2 {{ $loop->first ? 'border-primary' : 'border-border-subtle' }} bg-surface p-0"
                            data-gallery-thumb
                            data-gallery-src="@storefrontImage($img['url'], 800, 800, 85)"
                            data-gallery-alt="{{ $img['alt'] ?? '' }}"
                            aria-label="{{ t('View image') }}"
                        >
                            <img src="@storefrontImage($img['url'], 150, 150, 85)" alt="{{ $img['alt'] ?? '' }}" class="h-full w-full object-cover" loading="lazy">
                        </button>
                    @endforeach
                </div>

                <button type="button" data-gallery-scroll-next hidden
                    class="product-gallery__thumb-arrow inline-flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-lg border border-border-subtle bg-neutral-white text-neutral-800 transition duration-150 tablet:w-full hover:border-primary hover:text-primary"
                    aria-label="{{ t('Next images') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 tablet:hidden" aria-hidden="true">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden h-4 w-4 tablet:block" aria-hidden="true">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
            </div>
        @endif
        <div class="product-gallery__main aspect-square flex-1 overflow-hidden rounded-lg border border-border-subtle bg-surface">
            <img
                src="@storefrontImage($images[0]['url'], 800, 800, 85)"
                alt="{{ $images[0]['alt'] ?? ($product->name ?? '') }}"
                class="h-full w-full object-contain"
                data-gallery-main
            >
        </div>
    @elseif(!empty($store['product_placeholder']))
        <div class="product-gallery__main aspect-square flex-1 overflow-hidden rounded-lg border border-border-subtle bg-surface">
            <img
                src="{{ $store['product_placeholder'] }}"
                alt="{{ $product->name ?? '' }}"
                class="h-full w-full object-contain"
                data-gallery-main
            >
        </div>
    @else
        <div class="product-gallery__placeholder aspect-square flex flex-1 overflow-hidden rounded-lg border border-border-subtle">
            @include('partials.__image-placeholder', ['size' => 'h-12 w-12'])
        </div>
    @endif
</figure>

@if(count($images) > 1)
    <script type="module">
        const isTabletUp = window.matchMedia('(min-width: 768px)');

        for (const gallery of document.querySelectorAll('[data-gallery]')) {
            const main = gallery.querySelector('[data-gallery-main]');
            const thumbs = gallery.querySelectorAll('[data-gallery-thumb]');
            if (!main) {
                continue;
            }
            for (const thumb of thumbs) {
                thumb.addEventListener('click', () => {
                    main.src = thumb.dataset.gallerySrc ?? main.src;
                    main.alt = thumb.dataset.galleryAlt ?? '';
                    for (const other of thumbs) {
                        const active = other === thumb;
                        other.classList.toggle('border-primary', active);
                        other.classList.toggle('border-border-subtle', !active);
                    }
                });
            }

            // Scroll arrows for the thumbnail rail. The rail is a vertical column on
            // tablet+ and a horizontal strip on phones, so the arrows follow the same
            // axis: they measure and scroll height/top above 768px, width/left below.
            const track = gallery.querySelector('[data-gallery-scroll-track]');
            const prevBtn = gallery.querySelector('[data-gallery-scroll-prev]');
            const nextBtn = gallery.querySelector('[data-gallery-scroll-next]');
            if (!track || !prevBtn || !nextBtn) {
                continue;
            }

            const metrics = () => isTabletUp.matches
                ? { size: track.clientHeight, total: track.scrollHeight, offset: track.scrollTop }
                : { size: track.clientWidth, total: track.scrollWidth, offset: track.scrollLeft };

            const setSpent = (btn, spent) => {
                btn.disabled = spent;
                btn.classList.toggle('opacity-40', spent);
                // Swap the cursor rather than stacking both utilities: which one would
                // win is a base.css ordering detail, not something this file controls.
                btn.classList.toggle('cursor-not-allowed', spent);
                btn.classList.toggle('cursor-pointer', !spent);
            };

            const updateArrows = () => {
                // Arrow presence tracks overflow only. Removing an arrow at the ends
                // would give the track its 36px back mid-gesture, which reads as a jump
                // while trackpad-scrolling — so at the ends they stay put and just go
                // dim + disabled.
                const measured = metrics();
                const hasOverflow = measured.total > measured.size + 1;
                const wasHidden = prevBtn.hidden;
                prevBtn.hidden = !hasOverflow;
                nextBtn.hidden = !hasOverflow;
                if (!hasOverflow) {
                    return;
                }
                // Revealing the arrows just took height off the track, so re-measure
                // before deciding which end we are at.
                const { size, total, offset } = wasHidden ? metrics() : measured;
                setSpent(prevBtn, offset <= 0);
                setSpent(nextBtn, offset + size >= total - 1);
            };

            const scrollByPage = (direction) => {
                const step = direction * metrics().size * 0.8;
                track.scrollBy(isTabletUp.matches
                    ? { top: step, behavior: 'smooth' }
                    : { left: step, behavior: 'smooth' });
            };

            prevBtn.addEventListener('click', () => scrollByPage(-1));
            nextBtn.addEventListener('click', () => scrollByPage(1));

            track.addEventListener('scroll', updateArrows);
            window.addEventListener('resize', updateArrows);
            isTabletUp.addEventListener('change', updateArrows);
            updateArrows();
        }
    </script>
@endif
