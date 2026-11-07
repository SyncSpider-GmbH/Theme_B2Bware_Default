#!/usr/bin/env bash
# Zip themes/apofit into dist/apofit-<version>.zip.
# theme.json stays at the archive root. The default theme/ directory is not read.
set -euo pipefail
cd "$(dirname "$0")/.."
THEME_DIR="themes/apofit"
DIST_DIR="dist"
[ -f "$THEME_DIR/theme.json" ] || { echo "error: $THEME_DIR/theme.json not found" >&2; exit 1; }
slug=$(sed -n 's/.*"slug"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' "$THEME_DIR/theme.json" | head -n1)
version=$(sed -n 's/.*"version"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' "$THEME_DIR/theme.json" | head -n1)
slug=${slug:-apofit}
version=${version:-0.0.0}
if [ "$slug" = "default" ]; then
  echo "error: refusing to pack a package whose slug is still default" >&2
  exit 1
fi
mkdir -p "$DIST_DIR"
out="$DIST_DIR/${slug}-${version}.zip"
rm -f "$out"
out_abs="$(pwd)/$out"
( cd "$THEME_DIR" && zip -rq "$out_abs" . \
    -x "*.DS_Store" -x "__MACOSX/*" -x "*/.git/*" -x "*.map" -x ".cursor/*" -x ".cursor/**" )
echo "built $out"
