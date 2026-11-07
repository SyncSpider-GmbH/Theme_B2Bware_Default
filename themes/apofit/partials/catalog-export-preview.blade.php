{{--
    Live catalog-export preview: the first 10 products the current filters
    would export, with every column already resolved. Rendered inline on the
    account catalog-export page. A filter change refreshes it through the
    Section Rendering route
    (GET /{locale}/sections?sections=catalog-export-preview&q=…&category=…).
    Column toggles do not refetch: the embedded JSON holds every cell, and the
    page script paints the checked columns from it.

    $preview is null for a disabled feature or a signed-out visitor, so the
    public Section Rendering route can never leak catalog data.
    $selectedColumns is the checkbox selection to show on first paint.

    Expected data:
      $preview array{columns:list<string>, headers:list<string>,
                     labels:array<string, string>,
                     rows:list<array<string, string>>,
                     defaults:list<string>, truncated:bool, limit:int}|null
      $selectedColumns list<string>
--}}
@isset($preview)
    @if($preview)
        <script type="application/json" data-catalog-preview-data>@json($preview)</script>
        <div class="flex flex-col gap-2">
            <div class="flex items-center justify-between gap-3">
                <span class="text-sm font-semibold text-headings">@t('Preview')</span>
                @if(count($preview['rows']) > 0)
                    <span class="text-xs text-body/70">@t('First rows of your current selection.')</span>
                @endif
            </div>

            @if(count($preview['rows']) > 0)
                <div class="overflow-x-auto border border-border-subtle rounded-md">
                    <table class="sf-preview-table w-full text-sm text-left">
                        <thead>
                            <tr>
                                @foreach($selectedColumns ?? $preview['defaults'] as $key)
                                    <th><span class="sf-preview-cell">{{ $preview['labels'][$key] ?? $key }}</span></th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($preview['rows'] as $row)
                                <tr>
                                    @foreach($selectedColumns ?? $preview['defaults'] as $key)
                                        <td><span class="sf-preview-cell" @if(($row[$key] ?? '') !== '') title="{{ $row[$key] ?? '' }}" @endif>{{ $row[$key] ?? '' }}</span></td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($preview['truncated'])
                    <p class="text-xs text-body/70 m-0" data-preview-truncated>@t('Only the first rows are shown here. The download and shared link include every matching product.')</p>
                @endif
            @else
                <div class="px-4 py-6 text-center text-sm text-body/70 border border-border-subtle rounded-md">
                    @t('No products match the current filters.')
                </div>
            @endif
        </div>
    @endif
@endisset
