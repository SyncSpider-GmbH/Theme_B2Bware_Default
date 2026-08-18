@extends('layouts.auth')

@section('title', t('Select customer'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="auth-card auth-card--wide w-full max-w-2xl mx-auto my-8 bg-surface-card border border-border-subtle rounded-lg p-6 shadow-sm flex flex-col gap-4">
        <h1 class="font-primary text-2xl text-headings text-center m-0">@t('Select customer')</h1>

        @if($agent)
            <p class="text-center text-body m-0">
                @t('Signed in as')
                <strong class="text-headings">{{ trim(($agent->first_name ?? '') . ' ' . ($agent->last_name ?? '')) ?: ($agent->email ?? '') }}</strong>
            </p>
        @endif

        @if($isImpersonating && $selectedCustomer)
            <div class="p-3 rounded bg-info/10 text-info text-sm">
                @t('Currently impersonating:')
                <strong>{{ trim(($selectedCustomer->first_name ?? '') . ' ' . ($selectedCustomer->last_name ?? '')) ?: ($selectedCustomer->email ?? '') }}</strong>
            </div>
        @endif

        @include('components.banner', ['type' => 'success', 'message' => $messages['success'] ?? null])
        @include('components.banner', ['type' => 'info', 'message' => $messages['info'] ?? null])

        @if($agent)
            <form method="get" action="@routeUrl('store.customer-selection')" class="flex items-center gap-2" role="search">
                @if($redirectTo)
                    <input type="hidden" name="redirect" value="{{ $redirectTo }}">
                @endif
                <input type="search" name="q" value="{{ $search ?? '' }}"
                    placeholder="@t('Search by name, email or customer number')"
                    class="flex-1 px-3 py-2 border border-surface-input-stroke rounded bg-surface-input text-body focus:border-primary outline-none">
                <button type="submit" class="btn btn--secondary inline-flex items-center justify-center rounded border border-border-subtle bg-transparent text-headings font-medium px-4 py-2 cursor-pointer hover:bg-surface-hover">
                    @t('Search')
                </button>
            </form>
        @endif

        @if($customers->isEmpty())
            @include('components.empty-state', [
                'title' => ($search ?? '') !== ''
                    ? t('No customers match your search')
                    : t('No customers available to impersonate'),
            ])
        @else
            @storefrontForm('customer-selection', ['class' => 'flex flex-col gap-3'])
                @if($redirectTo)
                    <input type="hidden" name="redirect" value="{{ $redirectTo }}">
                @endif
                <ul class="customer-list flex flex-col gap-2 list-none m-0 p-0 max-h-96 overflow-y-auto">
                    @foreach($customers as $candidate)
                        <li>
                            <label class="flex items-start gap-2 p-3 border border-border-subtle rounded bg-surface-card hover:border-primary cursor-pointer">
                                <input type="radio" name="customer_id" value="{{ $candidate->id }}" required
                                    @checked(($selectedCustomer->id ?? null) === ($candidate->id ?? null))
                                    class="mt-1">
                                <span class="flex flex-col">
                                    <span class="text-headings">{{ trim(($candidate->first_name ?? '') . ' ' . ($candidate->last_name ?? '')) ?: ($candidate->email ?? '') }}</span>
                                    @if($candidate->email)
                                        <span class="text-body text-sm">{{ $candidate->email }}</span>
                                    @endif
                                    @if(!empty($candidate->customer_number))
                                        <span class="text-body text-xs opacity-70">#{{ $candidate->customer_number }}</span>
                                    @endif
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
                @storefrontError('customer_id')
                <button type="submit" class="btn btn--primary inline-flex items-center justify-center rounded bg-primary text-primary-content font-medium px-4 py-2 mt-2 transition-colors hover:bg-primary-600 cursor-pointer">
                    @t('Continue as selected customer')
                </button>
            @endstorefrontForm

            @include('components.pagination', ['paginator' => $customers])
        @endif

        @if($agent)
            <a href="@routeUrl('store.home')" class="text-center text-sm text-body underline hover:text-primary">
                @t('Continue without impersonating')
            </a>
        @endif

        @if($isImpersonating)
            @storefrontForm('impersonation-leave', ['class' => 'flex flex-col gap-2 mt-2'])
                <button type="submit" class="btn btn--ghost inline-flex items-center justify-center rounded border border-border-subtle bg-transparent text-headings font-medium px-4 py-2 cursor-pointer hover:bg-surface-hover">
                    @t('Stop impersonating')
                </button>
            @endstorefrontForm
        @endif
    </div>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
