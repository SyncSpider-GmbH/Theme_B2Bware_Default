# Theme Customization Implementation Guide

This document details all customizations made in this repository so that another AI agent or developer can implement the exact same changes in a different B2Bware theme file/repository.

---

## 1. Quick Order Page — Row Count Label (`theme/pages/account/quick-order.blade.php`)

- **Goal**: Display `"Add 1 item row."` on the quick order review submit button when exactly 1 valid item is present.
- **Blade Template Update**:
  Locate the submit button inside `@storefrontForm('quick-order-add')`:
  ```blade
  <span data-qo-review-submit-label>
      @isset($review)
          @if((int) data_get($review, 'valid_count') === 1)
              @t('Add 1 item row.')
          @else
              @t('Add %d products to cart', (int) data_get($review, 'valid_count'))
          @endif
      @else
          @t('Add to cart')
      @endisset
  </span>
  ```
- **JS Script Update**:
  In the `<script type="module">` block at the bottom of the page:
  1. Add the translated string template:
     ```js
     const addOneLabel = @json(t('Add 1 item row.'));
     ```
  2. Update `refreshReview()`:
     ```js
     const refreshReview = () => {
       if (!reviewSection || !reviewBody) return;
       const count = reviewBody.querySelectorAll("[data-qo-review-row]").length;
       if (reviewSubmitLabel) {
         if (count === 1) {
           reviewSubmitLabel.textContent = addOneLabel;
         } else {
           reviewSubmitLabel.textContent = addLabelTemplate.replace(
             "%d",
             String(count),
           );
         }
       }
       if (reviewEmpty) reviewEmpty.hidden = count > 0;
       const hasNotices = !!(
         reviewNotices && reviewNotices.childElementCount > 0
       );
       reviewSection.hidden = count === 0 && !hasNotices;
     };
     ```

---

## 2. Date Formatting Standardisation (`DD/MM/YYYY`)

- **Goal**: Ensure all displayed dates follow the explicit `DD/MM/YYYY` format rather than default locale short dates.
- **Files & Edits**:
  1. `theme/pages/account/api-keys.blade.php`:
     ```blade
     {{ formatDate(data_get($key, 'created_at'), 'DD/MM/YYYY') }}
     {{ formatDate(data_get($key, 'last_used_at'), 'DD/MM/YYYY') }}
     {{ formatDate(data_get($key, 'expires_at'), 'DD/MM/YYYY') }}
     ```
  2. `theme/pages/account/catalog-export.blade.php`:
     ```blade
     @formatDate(data_get($export, 'generated_at'), 'DD/MM/YYYY LT')
     ```
  3. `theme/pages/account/index.blade.php`:
     ```blade
     {{ formatDate($order->_created_at ?? null, 'DD/MM/YYYY') }}
     ```
  4. `theme/pages/account/order.blade.php`:
     ```blade
     {{ formatDate($order->_created_at ?? null, 'DD/MM/YYYY') }}
     ```
  5. `theme/pages/account/orders.blade.php`:
     ```blade
     {{ formatDate($order->_created_at ?? null, 'DD/MM/YYYY') }}
     ```
  6. `theme/pages/guest-order.blade.php`:
     ```blade
     {{ formatDate($order->_created_at ?? null, 'DD/MM/YYYY') }}
     ```

---

## 3. Direct Quantity Selection on Catalog Listing Cards (`theme/components/add-to-cart-button.blade.php`)

- **Goal**: Add a quantity selector directly to add-to-cart buttons on product listing cards.
- **Updates**:
  1. Accept `showQuantity` prop:
     ```blade
     @props(['product' => null, 'variant' => null, 'quantity' => 1, 'label' => null, 'redirect' => null, 'block' => false, 'icon' => true, 'showQuantity' => true])
     ```
  2. Embed quantity selector inside the `cart-add` form:
     ```blade
     @storefrontForm('cart-add', ['class' => $block ? 'add-to-cart flex w-full items-center gap-2' : 'add-to-cart flex items-center gap-2'])
         <input type="hidden" name="product_id" value="{{ $product->id }}">
         @if($variant && !empty($variant->id))
             <input type="hidden" name="variant_id" value="{{ $variant->id }}">
         @endif
         @if($redirect)
             <input type="hidden" name="redirect" value="{{ $redirect }}">
         @endif
         @if($showQuantity ?? true)
             @include('components.quantity-selector', ['value' => $quantity ?? 1, 'min' => 1, 'name' => 'quantity'])
         @else
             <input type="hidden" name="quantity" value="{{ $quantity ?? 1 }}">
         @endif
         <button type="submit" ...>
             ...
         </button>
     @endstorefrontForm
     ```

---

## 4. Zero Price Display / Quote Request (`theme/components/price.blade.php`)

- **Goal**: Replace the `"Request quote"` text with a quantity selector when `zero_price_as_quote` is enabled.
- **Updates**:
  Replace:
  ```blade
  <span class="price price--quote inline-flex items-center text-sm font-medium text-primary">@t('Request quote')</span>
  ```
  With:
  ```blade
  @include('components.quantity-selector', ['value' => 1, 'min' => 1, 'name' => 'quantity'])
  ```

---

## 5. Enhanced Discount & Savings Displays

- **Goal**: Highlight discount percentages and applied catalog/line discounts clearly.
- **Files & Edits**:
  1. `theme/components/price.blade.php`:
     - Display a `-X%` percentage badge next to strikethrough compare-at prices:
       ```blade
       @if(($comp = (data_get($priceView, 'compare_excl') ?? $compareAt)) && $comp > ($curr = (data_get($priceView, 'current_excl') ?? $amount)))
           <span class="price__compare text-sm text-body font-normal line-through">{{ formatCurrency($comp) }}</span>
           <span class="inline-flex items-center rounded-md bg-success-subtle px-1.5 py-0.5 text-xs font-semibold text-success-subtle-content border border-success/20">-{{ (int) round((($comp - $curr) / $comp) * 100) }}%</span>
       @endif
       ```
  2. `theme/partials/cart-line-items.blade.php`:
     - Render percentage savings and styled badges for applied catalog/line rules:

       ```blade
       @if($canSeePrices && $line['has_discount'])
           @if(($orig = (($store['display_prices_with_tax'] ?? 'excluding_tax') === 'including_tax' && $line['has_tax'] ? $line['original_price_incl'] : $line['original_price_excl']) * $line['quantity']) && $orig > $line['line_total'])
               <div class="flex items-center gap-1.5">
                   <span class="cart__item-was text-xs text-body line-through">{{ formatCurrency($orig) }}</span>
                   <span class="inline-flex items-center rounded bg-success-subtle px-1.5 py-0.5 text-xs font-semibold text-success-subtle-content border border-success/20">-{{ (int) round((($orig - $line['line_total']) / $orig) * 100) }}%</span>
               </div>
           @endif

           <div class="cart__item-rules flex flex-col items-end gap-1 mt-0.5">
               @foreach($line['applied_catalog_rules'] ?? [] as $rule)
                   @if(($rule['rule_name'] ?? '') !== '')
                       <span class="inline-flex items-center gap-1 rounded bg-surface-card px-2 py-0.5 text-xs font-medium text-success border border-success/30 shadow-xs">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0 text-success" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                           {{ $rule['rule_name'] }}
                       </span>
                   @endif
               @endforeach
           </div>
       @endif
       ```

  3. `theme/partials/cart-discounts.blade.php` & `theme/partials/checkout-summary.blade.php`:
     - Style applied price rules with green badges and checkmark icons.

---

## 6. Product Page — Hide SKU on Variant Parents & Show EAN (`theme/partials/product-details.blade.php`)

- **Goal**: Hide SKU on parent configurable products (where SKU is meaningless until a variant is chosen) and display EAN whenever available.
- **Updates**:
  Update `@section('sku')`:
  ```blade
  @section('sku')
      @php
          $targetProd = $displayProduct ?? $product;
          $isVariantParent = data_get($product, 'product_type') === 'configurable' || (!empty($variants) && $variants->isNotEmpty());
          $eanVal = data_get($targetProd, 'ean') ?? data_get($targetProd, 'gtin') ?? data_get($targetProd, 'barcode');
      @endphp
      @if(empty($isVariantParent) && !empty($targetProd->sku))
          <p class="product__sku text-sm text-body m-0">@t('SKU') {{ $targetProd->sku }}</p>
      @endif
      @if(!empty($eanVal))
          <p class="product__ean text-sm text-body m-0">@t('EAN') {{ $eanVal }}</p>
      @endif
  @show
  ```

---

## 7. My Account Dashboard & Navigation Modifications

1. **Hide Lifetime Spend Stat Card (`theme/pages/account/index.blade.php`)**:
   - Remove the `accountOverview['lifetime_spend']` stat card from the dashboard grid.
   - Adjust grid columns class from `xl:grid-cols-4` to `sm:grid-cols-3`.
2. **Gate / Hide API Keys (`theme/partials/account-nav.blade.php`)**:
   - Wrap the API Keys link in a store feature check:
     ```blade
     @if ($store['api_keys_enabled'] ?? false)
         <a href="@routeUrl('store.account.api-keys')" ...>@t('API Keys')</a>
     @endif
     ```

---

## 8. Theme Configuration (`theme/theme.json`)

- **Goal**: Set the custom client theme identity.
- **Updates**:
  ```json
  {
    "name": "Target Theme Name",
    "slug": "target_theme_slug",
    "author": "Author Name",
    "version": "1.0.0",
    "description": "Storefront theme description."
  }
  ```
