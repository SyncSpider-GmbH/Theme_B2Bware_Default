@extends('layouts.shop')

@section('title', t('Categories'))

{{-- Owner-editable, full-width catalog hero — rendered above the breadcrumbs
     via the layout's @yield('hero'). Renders nothing until a banner is assigned. --}}
@section('hero')
    @storefrontSlot('hero')
@endsection

@section('content')
    <div class="page page--categories flex flex-col gap-4">
        {{-- Owner-editable content region (top of page). --}}
        @storefrontSlot('content-top')
        <h1 class="font-primary text-2xl text-headings m-0">@t('Categories')</h1>

        @section('listing')
            @if($categories->isEmpty())
                @include('components.empty-state', [
                    'title' => t('No categories yet'),
                ])
            @else
                <ul class="categories grid grid-cols-2 gap-4 list-none m-0 p-0 md:grid-cols-3 lg:grid-cols-4">
                    @foreach($categories as $category)
                        <li>
                            <a
                                href="@routeUrl('store.category', ['slug' => data_get($category, 'seo.slug') ?: data_get($category, 'slug', '')])"
                                class="category-card flex h-full flex-col overflow-hidden rounded-xl border border-border-subtle bg-surface-card transition-colors hover:border-primary hover:no-underline"
                            >
                                <span class="category-card__image flex aspect-square w-full items-center justify-center overflow-hidden bg-surface">
                                    @if(data_get($category, 'resolved_main_media.media_url') ?? data_get($category, 'media.0.media_url') ?? data_get($category, 'seo.image.public_url') ?? data_get($category, 'seo.seo_image.public_url'))
                                        <img
                                            src="@storefrontImage(data_get($category, 'resolved_main_media.media_url') ?? data_get($category, 'media.0.media_url') ?? data_get($category, 'seo.image.public_url') ?? data_get($category, 'seo.seo_image.public_url'), 320, 320, 85)"
                                            alt="{{ data_get($category, 'name', '') }}"
                                            class="h-full w-full object-contain"
                                            loading="lazy"
                                        >
                                    @else
                                        @include('partials.__image-placeholder', ['size' => 'h-12 w-12'])
                                    @endif
                                </span>
                                <span class="category-card__name block px-3 py-4 text-center font-medium text-headings break-words">{{ data_get($category, 'name', '') }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        @show
        {{-- Owner-editable content region (bottom of page). --}}
        @storefrontSlot('content-bottom')
    </div>
@endsection
