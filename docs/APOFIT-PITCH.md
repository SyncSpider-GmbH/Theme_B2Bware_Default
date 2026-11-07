# APOfit Pitch-Theme

Das Standard-Theme in `theme/` bleibt `B2Bware Default` / Slug `default`. Das Pitch-Paket liegt in `themes/apofit/` und wird mit `./scripts/build-apofit-zip.sh` nach `dist/apofit-<version>.zip` gepackt. Es ist ein zweites Theme im selben Repository, kein neuer Remote und kein Ersatz für andere Mandanten.

Prüfung ohne Live-System: `./scripts/validate-apofit-theme.sh`.

## Installation nach Live-Reauth

1. Nur den APOfit-Mandanten lesen: Store, Theme-ID, aktive Version, ein paar Produkte und Kategorien, `category_ui_type`, `allow_guest_checkout`, `require_login_for_prices`, Währung und Sprache.
2. Aktives Theme sichern: ID, Version und, falls der Export existiert, das bisherige ZIP. Den Eintrag nicht löschen.
3. `dist/apofit-1.0.0.zip` hochladen und die Validator-Antwort abwarten. Nicht aktivieren, bevor die Prüfung grün ist.
4. Zuerst Vorschau, falls der Mandant das kann. Danach nur dieses neue Theme aktivieren.
5. Store-Einstellungen, die der Pitch braucht und die das Theme nicht setzen kann: Deutsch, EUR, öffentliche Preise (`require_login_for_prices` aus), Gästecheckout an, Kategorie-Navigation `mega_menu`. Staffelpreise müssen serverseitig an der Kundenrolle hängen. Ein Gast-HTML darf sie nicht enthalten. CSS ist dafür kein Mittel.

## Rollback

Vorheriges Theme wieder aktivieren. Eigene Settings auf die gesicherten Werte stellen. Keine Produkte löschen, die Woo-Integration nicht anfassen, keine Bestellung auslösen. Wenn Upload oder Aktivierung keinen sicheren Rückweg hat, nicht aktivieren.

## Gemessene öffentliche Storefront, 7. November 2026

Nur GET, ohne Anmeldung und ohne Warenkorb auf dieser Storefront.

Adresse: `https://344a67f6-41a1-4e75-9d43-a210a5798b58.store.b2bware.com/de-de`

- Sprache der Seite `de-de`, Attribut Währung `EUR`. Sichtbare Texte: Anmelden, Warenkorb, Kategorien, Favoriten.
- Es ist zusätzlich `en-001` verlinkt. Das Theme schaltet keine Sprache ab.
- `data-category-ui-type="list"`. Das neue Menü mit einer Hauptkategorie und dem Handy-Drilldown erscheint erst, wenn die Kategorie-Navigation im Shop auf `mega_menu` steht. Bis dahin zeigt der Kopf nur den Link Kategorien.
- `/de-de` liefert die Produktliste „Alle Produkte“. Beispiele: Produkt 3384, SKU V34790, und 3377, SKU V35710. Beide sind konfigurierbar. Auf der Karte steht „Anmelden, um zu kaufen“, ohne Euro-Betrag. Die Produktseite zeigt „Varianten auswählen“ und „Anmelden, um Preise zu sehen“, ohne Euro-Betrag und ohne `data-tier-prices`.
- Das Logo liegt als Shop-Mediendatei `apofit-logo-400`. Produktfotos kommen als vollständige WooCommerce-URLs und stehen im Rahmen mit `object-contain`. `@storefrontImage` verändert diese Hosts nicht.
- `/de-de/categories` rendert Kategoriekarten. Öffentlich lesbare Namen unter anderem Alltagshilfen, Aromatherapie, Beauty, Gesundheit, Neuheiten, Lesebrillen. Die API-Stichprobe des Parents war leer; die öffentliche Kategorieseite ist es nicht.
- `/de-de/home` antwortet auf dem aktiven Plattformdefault mit HTTP 500 und der Fehlerseite „Etwas ist schiefgelaufen“, ohne Stacktrace im HTML. Dieses Paket liefert `pages/home.blade.php`. Nach der Vorschau `/de-de/home` erneut öffnen. Wenn der Fehler bleibt, nicht über diese Route aktivieren; die Liste unter `/de-de` bleibt die Startseite.
- Gästecheckout ist zum Messzeitpunkt aus. Öffentliche B2C-Preise und der Gastkauf sind damit noch nicht belegbar. Staffelpreise waren im Gast-HTML nicht eingebettet. Das ist der Ist-Zustand vor dem Einschalten des Gästecheckouts, keine Freigabe der Preislogik.

## 1.0.1 gegenüber dem Admin-Zähler von 1.0.0

Das ZIP 1.0.0 hatte 115 Archiveinträge: 10 Ordner und 105 Dateien. Der Admin nannte 102 Dateien und 72 Overrides. 72 ist die Zahl der Seiten, Partials und Komponenten ohne die zwei Layouts. Die drei fehlenden Dateien gegenüber 105 liegen nicht als Upload-Protokoll vor. Die Schrift-404 kamen nicht von einem fehlenden ZIP-Eintrag: die CSS stand unter `/assets/css/storefront.css`, und `../fonts/*.woff2` wurde dadurch zu `/assets/fonts/*.woff2`, das mit 404 antwortet. 1.0.1 bettet die Schriften in die CSS ein.

## Indexierung

Öffentliche GETs vom 7. November 2026 auf dem noch aktiven Standard-Theme:

- `/de-de/products` und `/de-de/products/3384` enthalten `<meta name="robots" content="index, follow">`.
- Store Setup zeigt Indexierung aus. `seo.robots` ist null.
- Der Vertrag sagt: `$store['indexable']` steuert das Meta über `@storefrontSeo`. Ein Theme soll kein eigenes Robots-Meta setzen. Dieser Vertrag liefert hier kein noindex.

APOfit 1.0.1 lässt `@storefrontSeo` stehen und setzt davor in `layouts/shop.blade.php` und `layouts/auth.blade.php` zusätzlich `<meta name="robots" content="noindex, nofollow">`. Jede Demo-Seite hängt an genau einem dieser Layouts. Lokal geprüft wird die Quelldatei, nicht ein gerendertes Blade, weil hier keine Storefront läuft.

Vorschau nach dem Upload von 1.0.1, vor der Aktivierung: Seitenquelltext von Produktliste, Produkt, Kategorie, Warenkorb, Kasse, Anmeldung. Das erste Robots-Meta muss `noindex, nofollow` sein. Ein zweites `index, follow` von der Plattform kann darunter stehen. Nicht aktivieren, wenn das noindex fehlt.

## Offene Grenzen vor der Aktivierung

- Gästecheckout ist inzwischen an, Login für Preise aus, Mega-Menü an, Indexierung im Setup aus. Eine Produktseite zeigte Variantenpreise 23,90 und 21,90 Euro. Das ist eine Stichprobe, keine volle Preisabnahme. Warenkorb und Kasse mit Artikel, ohne Bestellabschluss, prüft der Parent in der Vorschau.
- Rollen sind noch nicht angelegt. Zuweisung und Rollenpreise sind kein Blocker. Nichts davon anlegen oder ändern. Gast-Staffeldaten bleiben eine serverseitige Grenze. Rolle A gegen Rolle B, Logout und Cache sind offen und gehören nicht zu 1.0.1.
- `mega_menu` setzen, sonst bleibt das neue Menü aus.
- Upload, Vorschau und Aktivierung nur im Browser-Admin. Dieses Paket wurde nicht hochgeladen.
- Kein SEO, keine Indexierung einschalten, kein DNS, kein Woo-Eingriff, kein Naturfeh.
- Der gefüllte Checkout von apofit.at wurde als Gast-HTML gelesen (Kundendaten, Versandwahl, Gutschein, PayPal/Karte/Klarna). Es wurde keine Bestellung abgeschickt. Die B2Bware-Kasse bleibt das Plattformformular; Zahlarten kommen vom Server und sind nicht fest verdrahtet.

## Nicht in diesem Lauf

Kein Live-Upload, kein Kundenkonto, keine Shop-Einstellung, keine Bestellung. Naturfeh ist nicht enthalten.
