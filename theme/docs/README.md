# Storefront theme authoring — documentation

> **Latest version.** This copy shipped with your theme download and may be outdated.
> The authoritative, always-current guide is online:
> https://b2bware.documentationai.com/advanced/storefront-themes
> Theme contract changelog: https://b2bware.documentationai.com/changelog
>
> To refresh this folder from the live wiki, run `./scripts/update-theme-docs.sh`
> in your theme repo.

This folder is **everything you need to build a B2Bware storefront theme** — no access to
platform source code required. It's split into focused pages so you (and your AI assistant)
can load only what a task needs.

## How to use this with an AI assistant

1. **Always load [`hard-rules.md`](hard-rules.md)** — the archive layout, canonical file
   surface, and the forbidden Blade/PHP that the upload validator hard-rejects.
2. Load the page(s) for the task from the matrix below (e.g. Blade work → `blade-api.md`;
   pricing/flags → `view-data.md`).
3. If your download might be old, open the **Live** link in the matrix below (or run
   `./scripts/update-theme-docs.sh` in your theme repo — pulls from the public wiki) before
   trusting a detail.
4. **Never invent** directives, form types, `$store` keys, or tokens that aren't listed here or
   on the live wiki.

## Load matrix

| Page | Covers | Load when | Live |
| --- | --- | --- | --- |
| [`hard-rules.md`](hard-rules.md) | Archive layout, canonical surface, forbidden Blade/PHP | **Always** | [/hard-rules](https://b2bware.documentationai.com/advanced/storefront-themes/hard-rules) |
| [`blade-api.md`](blade-api.md) | Allowed toolkit, directives, helpers | **Always for Blade work** | [/blade-api](https://b2bware.documentationai.com/advanced/storefront-themes/blade-api) |
| [`i18n-and-formatting.md`](i18n-and-formatting.md) | Translations, locale/currency, dates, numbers | On demand | [/i18n-and-formatting](https://b2bware.documentationai.com/advanced/storefront-themes/i18n-and-formatting) |
| [`forms.md`](forms.md) | Full `@storefrontForm` registry | Building any form | [/forms](https://b2bware.documentationai.com/advanced/storefront-themes/forms) |
| [`view-data.md`](view-data.md) | Globals, `$store`, feature flags, pricing, variants, rewards | **Usually yes** | [/view-data](https://b2bware.documentationai.com/advanced/storefront-themes/view-data) |
| [`ajax-and-runtime.md`](ajax-and-runtime.md) | Sections, hooks, events, `window.Storefront` | AJAX / interactivity | [/ajax-and-runtime](https://b2bware.documentationai.com/advanced/storefront-themes/ajax-and-runtime) |
| [`catalog.md`](catalog.md) | Facets, sort, price, in-stock, view, pagination | Catalog / listing pages | [/catalog](https://b2bware.documentationai.com/advanced/storefront-themes/catalog) |
| [`seo-and-images.md`](seo-and-images.md) | `@storefrontSeo` + `@storefrontImage` | SEO / media work | [/seo-and-images](https://b2bware.documentationai.com/advanced/storefront-themes/seo-and-images) |
| [`fetch-and-auth.md`](fetch-and-auth.md) | `@fetch`, `@storefrontAuthToken`, API reference | Server data / auth (security) | [/fetch-and-auth](https://b2bware.documentationai.com/advanced/storefront-themes/fetch-and-auth) |
| [`analytics-and-slots.md`](analytics-and-slots.md) | Analytics + consent + slots | Analytics / module slots | [/analytics-and-slots](https://b2bware.documentationai.com/advanced/storefront-themes/analytics-and-slots) |
| [`page-recipes.md`](page-recipes.md) | Per-page contracts: checkout, proposal-preview, account, auth, customer-selection | Building a specific page | [/page-recipes](https://b2bware.documentationai.com/advanced/storefront-themes/page-recipes) |
| [`styling.md`](styling.md) | Tailwind-first + the head cascade | Any visual work | [/styling](https://b2bware.documentationai.com/advanced/storefront-themes/styling) |
| [`tokens.md`](tokens.md) | Full token / dark-mode reference | Any visual work | [/tokens](https://b2bware.documentationai.com/advanced/storefront-themes/tokens) |
| [`checklist.md`](checklist.md) | Override strategy + pre-upload checklist | Before you upload | [/checklist](https://b2bware.documentationai.com/advanced/storefront-themes/checklist) |
| [`CHANGELOG.md`](CHANGELOG.md) | What changed in the theme contract | Checking what's new | [/changelog](https://b2bware.documentationai.com/changelog) |
