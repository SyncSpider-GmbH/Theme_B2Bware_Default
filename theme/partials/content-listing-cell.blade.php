@if(($column['code'] ?? '') === 'slug')
    <span class="text-body">{{ $entry['slug'] ?? '' }}</span>
@elseif(is_array(data_get($entry, 'fields_by_code.' . ($column['code'] ?? ''))))
    @include('partials.content-field-value', [
        'field' => data_get($entry, 'fields_by_code.' . ($column['code'] ?? '')),
        'compact' => true,
    ])
@else
    <span class="text-body/50">—</span>
@endif
