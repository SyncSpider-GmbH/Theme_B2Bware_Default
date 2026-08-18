{{--
    Logout confirmation page.

    GETting `/logout` shows this confirmation; submitting POSTs to
    `store.logout.post` which revokes the current AuthHub token,
    clears the `storefront_auth_token` cookie and redirects to the
    submitted `redirect` (or `/`).
--}}
@extends('layouts.auth')

@section('title', t('Log out'))

@section('content')
    {{-- Owner-editable content region (top of page). --}}
    @storefrontSlot('content-top')
    <div class="auth-card w-full max-w-md mx-auto my-8 bg-surface-card border border-border-subtle rounded-lg p-6 shadow-sm flex flex-col gap-4">
        <h1 class="font-primary text-2xl text-headings text-center m-0">@t('Log out')</h1>
        <p class="text-body text-center m-0">@t('Are you sure you want to log out?')</p>

        @storefrontForm('logout', ['class' => 'auth-form flex flex-col gap-3'])
            <button type="submit" class="btn btn--primary inline-flex items-center justify-center rounded bg-primary text-primary-content font-medium px-4 py-2 transition-colors hover:bg-primary-600 cursor-pointer">@t('Log out')</button>
            <a href="/" class="btn btn--secondary inline-flex items-center justify-center rounded bg-transparent text-headings border border-border-subtle font-medium px-4 py-2 transition-colors hover:border-primary hover:text-primary hover:no-underline">@t('Cancel')</a>
        @endstorefrontForm
    </div>
    {{-- Owner-editable content region (bottom of page). --}}
    @storefrontSlot('content-bottom')
@endsection
