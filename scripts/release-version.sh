#!/usr/bin/env bash
# Bump theme version from the latest GitHub release (or theme.json on first release).
# Writes version/tag to GITHUB_OUTPUT and updates theme/theme.json.
#
# Env:
#   BUMP   patch | minor | major  (default: patch)
#   GH_TOKEN  GitHub token (optional; uses gh auth from Actions)
set -euo pipefail

cd "$(dirname "$0")/.."
THEME_JSON="theme/theme.json"
BUMP="${BUMP:-patch}"
OUTPUT="${GITHUB_OUTPUT:-/dev/stdout}"

[ -f "$THEME_JSON" ] || { echo "error: $THEME_JSON not found" >&2; exit 1; }

theme_version=$(
  sed -n 's/.*"version"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' "$THEME_JSON" | head -n1
)
theme_version=${theme_version:-0.1.0}

latest=""
if command -v gh >/dev/null 2>&1 && [ -n "${GH_TOKEN:-}" ]; then
  latest=$(
    gh release list --limit 1 --json tagName -q '.[0].tagName' 2>/dev/null || true
  )
fi
if [ -z "$latest" ] || [ "$latest" = "null" ]; then
  latest=$(git tag -l 'v*' --sort=-v:refname 2>/dev/null | head -n1 || true)
fi

if [ -z "$latest" ] || [ "$latest" = "null" ]; then
  new="$theme_version"
else
  ver="${latest#v}"
  IFS=. read -r major minor patch _ <<< "$ver"
  major=${major:-0}
  minor=${minor:-0}
  patch=${patch:-0}
  case "$BUMP" in
    major)
      major=$((major + 1))
      minor=0
      patch=0
      ;;
    minor)
      minor=$((minor + 1))
      patch=0
      ;;
    *)
      patch=$((patch + 1))
      ;;
  esac
  new="${major}.${minor}.${patch}"
fi

perl -i -pe 's/"version"\s*:\s*"[^"]*"/"version": "'"$new"'"/' "$THEME_JSON"

{
  echo "version=$new"
  echo "tag=v$new"
} >> "$OUTPUT"

echo "release version: v$new (theme.json updated)"
