# B2Bware Default Theme

The reference storefront theme shipped with every B2Bware store. Use it as the starting
point for your own theme — copy it, restyle it, and re-upload.

## Where the authoring guide lives

Everything you need to build a theme is in **[`docs/`](docs/)**. Start at
**[`docs/README.md`](docs/README.md)** — it's the table of contents and the "load matrix" that
tells you (or your AI assistant) which page to read for a given task. Always read
[`docs/hard-rules.md`](docs/hard-rules.md) first.

> **Latest version.** The copy in `docs/` shipped with this download and may be outdated. The
> authoritative, always-current guide is online:
> https://b2bware.documentationai.com/advanced/storefront-themes
> Theme contract changelog: https://b2bware.documentationai.com/changelog

## Fastest way to start a new theme

Two public GitHub repos, both with the same project layout:

| Want | Repo |
| --- | --- |
| **Blank canvas** (no Blade — build from scratch) | https://github.com/SyncSpider-GmbH/theme_b2bware_starter_kit |
| **Full default theme** (all pages/partials/layouts) | https://github.com/SyncSpider-GmbH/theme_b2bware_default |

Prefer working in **[Cursor](https://cursor.com)** (recommended, not required): both repos ship
`.cursor/rules` that point your agent at the right docs. Any AI tool works — the same links are
in each repo's `README.md` / `AGENTS.md`.

## Layout

```
theme.json      metadata (required)
README.md       this file
rules.md        thin stub → docs/README.md
docs/           the full authoring guide (this is what you read)
layouts/        shop + auth shells
partials/       header, footer, cart sections, account, …
components/      product card, price, buttons, …
pages/          products, product, cart, checkout, account/*, …
assets/         css / js / images / fonts
```
