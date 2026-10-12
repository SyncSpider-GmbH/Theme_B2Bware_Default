# Theme Authoring Rules

**Read [`docs/README.md`](docs/README.md)** — it is the table of contents and load matrix for
the theme authoring contract. Always start with [`docs/hard-rules.md`](docs/hard-rules.md).

> **Latest version.** The docs in `docs/` shipped with this download and may be outdated. The
> authoritative, always-current guide is online:
> https://b2bware.documentationai.com/advanced/storefront-themes
> Theme contract changelog: https://b2bware.documentationai.com/changelog

## Images (hard rule)

Wrap every catalog / customer `media_url` in `@storefrontImage(...)`. Never put a
raw Bunny (`*.b-cdn.net`) or SpiderDesk `/media/...` URL in `<img src>`.
See [`docs/seo-and-images.md`](docs/seo-and-images.md) §9.11 and
[`docs/hard-rules.md`](docs/hard-rules.md) §9 (platform params + Bunny Dynamic Images API).
