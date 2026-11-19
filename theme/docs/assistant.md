---
title: Shop assistant widget
description: How a theme includes the platform chat widget.
---

## Include it

The widget is a platform asset, not theme-owned JavaScript. Put this once near the end of `<body>`, after `@storefrontScripts`:

```blade
@storefrontAssistant
```

The default theme does this in `layouts/shop.blade.php`.

When `$store['assistant_enabled']` is false the directive renders nothing: no button, no stylesheet, no script. Do not also render your own launcher for the same flag.

## What the visitor gets

- A floating button. On a narrow screen the chat is a full-screen sheet. From 768px up it is a panel.
- The name and greeting from `$store['assistant']`.
- Streamed replies, product cards, and confirm / reject for a proposed basket change or reorder.
- Each product card shows the name, image, short description, the price this visitor is allowed to see, and stock. **View product** links to the product page. **Add to cart** adds `quantity_min` (already a multiple of `quantity_step`) in one click and refreshes `[data-storefront-section="mini-cart"]`. When `can_add_to_cart` is false the card shows a sign-in hint, a variant hint, or an out-of-stock note instead (`purchase_block`).
- The conversation id is kept in `sessionStorage` and in the shop session. Closing the panel or reloading the page shows the same history.
- A paperclip attaches a PDF, CSV, text file, or image (5 MB). A microphone records a message and sends the transcript. Both use the assistant routes and stop working when the assistant is off.

English strings are the `@t()` keys. German (`de-de`) is built in until a Settings Hub translation replaces the English source. The key list lives in `partials/assistant-strings.blade.php` so Manage translations can see it.

The HTTP contract is `packages/Apps/AiHub/docs/storefront-assistant.md`.
