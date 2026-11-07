# Changelog

## 1.1.0 — 2026-11-07

- Kopfzeile weiß, dünne grüne Servicezeile, Produkte öffnet ein breites Menüraster mit grünen Überschriften. Keine zweite Scrollspalte und kein wiederholtes „Unterpunkte“.
- Produktseite: Bild links, Name, Kurztext, Variantenlinks, Preis und grüner Button rechts. Die lange Beschreibung steht darunter. Die native Variantentabelle bleibt erhalten.
- Kategorien ohne Bild haben keine Platzhalterfläche.
- Die bearbeitbare Startseite ist ein Builder-Entwurf außerhalb des Theme-ZIP.

## 1.0.3 — 2026-11-07

- Produktbeschreibungen können eine feste Tabelle enthalten. Bei Produkt 3384 ist sie 756 Pixel breit und hat die Produktseite seitlich verschoben. Die Tabelle bleibt lesbar und rollt nur in ihrem eigenen Bereich. Die Seite selbst bleibt im Bildschirm. Variantenbuttons liegen außerhalb dieses Bereichs.

## 1.0.2 — 2026-11-07

- Auf schmalen Bildschirmen bleibt die Kopfzeile in der Breite. Das Wort „Anmelden“ erscheint ab mittlerer Breite, das Logo wird kleiner. Die Seite bekommt dadurch keinen seitlichen Rollbalken. Inhalte werden nicht abgeschnitten.

## 1.0.1 — 2026-11-07

- Schriften liegen in der CSS-Datei. Die Vorschau hat die einzelnen Schriftdateien mit 404 beantwortet.
- Das Menü öffnet ein Sortiment. Die Hauptkategorien stehen in der Leiste links, die Unterpunkte rechts. Unkategorisiert und beide Wärmeflaschen-Namen bleiben enthalten.
- Beide Layouts setzen zuerst `noindex, nofollow`. Die Plattform kann darunter weiter `index, follow` ausgeben. Die strengere Angabe soll die Demo aus dem Index halten.

## 1.0.0 — 2026-11-07

- Erstes APOfit-Paket für die Pitch-Demo, getrennt vom Standard-Theme.
- Markenfarben, Schrift und Startseite.
- Kategorie-Menü: am Desktop eine Hauptkategorie zur Zeit, am Handy mit Zurück und Suche.
- Mobile Filterfläche auf Kategorieseiten.
- Staffelpreise und Vertragspreis-Hinweis nur für angemeldete Sitzungen.
- Kasse optisch beruhigt. Der gefüllte Checkout von apofit.at lag beim Bauen nicht als bestätigtes Bild vor, daher kein Pixel-Nachbau.
