@extends('layouts.shop')

@section('title', data_get($contentListing, 'type.plural_label', t('Content')))

@section('content')
@if($contentListing ?? null)
    <div class="flex w-full flex-col gap-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="m-0 font-primary text-2xl font-semibold text-headings">{{ data_get($contentListing, 'type.plural_label', t('Content')) }}</h1>

            @if(data_get($contentListing, 'entries', []) !== [])
                <form method="get" class="hidden overflow-hidden rounded-lg border border-border-subtle md:inline-flex" role="group" aria-label="@t('View')">
                    @if(is_string(data_get($contentListing, 'category')) && data_get($contentListing, 'category') !== '')
                        <input type="hidden" name="category" value="{{ data_get($contentListing, 'category') }}">
                    @endif
                    <button
                        type="submit"
                        name="view"
                        value="grid"
                        @class([
                            'flex h-9 w-9 items-center justify-center transition-colors cursor-pointer',
                            'bg-primary text-primary-content' => data_get($contentListing, 'listing_view') !== 'cards',
                            'bg-surface-card text-body hover:bg-surface-hover' => data_get($contentListing, 'listing_view') === 'cards',
                        ])
                        aria-label="@t('Data grid')"
                        aria-pressed="{{ data_get($contentListing, 'listing_view') === 'cards' ? 'false' : 'true' }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                            <line x1="8" y1="6" x2="21" y2="6"></line>
                            <line x1="8" y1="12" x2="21" y2="12"></line>
                            <line x1="8" y1="18" x2="21" y2="18"></line>
                            <line x1="3" y1="6" x2="3.01" y2="6"></line>
                            <line x1="3" y1="12" x2="3.01" y2="12"></line>
                            <line x1="3" y1="18" x2="3.01" y2="18"></line>
                        </svg>
                    </button>
                    <button
                        type="submit"
                        name="view"
                        value="cards"
                        @class([
                            'flex h-9 w-9 items-center justify-center transition-colors cursor-pointer',
                            'bg-primary text-primary-content' => data_get($contentListing, 'listing_view') === 'cards',
                            'bg-surface-card text-body hover:bg-surface-hover' => data_get($contentListing, 'listing_view') !== 'cards',
                        ])
                        aria-label="@t('Card view')"
                        aria-pressed="{{ data_get($contentListing, 'listing_view') === 'cards' ? 'true' : 'false' }}"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                    </button>
                </form>
            @endif
        </div>

        @include('partials.content-category-filters')

        @if(data_get($contentListing, 'entries', []) === [])
            <p class="m-0 text-sm text-body">@t('No published entries yet.')</p>
        @else
            <div @class(['overflow-x-auto rounded border border-border-subtle bg-surface-card', data_get($contentListing, 'listing_view') === 'cards' ? 'hidden' : 'hidden md:block'])>
                <table class="w-max min-w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="border-b border-border-subtle bg-surface-hover">
                            <th class="whitespace-nowrap px-3 py-2 font-medium text-headings">{{ t('Entry') }}</th>
                            @foreach(data_get($contentListing, 'columns', []) as $column)
                                <th class="whitespace-nowrap px-3 py-2 font-medium text-headings">{{ $column['label'] ?? $column['code'] ?? '' }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(data_get($contentListing, 'entries', []) as $entry)
                            <tr class="border-b border-border-subtle last:border-b-0 hover:bg-surface-hover">
                                <td class="whitespace-nowrap px-3 py-2 align-middle">
                                    @include('components.button', [
                                        'href' => $entry['href'] ?? '#',
                                        'variant' => 'secondary',
                                        'label' => ($entry['slug'] ?? '') !== '' ? $entry['slug'] : t('View'),
                                    ])
                                </td>
                                @foreach(data_get($contentListing, 'columns', []) as $column)
                                    <td class="whitespace-nowrap px-3 py-2 align-middle">
                                        @include('partials.content-listing-cell', ['entry' => $entry, 'column' => $column])
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <ul @class(['m-0 flex list-none flex-col gap-4 p-0', data_get($contentListing, 'listing_view') === 'cards' ? 'flex' : 'flex md:hidden'])>
                @foreach(data_get($contentListing, 'entries', []) as $entry)
                    <li class="rounded border border-border-subtle bg-surface-card p-4">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <p class="m-0 font-medium text-headings">{{ ($entry['slug'] ?? '') !== '' ? $entry['slug'] : t('Entry') }}</p>
                            @include('components.button', [
                                'href' => $entry['href'] ?? '#',
                                'variant' => 'secondary',
                                'label' => t('View'),
                            ])
                        </div>
                        <dl class="m-0 grid gap-3">
                            @foreach(data_get($contentListing, 'columns', []) as $column)
                                <div class="grid gap-1 border-t border-border-subtle pt-3 first:border-t-0 first:pt-0">
                                    <dt class="m-0 text-sm text-body/70">{{ $column['label'] ?? $column['code'] ?? '' }}</dt>
                                    <dd class="m-0">
                                        @include('partials.content-listing-cell', ['entry' => $entry, 'column' => $column])
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
@endsection
