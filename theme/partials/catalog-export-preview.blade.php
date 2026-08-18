{{--
    Live catalog-export preview: a small table of the first rows the current
    column + filter selection would produce. Rendered inline on the account
    catalog-export page and refreshed through the Section Rendering route
    (GET /{locale}/sections?sections=catalog-export-preview&columns[]=…&q=…&…)
    as the form changes. Values are the exact strings the export would emit.

    $preview is null for a disabled feature or a signed-out visitor, so the
    public Section Rendering route can never leak catalog data.

    Expected data:
      $preview array{columns:list<string>, headers:list<string>,
                     rows:list<list<string>>, truncated:bool, limit:int}|null
--}}
@isset($preview)
    @if($preview)
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
                                @foreach($preview['headers'] as $header)
                                    <th>@t($header)</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($preview['rows'] as $row)
                                <tr>
                                    @foreach($row as $cell)
                                        <td>{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($preview['truncated'])
                    <p class="text-xs text-body/70 m-0">@t('Only the first rows are shown here. The download and shared link include every matching product.')</p>
                @endif
            @else
                <div class="px-4 py-6 text-center text-sm text-body/70 border border-border-subtle rounded-md">
                    @t('No products match the current filters.')
                </div>
            @endif
        </div>
    @endif
@endisset
