<footer class="storefront-footer bg-secondary-800 text-neutral-white" role="contentinfo">
    {{-- Owner-editable, full-width footer promo (site-wide). Renders nothing
         until a shop manager assigns a widget to the 'footer.promo' slot. --}}
    @storefrontSlot('footer.promo')
    <div class="storefront-footer__top mx-auto grid w-full max-w-desktop grid-cols-1 gap-8 px-4 py-10 sm:grid-cols-2 lg:grid-cols-4">
        @section('footer.brand')
            <div class="storefront-footer__brand flex flex-col gap-4">
                <a href="@routeUrl('store.home')" class="inline-flex hover:no-underline" aria-label="{{ $store['name'] }}">
                    @if(!empty($store['logo']))
                        <img src="{{ $store['logo'] }}" alt="{{ $store['name'] }}" class="storefront-logo {{ !empty($store['logo_dark']) ? 'storefront-logo--light' : '' }} max-h-9 w-auto">
                        @if(!empty($store['logo_dark']))
                            <img src="{{ $store['logo_dark'] }}" alt="{{ $store['name'] }}" class="storefront-logo storefront-logo--dark max-h-9 w-auto">
                        @endif
                    @else
                        <span class="font-primary text-lg font-semibold text-neutral-white">{{ $store['name'] }}</span>
                    @endif
                </a>

                <p class="storefront-footer__tagline m-0 max-w-xs text-sm text-neutral-white/60">@t('Wholesale catalog and procurement portal for trade partners.')</p>

                @if(!empty($store['contact_phone']) || !empty($store['contact_email']) || !empty($store['contact_address']))
                    <ul class="storefront-footer__contact m-0 flex list-none flex-col gap-2 p-0 text-sm">
                        @if(!empty($store['contact_phone']))
                            <li>
                                <a href="tel:{{ $store['contact_phone'] }}" class="inline-flex items-center gap-2 text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0" aria-hidden="true">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                    <span>{{ $store['contact_phone'] }}</span>
                                </a>
                            </li>
                        @endif
                        @if(!empty($store['contact_email']))
                            <li>
                                <a href="mailto:{{ $store['contact_email'] }}" class="inline-flex items-center gap-2 text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 shrink-0" aria-hidden="true">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                    <span>{{ $store['contact_email'] }}</span>
                                </a>
                            </li>
                        @endif
                        @if(!empty($store['contact_address']))
                            <li class="flex items-start gap-2 text-neutral-white/70">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 h-4 w-4 shrink-0" aria-hidden="true">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span>{{ $store['contact_address'] }}</span>
                            </li>
                        @endif
                    </ul>
                @endif

                @if(collect($store['social'] ?? [])->filter()->isNotEmpty())
                    <div class="storefront-footer__social flex flex-wrap items-center gap-3">
                        @if(!empty($store['social']['facebook_url']))
                            <a href="{{ $store['social']['facebook_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6" aria-hidden="true"><path d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07c0 6.02 4.39 11.01 10.13 11.93v-8.44H7.08v-3.49h3.05V9.41c0-3.02 1.79-4.69 4.53-4.69 1.31 0 2.68.24 2.68.24v2.97h-1.51c-1.49 0-1.96.93-1.96 1.89v2.25h3.33l-.53 3.49h-2.8v8.44C19.61 23.08 24 18.09 24 12.07z"/></svg>
                            </a>
                        @endif
                        @if(!empty($store['social']['instagram_url']))
                            <a href="{{ $store['social']['instagram_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6" aria-hidden="true"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41-.56-.22-.96-.48-1.38-.9-.42-.42-.68-.82-.9-1.38-.16-.42-.36-1.06-.41-2.23-.06-1.27-.07-1.65-.07-4.85s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41 1.27-.06 1.65-.07 4.85-.07M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63c-.79.31-1.46.72-2.13 1.38C1.35 2.68.94 3.35.63 4.14.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.31.79.72 1.46 1.38 2.13.67.66 1.34 1.07 2.13 1.38.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56.79-.31 1.46-.72 2.13-1.38.66-.67 1.07-1.34 1.38-2.13.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91-.31-.79-.72-1.46-1.38-2.13C21.32 1.35 20.65.94 19.86.63 19.1.33 18.22.13 16.95.07 15.67.01 15.26 0 12 0zm0 5.84a6.16 6.16 0 100 12.32 6.16 6.16 0 000-12.32zM12 16a4 4 0 110-8 4 4 0 010 8zm6.41-10.85a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z"/></svg>
                            </a>
                        @endif
                        @if(!empty($store['social']['youtube_url']))
                            <a href="{{ $store['social']['youtube_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6" aria-hidden="true"><path d="M23.5 6.2a3.02 3.02 0 00-2.12-2.14C19.5 3.55 12 3.55 12 3.55s-7.5 0-9.38.51A3.02 3.02 0 00.5 6.2 31.6 31.6 0 000 12a31.6 31.6 0 00.5 5.8 3.02 3.02 0 002.12 2.14c1.88.51 9.38.51 9.38.51s7.5 0 9.38-.51a3.02 3.02 0 002.12-2.14A31.6 31.6 0 0024 12a31.6 31.6 0 00-.5-5.8zM9.55 15.57V8.43L15.82 12l-6.27 3.57z"/></svg>
                            </a>
                        @endif
                        @if(!empty($store['social']['linkedin_url']))
                            <a href="{{ $store['social']['linkedin_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 110-4.12 2.06 2.06 0 010 4.12zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.22.79 24 1.77 24h20.45c.98 0 1.78-.78 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z"/></svg>
                            </a>
                        @endif
                        @if(!empty($store['social']['x_url']))
                            <a href="{{ $store['social']['x_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="X" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6" aria-hidden="true"><path d="M18.9 1.15h3.68l-8.04 9.19L24 22.85h-7.41l-5.8-7.58-6.64 7.58H.47l8.6-9.83L0 1.15h7.6l5.24 6.93 6.06-6.93zm-1.29 19.5h2.04L6.49 3.24H4.3L17.61 20.65z"/></svg>
                            </a>
                        @endif
                        @if(!empty($store['social']['tik_tok_url']))
                            <a href="{{ $store['social']['tik_tok_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.89-4.64V9.39a6.33 6.33 0 00-5.4 10.69 6.33 6.33 0 0010.86-4.43V8.5a8.16 8.16 0 004.77 1.52v-3.4a4.85 4.85 0 01-.7.07z"/></svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @show

        @section('footer.account')
            <nav class="storefront-footer__col flex flex-col gap-3" aria-label="@t('Account')">
                <h2 class="m-0 text-sm font-semibold text-neutral-white">@t('Account')</h2>
                <ul class="m-0 flex list-none flex-col gap-2 p-0">
                    <li><a href="@routeUrl('store.account.orders')" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">@t('My orders')</a></li>
                    <li><a href="@routeUrl('store.account.shipping-addresses')" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">@t('Addresses')</a></li>
                    @if($store['favorites_enabled'] ?? true)
                        <li><a href="@routeUrl('store.favorites')" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">@t('Favorites')</a></li>
                    @endif
                </ul>
            </nav>
        @show

        @section('footer.shop')
            <nav class="storefront-footer__col flex flex-col gap-3" aria-label="@t('Shop')">
                <h2 class="m-0 text-sm font-semibold text-neutral-white">@t('Shop')</h2>
                <ul class="m-0 flex list-none flex-col gap-2 p-0">
                    <li><a href="@routeUrl('store.products')" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">@t('All products')</a></li>
                    @foreach(($rootCategories ?? collect())->take(4) as $category)
                        <li><a href="@routeUrl('store.category', ['slug' => data_get($category, 'seo.slug') ?: data_get($category, 'slug', '')])" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </nav>
        @show

        @section('footer.support')
            @if(!empty($store['contact_email']) || collect($store['legal'] ?? [])->filter()->isNotEmpty())
                <nav class="storefront-footer__col flex flex-col gap-3" aria-label="@t('Support')">
                    <h2 class="m-0 text-sm font-semibold text-neutral-white">@t('Support')</h2>
                    <ul class="m-0 flex list-none flex-col gap-2 p-0">
                        @if(!empty($store['contact_email']))
                            <li><a href="mailto:{{ $store['contact_email'] }}" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">@t('Contact us')</a></li>
                        @endif
                        @if(!empty($store['legal']['shipping_and_payment_policy_url']))
                            <li><a href="{{ $store['legal']['shipping_and_payment_policy_url'] }}" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">@t('Shipping & Payment')</a></li>
                        @endif
                        @if(!empty($store['legal']['return_policy_url']))
                            <li><a href="{{ $store['legal']['return_policy_url'] }}" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">@t('Returns')</a></li>
                        @endif
                        @if(!empty($store['legal']['terms_and_conditions_url']))
                            <li><a href="{{ $store['legal']['terms_and_conditions_url'] }}" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">@t('Terms & Conditions')</a></li>
                        @endif
                        @if(!empty($store['legal']['privacy_policy_url']))
                            <li><a href="{{ $store['legal']['privacy_policy_url'] }}" class="text-neutral-white/70 transition-colors hover:text-neutral-white hover:no-underline">@t('Privacy Policy')</a></li>
                        @endif
                    </ul>
                </nav>
            @endif
        @show
    </div>

    @section('footer.copyright')
        <div class="storefront-footer__bottom border-t border-neutral-white/10">
            <div class="mx-auto w-full max-w-desktop px-4 py-4">
                <p class="m-0 text-sm text-neutral-white/50">&copy; {{ now()->year }} {{ $store['name'] }}. @t('All rights reserved.')</p>
                {{-- Owner-editable footer note. Renders nothing until a shop
                     manager assigns content to the 'footer-note' slot. --}}
                @storefrontSlot('footer-note')
            </div>
        </div>
    @show
</footer>
