{{--
    Shared renderer for AttributesHub attribute collections.

    Props:
    - $collections: theme-ready array from StorefrontAttributeCollections —
      [{code, name, collapsed, rows: [{code, name, value, is_url, is_html, is_multiline}]}]
    - $isCompact (optional bool): compact density for cart line items.

    Each collection renders as a native <details> block (open unless the
    collection is flagged collapsed_by_default), a heading and a <dl> of rows
    matching the PDP specifications markup. Renders nothing for empty input.
--}}
@if(!empty($collections))
    <div class="attribute-collections flex flex-col {{ ($isCompact ?? false) ? 'gap-2 mt-2' : 'gap-4' }}">
        @foreach($collections as $collection)
            <details
                class="attribute-collection overflow-hidden rounded-lg border border-border-subtle bg-surface-card"
                data-collection="{{ $collection['code'] }}"
                @unless($collection['collapsed']) open @endunless
            >
                <summary class="attribute-collection__heading flex cursor-pointer items-center justify-between gap-2 px-4 {{ ($isCompact ?? false) ? 'py-2 text-sm' : 'py-3' }} font-medium text-headings">
                    {{ $collection['name'] }}
                    <span class="attribute-collection__marker shrink-0 text-body" aria-hidden="true"></span>
                </summary>
                <dl class="attribute-collection__rows m-0 border-t border-border-subtle">
                    @foreach($collection['rows'] as $row)
                        <div class="attribute-collection__row flex items-start justify-between gap-4 border-b border-border-subtle px-4 {{ ($isCompact ?? false) ? 'py-1.5' : 'py-3' }} last:border-b-0">
                            <dt class="{{ ($isCompact ?? false) ? 'text-xs' : 'text-sm' }} text-body">{{ $row['name'] }}</dt>
                            <dd class="m-0 text-right {{ ($isCompact ?? false) ? 'text-xs' : 'text-sm' }} font-medium text-headings break-words">
                                @if($row['is_url'])
                                    <a href="{{ $row['value'] }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:text-primary-600 break-all">{{ $row['value'] }}</a>
                                @elseif($row['is_html'])
                                    <div class="storefront-richtext">{!! $row['value'] !!}</div>
                                @elseif($row['is_multiline'])
                                    <span class="whitespace-pre-line">{{ $row['value'] }}</span>
                                @else
                                    {{ $row['value'] }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </details>
        @endforeach
    </div>
@endif
