@if(!empty($breadcrumbs))
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-1.5 list-none m-0 p-0 text-sm">
            @foreach($breadcrumbs as $i => $crumb)
                <li class="breadcrumbs__item flex items-center gap-1.5">
                    @if($i > 0)
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="breadcrumbs__separator h-3.5 w-3.5 shrink-0 text-border" aria-hidden="true">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    @endif
                    @if($i === count($breadcrumbs) - 1)
                        <span class="breadcrumbs__current font-medium text-headings" aria-current="page">{{ $crumb['label'] }}</span>
                    @else
                        <a href="{{ $crumb['url'] }}" class="breadcrumbs__link font-medium text-body transition-colors hover:text-headings hover:no-underline">{{ $crumb['label'] }}</a>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
