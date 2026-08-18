@extends('layouts.shop')

@section('title', $page['title'] ?? t('Page'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <article class="page page--custom flex flex-col gap-4" data-slug="{{ $page['slug'] }}">
        <header class="page__head flex flex-wrap items-baseline gap-3">
            <h1 class="font-primary text-2xl text-headings m-0">{{ $page['title'] ?? '' }}</h1>
        </header>

        <div class="page__body flex flex-col gap-3 text-body">
            @if(!empty($page['body']))
                {!! $page['body'] !!}
            @endif

            @foreach($pageBlocks as $block)
                <section class="page__block flex flex-col gap-3" data-block="{{ $block->type ?? '' }}">
                    {!! $block->html ?? '' !!}
                </section>
            @endforeach

            @if(empty($page['body']) && $pageBlocks->isEmpty())
                @include('components.empty-state', [
                    'title' => t('This page is empty.'),
                ])
            @endif
        </div>
    </article>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
