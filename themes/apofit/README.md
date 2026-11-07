# APOfit

## Purpose

Storefront für den APOfit-Shop. Öffentliche B2C-Preise und Bestellung als Gast, ruhige Natur-Optik, ein aufgeräumtes Kategorie-Menü. Staffel- und Sonderpreise erscheinen nur nach Anmeldung und nur, wenn der Server sie für genau dieses Konto liefert.

## What this theme changes

- Kopfzeile, Logofläche, grüne Kaufbuttons und Fußzeile im APOfit-Grün auf hellem Grund.
- Desktop-Menü öffnet jeweils nur eine Hauptkategorie mit ihren Unterpunkten. Die Einträge kommen aus dem Kategoriebaum des Shops, nicht aus festen Alt-Links.
- Auf dem Handy ein eigenes Menü mit Zurück, Suche und weiteren Ebenen, sofern der Shop sie mitliefert. Filter in einer Kategorie öffnen als Fläche über der Produktliste.
- Startseite mit Sortiment-Kacheln aus denselben Kategorien.
- Produktbilder bleiben vollständig im Rahmen sichtbar.
- Kasse bleibt das Shop-Formular: Kontakt, Adressen, Versand, Zahlung und Summe kommen vom Server. Die Zusammenfassung steht neben bzw. unter den Angaben, der Bestellbutton ist klar grün.
- Gäste sehen keine Staffelkarten und keine eingebetteten Staffelpreise. Angemeldete Kunden sehen nur die Staffeln, die der Server für ihre Sitzung schickt.
- Schrift: Maven Pro für Überschriften, Inter für Text. Beide stecken in der Theme-CSS, damit kein separater Schriftabruf 404 liefert.

## Scripts and libraries

- Shop-Laufzeit der Plattform für Warenkorb, Formulare und Abschnitte.
- Kleine Skripte direkt in Menü und Kategoriefilter: öffnen, schließen, Suche, Zurück, Escape. Ohne JavaScript bleibt ein Sortiment-Aufklapper mit den Hauptlinks, der Warenkorb funktioniert über normale Formulare.
- Schriftarten Inter und Maven Pro, SIL Open Font License, als Daten direkt in der Theme-CSS. Kein externes Schrift-CDN, kein Tailwind-CDN, keine weitere Bibliothek.
- In Shop- und Anmelde-Layout steht vor den Plattform-SEO-Angaben ein `noindex, nofollow`. Das ist die Demo-Sperre, solange die Plattform trotz ausgeschalteter Indexierung `index, follow` sendet.
- Keine Anbindung an andere Shops.

## Still on the default theme

Dieses Paket ist ein eigenes Theme mit eigener Kennung `apofit`. Es ersetzt das Standard-Theme nicht und ändert keine anderen Shops. Konto, Anmeldung, Bestellungen, Fehlerseiten und die übrigen mitgelieferten Seiten behalten den Aufbau des Standard-Themes und übernehmen nur Farben und Schrift.

Preise, Steuer, Versand, Zahlung, Gästebestellung und die Warenkorbrechnung berechnet weiterhin der Server. Dieses Theme zeigt sie nur an.

Neue Fassungen des Standard-Themes fließen nicht automatisch hier hinein, weil die Seiten in diesem Paket vollständig mitgeliefert werden.

## Vor dem Aktivieren

Die Kategorie-Navigation des Shops muss auf Mega-Menü stehen, sonst bleibt nur der Link „Kategorien“. Gästecheckout und EUR stellt der Shop, nicht dieses Theme. Nach dem Einschalten prüfen, dass Gäste Beträge sehen und keine Staffelpreise im Seitenquelltext stehen. Die Startseite unter `/home` auf dem bisherigen Standard-Theme liefert derzeit einen Fehler 500; dieses Theme bringt eine eigene Startseite mit. Erst in der Vorschau prüfen.
