# Theme B2Bware Default

The complete **B2Bware default storefront theme** — every layout, partial, component and page,
ready to restyle and re-upload as your own theme.

- **Yes** all the Blade and assets the default storefront renders (under [`theme/`](theme/)).
- **Yes** the full authoring contract under [`theme/docs/`](theme/docs/).
- We **recommend [Cursor](https://cursor.com)**; `.cursor/rules` points agents at
  the right docs. Other AI tools: see [`AGENTS.md`](AGENTS.md).

## Repo naming

| Repo type | GitHub name | Example |
| --- | --- | --- |
| **Kit** (template / reference) | `theme_b2bware_*` (snake_case) | `theme_b2bware_starter_kit`, `theme_b2bware_default` |
| **Client theme** | `Theme_B2Bware_*` (PascalCase) | `Theme_B2Bware_Purbeck`, `Theme_B2Bware_Ruebig` |

When you fork this repo for a client, rename to `Theme_B2Bware_<Client>`.
Existing client repos keep their current names — no remote rename needed.

## Starter kit vs default theme

| Need | Start from |
| --- | --- |
| Small overrides — custom header/footer, a few pages, keep platform defaults elsewhere | **[theme_b2bware_starter_kit](https://github.com/SyncSpider-GmbH/theme_b2bware_starter_kit)** |
| Full control — restyle every layout, partial, component and page | **[theme_b2bware_default](https://github.com/SyncSpider-GmbH/theme_b2bware_default)** (this repo) |

Both repos share the same project layout (`theme/docs/`, `scripts/`, `AGENTS.md`, `.cursor/rules/`).

## Quick start

1. Use this template / fork / download ZIP → rename to `Theme_B2Bware_<Client>`.
2. Edit `theme/theme.json` (`name`, `slug`, `author`, `version`) so it is *your* theme.
3. Fill [`docs/CLIENT-CONTEXT.md`](docs/CLIENT-CONTEXT.md) (staging URLs, brand notes).
4. Restyle `theme/layouts|partials|components|pages` — read `theme/docs/` before you change
   contracts (view data, forms, tokens).
5. Upload the theme zip in the admin **Themes** page (see [Built zips](#built-zips) below).

## Built zips

Built ZIPs are **not committed** to git (`dist/*.zip` is gitignored). CI builds them for you.

### Download the latest zip

1. Open **[Actions → Build theme zip](https://github.com/SyncSpider-GmbH/theme_b2bware_default/actions/workflows/build-theme-zip.yml)**.
2. Click the latest successful run on `main`.
3. Under **Artifacts**, download `<slug>-<version>.zip` (matches `theme/theme.json`).

### GitHub Releases (automatic)

Every push to `main` creates a new **[GitHub Release](https://github.com/SyncSpider-GmbH/theme_b2bware_default/releases)** with the
theme zip attached. CI reads the latest release tag, bumps the **patch** version
(`v1.0.0` → `v1.0.1`), updates `theme/theme.json`, builds the zip, and publishes
the release. No manual tags needed.

For a **minor** or **major** bump, run **Actions → Build theme zip → Run workflow**
and pick the bump type.

The version-bump commit uses `[skip ci]` so it does not trigger another release.

### Build locally

```bash
./scripts/build-zip.sh
# → dist/<slug>-<version>.zip
```

## Brand colours and fonts

For a **custom client theme**, put brand tokens in `theme/assets/css/storefront.css`
(`--brand-*` variables, fonts). You are building a bespoke theme — the CSS is the right
place for a fixed brand look.

Use **Admin → Appearance** when you want store owners to tweak colours/fonts in the UI
without a redeploy — that pattern fits adapting the **default theme** more than a fully
custom build. Either approach is valid; decide per client (single fixed style vs.
owner-controlled palette).

See [`theme/docs/styling.md`](theme/docs/styling.md) §10.1 and §10.4 for token overrides
and the head cascade.

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
