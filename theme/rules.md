# Theme Authoring Rules

**Read [`docs/README.md`](docs/README.md)** — it is the table of contents and load matrix for
the theme authoring contract. Always start with [`docs/hard-rules.md`](docs/hard-rules.md).

Slots are documented in [`docs/analytics-and-slots.md`](docs/analytics-and-slots.md). Use
`@storefrontSlot('key')` for an additive region and a balanced
`@startStorefrontSlot('key')` / `@endStorefrontSlot` pair for replaceable theme-default Blade.

> **Latest version.** The docs in `docs/` shipped with this download and may be outdated. The
> authoritative, always-current guide is online:
> https://b2bware.documentationai.com/advanced/storefront-themes
> Theme contract changelog: https://b2bware.documentationai.com/changelog

## Images (hard rule)

Wrap every catalog / customer `media_url` in `@storefrontImage(...)`. Never put a
raw Bunny (`*.b-cdn.net`) or SpiderDesk `/media/...` URL in `<img src>`.
Product photos use `object-contain` inside a fixed frame, and grid cards share a height.
See [`docs/seo-and-images.md`](docs/seo-and-images.md) §9.11 and
[`docs/hard-rules.md`](docs/hard-rules.md) §9 (platform params + Bunny Transform API).
