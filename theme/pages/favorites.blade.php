@extends('layouts.shop')

@section('title', t('Favorites'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="page page--account page--favorites flex flex-col gap-5">
        <div class="flex flex-col md:flex-row gap-6">
            @include('partials.account-nav', ['active' => 'favorites'])

            <div class="flex-1 min-w-0 flex flex-col gap-4">
                @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])

                <div class="flex flex-col gap-0.5">
                    <h1 class="font-primary text-xl font-semibold text-headings m-0">@t('Favorites')</h1>
                    <p class="text-sm text-body/70 m-0">
                        {{ $favoriteItems->count() }} @t('saved products')
                    </p>
                </div>

                @if($lists->isNotEmpty())
                    <div class="favorites__lists flex flex-col gap-3 p-4 bg-surface-card border border-border-subtle rounded-lg">
                        {{-- List switcher --}}
                        <div class="flex flex-wrap gap-2">
                            @foreach($lists as $list)
                                @if($list['id'] === $activeListId)
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-primary text-primary-content text-sm font-medium">
                                        {{ $list['name'] }}
                                        <span class="opacity-80">({{ $list['count'] }})</span>
                                    </span>
                                @else
                                    @storefrontForm('favorite-list-select', ['class' => 'inline'])
                                        <input type="hidden" name="list_id" value="{{ $list['id'] }}">
                                        <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border border-border-subtle bg-transparent text-headings text-sm cursor-pointer hover:bg-surface-hover">
                                            {{ $list['name'] }}
                                            <span class="opacity-70">({{ $list['count'] }})</span>
                                        </button>
                                    @endstorefrontForm
                                @endif
                            @endforeach
                        </div>

                        {{-- List management: create / rename / delete --}}
                        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3 sm:items-end pt-3 border-t border-border-subtle">
                            @storefrontForm('favorite-list-create', ['class' => 'flex items-end gap-2'])
                                <label class="flex flex-col gap-1 text-sm text-body">
                                    @t('New list')
                                    <input type="text" name="name" maxlength="100" placeholder="@t('List name')" required
                                           class="px-3 py-1.5 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
                                </label>
                                <button type="submit" class="px-3 py-1.5 rounded-md bg-primary text-primary-content text-sm cursor-pointer hover:bg-primary-600">
                                    @t('Create')
                                </button>
                            @endstorefrontForm

                            @storefrontForm('favorite-list-rename', ['class' => 'flex items-end gap-2'])
                                <input type="hidden" name="list_id" value="{{ $activeListId }}">
                                <label class="flex flex-col gap-1 text-sm text-body">
                                    @t('Rename current list')
                                    <input type="text" name="name" maxlength="100" required
                                           value="{{ old('name', data_get($lists->firstWhere('id', $activeListId), 'name', '')) }}"
                                           class="px-3 py-1.5 border border-surface-input-stroke rounded-md bg-surface-input text-body text-sm focus:border-primary outline-none">
                                </label>
                                <button type="submit" class="px-3 py-1.5 rounded-md border border-border-subtle bg-transparent text-headings text-sm cursor-pointer hover:bg-surface-hover">
                                    @t('Rename')
                                </button>
                            @endstorefrontForm

                            @if($lists->count() > 1)
                                @storefrontForm('favorite-list-delete', ['class' => 'flex items-end'])
                                    <input type="hidden" name="list_id" value="{{ $activeListId }}">
                                    <button type="submit" class="px-3 py-1.5 rounded-md border border-error/40 bg-transparent text-error text-sm cursor-pointer hover:bg-error/10">
                                        @t('Delete current list')
                                    </button>
                                @endstorefrontForm
                            @endif
                        </div>
                        @storefrontError('list_name')
                    </div>
                @endif

                @if($favoriteItems->isEmpty())
                    @include('components.empty-state', [
                        'title'   => t('No favorites yet'),
                        'message' => t('Heart products to save them here.'),
                    ])
                @else
                    <ul class="catalog__grid grid grid-cols-2 md:grid-cols-3 gap-4 list-none m-0 p-0">
                        @foreach($favoriteItems as $item)
                            @if($products->get((int) data_get($item, 'id', 0)))
                                <li class="flex flex-col gap-2">
                                    @include('components.product-card', ['product' => $products->get((int) data_get($item, 'id', 0))])
                                    @storefrontForm('favorite-toggle', ['class' => 'self-stretch'])
                                        <input type="hidden" name="product_id" value="{{ data_get($item, 'id') }}">
                                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md border border-border-subtle bg-transparent text-body cursor-pointer hover:bg-surface-hover hover:text-error text-sm transition-colors">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18" /><path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2" /><path d="M19 6l-1 14a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1L5 6" /></svg>
                                            @t('Remove')
                                        </button>
                                    @endstorefrontForm
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
