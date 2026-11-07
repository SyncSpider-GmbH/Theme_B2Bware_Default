@extends('layouts.auth')

@section('title', (($searchMode ?? 'company') === 'user' || !empty($selectedCompany)) ? t('Select user') : t('Select company'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="auth-card auth-card--wide w-full max-w-2xl mx-auto my-8 bg-surface-card border border-border-subtle rounded-lg p-6 shadow-sm flex flex-col gap-4">
        <h1 class="font-primary text-2xl text-headings text-center m-0">
            {{ (($searchMode ?? 'company') === 'user' || !empty($selectedCompany)) ? t('Select user') : t('Select company') }}
        </h1>

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

        @section('customer-selection.search')
        @if($agent)
            <div class="flex items-center justify-center gap-2">
                <a href="@routeUrl('store.customer-selection', array_filter(['mode' => 'company', 'q' => ($search ?? '') !== '' ? $search : null, 'redirect' => ($redirectTo ?? '') !== '' ? $redirectTo : null]))"
                    class="inline-flex items-center justify-center rounded px-4 py-2 text-sm font-medium no-underline {{ ($searchMode ?? 'company') === 'company' && empty($selectedCompany) ? 'bg-primary text-primary-content' : 'border border-border-subtle bg-transparent text-headings hover:bg-surface-hover' }}">
                    @t('Company')
                </a>
                <a href="@routeUrl('store.customer-selection', array_filter(['mode' => 'user', 'q' => ($search ?? '') !== '' ? $search : null, 'redirect' => ($redirectTo ?? '') !== '' ? $redirectTo : null]))"
                    class="inline-flex items-center justify-center rounded px-4 py-2 text-sm font-medium no-underline {{ ($searchMode ?? 'company') === 'user' ? 'bg-primary text-primary-content' : 'border border-border-subtle bg-transparent text-headings hover:bg-surface-hover' }}">
                    @t('User')
                </a>
            </div>
            <form method="get" action="@routeUrl('store.customer-selection')" class="flex items-center gap-2" role="search">
                <input type="hidden" name="mode" value="{{ $searchMode ?? 'company' }}">
                @if(!empty($selectedCompany))
                    <input type="hidden" name="company" value="{{ $selectedCompany->id }}">
                @endif
                @if($redirectTo)
                    <input type="hidden" name="redirect" value="{{ $redirectTo }}">
                @endif
                <input type="search" name="q" value="{{ $search ?? '' }}"
                    placeholder="{{ (($searchMode ?? 'company') === 'user' || !empty($selectedCompany)) ? t('Search by name, email, or customer number') : t('Search by company name, company number, or email') }}"
                    class="flex-1 px-3 py-2 border border-surface-input-stroke rounded bg-surface-input text-body focus:border-primary outline-none">
                <button type="submit" class="btn btn--secondary inline-flex items-center justify-center rounded border border-border-subtle bg-transparent text-headings font-medium px-4 py-2 cursor-pointer hover:bg-surface-hover">
                    @t('Search')
                </button>
            </form>
        @endif
        @show

        @section('customer-selection.list')
        {{-- Fixed-height results so search and continue stay put as matches change. --}}
        @if(($searchMode ?? 'company') === 'company' && empty($selectedCompany))
            @storefrontForm('customer-selection', ['class' => 'flex flex-col gap-3'])
                @if($redirectTo)
                    <input type="hidden" name="redirect" value="{{ $redirectTo }}">
                @endif
                <div class="customer-selection__results h-96 shrink-0 overflow-y-auto">
                    @if($companies->isEmpty())
                        @include('components.empty-state', [
                            'title' => ($search ?? '') !== ''
                                ? t('No companies match your search')
                                : t('No companies available'),
                        ])
                    @else
                        <ul class="customer-list flex flex-col gap-2 list-none m-0 p-0">
                            @foreach($companies as $company)
                                <li>
                                    @if($company->user)
                                        <label class="flex items-start gap-2 p-3 border border-border-subtle rounded bg-surface-card hover:border-primary cursor-pointer">
                                            <input type="radio" name="customer_id" value="{{ $company->user->id }}" required
                                                @checked((int) ($selectedCustomer->id ?? 0) === (int) $company->user->id)
                                                class="mt-1">
                                            <span class="flex flex-col">
                                                <span class="text-headings">{{ $company->name }}</span>
                                                @if(!empty($company->company_number))
                                                    <span class="text-body text-xs opacity-70">#{{ $company->company_number }}</span>
                                                @endif
                                                <span class="text-body text-sm">{{ $company->user->name }}</span>
                                                @if($company->user->email)
                                                    <span class="text-body text-sm">{{ $company->user->email }}</span>
                                                @endif
                                            </span>
                                        </label>
                                    @else
                                        <a href="@routeUrl('store.customer-selection', array_filter(['mode' => 'company', 'company' => $company->id, 'q' => ($search ?? '') !== '' ? $search : null, 'redirect' => ($redirectTo ?? '') !== '' ? $redirectTo : null]))"
                                            class="flex items-start gap-2 p-3 border border-border-subtle rounded bg-surface-card hover:border-primary no-underline">
                                            <span class="flex flex-col">
                                                <span class="text-headings">{{ $company->name }}</span>
                                                @if(!empty($company->company_number))
                                                    <span class="text-body text-xs opacity-70">#{{ $company->company_number }}</span>
                                                @endif
                                                <span class="text-body text-sm">{{ $company->user_count }} @t('users')</span>
                                            </span>
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        @include('components.pagination', ['paginator' => $companies])
                    @endif
                </div>
                @storefrontError('customer_id')
                <button type="submit" @disabled($companies->isEmpty() || $companies->getCollection()->whereNotNull('user')->isEmpty()) class="btn btn--primary inline-flex items-center justify-center rounded bg-primary text-primary-content font-medium px-4 py-2 transition-colors hover:bg-primary-600 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    @t('Continue as selected user')
                </button>
            @endstorefrontForm
        @else
            @storefrontForm('customer-selection', ['class' => 'flex flex-col gap-3'])
                @if($redirectTo)
                    <input type="hidden" name="redirect" value="{{ $redirectTo }}">
                @endif
                <div class="customer-selection__results h-96 shrink-0 overflow-y-auto">
                    @if(!empty($selectedCompany))
                        <p class="text-center text-body m-0 mb-2">{{ $selectedCompany->name }}</p>
                    @endif
                    @if($customers->isEmpty())
                        @include('components.empty-state', [
                            'title' => ($search ?? '') !== ''
                                ? t('No users match your search')
                                : t('No users available'),
                        ])
                    @else
                        <ul class="customer-list flex flex-col gap-2 list-none m-0 p-0">
                            @foreach($customers as $candidate)
                                <li>
                                    <label class="flex items-start gap-2 p-3 border border-border-subtle rounded bg-surface-card hover:border-primary cursor-pointer">
                                        <input type="radio" name="customer_id" value="{{ $candidate->id }}" required
                                            @checked((int) ($selectedCustomer->id ?? 0) === (int) ($candidate->id ?? 0))
                                            class="mt-1">
                                        <span class="flex flex-col">
                                            <span class="text-headings">{{ trim(($candidate->first_name ?? '') . ' ' . ($candidate->last_name ?? '')) ?: ($candidate->email ?? '') }}</span>
                                            @if($candidate->company && ($candidate->company->name ?? '') !== '')
                                                <span class="text-body text-sm">{{ $candidate->company->name }}</span>
                                            @endif
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
                        @include('components.pagination', ['paginator' => $customers])
                    @endif
                </div>
                @storefrontError('customer_id')
                <button type="submit" @disabled($customers->isEmpty()) class="btn btn--primary inline-flex items-center justify-center rounded bg-primary text-primary-content font-medium px-4 py-2 transition-colors hover:bg-primary-600 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    @t('Continue as selected user')
                </button>
            @endstorefrontForm
        @endif
        @show

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
