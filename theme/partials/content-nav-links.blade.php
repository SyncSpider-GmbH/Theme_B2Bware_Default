@foreach($storefrontContentNav as $item)
    <a href="{{ $item['href'] ?? ('/' . trim(($locale ?? '') . '/' . ($item['path'] ?? ''), '/')) }}"
        class="inline-flex items-center gap-2 rounded px-3 py-2 font-medium text-headings hover:bg-surface-hover hover:no-underline">
        {{ $item['label'] ?? '' }}
    </a>
@endforeach
