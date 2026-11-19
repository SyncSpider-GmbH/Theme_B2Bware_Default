@extends('layouts.shop')

@section('title', data_get($contentEntry, 'type.singular_label', t('Content')))

@section('content')
@if($contentEntry ?? null)
    <article class="mx-auto flex w-full max-w-3xl flex-col gap-6">
        <p class="m-0">
            <a
                href="/{{ trim(($locale ?? '') . '/' . data_get($contentEntry, 'type.listing_path', data_get($contentEntry, 'type.slug', '')), '/') }}"
                class="text-sm text-accent-primary no-underline hover:underline"
            >{{ data_get($contentEntry, 'type.plural_label', t('Back')) }}</a>
        </p>
        <h1 class="m-0 font-primary text-3xl font-semibold text-headings">
            {{ data_get($contentEntry, 'type.singular_label', t('Content')) }}
        </h1>
        <dl class="flex flex-col gap-5">
            @foreach(data_get($contentEntry, 'entry.fields', []) as $field)
                @continue(in_array($field['picker_id'] ?? '', ['password', 'json'], true))
                @continue(($field['show_on_storefront'] ?? true) === false)
                <div class="flex flex-col gap-1">
                    <dt class="m-0 text-sm font-medium text-headings">{{ $field['label'] ?? $field['code'] ?? '' }}</dt>
                    <dd class="m-0">
                        @include('partials.content-field-value', ['field' => $field, 'compact' => false])
                    </dd>
                </div>
            @endforeach
        </dl>
    </article>
@endif
@endsection
