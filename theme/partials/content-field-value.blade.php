@if(($field['empty'] ?? false) && ($field['picker_id'] ?? 'text') !== 'bool')
    <span class="text-body/50">—</span>
@elseif(($field['picker_id'] ?? 'text') === 'text-rich')
    @if($compact ?? false)
        <span class="block max-w-[16rem] truncate text-body" title="{{ $field['display'] ?? '' }}">{{ $field['excerpt'] ?? $field['display'] ?? '' }}</span>
    @elseif(($field['html'] ?? '') !== '')
        <div class="storefront-richtext text-body">{!! $field['html'] !!}</div>
    @else
        <span class="text-body">{{ $field['display'] ?? '' }}</span>
    @endif
@elseif(($field['picker_id'] ?? '') === 'text-link' && ($field['href'] ?? '') !== '')
    @include('components.button', [
        'href' => $field['href'],
        'variant' => 'secondary',
        'label' => t('Open'),
        'target' => '_blank',
        'rel' => 'noopener noreferrer',
    ])
@elseif(($field['picker_id'] ?? '') === 'text-email' && is_string($field['value'] ?? null) && ($field['value'] ?? '') !== '')
    @include('components.button', [
        'href' => 'mailto:' . $field['value'],
        'variant' => 'secondary',
        'label' => t('Email'),
    ])
@elseif(($field['picker_id'] ?? '') === 'text-color' && ($field['hex'] ?? '') !== '')
    <span class="inline-flex items-center gap-2 text-body">
        <span class="inline-block size-5 shrink-0 rounded-full border border-border-subtle" style="background-color: {{ $field['hex'] }}" aria-hidden="true"></span>
        <span>{{ $field['hex'] }}</span>
    </span>
@elseif(($field['picker_id'] ?? '') === 'number' && is_numeric($field['value'] ?? null))
    <span class="text-body">{{ formatNumber($field['value']) }}</span>
@elseif(($field['picker_id'] ?? '') === 'date' && is_string($field['value'] ?? null) && ($field['value'] ?? '') !== '')
    <time class="text-body" datetime="{{ $field['value'] }}">{{ formatDate($field['value'], str_contains($field['value'], 'T') ? 'L LT' : 'L') }}</time>
@elseif(($field['picker_id'] ?? '') === 'bool')
    <span class="text-body">{{ ($field['value'] ?? false) ? t('Yes') : t('No') }}</span>
@elseif(($field['picker_id'] ?? '') === 'media' && is_array($field['media'] ?? null) && ($field['media']['url'] ?? '') !== '')
    @if(!empty($field['media']['is_image']))
        <a href="{{ $field['media']['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-block">
            <img
                src="@storefrontImage($field['media']['url'], ($compact ?? false) ? 80 : 800, ($compact ?? false) ? 80 : 800, 85)"
                alt="{{ $field['media']['name'] ?? '' }}"
                width="{{ ($compact ?? false) ? 80 : 800 }}"
                height="{{ ($compact ?? false) ? 80 : 800 }}"
                class="{{ ($compact ?? false) ? 'h-20 w-20' : 'max-h-80 w-full max-w-xl' }} rounded object-contain"
                loading="lazy"
            />
        </a>
    @else
        @include('components.button', [
            'href' => $field['media']['url'],
            'variant' => 'secondary',
            'label' => ($field['media']['name'] ?? '') !== '' ? $field['media']['name'] : t('Open'),
            'target' => '_blank',
            'rel' => 'noopener noreferrer',
        ])
    @endif
@elseif(in_array($field['picker_id'] ?? '', ['options', 'tags', 'category'], true) && ($field['items'] ?? []) !== [])
    @if($compact ?? false)
        <span class="block max-w-[16rem] truncate text-body">{{ implode(', ', $field['items']) }}</span>
    @else
        <ul class="m-0 flex list-none flex-wrap gap-2 p-0">
            @foreach($field['items'] as $item)
                <li class="rounded-full bg-surface-hover px-3 py-1 text-sm text-body">{{ $item }}</li>
            @endforeach
        </ul>
    @endif
@elseif(($field['picker_id'] ?? '') === 'complex' && ($field['fields'] ?? []) !== [])
    @if($compact ?? false)
        <span class="block max-w-[16rem] truncate text-body">{{ $field['display'] ?? '' }}</span>
    @else
        <dl class="m-0 grid gap-3">
            @foreach($field['fields'] as $child)
                <div>
                    <dt class="m-0 text-sm text-body/70">{{ $child['label'] }}</dt>
                    <dd class="m-0 mt-1">
                        @include('partials.content-field-value', ['field' => $child, 'compact' => false])
                    </dd>
                </div>
            @endforeach
        </dl>
    @endif
@elseif(($field['picker_id'] ?? '') === 'complex-collection')
    @if($compact ?? false)
        <span class="text-body">{{ (int) count($field['rows'] ?? []) === 1 ? t('1 item') : t('%d items', count($field['rows'] ?? [])) }}</span>
    @else
        <div class="flex flex-col gap-4">
            @foreach($field['rows'] ?? [] as $row)
                <div class="rounded border border-border-subtle p-4">
                    <dl class="m-0 grid gap-3">
                        @foreach($row['fields'] ?? [] as $child)
                            <div>
                                <dt class="m-0 text-sm text-body/70">{{ $child['label'] }}</dt>
                                <dd class="m-0 mt-1">
                                    @include('partials.content-field-value', ['field' => $child, 'compact' => false])
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endforeach
        </div>
    @endif
@elseif(($field['display'] ?? '') !== '')
    <span class="text-body {{ ($compact ?? false) ? 'block max-w-[16rem] truncate' : '' }}">{{ $field['display'] }}</span>
@else
    <span class="text-body/50">—</span>
@endif
