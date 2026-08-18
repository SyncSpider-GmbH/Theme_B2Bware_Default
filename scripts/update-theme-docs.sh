#!/usr/bin/env bash
# Refresh theme/docs/ topic pages from the live public documentation wiki.
# Never touches your Blade, CSS, assets, theme.json, README.md or CHANGELOG.md.
#
# No credentials required — the docs are public:
#
#   ./scripts/update-theme-docs.sh
#
# Source: https://github.com/SyncSpider-GmbH/b2bware-documentationai-wiki
# Live:   https://b2bware.documentationai.com/advanced/storefront-themes
#
# Override the source repo/branch only if you know why:
#   B2BWARE_DOCS_WIKI_REPO    default: SyncSpider-GmbH/b2bware-documentationai-wiki
#   B2BWARE_DOCS_WIKI_BRANCH  default: main
set -euo pipefail
cd "$(dirname "$0")/.."

WIKI_REPO="${B2BWARE_DOCS_WIKI_REPO:-SyncSpider-GmbH/b2bware-documentationai-wiki}"
WIKI_BRANCH="${B2BWARE_DOCS_WIKI_BRANCH:-main}"

case "${1:-}" in
  -h|--help) sed -n '2,14p' "$0"; exit 0 ;;
  "")        ;;
  *)         echo "usage: $0" >&2; exit 2 ;;
esac

[ -d theme ] || { echo "error: run this from a theme repo (no theme/ directory here)" >&2; exit 1; }
command -v curl >/dev/null 2>&1 || { echo "error: 'curl' is required" >&2; exit 1; }
command -v tar  >/dev/null 2>&1 || { echo "error: 'tar' is required"  >&2; exit 1; }

tmp=$(mktemp -d)
trap 'rm -rf "$tmp"' EXIT

url="https://codeload.github.com/${WIKI_REPO}/tar.gz/refs/heads/${WIKI_BRANCH}"
echo "fetching docs from github.com/${WIKI_REPO} (${WIKI_BRANCH})"
curl -fsSL "$url" -o "$tmp/wiki.tar.gz" || {
  echo "error: could not download $url" >&2
  echo "Check your internet connection." >&2
  exit 1
}

mkdir -p "$tmp/extracted"
tar -xzf "$tmp/wiki.tar.gz" -C "$tmp/extracted"
root="$(find "$tmp/extracted" -mindepth 1 -maxdepth 1 -type d | head -n1)"
wiki_dir="$root/advanced/storefront-themes"
[ -d "$wiki_dir" ] || {
  echo "error: archive has no advanced/storefront-themes/ — nothing changed" >&2
  echo "The storefront-themes docs must be on branch '${WIKI_BRANCH}' of ${WIKI_REPO}." >&2
  exit 1
}

mkdir -p theme/docs

# Every *.md in the wiki is a theme docs page except overview.md (wiki-only
# landing). README.md / CHANGELOG.md are the theme download's own files and
# are never overwritten.
changed=0
for src in "$wiki_dir"/*.md; do
  [ -f "$src" ] || continue
  name="$(basename "$src")"
  case "$name" in overview.md) continue ;; esac
  dst="theme/docs/$name"
  if [ -f "$dst" ] && cmp -s "$src" "$dst"; then
    continue
  fi
  cp "$src" "$dst"
  echo "→ $name"
  changed=$((changed+1))
done

# Remove local docs pages that no longer exist on the wiki (keep README.md /
# CHANGELOG.md which are theme-owned).
for dst in theme/docs/*.md; do
  [ -f "$dst" ] || continue
  name="$(basename "$dst")"
  case "$name" in README.md|CHANGELOG.md) continue ;; esac
  [ -f "$wiki_dir/$name" ] && continue
  rm "$dst"
  echo "× $name (removed — not on wiki)"
  changed=$((changed+1))
done

if [ "$changed" -eq 0 ]; then
  echo "done: theme/docs already up to date"
else
  echo "done: $changed page(s) refreshed"
  echo "review with: git diff --stat theme/docs"
fi
