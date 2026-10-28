{{-- Attribute and definition facets. Included by the products and category filter forms. --}}
@foreach($facets as $facet)
    @if(($facet['kind'] ?? 'attribute') === 'definition')
        <section class="catalog-filter flex flex-col gap-2 {{ ($categoryFacet ?? collect())->isNotEmpty() || !$loop->first ? 'border-t border-border-subtle pt-4' : '' }}">
            <h2 class="text-sm font-semibold text-headings m-0">{{ $facet['name'] }}</h2>
            <div class="flex flex-col gap-1.5">
                @foreach($facet['values'] as $value)
                    <label class="flex items-center gap-2 text-sm text-body cursor-pointer">
                        <input
                            type="checkbox"
                            name="attr_def[{{ $facet['attribute_id'] }}][{{ $facet['definition_name'] }}][]"
                            value="{{ $value }}"
                            class="shrink-0"
                            @checked(in_array($value, $selectedDefinitions[$facet['attribute_id']][$facet['definition_name']] ?? [], true))
                        >
                        <span class="truncate">{{ $value }}</span>
                    </label>
                @endforeach
            </div>
        </section>
    @else
        <section
            class="catalog-filter flex flex-col gap-2 {{ ($categoryFacet ?? collect())->isNotEmpty() || !$loop->first ? 'border-t border-border-subtle pt-4' : '' }}"
            data-facet-display="{{ $facet['display'] ?? (($facet['multiple'] ?? false) ? 'checkbox' : 'radio') }}"
            data-facet-code="{{ $facet['code'] ?? '' }}"
        >
            <h2 class="text-sm font-semibold text-headings m-0">{{ $facet['name'] }}</h2>
            @if(($facet['display'] ?? null) === 'searchable_select')
                <input
                    type="search"
                    data-facet-search
                    autocomplete="off"
                    placeholder="@t('Search')"
                    class="w-full rounded-lg border border-surface-input-stroke bg-surface-input px-2 py-1.5 text-sm text-body outline-none focus:border-primary"
                >
            @endif
            <div @class([
                'flex flex-col gap-1.5',
                'max-h-48 overflow-y-auto' => ($facet['display'] ?? null) === 'searchable_select',
            ])>
                @foreach($facet['values'] as $value)
                    <label class="flex items-center gap-2 text-sm text-body cursor-pointer" data-facet-option data-facet-value="{{ $value }}">
                        <input
                            type="{{ (($facet['display'] ?? null) === 'checkbox' || ($facet['multiple'] ?? false)) ? 'checkbox' : 'radio' }}"
                            name="attr[{{ $facet['id'] }}]{{ ($facet['multiple'] ?? false) ? '[]' : '' }}"
                            value="{{ $value }}"
                            @class([
                                'shrink-0',
                                'sr-only' => ($facet['display'] ?? null) === 'boolean_icon',
                            ])
                            @checked(in_array($value, $selectedAttributes[$facet['id']] ?? [], true))
                        >
                        @if(($facet['display'] ?? null) === 'color_swatch')
                            @if(!empty($facet['swatches'][$value]))
                                <span
                                    class="inline-block h-4 w-4 shrink-0 rounded-full border border-border-subtle"
                                    style="background-color: {{ $facet['swatches'][$value] }}"
                                    data-swatch="{{ $facet['swatches'][$value] }}"
                                ></span>
                            @else
                                <span class="inline-block h-4 w-4 shrink-0 rounded-full border border-border-subtle bg-surface-hover"></span>
                            @endif
                        @endif
                        @if(($facet['display'] ?? null) === 'boolean_icon')
                            <span @class([
                                'inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border text-body',
                                'border-primary bg-primary-subtle text-primary' => in_array($value, $selectedAttributes[$facet['id']] ?? [], true),
                                'border-border-subtle' => !in_array($value, $selectedAttributes[$facet['id']] ?? [], true),
                            ])>
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm4.7 8.2a1 1 0 0 0-1.4-1.4L11 13.1l-2.3-2.3a1 1 0 0 0-1.4 1.4l3 3a1 1 0 0 0 1.4 0l5-5Z" clip-rule="evenodd"/></svg>
                            </span>
                        @endif
                        <span class="truncate">{{ $value }}</span>
                        @if(isset($facet['counts'][$value]))
                            <span class="ml-auto shrink-0 text-xs text-body" data-facet-count="{{ $facet['counts'][$value] }}">{{ $facet['counts'][$value] }}</span>
                        @endif
                    </label>
                @endforeach
            </div>
        </section>
    @endif
@endforeach
