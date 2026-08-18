# Theme B2Bware Default

The complete **B2Bware default storefront theme** — every layout, partial, component and page,
ready to restyle and re-upload as your own theme.

- **Yes** all the Blade and assets the default storefront renders (under [`theme/`](theme/)).
- **Yes** the full authoring contract under [`theme/docs/`](theme/docs/).
- We **recommend [Cursor](https://cursor.com)**; `.cursor/rules` points agents at
  the right docs. Other AI tools: see [`AGENTS.md`](AGENTS.md).

## Quick start

1. Use this template / fork / download ZIP → rename to `Theme_B2Bware_<Client>`.
2. Edit `theme/theme.json` (`name`, `slug`, `author`, `version`) so it is *your* theme.
3. Fill [`docs/CLIENT-CONTEXT.md`](docs/CLIENT-CONTEXT.md) (staging URLs, brand notes).
4. Restyle `theme/layouts|partials|components|pages` — read `theme/docs/` before you change
   contracts (view data, forms, tokens).
5. `./scripts/build-zip.sh` → upload `dist/<slug>-<version>.zip` in the admin Themes page.

## Want to start from a blank canvas instead?

Use **[theme_b2bware_starter_kit](https://github.com/SyncSpider-GmbH/theme_b2bware_starter_kit)** —
same project layout, but with no Blade, so you design every page yourself.

## Keeping docs fresh

`theme/docs/` is updated here whenever the theme contract changes. On an old fork, refresh
the topic pages from the **live public wiki** — no credentials needed, and your
Blade/CSS/`theme.json`/`README.md`/`CHANGELOG.md` are never touched:

```bash
./scripts/update-theme-docs.sh
```

That pulls https://github.com/SyncSpider-GmbH/b2bware-documentationai-wiki
(`advanced/storefront-themes/`).

Live docs: https://b2bware.documentationai.com/advanced/storefront-themes
