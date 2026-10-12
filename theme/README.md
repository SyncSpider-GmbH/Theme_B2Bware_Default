# B2Bware Default

## Purpose

The reference storefront theme shipped with every B2Bware store. It renders every storefront
page — catalog, product, cart, checkout, account and sign-in — and is the fallback for any file a
custom theme does not ship, so stores on a custom theme keep receiving its updates.

## What this theme changes

- Nothing on top of the platform: this is the platform default itself.
- Colors, fonts and the logo come from Store setup → Appearance.
- Pages expose `@section` override points (cart, checkout, favorites, account, sign-in, header,
  footer, product and listings) so a custom theme can replace a single block and keep the rest.

## Scripts and libraries

- Storefront runtime from the platform (`@storefrontScripts`): cart updates without page reloads,
  section refresh and forms.
- No third-party scripts, fonts or libraries.

## Still on the default theme

- Everything — this theme is the default.

## Building your own theme

Every theme must ship a `README.md` with the four sections above, written for the shop owner; the
store admin shows it on the theme's page and an upload is rejected while a section is empty. An
optional `CHANGELOG.md` is shown next to it. See [`docs/theme-readme.md`](docs/theme-readme.md).

Most themes should **not** be a copy of this repo. Start from the starter kit, restyle with CSS,
and override single sections of a default page with `@extends('default::pages.…')` (see
[`docs/blade-api.md` §9.0](docs/blade-api.md)). Copy files from this repo only when you
intentionally want to fork them — copied files no longer receive default-theme updates.

| Want | Repo |
| --- | --- |
| **Starter kit** (recommended — CSS plus only the sections you change; everything else stays on the default theme) | https://github.com/SyncSpider-GmbH/theme_b2bware_starter_kit |
| **Full default theme** (all pages/partials/layouts — for forking specific files on purpose) | https://github.com/SyncSpider-GmbH/theme_b2bware_default |

The authoring guide is in **[`docs/`](docs/)**. Start at **[`docs/README.md`](docs/README.md)**
— the table of contents and the "load matrix" that tells you (or your AI assistant) which page to
read for a given task. Always read [`docs/hard-rules.md`](docs/hard-rules.md) first.

> **Latest version.** The copy in `docs/` shipped with this download and may be outdated. The
> authoritative, always-current guide is online:
> https://b2bware.documentationai.com/advanced/storefront-themes
> Theme contract changelog: https://b2bware.documentationai.com/changelog

Prefer working in **[Cursor](https://cursor.com)** (recommended, not required): both repos ship
`.cursor/rules` that point your agent at the right docs. Any AI tool works — the same links are
in each repo's `README.md` / `AGENTS.md`.

```
theme.json      metadata (required)
README.md       this file — what the theme does (required)
CHANGELOG.md    what changed per version (optional)
rules.md        thin stub → docs/README.md
docs/           the full authoring guide (this is what you read)
layouts/        shop + auth shells
partials/       header, footer, cart sections, account, …
components/     product card, price, buttons, …
pages/          products, product, cart, checkout, account/*, …
assets/         css / js / images / fonts
```
