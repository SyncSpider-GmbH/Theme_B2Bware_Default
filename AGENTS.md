# Agent instructions (any AI tool)

This is the **full default** B2Bware storefront theme repo — all Blade is present and
yours to restyle. Cursor is recommended but not required.

1. Start at [`theme/docs/README.md`](theme/docs/README.md) (load matrix).
2. Always read [`theme/docs/hard-rules.md`](theme/docs/hard-rules.md).
3. Prefer the live docs when the clone may be old:
   https://b2bware.documentationai.com/advanced/storefront-themes
4. Never invent directives, form types, `$store` keys, or tokens not in the docs.
5. Keep edits inside the canonical file surface — the upload validator rejects new
   file paths that are not part of it.
6. Build an upload zip with `./scripts/build-zip.sh` → upload in admin, or download the
   latest artifact from the repo's **Build theme zip** GitHub Action (see root `README.md`).
