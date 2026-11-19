{{-- Category taxonomy filter for content listings (`contentListing.category_filters`).
     Terms come from every category picker on the type — merchant edits to the
     taxonomy show up here without a theme change. Active term is `?category=`. --}}
@if(data_get($contentListing, 'category_filters', []) !== [])
    <nav class="flex flex-wrap items-center gap-2" aria-label="@t('Categories')">
        <a
            href="/{{ trim(($locale ?? '') . '/' . data_get($contentListing, 'type.listing_path', ''), '/') }}{{ data_get($contentListing, 'listing_view') === 'cards' ? '?view=cards' : '' }}"
            @class([
                'inline-flex items-center rounded-lg border px-3 py-1.5 text-sm font-medium no-underline transition-colors',
                'border-primary bg-primary text-primary-content' => !is_string(data_get($contentListing, 'category')) || data_get($contentListing, 'category') === '',
                'border-border-subtle bg-surface-card text-body hover:border-primary hover:text-primary' => is_string(data_get($contentListing, 'category')) && data_get($contentListing, 'category') !== '',
            ])
            @if(!is_string(data_get($contentListing, 'category')) || data_get($contentListing, 'category') === '') aria-current="page" @endif
        >@t('All')</a>
        @foreach(data_get($contentListing, 'category_filters', []) as $filter)
            <a
                href="/{{ trim(($locale ?? '') . '/' . data_get($contentListing, 'type.listing_path', ''), '/') }}?category={{ urlencode($filter['slug'] ?? '') }}{{ data_get($contentListing, 'listing_view') === 'cards' ? '&view=cards' : '' }}"
                @class([
                    'inline-flex items-center rounded-lg border px-3 py-1.5 text-sm font-medium no-underline transition-colors',
                    'ml-3' => ($filter['parent'] ?? null) !== null && ($filter['parent'] ?? '') !== '',
                    'border-primary bg-primary text-primary-content' => is_string(data_get($contentListing, 'category')) && (string) data_get($contentListing, 'category') === (string) ($filter['slug'] ?? ''),
                    'border-border-subtle bg-surface-card text-body hover:border-primary hover:text-primary' => !(is_string(data_get($contentListing, 'category')) && (string) data_get($contentListing, 'category') === (string) ($filter['slug'] ?? '')),
                ])
                @if(is_string(data_get($contentListing, 'category')) && (string) data_get($contentListing, 'category') === (string) ($filter['slug'] ?? '')) aria-current="page" @endif
            >{{ $filter['label'] ?? $filter['slug'] ?? '' }}</a>
        @endforeach
    </nav>
@endif
