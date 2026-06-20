# Kielkraft - Designsprache

Richtung: **vertrauenswürdiger deutscher Marine-Webshop**, abgeleitet aus den
Referenzen des Kunden (awn.de, bootshop-online.shop, aussenborder-markt.de).
Vertraute, konversionsstarke E-Commerce-Konvention statt editorialem Minimalismus –
sauber und einen Tick hochwertiger als der Wettbewerb umgesetzt.

## 1) Token-System

### Farbe
| Token | Hex | Rolle |
|-------|-----|-------|
| Navy | `#0B2F55` | Marke: Header, Mega-Nav, primäre CTA |
| Navy-900 | `#061B30` | Trust-Bar oben, Footer |
| Link-Blau | `#1366C4` | Produktlinks, sekundäre Aktionen |
| Signal-Rot | `#D8232A` | Sale/Rabatt-Badges, Ersparnis (sparsam) |
| Grün | `#1F9D55` | Lagerstatus „Lieferbar" |
| Gold | `#E0A12E` | „Bestseller / TOP" |
| Gischt-Grau | `#F3F6F9` | Seiten-Hintergrund |

### Typografie
- **Display/Headlines/Preise:** Archivo (700/800) – sturdy Grotesk, Retail-Autorität.
- **Body/UI:** Source Sans 3 (400/600/700) – sehr lesbar, vertrauenswürdig im Commerce.
- Preise/Specs mit **Tabellenziffern** (`tabular-nums`). Selbst-gehostet (woff2).

### Layout
- **Dreistufiger Header:** (1) Trust-/Service-Leiste (Telefon, Kauf auf Rechnung,
  transparente Fracht, Sprache) → (2) Logo + **prominente Suche** (Kategorie-Dropdown)
  + Konto + Warenkorb mit Betrag → (3) **Mega-Menü** mit Modell-Flyouts.
- **Home:** Promo-Hero → Trust-Strip → Kategorie-Grid → Bestseller-Grid →
  Marken-Showcase (Tohatsu/ePropulsion) → „Warum Kielkraft" → Kaufberater-CTA.
- **Kategorie:** Banner + **Filter-Sidebar** + Toolbar (Anzahl, Sortierung) + Produktgrid.
- **PDP:** Galerie + sticky **Kaufbox** (UVP durchgestrichen + Ersparnis-Badge,
  Varianten, Lager/Lieferzeit, CTA, Wallets, Micro-Trust, Rechnungskauf).
- Dichte: mittel-hoch, vergleichs-/spec-orientiert. Radien klein (4–12px).

### Signatur (ehrlich-differenzierend)
**Fracht- & Endpreis-Transparenz** als durchgängiges Element auf Karte + Kaufbox
(„inkl. MwSt. · zzgl. Fracht X €", „keine versteckten Kosten") – genau dort, wo der
Wettbewerber verschleiert. Schlägt die Referenzen auf der Vertrauensachse, ohne zu faken.

## 2) Klischee-Check (frontend-design-Skill)
Navy + Weiß + Signal-Rot ist **keine** der drei KI-Default-Paletten
(Creme/Serif/Terrakotta · Fast-Schwarz+Acid · Broadsheet-Haarlinien). Es ist die
**bewusst gewählte Branchen-Konvention der Kundenreferenzen** – und laut Skill gewinnt
die explizite Vorgabe des Briefs. Ausgeführt mit Präzision (Spacing, Typo, Trust).

## 3) Ehrlichkeit (Pflicht)
Keine erfundenen Bewertungen/Sterne, keine Fake-Countdowns oder erfundenen Lagerstände.
Rabatte nur aus echten UVP-Daten (durchgestrichen). Produktfotos sind markierte
Platzhalter, bis echte Hersteller-Fotos vorliegen. Rechtstexte als „anwaltlich prüfen".

## 4) Qualitäts-Boden
Responsiv bis Mobile (Header klappt, Mega-Nav → Burger), sichtbarer Tastatur-Fokus,
`prefers-reduced-motion`, Kontrast WCAG AA, selbst-gehostete Fonts (Speed/Datenschutz).

## Vorherige Richtung (verworfen)
Erste Iteration war editorial/„Instrumenten-Konsole" (Petrol/Brass/Teal, Bricolage +
IBM Plex). Auf Kundenwunsch zugunsten der Webshop-Konvention ersetzt.
