# Theme contract changelog

> **Latest version.** This copy shipped with your theme download and may be outdated.
> The authoritative, always-current changelog is online:
> https://b2bware.documentationai.com/changelog

This file tracks changes to the **theme authoring contract** (directives, form types, `$store`
keys, canonical surface, tokens). It is not the platform-wide release log. Dates are UTC.

## 2026-10-27 — Quieter impersonation banner

- **Default theme:** the impersonation bar uses the page surface, body text, and outline actions. It no longer uses the warning fill. See `partials/impersonation-banner.blade.php`.
- **Default theme:** customer selection keeps the search field and **Continue as selected user** in place. Results scroll inside a fixed-height pane, so a new search does not move the form.

## 2026-10-26 — Sales agent company search

- **Customer selection** opens on company search. `$searchMode` is `company` or `user`. `$companies` is the company-step paginator (empty on the user step and the drill-in). `$selectedCompany` is set while drilling into one company. `$customers` is the user list for user mode and the drill-in. On the company step it is still the flat people list for `q`, so an override that only loops `$customers` keeps showing people.
- **Company row:** `id`, `name`, `company_number`, `email`, `user_count`, and `user` (one eligible user, or null). One user submits that `customer_id`. Several users link to `?company={id}`.
- **Form** is unchanged: `customer-selection` posts `customer_id` and optional `redirect`. A copied `pages/customer-selection.blade.php` does not pick up the company layout until that file is updated. See `page-recipes.md` §10.4.

## 2026-10-25 — Store fallback payment method

- **`$store['default_billing_method_id']`** and **`$store['hide_payment_method']`** come from Settings → Configuration → Storefront. The id is the store fallback, not the method selected for this shopper. Hide is false when no fallback is saved.
- **Checkout `$defaultBillingMethodId`** is the shopper's own default when that method is visible, else the store fallback when it is visible, else empty. Hide removes the Payment method block only when a method resolved. See `view-data.md` and `page-recipes.md` §10.2.

## 2026-10-25 — Built-in catalog sort orders

- **Default sort** on Store setup → Features can be Newest, Oldest, Price: low to high, or Price: high to low, as well as a sortable product attribute. An empty choice stays Newest. Price order uses the product's regular price.
- **`$sort`** accepts `price_asc` and `price_desc` on All Products and category listings. See `catalog.md`.

## 2026-10-25 — Catalog sort no longer has a relevance option

- **Default theme:** All Products, category listings, and catalog export no longer list a relevance sort. Remove that `<option>` from a theme that copied it.
- **`$sort`** is the store's default sorting attribute, or `newest` when none is configured, whenever the URL omits `sort` or sends a key the catalog does not accept. An accepted `sort` is echoed as-is. Search uses the same key. See `catalog.md`.

## 2026-10-23 — Product photos stay whole; grid cards share a height

- **Default theme:** product photos on the grid card, the list thumbnail, gallery thumbnails, the cart, orders, and proposals use `object-contain`. Grid cells are `h-full` so the square image, two-line title, and bottom price block line up. Logos, flags, and sign-in backgrounds are unchanged.
- **Hard rule:** do not crop a product photo with `object-cover` or with `@storefrontImage`'s `aspect_ratio` argument. Width and height together fit inside the box. Bunny Optimizer must be enabled on the pull zone or the original file is served. The URL helper is unchanged. See `seo-and-images.md` §9.11 and `hard-rules.md` §9.

## 2026-10-23 — Definition-cell catalog filters (`attr_def`) and facet counts

- **`$facets` may be `kind=definition`.** An attribute with a discrete definition no longer lists member labels. Each definition is its own facet: string `id` `"<attribute_id>:<definition name>"`, plus `attribute_id`, `definition_name`, and cell `values`. Do not submit that `id` as `attr[<id>]`. See `catalog.md`.
- **`$selectedDefinitions`** is `attribute_id => [definition name => [cell, …]]`.
- **Query `attr_def[<id>][<definition name>][]`.** OR within one definition, AND across definitions on the same attribute, OR with `attr` on that attribute, AND across attributes. Keep it in hidden inputs with the other catalog state.
- **`$activeFilters` `kind=definition`.** One chip per selected cell. `name` is the definition name.
- **Attribute facets (`kind=attribute`) include `$facets[].counts`.** Each remaining label maps to how many products it would leave. `values` omits a label whose count is zero. Search, category, the other selected attributes, the price range, and "In stock only" all narrow that list. The selected facet still shows its other values.
- An impossible filter URL stays put and shows an empty grid. The chips stay so the shopper can remove one filter. The page does not redirect.

## 2026-10-23 — Catalog facet display types

- **`$facets` attribute rows** now include `code` (`attribute_code`), `display`, and `swatches`. `values` is still a list of label strings. `display` is `null` or `checkbox`, `searchable_select`, `color_swatch`, `boolean_icon`. `null` keeps today's checkboxes (multiselect) or radios. Do not pick a control from the translated `name`. See `catalog.md`.
- **Swatch attributes** flagged for filtering are facets and stay single-value.
- **Default theme** renders the four controls on the product list and category pages. The in-stock filter uses the same icon chip and still submits `in_stock=1`.

## 2026-10-11 — Required theme README, section overrides on default pages

- **Hard rule:** every theme ships a root `README.md` with four filled-in sections (`Purpose`,
  `What this theme changes`, `Scripts and libraries`, `Still on the default theme`). Uploads
  without it are rejected at the manifest step. An optional root `CHANGELOG.md` is kept too. The
  store admin shows both on the theme's page. See `theme-readme.md` and `hard-rules.md` §1.

- **New override points** on the default theme: `cart.*`, `checkout.*`, `favorites.*`,
  `account.nav` / `account.main` on every account page (plus `addresses.list` /
  `addresses.company`), `<page>.brand` / `<page>.form` on login, register, forgot-password,
  reset-password and verify-email, and new sections on category, guest-order, proposal-preview,
  customer-selection, error and not-found. The rendered HTML is unchanged for themes that do not
  override them.
- **Docs:** `blade-api.md` §9.0 documents `@extends('default::pages.…')`, single `@section`
  overrides and `@parent`, and lists every override point (including the existing header,
  footer, product and listing sections). `checklist.md` §12 now leads with this approach and
  states that copying a whole page file opts that page out of default-theme updates.
- **Upload:** a new version can be uploaded from the theme's own admin page. The `theme.json`
  name must stay the same as the theme it is uploaded into.

## 2026-10-08 — `@storefrontImage` hard rule + desk media + Bunny API notes

- **Hard rule** (also in `hard-rules.md` §9): every catalog / customer `media_url` in an
  `<img src>` **must** use `@storefrontImage(...)`. Raw Bunny or SpiderDesk media URLs fail
  review.
- **Platform:** `@storefrontImage` now appends sizing params for the SpiderDesk / user-service
  host (`USER_SERVICE_BASE_URL`) as well as Bunny `*.b-cdn.net` / configured pull zones — so
  desk `/media/...` tiles resize for every tenant, not only Bunny-hosted media.
- **Docs:** `seo-and-images.md` §9.11 documents the platform subset (`width`, `height`,
  `quality`, `aspect_ratio`) and the broader Bunny Dynamic Images API (crop, format, filters,
  …) for author reference.

## 2026-10-01 — Subscriptions

- **New form type `subscription-checkout`** (instant subscription checkout of one product,
  signed-in customers only), contributed by SubscriptionHub inside its own `product.detail.meta`
  slot contribution. Themes may override `modules/subscription-hub/product-subscription.blade.php`.
  See `forms.md`.
- **New form types `subscription-cancel`, `subscription-resume` and `subscription-pay`** (`_params: ['subscription' => id]`),
  contributed by SubscriptionHub. See `forms.md`.
- **New module page** `/account/subscriptions` with its own `account.nav` entry (shown only to
  customers who subscribed). Themes may override `modules/subscription-hub/account-subscriptions.blade.php`
  and `modules/subscription-hub/account-nav.blade.php`; nothing to add to your own sidebar.

## 2026-10-01 — Category UI type in the header

- **`$store['category_ui_type']`** (`list` default | `mega_menu`) and **`$store['mega_menu_depth_start']`** (`0` default) drive the header category nav. `list` links to the categories index. `mega_menu` opens `$megaMenuCategories` (that depth and the next). See `view-data.md`.
- Default theme: `partials/nav.blade.php`. `$rootCategories` is unchanged (footer, catalog export).

## 2026-10-02 — Single-select catalog facets

- **`$facets[].multiple`** is true only for multiselect attributes. Select and radio facets are single-value: the default theme renders them as radios named `attr[<id>]`, and the server keeps one label even when the query repeats values. Multiselect stays `attr[<id>][]`. See `catalog.md`.

## 2026-10-02 — Applied catalog filters

- **`$activeFilters`** on All Products and category listings that show products. One chip per applied attribute value, price bound, in-stock flag, and search query, each with a `url` that removes only that constraint. See `catalog.md`.
- Default theme: `partials/catalog-active-filters.blade.php`, included above the product grid on `pages/products.blade.php` and `pages/category.blade.php`.

## 2026-10-01 — CAPTCHA on storefront forms

- **New directive `@storefrontCaptcha`** — place the tenant's CAPTCHA widget at an exact spot
  inside a `@storefrontForm`. Entirely optional: a protected form that omits it gets the widget
  injected just before `</form>`, and using the directive suppresses that injection so the
  widget never renders twice. See `blade-api.md` and `forms.md`.
- **New `$store['captcha']`** — client-safe configuration (`enabled`, `provider`, `site_key`,
  `token_input`, `protected_forms`) for themes that want to render their own copy around the
  widget. The secret key stays server-side.
- **The provider script loads through `@storefrontScripts`.** A layout without it cannot submit
  a protected form.
- Tenants choose which of `login`, `register`, `forgot-password`, `reset-password`,
  `verify-email`, `resend-verification` and `cms-form` to protect; verification is server-side,
  and the failure message renders inside the widget wrapper. No theme change is required.

## 2026-09-28 — Attribute collections on the storefront

- **New view data**: `$productCollections` (product), `$cartLines[*]['attribute_collections']`
  (cart lines), `$checkoutAttributeGroups` (checkout), `$orderCollections` (account/order,
  guest-order), `$accountCollections` (account/profile), `$categoryCollections` (category) —
  theme-ready, locale-resolved AttributesHub attribute collections per surface flag.
  See `view-data.md` §Attribute collections.
- **New canonical partials**: `partials/attribute-collections.blade.php` (shared `<details>`
  renderer, `isCompact` density switch) and `partials/checkout-attribute-field.blade.php`
  (one checkout custom-attribute input, extracted from the checkout page).
- Default theme: PDP Specifications tab, cart line items, checkout fieldsets, order pages,
  profile page and category page now render flagged collections. Additive — per-attribute
  flags and the flat specifications list are unchanged.

## 2026-09-22 — Default catalog sort

- **`$sort` on All Products and category listings** may be `<attribute_code>` or `-<attribute_code>` when the store has a default sorting attribute and the URL has no `sort`. Direction follows the store setting (ascending unless set to descending). An explicit `sort` query, including `relevance`, is unchanged. No new `$store` key.

## 2026-09-21 — Incoming stock on the product page

- **`$stockInfo.incoming`** (and `$variantRows[*].stock.incoming`): list of
  `{ quantity, date, undated }` when ProductHub **Show incoming on storefront** is on.
  Quantity on `$stockInfo` is **buyable**, not raw on-hand. Catalog `in_stock` filters
  `stock.buyable_qty > 0`. See `view-data.md` (`$stockInfo`) and `catalog.md`.
- Default theme: simple English `@t('Incoming')` list on `partials/product-details.blade.php`.
  Switch off → no incoming in theme data.

## 2026-09-04 — Address country is a select

- **New helpers:** `storefront_countries()` (canonical `['name','iso2','iso3']` list, sorted by
  name) and `storefront_country_name($value)` (resolve a name/ISO2/ISO3 value to its canonical
  name for preselecting an `<option>`) — see `blade-api.md` §9.
- **Address `country` is now a `<select>`** in the account address form, checkout (billing +
  shipping) and the registration company block, built from `storefront_countries()`. The server
  validates it with the `ValidCountry` rule (accepts name, ISO2 or ISO3); the stored value is the
  country **name**. Do not render `country` as a free-text input — see `forms.md` §9.5.

## 2026-08-31 — Pricing rendering contract

- **New doc:** `pricing.md` (§9.16) — the single source of truth for price resolution and the
  per-surface rendering matrix (catalog card / product detail / cart line). Matrix rows are
  asserted by platform tests.
- **`priceViews` / `priceView` behaviour:** `compare_excl` / `compare_incl` are now populated
  for **every** discount type (group, company-group, 1+ tier, catalog rule, ERP provider) —
  previously only date-windowed special prices produced a compare-at. Catalog listings also
  stack catalog price rules, so the displayed price always equals the cart charge.
- **Cart lines:** `original_price_*` / `has_discount` now baseline on the regular list price,
  so tier- and contract-priced lines report a discount.
- **Product page:** `$tierPrices` now excludes tiers equal to the displayed unit price (a 1+
  tier is the base price, not a volume discount). `$contractPrice` is unchanged in shape; the
  default theme renders it as a label-only badge inside the main price block instead of a
  separate box.
- **List view (default theme):** rows emit `data-line-original-unit` + `data-tier-prices`;
  the live total is tier-aware and shows a struck original total when discounted.

## 2026-08-24 — Graceful account deletion (grace period)

- **`profile-delete` behaviour change:** no longer deletes immediately — it now *requests* deletion
  (still takes `current_password`) and emails a 6-digit confirmation code.
- **New form types:** `profile-delete-confirm` (`token`, 6 digits) and `profile-delete-cancel`.
- **New profile page view data:** `$deletionRequest` (`null` or
  `{status: 'awaiting_confirmation'|'scheduled', scheduled_for, expires_at, grace_days, …}`).
  After confirmation the account is soft-deleted only once the grace period (default 14 days)
  elapses; the customer can cancel during that window.
- See `forms.md` and `page-recipes.md` §10.5.

## 2026-08-18 — Parent category branch display mode

- **New `$store` key:** `branch_display_mode` (`children` default | `products` | `both`) — store-wide default for how parent categories with children render.
- **Category page view data:** `$branchDisplayMode`, `$showChildren`, `$showProducts`. Themes must branch on these (not `$isLeaf` alone). When products show on a branch, the listing uses descendant-aware `anchor_category_id` filtering.
- **Per-category override:** ProductHub `Category.branch_display_mode` (`null` = inherit store default).
- See `catalog.md` and `view-data.md`.

