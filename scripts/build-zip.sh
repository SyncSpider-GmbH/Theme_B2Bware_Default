#!/usr/bin/env bash
# Zip theme/ into dist/<slug>-<version>.zip for upload in the b2bware admin.
# theme.json MUST be at the zip root (no wrapper folder).
set -euo pipefail
cd "$(dirname "$0")/.."
THEME_DIR="theme"
DIST_DIR="dist"
[ -f "$THEME_DIR/theme.json" ] || { echo "error: $THEME_DIR/theme.json not found" >&2; exit 1; }
slug=$(sed -n 's/.*"slug"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' "$THEME_DIR/theme.json" | head -n1)
version=$(sed -n 's/.*"version"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' "$THEME_DIR/theme.json" | head -n1)
slug=${slug:-theme}; version=${version:-0.0.0}
mkdir -p "$DIST_DIR"
out="$DIST_DIR/${slug}-${version}.zip"
rm -f "$out"
( cd "$THEME_DIR" && zip -rq "../$out" . \
    -x "*.DS_Store" -x "__MACOSX/*" -x "*/.git/*" -x "*.map" )
echo "built $out"
