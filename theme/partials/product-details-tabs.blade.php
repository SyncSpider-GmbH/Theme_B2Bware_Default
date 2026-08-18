<section class="product__tabs flex flex-col gap-4" data-tabs>
    <div class="product__tablist flex flex-wrap gap-6 border-b border-border-subtle" role="tablist" aria-label="{{ t('Product information') }}">
        <button type="button" role="tab" id="tab-description" aria-controls="panel-description" aria-selected="true" data-tab="description" class="product__tab-btn -mb-px border-b-2 border-primary py-3 text-sm font-medium text-primary cursor-pointer">@t('Description')</button>
        @if(!empty($productAttributes))
            <button type="button" role="tab" id="tab-specifications" aria-controls="panel-specifications" aria-selected="false" data-tab="specifications" class="product__tab-btn -mb-px border-b-2 border-transparent py-3 text-sm font-medium text-body cursor-pointer hover:text-headings">@t('Specifications')</button>
        @endif
        @if($attachments->isNotEmpty())
            <button type="button" role="tab" id="tab-downloads" aria-controls="panel-downloads" aria-selected="false" data-tab="downloads" class="product__tab-btn -mb-px border-b-2 border-transparent py-3 text-sm font-medium text-body cursor-pointer hover:text-headings">@t('Downloads')</button>
        @endif
    </div>

    <div id="panel-description" role="tabpanel" aria-labelledby="tab-description" data-tab-panel="description" class="product__tab-panel text-body">
        {!! $product->description ?? '' !!}
    </div>

    @if(!empty($productAttributes))
        <div id="panel-specifications" role="tabpanel" aria-labelledby="tab-specifications" data-tab-panel="specifications" class="product__tab-panel">
            <dl class="product__specs m-0 overflow-hidden rounded-lg border border-border-subtle">
                @foreach($productAttributes as $attr)
                    <div class="product__spec-row flex items-start justify-between gap-4 border-b border-border-subtle bg-surface-card px-4 py-3 last:border-b-0">
                        <dt class="text-sm text-body">{{ $attr['name'] }}</dt>
                        <dd class="m-0 text-right text-sm font-medium text-headings break-words">
                            @if($attr['is_url'])
                                <a href="{{ $attr['value'] }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-primary-600 break-all">{{ $attr['value'] }}</a>
                            @elseif($attr['is_html'])
                                <div class="storefront-richtext">{!! $attr['value'] !!}</div>
                            @elseif($attr['is_multiline'])
                                <span class="whitespace-pre-line">{{ $attr['value'] }}</span>
                            @else
                                {{ $attr['value'] }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @endif

    @if($attachments->isNotEmpty())
        <div id="panel-downloads" role="tabpanel" aria-labelledby="tab-downloads" data-tab-panel="downloads" class="product__tab-panel">
            <ul class="m-0 flex list-none flex-col gap-2 p-0">
                @foreach($attachments as $file)
                    <li>
                        <a
                            href="{{ data_get($file, 'media_url') ?: '#' }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center justify-between gap-2 rounded border border-border-subtle p-3 hover:border-primary hover:no-underline"
                        >
                            <span class="font-medium text-body break-all">{{ data_get($file, 'file.file_name') ?: (data_get($file, 'alt_text') ?: t('Attachment')) }}</span>
                            <span class="shrink-0 text-sm text-primary">@t('Download')</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</section>

<script type="module">
    for (const tabs of document.querySelectorAll('[data-tabs]')) {
        const buttons = [...tabs.querySelectorAll('[data-tab]')];
        const panels = [...tabs.querySelectorAll('[data-tab-panel]')];
        if (buttons.length === 0) {
            continue;
        }
        const activate = (name) => {
            for (const b of buttons) {
                const on = b.dataset.tab === name;
                b.setAttribute('aria-selected', on ? 'true' : 'false');
                b.tabIndex = on ? 0 : -1;
                b.classList.toggle('border-primary', on);
                b.classList.toggle('text-primary', on);
                b.classList.toggle('border-transparent', !on);
                b.classList.toggle('text-body', !on);
            }
            for (const p of panels) {
                p.hidden = p.dataset.tabPanel !== name;
            }
        };
        buttons.forEach((b, i) => {
            b.addEventListener('click', () => activate(b.dataset.tab));
            b.addEventListener('keydown', (e) => {
                if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') {
                    return;
                }
                e.preventDefault();
                const dir = e.key === 'ArrowRight' ? 1 : -1;
                const next = buttons[(i + dir + buttons.length) % buttons.length];
                next.focus();
                activate(next.dataset.tab);
            });
        });
        activate(buttons[0].dataset.tab);
    }
</script>

