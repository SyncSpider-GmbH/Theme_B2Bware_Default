# APOfit Builder-Startseite (Entwurf)

`apofit-home-builder.json` ist der native `content_json` für `create_page` / `update_page`.
Nicht im Theme-ZIP. Der Parent veröffentlicht die Seite und setzt `home_page` auf den URL-Key `startseite`, nicht auf eine numerische ID.

Enthalten: Hero, drei Kacheln, Story, Werte-Icons, Themenwelten. Bilder sind die öffentlichen Original-URLs. Links sind native Kategoriepfade (`/categories/...`), keine WooCommerce-URLs.

Nicht enthalten: Bestseller-Preise. Die gehören auf native Produktkarten, sobald eine Katalog-Datenquelle gebunden ist. Keine Preise wurden in den Entwurf geschrieben.

Marken, Über uns, News und Kontakt erscheinen im Kopf nur über `$storefrontContentNav`, wenn der Shop diese Inhalte für die Navigation markiert.
