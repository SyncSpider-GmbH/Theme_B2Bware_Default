#!/usr/bin/env bash
# Static checks for the APOfit package. Does not touch a database or a live tenant.
set -euo pipefail
cd "$(dirname "$0")/.."
THEME_DIR="themes/apofit"
fail=0
say() { printf '%s\n' "$1"; }
bad() { printf 'FAIL %s\n' "$1" >&2; fail=1; }

python3 - <<'PY' || bad "theme.json is not valid JSON or missing required keys"
import json
from pathlib import Path
data = json.loads(Path("themes/apofit/theme.json").read_text(encoding="utf-8"))
required = ["name", "slug", "author", "version", "description", "license"]
missing = [key for key in required if not str(data.get(key, "")).strip()]
if missing:
    raise SystemExit("missing " + ", ".join(missing))
if data["slug"] == "default" or data["name"] == "B2Bware Default":
    raise SystemExit("package still uses the default theme identity")
print("theme.json", data["slug"], data["version"])
PY

default_name=$(sed -n 's/.*"name"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' theme/theme.json | head -n1)
default_slug=$(sed -n 's/.*"slug"[[:space:]]*:[[:space:]]*"\([^"]*\)".*/\1/p' theme/theme.json | head -n1)
if [ "$default_name" != "B2Bware Default" ] || [ "$default_slug" != "default" ]; then
  bad "theme/theme.json identity changed"
else
  say "default theme identity unchanged"
fi

readme="themes/apofit/README.md"
if [ ! -f "$readme" ]; then
  bad "README.md missing"
else
  size=$(wc -c < "$readme")
  if [ "$size" -gt 65536 ]; then
    bad "README.md larger than 64KB"
  fi
  python3 - <<'PY' || bad "README.md sections"
from pathlib import Path
text = Path("themes/apofit/README.md").read_text(encoding="utf-8")
text.encode("utf-8")
for heading in ["## Purpose", "## What this theme changes", "## Scripts and libraries", "## Still on the default theme"]:
    if heading not in text:
        raise SystemExit("missing " + heading)
    body = text.split(heading, 1)[1]
    nxt = body.find("\n## ")
    chunk = body if nxt < 0 else body[:nxt]
    if not chunk.strip().strip("#").strip():
        raise SystemExit("empty " + heading)
print("README sections ok", len(text.encode("utf-8")), "bytes")
PY
fi

if grep -R -n -E '@php|<\?php|<\?=' "$THEME_DIR" --include='*.blade.php' --include='*.css' --include='*.json' >/tmp/apofit-forbidden.txt; then
  bad "forbidden PHP in theme"
  cat /tmp/apofit-forbidden.txt >&2
else
  say "no inline PHP"
fi

if grep -R -n -E 'app\(|request\(|config\(|env\(|session\(|cookie\(|auth\(' "$THEME_DIR" --include='*.blade.php' >/tmp/apofit-helpers.txt; then
  bad "forbidden helpers"
  cat /tmp/apofit-helpers.txt >&2
else
  say "no forbidden helpers"
fi

if grep -R -n -E 'apofit\.at/kategorie|apofit\.at/shop' "$THEME_DIR" --include='*.blade.php' --include='*.css' --include='*.json' >/tmp/apofit-urls.txt; then
  bad "hardcoded legacy shop URLs"
  cat /tmp/apofit-urls.txt >&2
else
  say "no hardcoded legacy category URLs"
fi

if grep -n 'data-tier-prices' "$THEME_DIR/components/product-price-data-attributes.blade.php" >/dev/null; then
  if grep -n 'isAuthenticated && $canSeePrices' "$THEME_DIR/components/product-price-data-attributes.blade.php" >/dev/null; then
    say "tier attribute gated on authenticated session"
  else
    bad "data-tier-prices is not gated on authentication"
  fi
else
  say "no data-tier-prices attribute"
fi

if grep -n 'menu_path' "$THEME_DIR/partials/nav.blade.php" >/dev/null \
  && grep -n 'category_ui_type' "$THEME_DIR/partials/nav.blade.php" >/dev/null; then
  say "mega menu is data-driven"
else
  bad "nav is missing menu_path or category_ui_type"
fi

./scripts/build-apofit-zip.sh
zipfile=$(ls -1 dist/apofit-*.zip | head -n1)
python3 - "$zipfile" <<'PY' || bad "zip root"
import sys, zipfile
path = sys.argv[1]
with zipfile.ZipFile(path) as archive:
    names = archive.namelist()
    if "theme.json" not in names or "README.md" not in names:
        raise SystemExit("theme.json or README.md not at zip root")
    if any(name.startswith("theme/") for name in names):
        raise SystemExit("zip contains a theme/ wrapper")
    required = [
        "layouts/shop.blade.php",
        "partials/header.blade.php",
        "partials/nav.blade.php",
        "partials/footer.blade.php",
        "partials/mini-cart.blade.php",
        "pages/home.blade.php",
        "pages/product.blade.php",
        "pages/category.blade.php",
        "pages/cart.blade.php",
        "pages/checkout.blade.php",
        "assets/css/storefront.css",
    ]
    missing = [item for item in required if item not in names]
    if missing:
        raise SystemExit("missing " + ", ".join(missing))
    print("zip", path, "files", len(names))
PY

if [ "$fail" -ne 0 ]; then
  exit 1
fi
say "APOfit package ok"
