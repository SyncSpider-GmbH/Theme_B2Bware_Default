# Changelog

## 2026-11-15

- Product page: the gallery no longer stretches to the height of a long description. The main
  image stays square and at the top, on the card surface instead of the tinted one, and
  stays in view on tablet and desktop while the details scroll.

## 2026-11-14

- Header search: typing no longer reloads the product listing. It opens a suggestion panel
  under the field with product image, name, SKU, price and the filters that apply to the
  matches. The page changes only on Enter, on "Show all results" or on a click on a
  suggestion. Filters chosen in the panel are carried into the listing.
- New translation keys: `Search suggestions`, `Show all results`.

## 2026-11-13

- Product page: the product name renders as an `<h1>` on a full page load. The heading
  section is `product-title` (the `title` section stays the browser title).

## 2026-11-02

- New `product.purchase.fields` slot inside the product page's add-to-cart form and the variant table's
  bulk-add form, so modules can collect purchase-time options (engraving text, print positions).
- Product cards and the add-to-cart button show **Choose options** for products listed in
  `$configurationRequiredProductIds` instead of adding them directly.

## 2026-10-11

- New override points on cart, checkout, favorites, account, address, sign-in, category,
  guest-order, proposal, customer-selection and error pages, so a custom theme can replace one
  block of a page and keep receiving updates for the rest.
- Every theme now ships a README.md describing what it changes; it is shown on the theme's page
  in the store admin.
